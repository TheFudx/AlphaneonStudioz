<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDownloads extends Model
{
    use HasFactory;

    public  $table = 'user_downloads';
    public $fillable = [
        'user_id',
        'video_id',
        'video_title',
        'video_description',
        'video_thumbnail',
        'video_type',
        'external_url',
        'cached_url'
    ];
}
