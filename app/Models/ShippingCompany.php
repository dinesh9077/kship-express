<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class ShippingCompany extends Model
{
    use HasFactory;   
	
	// Specify the fields that are allowed for mass assignment
    protected $fillable = [
        'user_id',
        'name',
        'api_key',
        'secret_key',
        'email',
        'password',
        'url',
        'mode',
        'status',
        'tax',
        'logo',
        'created_at',
        'updated_at', 
        'expired_at',
    ];
	
	public function hasExpired()
    {
        $expirationDateTime = Carbon::parse($this->expired_at);
        $currentDateTime = Carbon::now(); 
        return $currentDateTime->diffInMinutes($expirationDateTime) > 30; // 60 minutes = 1 hour
    }
}
