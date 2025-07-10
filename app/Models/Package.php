<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;

class Package extends Model
{
    use HasFactory;

    protected $table = 'package';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'price' => 'integer',
        'type_id' => 'string',
        'type' => 'string',
        'time' => 'string',
        'android_product_package' => 'string',
        'ios_product_package' => 'string',
    ];

    public function type() {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
