<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'description', 'file_path'])]
class Video extends Model
{
    protected static function booted(): void
    {
        // Hapus file video dari storage saat datanya dihapus
        static::deleting(function (Video $video) {
            Storage::disk('local')->delete($video->file_path);
        });
    }
}