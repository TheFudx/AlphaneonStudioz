<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kluphs extends Model
{
    use HasFactory;

    protected $table = 'kluphs';
    // protected $guarded = array();
    protected $fillable = [
        'id',
'language_id',
'user_id',
'type_id',
'video_type',
'name',
'thumbnail',
'landscape',
'description',
'download',
'video_upload_type',
'video_320',
'video_480',
'video_720',
'video_1080',
'video_extension',
'video_duration',
'release_date',
'release_year',
'imdb_rating',
'view',
'status'
    ];
    // protected $casts = [
    //     'id' => 'integer',
    //     'language_id' => 'string',
    //     'user_id' => 'integer',
    //     'type_id' => 'integer',
    //     'video_type' => 'integer',
    //     'name' => 'string',
    //     'thumbnail' => 'string',
    //     'landscape' => 'string',
    //     'description' => 'string',
    //     'download' => 'integer',
    //     'video_upload_type' => 'string',
    //     'video_320' => 'string',
    //     'video_480' => 'string',
    //     'video_720' => 'string',
    //     'video_1080' => 'string',
    //     'video_extension' => 'string',
    //     'video_duration' => 'integer',
    //     'release_date' => 'string',
    //     'release_year' => 'string',
    //     'imdb_rating' => 'integer',
    //     'view' => 'integer',
    //     'status' => 'integer',
    // ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function cast()
    {
        return $this->belongsTo(Cast::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
