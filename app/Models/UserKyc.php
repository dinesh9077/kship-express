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
		'pancard_status',
		'pancard_text',
		'pan_full_name',
		'pancard_category',
		'pan_reason',
		'aadhar',
		'aadhar_front',
		'aadhar_status',
		'aadhar_text',
		'aadhar_full_name',
		'aadhar_address',
		'aadhar_dob',
		'aadhar_gender',
		'aadhar_zip',
		'aadhar_reason',
		'aadhar_role',
		'pan_role',
	];

	// Cast boolean fields properly
	protected $casts = [ 
		'pancard_text' => 'array',
		'aadhar_text' => 'array',
	];
		
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
}
