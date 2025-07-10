<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musics extends Model
{
    use HasFactory;

    public $table = 'musics';

    public function artists()
    {
        return $this->hasOne(Artists::class, 'id', 'artists_id');
    }
}
