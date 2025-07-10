<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kluphs;
use App\Models\Klike;
class LikeController extends Controller
{
    public function klike(Request $request, Kluphs $kluphs)
    {
        $user = app('logged-in-user');
        $alreadyLiked = $kluphs->klikes()->where('user_id', $user->id)->exists();
        if ($alreadyLiked) {
            // Unlike the video
            $kluphs->klikes()->detach($user->id);
            return response()->json(['liked' => false, 'likesCount' => $kluphs->klikes()->count()]);
        }
        // Like the video
        $kluphs->klikes()->attach($user->id);
        return response()->json(['liked' => true, 'likesCount' => $kluphs->klikes()->count()]);
    }
}
