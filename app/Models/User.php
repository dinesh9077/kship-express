<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'otp',
		'remember_token',
		'mobile',
		'profile_image',
		'address',
		'state',
		'city',
		'country',
		'role',
		'staff_id',
		'company_name',
		'zip_code',
		'gender',
		'charge_type',
		'charge',
		'status',
		'xpass',
		'kyc_status',
		'wallet_amount',
		'sms_date',
	];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	protected $dates = ['deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    public function hasPermissionTo($permission)
    {
        // Check if the user has the given permission
        return $this->permissions()->where('slug', $permission)->exists();
    }

    public function permissions()
    {
        // Define the relationship with the permissions table
        return $this->hasMany(Permission::class, 'user_id', 'id');
    }
	
    public function userKyc()
    {
        // Define the relationship with the permissions table
        return $this->hasOne(UserKyc::class, 'user_id');
    }
	
    public function createdBy()
    {
        // Define the relationship with the permissions table
        return $this->belongsTo(User::class, 'staff_id');
    }
	
	public function rolePermissions()
	{
		return $this->hasMany(RolePermission::class, 'user_id');
	}
	
	public function orders()
	{
		return $this->hasMany(Order::class, 'user_id');
	}
}
