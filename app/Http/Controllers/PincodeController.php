<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Imports\PincodeChargeImport;
	use App\Models\PincodeService;
	use App\Models\Permission;
	use Excel,Auth;
	class PincodeController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');   
		}
		
		public function index()
		{
			return view('pincode-service.index');
		}
		
		public function import(Request $request)
		{
			$file = $request->file('excel_file');
			$extension = $file->getClientOriginalExtension();
			
			if($extension === 'xlsx') 
			{
				try
				{	
					Excel::import(new PincodeChargeImport, $request->file('excel_file'));  
					return response()->json(['status'=>'success','msg'=>'The Excel has been imported successfully.']);
				}
				catch(\Exception $e)
				{
					return response()->json(['status'=>'error','msg'=>$e->getMessage()]);
				} 
			}
			else 
			{
				return response()->json(['status'=>'error','msg'=>'The uploaded file is not an Excel file.']); 
			}   
		}
		
		public function listAjax(Request $request)
		{
		    
			$draw = $request->post('draw');
			$start = $request->post("start");
			$limit = $request->post("length"); // Rows display per page
			
			$columnIndex_arr = $request->post('order');
			$columnName_arr = $request->post('columns');
			$order_arr = $request->post('order');
			$search_arr = $request->post('search');
			$status = $request->post('status');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			
			if($order = 'action')  
			{
				$order = 'id';
			}
			
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = PincodeService::where('id','!=','');  
			$totalData = $query->get()->count();
			
			$totalFiltered = PincodeService::where('id','!=','');   
			 
			
			$values = PincodeService::where('id','!=',''); 
			$values->offset($start)->limit($limit)->orderBy($order,$dir);
			$users_permission = Permission::where('user_id',$id)->where('slug','6')->first();
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('origin_pincode', 'LIKE',"%{$search}%")->where('origin_city', 'LIKE',"%{$search}%")->orWhere('origin_state', 'LIKE',"%{$search}%")->orWhere('origin_center', 'LIKE',"%{$search}%")->orWhere('des_pincode', 'LIKE',"%{$search}%")->orWhere('des_city', 'LIKE',"%{$search}%")->orWhere('des_state', 'LIKE',"%{$search}%")->orWhere('shipping_charge', 'LIKE',"%{$search}%")->orWhere('created_at', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('origin_pincode', 'LIKE',"%{$search}%")->where('origin_city', 'LIKE',"%{$search}%")->orWhere('origin_state', 'LIKE',"%{$search}%")->orWhere('origin_center', 'LIKE',"%{$search}%")->orWhere('des_pincode', 'LIKE',"%{$search}%")->orWhere('des_city', 'LIKE',"%{$search}%")->orWhere('des_state', 'LIKE',"%{$search}%")->orWhere('shipping_charge', 'LIKE',"%{$search}%")->orWhere('created_at', 'LIKE',"%{$search}%");
				});  
			}
			
			$values = $values->get(); 
			$totalFiltered = $totalFiltered->count();
			
			$data = array();
			if(!empty($values))
			{
				$i = $start + 1; 
				foreach ($values as $value)
				{    
					if($value->status == 1)
					{
						$status = '<p class="prepaid">Active</p>';
					}
					else
					{
						$status = '<p class="cod">In-Active</p>';
					}
					$mainData['id'] = $i; 
					$mainData['origin_pincode'] = $value->origin_pincode;
					$mainData['origin_city'] = $value->origin_city;
					$mainData['origin_state'] = $value->origin_state;
					$mainData['origin_center'] = $value->origin_center;
					$mainData['origin_serviceable'] = $value->origin_serviceable;
					$mainData['des_pincode'] = $value->des_pincode;
					$mainData['des_city'] = $value->des_city;
					$mainData['des_state'] = $value->des_state;
					$mainData['shipping_charge'] = $value->shipping_charge;
					$mainData['status'] = $status;
					$mainData['created_at'] = date('Y-m-d',strtotime($value->created_at));
					$mainData['action'] = ""; 
					if($role != 'admin')
					{
					    if($users_permission->type == '2')
    				    {
    					    $mainData['action'] .= '<a href="javascript:;" data-id="'.$value->id.'" data-status="'.$value->status.'" data-shipping_charge="'.$value->shipping_charge.'" onclick="editPrice(this,event)" class="btn btn-icon waves-effect waves-light action-icon mr-1"> <i class="mdi mdi-pencil"></i> </a>'; 
    				    }
					} else if($role == 'admin')
					{
					    	$mainData['action'] .= '<a href="javascript:;" data-id="'.$value->id.'" data-status="'.$value->status.'" data-shipping_charge="'.$value->shipping_charge.'" onclick="editPrice(this,event)" class="btn btn-icon waves-effect waves-light action-icon mr-1"> <i class="mdi mdi-pencil"></i> </a>'; 
					}
					
				
					$data[] = $mainData;
					$i++;
				}
			}
			
			$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalData,
			"iTotalDisplayRecords" => $totalFiltered,
			"aaData" => $data
			); 
			
			echo json_encode($response);
			exit;
		}
		
		public function updateCharge(Request $request)
		{
			$id = $request->id;
			$data = $request->except('_token','id'); 
			PincodeService::where('id',$id)->update($data);
			return response()->json(['status'=>'success','msg'=>'The shipping charge has been updated successfully.']); 
		}
	}
