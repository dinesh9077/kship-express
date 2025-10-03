<?php

namespace App\Http\Controllers;

use App\Models\CourierCommission;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class CourierCommissionController extends Controller
{
    public function index()
    {
        $commissions = CourierCommission::all();
        $shippingCompanies = ShippingCompany::select('id', 'name')->where('status',1)->get();
        return view('courier_commission.index', compact('commissions', 'shippingCompanies'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'commissions' => 'required|array',
            'commissions.*.id' => 'nullable|exists:courier_commissions,id',
            'commissions.*.shipping_company' => 'required|string',
            'commissions.*.courier_id' => 'required|string',
            'commissions.*.courier_name' => 'required|string',
            'commissions.*.type' => 'required|in:fix,percentage',
            'commissions.*.value' => 'required|numeric' 
        ]);
        
        $ids = array_filter(array_column($data['commissions'], 'id'));
        CourierCommission::whereNotIn('id', $ids)->delete();
        foreach ($data['commissions'] as $commission) {
          
            if (!empty($commission['id'])) {
                // update existing
                CourierCommission::where('id', $commission['id'])->update([
                    'shipping_company' => $commission['shipping_company'],
                    'courier_id' => $commission['courier_id'],
                    'courier_name' => $commission['courier_name'],
                    'type' => $commission['type'],
                    'value' => $commission['value'],
                ]);
            } else {
                // create new
                CourierCommission::create([
                    'shipping_company' => $commission['shipping_company'],
                    'courier_id' => $commission['courier_id'],
                    'courier_name' => $commission['courier_name'],
                    'type' => $commission['type'],
                    'value' => $commission['value'],
                ]);
            }
        }

        return redirect()->route('courier.commission')
            ->with('success', 'Courier commissions updated successfully.');
    }


}
