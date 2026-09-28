<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MusicArtists;
use App\Models\MusicAlbums;
use App\Models\Genre;

class Musics extends Model
{
    use HasFactory;

    public $table = 'musics';

    public function artists()
    {
        return $this->hasOne(MusicArtists::class, 'id', 'artist_id');
    }
    public function albums()
    {
        return $this->hasOne(MusicAlbums::class, 'id', 'album_id');
    }
    public function geners()
    {
        return $this->hasOne(Genre::class, 'id', 'gener_id');
    }
}
