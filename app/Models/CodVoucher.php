<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodVoucher extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'id', 'voucher_no',	'user_id', 'remarks', 'amount',	'voucher_date', 'voucher_status', 'reference_no', 'payout_date', 'created_at', 'updated_at'
    ];
	 
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	 
	
	public function codVoucherOrders()
	{
		return $this->hasMany(Order::class, 'cod_voucher_id');
	}
}
