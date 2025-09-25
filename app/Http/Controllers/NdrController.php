<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;  
use App\Models\NdrDetail;  
use App\Models\NdrRequest;
use DB,Auth,File,Helper,Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NDRController extends Controller
{
    public function __construct()
	{ 
		$this->middleware('auth')->except(['orderLableGenerate','orderTrackingHistoryGlobal']);  
		error_reporting(0);
	}
		
    public function index()
	{  
		$status = request()->status ?? 'shipmozo'; 
		return view('ndr-order', compact('status')); 
	}
 
    public function ndrAjax(Request $request)
	{
		$draw = $request->post('draw');
		$start = $request->post("start");
		$limit = $request->post("length");
		$search = $request->input('search');

		$columnIndex = $request->post('order')[0]['column'] ?? 0;
		$order = $request->post('columns')[$columnIndex]['data'] ?? 'id';
		$dir = $request->post('order')[0]['dir'] ?? 'asc';
		
		$status = $request->post('status');

		if ($order == 'action') {
			$order = 'id';
		}

		$role = Auth::user()->role;
		$userId = Auth::user()->id;
		
		$query = NdrDetail::with(['orderAwb', 'orderAwb.user', 'orderAwb.customer']); 

		// Filter by shipping company
		$query->where('shipping_id', $status === "xpressbees" ? 1 : 2);

		// Restrict query based on role
		if ($role !== "admin") {
			$query->whereHas('orderAwb', function ($q) use ($userId) {
				$q->where('user_id', $userId);
			});
		}
		$query->whereHas('orderAwb.user');

		// Apply search filter with proper table references
		$query->when(!empty($search), function ($q) use ($search) {
			$q->whereHas('orderAwb.customer', function ($q) use ($search) {
				$q->where('first_name', 'LIKE', "%{$search}%")
				  ->orWhere('last_name', 'LIKE', "%{$search}%")
				  ->orWhere('mobile', 'LIKE', "%{$search}%");
			})
			->orWhereHas('orderAwb.user', function ($q) use ($search) {
				$q->where('name', 'LIKE', "%{$search}%")
				  ->orWhere('email', 'LIKE', "%{$search}%")
				  ->orWhere('company_name', 'LIKE', "%{$search}%")
				  ->orWhere('mobile', 'LIKE', "%{$search}%");
			})
			->orWhereHas('orderAwb', function ($q) use ($search) {
				$q->where('awb_number', 'LIKE', "%{$search}%") 
				  ->orWhere('status_courier', 'LIKE', "%{$search}%")
				  ->orWhere('order_manual_id', 'LIKE', "%{$search}%")
				  ->orWhere('created_at', 'LIKE', "%{$search}%")
				  ->orWhere('id', 'LIKE', "%{$search}%");
			});
		});
 
		// Get total records count before applying limit/offset
		$totalData = $query->count();
		$totalFiltered = $query->count();

		// Apply pagination & sorting
		$orders = $query->orderBy("ndr_details.ndr_date", "desc")
			->offset($start)
			->limit($limit)
			->get();
		 
		$data = [];
		$i = $start + 1;

		foreach ($orders as $order)
		{ 
			$userName = $order->orderAwb && $order->orderAwb->user ? $order->orderAwb->user->name : 'N/A'; 
			$userEmail = $order->orderAwb && $order->orderAwb->user ? $order->orderAwb->user->email : 'N/A'; 
			$userMobile = $order->orderAwb && $order->orderAwb->user ? $order->orderAwb->user->mobile : 'N/A'; 
			$userCompanyName = $order->orderAwb && $order->orderAwb->user ? $order->orderAwb->user->company_name : 'N/A';
			
			$customerFName = $order->orderAwb && $order->orderAwb->customer ? $order->orderAwb->customer->first_name : 'N/A'; 
			$customerLName = $order->orderAwb && $order->orderAwb->customer ? $order->orderAwb->customer->last_name : 'N/A'; 
			$customerEmail = $order->orderAwb && $order->orderAwb->customer ? $order->orderAwb->customer->email : 'N/A'; 
			$customerMobile = $order->orderAwb && $order->orderAwb->customer ? $order->orderAwb->customer->mobile : 'N/A';  
			
			$totalAmount = $order->orderAwb->total_amount ?? 0;
			$orderType = $order->orderAwb->order_type ?? '';
			$orderStatus = $order->orderAwb->status_courier ?? 'New';
			
			$shippingId = $order->orderAwb->shipping_company_id ?? '';
			$orderId = $order->orderAwb->id ?? '';
			$awbNo = $order->orderAwb->awb_number ?? '';
			$orderManualId = $order->orderAwb->order_manual_id ?? '';
			$orderDate = $order->orderAwb->order_date ?? null; 
			$orderDate = $orderDate ? date('d-m-Y', strtotime($orderDate)) : '';
			 
			$ndrRequest = NdrRequest::where('order_id', $orderId)->orderByDesc('created_at')->first();

			$takeAction = ($ndrRequest && $ndrRequest->created_at->toDateString() > $order->ndr_date || in_array(strtolower($orderStatus), ["delivered", "rto"])) ? "" : 
			"<div class='main-btn-1'>
				<a href='javascript:;'>
					<button type='button' class='btn-light-1' 
						onclick='openNdrUpdateModal(this, event)' 
						data-id='{$orderId}' 
						data-awb_number='{$awbNo}' 
						data-shipping_id='{$shippingId}'> 
						Take Action 
					</button>
				</a>
			</div>";
             
			$data[] = [
				'id' => $i++,
				'order_date' => "<div class='main-cont1-2'>
									<p>#{$orderManualId}</p> 
									<p>#{$order->awb_number}</p> 
									<p>{$orderDate}</p> 
								 </div>",
				'ndr_date' => date('d-m-Y', strtotime($order->ndr_date)),
				'seller' => "<div class='main-cont1-2'>
										<p>{$userName} ({$userCompanyName})</p>
										<p>{$userEmail}</p>
										<p>{$userMobile}</p>
									 </div>", 
				'customer' => "<div class='main-cont1-2'>
										<p>{$customerFName} {$customerLName}</p>
										<p>{$customerEmail}</p>
										<p>{$customerMobile}</p>
									   </div>", 
				'payment' => "<div class='main-cont1-2'>
										<p>{$totalAmount}</p>
										<p class='" . strtolower($orderType) . "'>{$orderType}</p>
									</div>",
				'status_courier' => "<p class='prepaid'>{$orderStatus}</p>",
				'exception_info' => "<div class='main-cont1-2'>
										<p>{$order->total_attempts} Attempt(s)</p> 
										<p>{$order->remarks}</p>  
									 </div>",
				'action' => $order->total_attempts < 3 ? $takeAction : ''
			];
		}

		return response()->json([
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
		]);
	}

    public function raiserequest(Request $request)
	{ 
		$validator = Validator::make($request->all(), [
			'order_id' => 'required|exists:orders,id',
			'action' => 'required|in:re-attempt,change_address,change_phone',
			'shipping_company_id' => 'required',
			'awb_number' => 'required',
			'reattemptdate' => 'required|date',
			'remarks' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()]);
		}

		try {
			$shipping_id = $request->input('shipping_company_id');
			$awb_number = $request->input('awb_number');
			$action = $request->input('action');

			if ($shipping_id == 1) {
				$url = 'https://shipment.xpressbees.com/api/ndr/create';
				$api_token = Helper::xpressBeesToken($shipping_id);

				if (!$api_token) {
					return response()->json(['status' => 'error', 'msg' => 'Failed to retrieve API token']);
				}

				$token = 'Bearer ' . $api_token;

				// Construct action_data dynamically based on action type
				$actionData = [];
				
				$actionData = ['re_attempt_date' => $request->input('reattemptdate')]; 
				if ($action === 'change_address') {
					$actionData = [ 
						'address_1' => $request->input('address_1')
					];
				} elseif ($action === 'change_phone') {
					$actionData = [
						'phone' => $request->input('phone'),
					];
				}
				$actionData = ['remarks' => $request->input('remarks')];
				// Final request payload
				$data = [
					[
						"awb" => $awb_number,
						"action" => $action,
						"action_data" => $actionData
					]
				];

				// Perform API request
				$response = Http::withHeaders([
					'Content-Type' => 'application/json',
					'Authorization' => $token,
				])->post($url, $data);

				// Handle API response
				if ($response->successful()) {
					$res = $response->json();

					if (!empty($res[0]['status']) && $res[0]['status'] !== false) 
					{ 
						DB::beginTransaction();

						NdrRequest::create([
							'order_id' => $request->input('order_id'),
							'action' => $action,
							'shipping_company_id' => $shipping_id,
							'reattemptdate' => $request->input('reattemptdate'),
							'remarks' => $request->input('remarks'),
							'awb_number' => $awb_number, 
							'address_1' => $request->input('address_1', null), 
							'phone' => $request->input('phone', null),
						]);
 
						DB::commit();

						return response()->json(['status' => 'success', 'msg' => 'NDR Request raised successfully.']);
					}

					return response()->json(['status' => 'error', 'msg' => $res[0]['message']]);
				}

				return response()->json(['status' => 'error', 'msg' => $response->body()]);
			}
		} catch (\Exception $e) {
		
			DB::rollBack();
			return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
		}
	}

}