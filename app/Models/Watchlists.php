<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlists extends Model
{
    use HasFactory;
    protected $table = 'watchlists';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'video_id' => 'integer',
        'user_id' => 'integer',
    ];
}
