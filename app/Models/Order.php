<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Order extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'user_id', 'order_prefix', 'order_type', 'vendor_id', 'vendor_address_id',
        'customer_id', 'customer_address_id', 'shipping_company_id', 'shipping_mode',
        'tax', 'tax_percentage', 'round_off', 'shipping_charge', 'total_amount',
        'status_courier', 'courier_logo', 'reason_cancel', 'order_date', 'order_cancel_date', 'delivery_date',
        'status', 'is_online', 'weight', 'length', 'width', 'height', 'packaging_id',
        'shipment_id', 'awb_number', 'courier_name', 'courier_id', 'label', 'manifest',
        'weight_status', 'weight_update_date', 'applicable_weight', 'weight_freeze_status',
        'percentage_amount', 'is_voucher', 'voucher_no', 'is_remittance', 'is_payout',
        'remittance_reference_id', 'remittance_amount', 'remittance_date', 'is_invoice',
        'cod_charges', 'rto_charge', 'pickup_location_name', 'pickup_time', 'pickup_id',
        'pickup_date', 'expected_package_count', 'on_pro_id', 'warehouse_id', 'freight_mode',
        'cod_amount', 'dimension_type', 'invoice_no', 'invoice_amount', 'ewaybillno', 'lr_no',
		'api_response', 'api_response1', 'invoice_document', 'weight_order', 'insurance_type', 'is_fragile_item'
    ];
	
	protected $casts = [ 
		'api_response' => 'array',
		'api_response1' => 'array',
		'invoice_document' => 'array'
	];
	
	public static function generateOrderNumber($userId)
	{  
		$lastOrderCount = self::where('user_id', $userId)
        ->count();
		
        $orderNumber = null;
        $increment = $lastOrderCount + 1; 
				
        do { 
			$orderNumber = (int) ($userId . $increment);
 
            $exists = self::where('order_prefix', $orderNumber)
                ->exists();
    
            $increment++;
        } while ($exists);
		
        return $orderNumber;
	}
	
	public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id');
	}
	
	public function customerAddress()
	{
		return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
	}
	
	public function warehouse()
	{
		return $this->belongsTo(CourierWarehouse::class, 'warehouse_id');
	}
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function shippingCompany()
	{
		return $this->belongsTo(ShippingCompany::class, 'shipping_company_id');
	}
	
	public function orderItems()
	{
		return $this->hasMany(OrderItem::class, 'order_id');
	}
	
	public function excessWeight()
	{
		return $this->hasOne(ExcessWeight::class, 'order_id');
	}
	 
}
