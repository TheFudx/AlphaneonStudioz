<?php

namespace App\Http\Controllers\MusicPlayer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Musics;

class HomeController extends Controller
{
    //

    public function index()
    {
        $musics = Musics::with('artists')->get();
        // return view('musicplayer.index', compact('musics'));
        return view('musicplayer.index_old', compact('musics'));
    }
}
