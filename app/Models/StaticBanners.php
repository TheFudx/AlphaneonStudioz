<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticBanners extends Model
{
    use HasFactory;
    protected $table = 'static_banners';
    protected $guarded = array();

    protected $staticbanners = [
      'id', 'title', 'release_description',  'url', 'thumbnail_url', 'landscape_url',
    ];
}
