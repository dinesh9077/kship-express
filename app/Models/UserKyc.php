<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
    use HasFactory;
	// Fillable fields
	protected $fillable = [
		'user_id',
		'pancard',
		'pancard_image',
		'pancard_status',
		'aadhar',
		'aadhar_front',
		'aadhar_back',
		'aadhar_status',
		'gst',
		'gst_image',
		'gst_status',
		'bank_passbook',
		'bank_name',
		'account_holder_name',
		'account_number',
		'ifsc_code',
		'bank_status',
		'pancard_text',
		'aadhar_text',
		'bank_text',
		'gst_text',
		'aadhar_full_name',
		'pan_full_name'
	];

	// Cast boolean fields properly
	protected $casts = [
		'pancard_status' => 'boolean',
		'aadhar_status' => 'boolean',
		'gst_status' => 'boolean',
		'bank_status' => 'boolean',
		'pancard_text' => 'array',
		'aadhar_text' => 'array',
	];
		
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
