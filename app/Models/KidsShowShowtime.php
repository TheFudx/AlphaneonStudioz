<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowShowtime extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_showtimes';
    protected $fillable = [
        'show_id', 'stage_id','date', 'start_time'
    ];

    public function show()
    {
        return $this->belongsTo(KidsShow::class);
    }
    
    public function stage()
    {
        return $this->belongsTo(KidsShowStage::class);
    }
}
