<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;
	
	protected $fillable = [
		'user_id',
		'amount',
		'status',
		'order_id',
		'transaction_type',
		'txn_number',
		'payable_response',
		'payment_receipt',
		'note',
		'transaction_status',
		'reject_note',
	];

	protected $casts = [
		'payable_response' => 'array',
	];
	protected $hidden = [
		'payable_response',
	];
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

}
