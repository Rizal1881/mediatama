<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Video;
use App\Models\VideoAccess;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Permintaan terbaru customer ini untuk tiap video
        $latestRequests = AccessRequest::with('videoAccess')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->unique('video_id')
            ->keyBy('video_id');

        $items = Video::latest()->get()->map(function (Video $video) use ($latestRequests) {
            $request = $latestRequests->get($video->id);
            $access = $request?->videoAccess;

            if (! $request) {
                $status = 'none';
            } elseif ($request->status === 'pending') {
                $status = 'pending';
            } elseif ($request->status === 'rejected') {
                $status = 'rejected';
            } else {
                // approved: aktif atau sudah habis
                $status = ($access && $access->isActive()) ? 'active' : 'expired';
            }

            return [
                'video' => $video,
                'status' => $status,
                'expires_at' => $access?->expires_at,
            ];
        });

        return view('customer.videos.index', ['items' => $items]);
    }

    public function requestAccess(Video $video)
    {
        $user = auth()->user();

        $latest = AccessRequest::with('videoAccess')
            ->where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->latest('id')
            ->first();

        if ($latest) {
            if ($latest->status === 'pending') {
                return back()->with('error', 'Permintaan Anda masih menunggu persetujuan admin.');
            }

            if ($latest->status === 'approved' && $latest->videoAccess?->isActive()) {
                return back()->with('error', 'Akses Anda untuk video ini masih aktif.');
            }
        }

        AccessRequest::create([
            'user_id' => $user->id,
            'video_id' => $video->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan akses berhasil dikirim. Silakan tunggu persetujuan admin.');
    }

    public function stream(Video $video)
    {
        // Akses milik user yang sedang login, untuk video ini saja
        $access = VideoAccess::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->latest('expires_at')
            ->first();

        abort_unless($access && $access->isActive(), 403, 'Akses video tidak berlaku.');

        $disk = Storage::disk('local');
        abort_unless($disk->exists($video->file_path), 404);

        return response()->file($disk->path($video->file_path), [
            'Content-Type' => $disk->mimeType($video->file_path) ?: 'video/mp4',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function watch(Video $video)
    {
        $access = VideoAccess::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->latest('expires_at')
            ->first();

        if (! $access || ! $access->isActive()) {
            return redirect()
                ->route('customer.videos.index')
                ->with('error', 'Akses video tidak berlaku. Silakan request akses.');
        }

        // Sisa waktu dihitung di server supaya tidak bergantung pada jam komputer customer
        $remaining = max(0, (int) now()->diffInSeconds($access->expires_at, false));

        return view('customer.videos.watch', [
            'video' => $video,
            'remaining' => $remaining,
            'expiresAt' => $access->expires_at,
        ]);
    }
}