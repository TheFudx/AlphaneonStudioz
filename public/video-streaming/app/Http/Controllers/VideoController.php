<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi,flv|max:20000',
        ]);

        $video = $request->file('video');
        $originalPath = $video->store('videos/original');

        $videoModel = new Video();
        $videoModel->original_video = $originalPath;
        $videoModel->save();

        $this->transcodeVideo($videoModel);

        return redirect()->back()->with('success', 'Video uploaded and transcoding started.');
    }

    private function transcodeVideo(Video $video)
    {
        try {
            $path = storage_path('app/' . $video->original_video);
            $ffmpeg = FFMpeg::create();
            $videoStream = $ffmpeg->open($path);
    
            $qualities = [
                '240' => 240,
                '360' => 360,
                '480' => 480,
                '720' => 720,
                '1080' => 1080,
            ];
    
            foreach ($qualities as $key => $height) {
                $outputDir = storage_path('app/videos/' . $key);
                if (!file_exists($outputDir)) {
                    mkdir($outputDir, 0755, true);
                }
                $outputPath = $outputDir . '/' . basename($path, '.mp4') . '_' . $key . '.mp4';
    
                $thumbnailDir = storage_path('app/videos/thumbnails');
                if (!file_exists($thumbnailDir)) {
                    mkdir($thumbnailDir, 0755, true);
                }
                $thumbnailPath = $thumbnailDir . '/' . basename($path, '.mp4') . '_' . $key . '.png';
    
                // Log the paths
                Log::info("Transcoding to: " . $outputPath);
                Log::info("Saving thumbnail to: " . $thumbnailPath);
    
                $videoStream
                    ->filters()
                    ->resize(new \FFMpeg\Coordinate\Dimension($height, $height))
                    ->synchronize();
    
                // Save the frame
                $videoStream
                    ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(10))
                    ->save($thumbnailPath);
    
                $format = new X264();
                $format->setKiloBitrate(500);
                $videoStream->save($format, $outputPath);
    
                // Log after saving the video
                Log::info("Saved transcoded video to: " . $outputPath);
    
                $video->{'transcoded_video_' . $key} = 'videos/' . $key . '/' . basename($path, '.mp4') . '_' . $key . '.mp4';
            }
    
            $video->status = 'completed';
            $video->save();
    
            // Log completion
            Log::info('Transcoding completed for video: ' . $video->id);
    
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error during transcoding: ' . $e->getMessage());
        }
    }
    
    public function index()
    {
        $videos = Video::where('status', 'completed')->get();
        return view('index', compact('videos'));
    }
}
