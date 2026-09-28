<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Season;
use App\Models\Episodes;

class Series extends Model
{
    use HasFactory;

    protected $table = 'series';
    protected $guarded = array();

    protected $Series = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'image' => 'string',
        'image_url' => 'string',
        
        'type' => 'string',
        'status' => 'integer',
       
    ];
    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
    public function episodes()
{
    return $this->hasMany(Episodes::class, 'series_id');
}
}