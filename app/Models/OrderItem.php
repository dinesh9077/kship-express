<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
class OrderItem extends Model
{
    use HasFactory;
	  
	protected $fillable = [
        'order_id', 'product_category', 'product_name', 'product_image', 
        'product_discription', 'amount', 'quantity', 'ewaybillno', 'dimensions',
    ];

    protected $casts = [
        'dimensions' => 'array', // Cast dimensions as an array
    ];
}
