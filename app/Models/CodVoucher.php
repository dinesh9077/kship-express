<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodVoucher extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'voucher_no',
        'user_id',
        'order_id',
        'shipping_company_id',
        'amount',
        'voucher_date',
        'voucher_status',
        'reference_no',
        'payout_date',
        'created_at',
        'updated_at'
    ];
}
