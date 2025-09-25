<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;
	protected $rowNumber = 0; // Start at 0 or 1 depending on if you use headings
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
	
    public function collection()
    {
        $currentDate = date('Y-m-d'); 
        $role = Auth::user()->role;
        $userId = Auth::id();

        $query = Order::with(['user', 'warehouse', 'customer', 'customerAddress', 'orderItems']);

        if ($role == "user") {
             $query->where('user_id', $userId);
        }

        if ($this->request->filled('user_id') && $this->request->user_id != "undefined") {
            $query->where('user_id', $this->request->user_id);
        }

        if ($this->request->filled('fromdate') && $this->request->filled('todate')) {
            $query->whereBetween('order_date', [$this->request->fromdate, $this->request->todate]);
        } elseif ($this->request->filled('fromdate')) {
            $query->whereDate('order_date', $this->request->fromdate);
        } elseif ($this->request->filled('todate')) {
            $query->whereDate('order_date', $this->request->todate);
        } else {
            $query->whereDate('order_date', $currentDate);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('created_at', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function($q) use ($search){
                        $q->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%") 
                          ->orWhere('mobile', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function($q) use ($search){
                        $q->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%")
                          ->orWhere('company_name', 'LIKE', "%{$search}%")
                          ->orWhere('mobile', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('warehouse', function($q) use ($search){
                        $q->where('contact_name', 'LIKE', "%{$search}%")
                          ->orWhere('contact_number', 'LIKE', "%{$search}%")
                          ->orWhere('warehouse_name', 'LIKE', "%{$search}%");
                    })  
                    ->orWhere('awb_number', 'LIKE', "%{$search}%")
                    ->orWhere('courier_name', 'LIKE', "%{$search}%")
                    ->orWhere('status_courier', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%")
                    ->orWhere('order_prefix', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('orders.id', 'desc')->get(); 
    }
	
	public function headings(): array
    {
        return [
            'Sr.No',
            'Seller Details',
            'Order Details',
            'Customer Details',
            'Customer Address',
            'City',
            'State',
            'Country',
            'Pincode',
            'Package Details',
            'Payment',
            'Pickup Address',
            'Status',
            'Created At',
        ];
    }

    public function map($order): array
    {
		$orderItems = $order->orderItems;
		$productDetails = $orderItems->map(fn ($item) => "<p>Weight In Kg: ".($item->dimensions['weight'] ?? '')." Length: ".($item->dimensions['length'] ?? '')." Width: ".($item->dimensions['width'] ?? '')." Height: ".($item->dimensions['height'] ?? '')."</p>")->implode(' | '); 
		$noofBox = $orderItems->sum('quantity');
		$totalWeightInKg = $orderItems->sum('dimensions.weight') ?? 0;
		
		$totalOrderTypeAmount =  $order->order_type == "cod" ? $order->cod_amount : $order->invoice_amount;
		$totalOrderTypeLabel =  $order->order_type == "cod" ? 'Cod Amount' : 'Invoice Amount';
		
		$warehouse = $order->warehouse;

		$addressParts = [
			$warehouse->warehouse_name ?? 'N/A',
			$warehouse->contact_name ?? 'N/A',
			'Address: ' . ($warehouse->address ?? 'N/A'),
			$warehouse->city ?? '',
			$warehouse->state ?? '',
			$warehouse->zip_code ?? '',
			$warehouse->contact_number ?? '',
		];

		$fullAddress = implode(', ', array_filter($addressParts));

		$this->rowNumber++; // Increment on each map
		
		$customeraddr = $order->customerAddress;  
		 
		$productDetails = $order->orderItems
			->map(fn($item) => "{$item->product_description} Amount: {$item->amount} No Of Box: {$item->quantity} Sku: {$item->sku}")
			->implode(' | ');		
		return [
			$this->rowNumber,

			Str::substr(optional($order->user)->name, 0, 4),

			collect([
				$order->order_prefix . $order->id,
				$order->awb_number ? "Awb Number: {$order->awb_number}" : null,
				$order->courier_name ? "Courier: {$order->courier_name}" : null,
				$productDetails ? "Product Details: {$productDetails}" : null,
			])->filter()->implode(" | "),

			collect([
				optional($order->customer)->first_name . ' ' . optional($order->customer)->last_name,
				optional($order->customer)->email,
				optional($order->customer)->mobile, 
			])->filter()->implode(" | "),
			
			$customeraddr->address,
			$customeraddr->city,
			$customeraddr->state,
			$customeraddr->country,
			$customeraddr->zip_code,
			
			collect([
				"No Of Box: {$noofBox}",
				"Weight In Kg: {$totalWeightInKg}",
				$productDetails ? strip_tags($productDetails) : null,
			])->filter()->implode(" | "),

			collect([
				$order->order_type,
				$totalOrderTypeLabel ? "{$totalOrderTypeLabel} : {$totalOrderTypeAmount}" : null,
			])->filter()->implode(" | "),

			$fullAddress,

			$order->status_courier,

			$order->created_at->format('Y-m-d H:i:s'),
		];  
    }
}
