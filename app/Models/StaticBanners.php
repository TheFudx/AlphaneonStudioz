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

    public function getVideoIdAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }
        $details = $this->watchlist_details;
        return $details['id'] ?? null;
    }

    public function getCategoryIdAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }
        $details = $this->watchlist_details;
        return $details['category_id'] ?? null;
    }

    public function getWatchlistDetailsAttribute()
    {
        $id = $this->attributes['video_id'] ?? null;
        $typeId = $this->attributes['type_id'] ?? null;
        $categoryId = $this->attributes['category_id'] ?? null;

        // If type_id is 10 (Web Series), the target type is 'episode'
        $type = ($typeId == 10) ? 'episode' : 'video';

        // If ID is empty/null, try to resolve it from the URL
        if (empty($id) && !empty($this->url)) {
            // Strip query params and hash
            $cleanUrl = strtok($this->url, '?#');
            $path = parse_url($cleanUrl, PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $lastSegment = end($segments);

            if ($lastSegment) {
                if ($type == 'episode') {
                    // Try to decode/decrypt the ID if it's hashed
                    $decodedId = \App\Helpers\VideoHelper::decryptID($lastSegment);
                    if ($decodedId) {
                        $id = $decodedId;
                        $episode = \App\Models\Episodes::find($id);
                        if ($episode && empty($categoryId)) {
                            $categoryId = $episode->category_id;
                        }
                    } else {
                        // Or try to find episode by slug
                        $episode = \App\Models\Episodes::where('slug', $lastSegment)->first();
                        if ($episode) {
                            $id = $episode->id;
                            if (empty($categoryId)) {
                                $categoryId = $episode->category_id;
                            }
                        }
                    }
                } else {
                    // Try to decode/decrypt the ID if it's hashed
                    $decodedId = \App\Helpers\VideoHelper::decryptID($lastSegment);
                    if ($decodedId) {
                        $id = $decodedId;
                        $video = \App\Models\Video::find($id);
                        if ($video && empty($categoryId)) {
                            $categoryId = $video->category_id;
                        }
                    } else {
                        // Or try to find video by slug
                        $video = \App\Models\Video::where('slug', $lastSegment)->first();
                        if ($video) {
                            $id = $video->id;
                            if (empty($categoryId)) {
                                $categoryId = $video->category_id;
                            }
                        }
                    }
                }
            }
        }

        // If still empty, try to match by exact title
        if (empty($id) && !empty($this->title)) {
            if ($type == 'episode') {
                $episode = \App\Models\Episodes::where('name', $this->title)->first();
                if ($episode) {
                    $id = $episode->id;
                    if (empty($categoryId)) {
                        $categoryId = $episode->category_id;
                    }
                }
            } else {
                $video = \App\Models\Video::where('name', $this->title)->first();
                if ($video) {
                    $id = $video->id;
                    if (empty($categoryId)) {
                        $categoryId = $video->category_id;
                    }
                }
            }
        }

        return [
            'id' => $id,
            'type' => $type,
            'type_id' => $typeId,
            'category_id' => $categoryId,
        ];
    }
}
