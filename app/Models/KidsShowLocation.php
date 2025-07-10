<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidsShowLocation extends Model
{
    use HasFactory;
    protected $connection = 'second_db'; // Use the second database
    protected $table = 'kids_show_locations';
    
    protected $fillable = [
        'name', 'location', 'total_stage'
    ];
}
