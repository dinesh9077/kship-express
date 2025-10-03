<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_company',
        'courier_id',
        'courier_name',
        'type',
        'value',
    ];
}
