<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
	
	protected $fillable = [
		'user_id',
		'awb_number',
		'contact_name',
		'contact_phone',
		'ticket_no',
		'text',
		'revert',
		'status',
		'created_at',
		'updated_at'
	];

}
