<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierWarehouse extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'user_id',
        'warehouse_name',
        'shipping_id',
        'warehouse_status',
        'company_name',
        'contact_name',
        'contact_number',
        'address',
        'delhivery_status',
        'api_response',
        'delhivery_status1',
        'api_response1',
        'city',
        'state',
        'country',
        'zip_code'
    ];
	
	protected $casts = [
        'api_response' => 'array', // Cast JSON to an array automatically
        'api_response1' => 'array', // Cast JSON to an array automatically
    ];
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
