<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'season';
    protected $guarded = array();

    protected $Season = [
        'id' => 'integer',
        'series_id' => 'string',
        'season' => 'string',
       
       
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }
    public function episodes()
    {
        return $this->hasMany(Episodes::class);
    }
}