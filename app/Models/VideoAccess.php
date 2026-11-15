<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['access_request_id', 'user_id', 'video_id', 'start_at', 'expires_at'])]
class VideoAccess extends Model
{
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function accessRequest(): BelongsTo
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    // Akses dianggap aktif kalau sekarang berada di antara waktu mulai dan waktu habis
    public function isActive(): bool
    {
        return now()->between($this->start_at, $this->expires_at);
    }
}