<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Http;
use Illuminate\Support\Facades\Response;
use App\Models\Video;
use App\Models\UserDownloads;
use Auth;
class VideoDownloadController extends Controller
{
    //
    public function downlodedVideos(){
       
        $userDownloads = UserDownloads::join('video','video.id','=','user_downloads.video_id')
                            ->where('user_downloads.user_id', auth()->id())
            ->orderBy('user_downloads.created_at', 'desc')
            ->get();


        return view('downloded-videos', compact('userDownloads'));
    }
    public function deleteDownloadFromDatabase(Request $request, $downloadId)
    {
        if (!Auth::check()) {
            return response()->json(['success'=>false,'message' => 'Unauthorized'], 401);
        }

        try {
            $userDownload = UserDownloads::where('video_id', $downloadId)
                                        ->where('user_id', Auth::id()) // Ensure user can only delete their own downloads
                                        ->first();

            if (!$userDownload) {
                Log::warning('Attempted to delete non-existent or unauthorized download.', ['download_id' => $downloadId, 'user_id' => Auth::id()]);
                return response()->json(['success'=>false,'message' => 'Download record not found or not authorized.'], 404);
            }

            $userDownload->delete();
            Log::info('Successfully deleted download record from database.', ['download_id' => $downloadId, 'video_url' => $userDownload->video_url, 'user_id' => Auth::id()]);

            return response()->json(['success'=>true,'message' => 'Download record deleted successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting download record from database: ' . $e->getMessage(), ['download_id' => $downloadId, 'user_id' => Auth::id(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success'=>false,'message' => 'Failed to delete download record.'], 500);
        }
    }
    public function proxyVideo($externalUrl,Request $request){
        Log::debug('Proxy route hit. External URL:', ['url' => $externalUrl]);

        if (!filter_var($externalUrl, FILTER_VALIDATE_URL) || !str_contains($externalUrl, 'alphastudioz.in/admin_panel/public/')) {
            Log::warning('Invalid external URL detected.', ['url' => $externalUrl]);
            abort(400, 'Invalid video URL provided.');
        }

        try {
            Log::debug('Attempting to fetch from external URL:', ['url' => $externalUrl]);
            
            // TEMPORARY: Disable SSL verification for debugging
            $response = Http::withoutVerifying()->timeout(300)->get($externalUrl);
            // REVERT THIS LINE FOR PRODUCTION:
            // $response = Http::timeout(300)->get($externalUrl); // Original line

            Log::debug('External HTTP response status:', ['status' => $response->status()]);

            if (!$response->successful()) { // Check if the external request was NOT successful
                Log::error('External video fetch failed!', [
                    'externalUrl' => $externalUrl,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                abort($response->status(), 'Failed to fetch video from external source.');
            }

            $headers = $response->headers();
            $originalContentLength = $headers['Content-Length'][0] ?? null;
            $range = $request->header('Range');

            if ($range && $originalContentLength) {
                preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);
                $start = (int) $matches[1];
                $end = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : ($originalContentLength - 1);
                $length = $end - $start + 1;

                $rangedResponse = Http::withoutVerifying() // Apply withoutVerifying here too
                                    ->withHeaders(['Range' => $range])
                                    ->get($externalUrl);

                if ($rangedResponse->successful()) {
                    
                    return Response::stream(function () use ($rangedResponse) {
                        echo $rangedResponse->body();
                    }, 206, [
                        'Content-Type' => $rangedResponse->header('Content-Type'),
                        'Accept-Ranges' => 'bytes',
                        'Content-Length' => $rangedResponse->header('Content-Length'),
                        'Content-Range' => $rangedResponse->header('Content-Range'),
                    ]);
                } else {
                    Log::error('Failed to fetch video range from external server.', ['url' => $externalUrl, 'status' => $rangedResponse->status(), 'body' => $rangedResponse->body()]);
                    abort($rangedResponse->status(), 'Failed to fetch video range from external server.');
                }

            } else {
                if ($response->status() == 200) {
                    $video = Video::active()->where('video_320_url', $externalUrl)->first();
                    $userDownload = new UserDownloads();
                    $userDownload->user_id = auth()->id();
                    $userDownload->video_id = $video->id;
                    $userDownload->video_title = $video->name;
                    $userDownload->video_description = $video->description;
                    $userDownload->video_thumbnail = $video->landscape_url;
                    $userDownload->video_type = 'video';
                    $userDownload->external_url = 'http://127.0.0.1:8000/proxy-video/'.$externalUrl;
                    $userDownload->cached_url = 'http://127.0.0.1:8000/proxy-video/'.$externalUrl; // Assuming cached_url is the same    
                    $userDownload->save();
                }
                return Response::stream(function () use ($response) {echo $response->body();}, 200, 
                [
                    'Content-Type' => $headers['Content-Type'][0] ?? 'application/octet-stream',
                    'Content-Length' => $originalContentLength,
                    'Accept-Ranges' => 'bytes',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Video proxy error (caught exception): ' . $e->getMessage(), ['url' => $externalUrl, 'trace' => $e->getTraceAsString()]);
            abort(500, 'Internal server error during video proxying.');
        }
    }
}
