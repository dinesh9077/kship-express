<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcessWeight extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'order_id',
        'chargeable_weight',
        'excess_weight',
        'excess_charge',
        'status',
        'image_path',
        'created_at',
        'updated_at'
    ];
}
