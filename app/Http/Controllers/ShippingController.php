<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\User;
	use App\Models\ShippingCompany;
	use App\Models\ProductCategory;
	use App\Models\PincodeService;
	use App\Models\Packaging;
	use DB,Auth,File,Hash,Helper;
	use Illuminate\Support\Facades\Http;
	use App\Services\ShipMozo;
	use App\Services\DelhiveryService;
	use App\Services\DelhiveryB2CService;
	use Carbon\Carbon;
	
	class ShippingController extends Controller
	{
		protected $delhiveryService;
		protected $delhiveryB2CService;
		protected $shipMozo;
		
		public function __construct()
		{
			$this->middleware('auth');
			$this->shipMozo = new ShipMozo();  
			$this->delhiveryService = new DelhiveryService();  
			$this->delhiveryB2CService = new DelhiveryB2CService();  
		}
		
		public function index()
		{
			if (auth()->user()->role === 'user') {
				abort(403, 'Permission denied');
			} 
			$shippings = ShippingCompany::latest()->where('status', 1)->get();
			return view('shipping.index', compact('shippings'));
		}

		public function storeShipping(Request $request)
		{
			$user_id = Auth::user()->id;
			$data = $request->except('_token', 'logo');
			$data['user_id'] = $user_id;
			$data['created_at'] = Carbon::now();
			$data['updated_at'] = Carbon::now();

			$path_avatar = "storage/app/public/shipping-logo";
			if (!File::isDirectory($path_avatar)) {
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			DB::beginTransaction();
			try {
				if ($request->hasFile('logo')) {
					$photo_image = $request->file('logo');
					$extension = $photo_image->getClientOriginalExtension();
					if (in_array($extension, ['png', 'jpeg', 'jpg'])) {
						$getAvatar = strtolower($request->name) . '.' . $extension;
						$filename = $path_avatar . "/" . $getAvatar;
						$photo_image->move($path_avatar, $getAvatar);
						$data['logo'] = $getAvatar;
					} else {
						return redirect()->route('shipping.company')->with('error', 'Only png, jpg and jpeg images are allowed.');
					}
				}

				if (ShippingCompany::whereName($request->name)->exists()) {
					return redirect()->route('shipping.company')->with('error', 'The shipping company name already exists.');
				}
  
				ShippingCompany::create($data);

				DB::commit();

				return redirect()->route('shipping.company')->with('success', 'The shipping company has been successfully added.');
			} catch (\Exception $e) {
				DB::rollBack();
				return redirect()->route('shipping.company')->with('error', $e->getMessage());
			}
		}

		public function updateShipping(Request $request)
		{  
			$id = $request->id;
			$data = $request->except('_token', 'id', 'logo');
			$data['updated_at'] = Carbon::now();

			$path_avatar = "storage/app/public/shipping-logo";
			if (!File::isDirectory($path_avatar)) {
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			DB::beginTransaction();
			try {
				if ($request->hasFile('logo')) {
					$photo_image = $request->file('logo');
					$extension = $photo_image->getClientOriginalExtension();
					if (in_array($extension, ['png', 'jpeg', 'jpg'])) {
						$getAvatar = strtolower($request->name) . '.' . $extension;
						$filename = $path_avatar . "/" . $getAvatar;
						$photo_image->move($path_avatar, $getAvatar);
						$data['logo'] = $getAvatar;
					} else {
						return redirect()->route('shipping.company')->with('error', 'Only png, jpg and jpeg images are allowed.');
					}
				}

				if (ShippingCompany::where('id', '!=', $id)->whereName($request->name)->exists()) {
					return redirect()->route('shipping.company')->with('error', 'The shipping company name already exists.');
				}
 
				ShippingCompany::whereId($id)->update($data);

				DB::commit();

				return redirect()->route('shipping.company')->with('success', 'The shipping company has been updated successfully.');
			} catch (\Exception $e) {
				DB::rollBack();
				return redirect()->route('shipping.company')->with('error', $e->getMessage());
			}
		}
		
		public function productCategory()
		{ 
			$products = ProductCategory::latest()->get();	
			return view('shipping.product-category',compact('products'));
		}
		
		public function storeProductCategory(Request $request)
		{
			$user_id = Auth::user()->id;
			$data = $request->except('_token'); 
			$data['user_id'] = $user_id;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			if(ProductCategory::whereName($request->name)->exists())
			{
				return response()->json(['status'=>'error','msg'=>'The product category name already exists.']);
			}  
			try
			{
				$id = ProductCategory::insertGetId($data);   
				return redirect()->route('product-category')->with('success','The product category has been successfully added.');
			}
			catch(\Exception $e)
			{ 
				return redirect()->route('product-category')->with('error',$e->getMessage());
			}
		}
		
		public function updateProductCategory(Request $request)
		{
			$id = $request->id;
			$data = $request->except('_token','id');  
			$data['updated_at'] = date('Y-m-d H:i:s');
			if(ProductCategory::whereName($request->name)->exists())
			{
				return response()->json(['status'=>'error','msg'=>'The product category name already exists.']);
			}  
			try
			{
				ProductCategory::whereId($id)->update($data);   
				return redirect()->route('product-category')->with('success','The product category have been updated successfully.');
			}
			catch(\Exception $e)
			{ 
				return redirect()->route('product-category')->with('error',$e->getMessage());
			}
		}
		
		public function deleteProductCategory($id)
		{ 
			try
			{ 
				$user = ProductCategory::find($id);
				$user->delete();
				return redirect()->route('product-category')->with('success','The product category has been successfully deleted.'); 
			}
			catch(\Exception $e)
			{ 
				return redirect()->route('product-category')->with('error','Something went wrong.'); 
			}  
		}
		
		public function shipmentPackaging()
		{  
			return view('shipping.packaging.index');
		}
		
		public function shipmentPackagingAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows display per page
			
			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order');
			$search_arr = $request->get('search');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
		 
			$role = Auth::user()->role;
			$id = Auth::user()->id;
			
			$query = Packaging::where('packagings.id','!=','');   
			$totalData = $query->get()->count(); 
			$totalFiltered = Packaging::where('packagings.id','!=','');  
			$values = Packaging::select('packagings.*'); 
			$values->offset($start)->limit($limit)->orderBy('packagings'.'.'.$order,$dir);
			
			if(!empty($request->input('search')))
			{ 
				$search = $request->input('search');
				$values = $values->where(function ($query) use ($search) 
				{
					return $query->where('packagings.name', 'LIKE',"%{$search}%")->orWhere('packagings.type', 'LIKE',"%{$search}%")->orWhere('packagings.sku', 'LIKE',"%{$search}%")->orWhere('packagings.created_at', 'LIKE',"%{$search}%");
				});
				
				$totalFiltered = $totalFiltered->where(function ($query) use ($search) {
					return $query->where('packagings.name', 'LIKE',"%{$search}%")->orWhere('packagings.type', 'LIKE',"%{$search}%")->orWhere('packagings.sku', 'LIKE',"%{$search}%")->orWhere('packagings.created_at', 'LIKE',"%{$search}%");
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
					$mainData['id'] = $i; 
					$mainData['name'] = $value->name;
					$mainData['package_dimension'] = $value->length.' x '.$value->width.' x '.$value->height.' (cm)'; 
					$mainData['package_type'] = $value->type; 
					$mainData['sku'] = $value->sku;
					$mainData['status'] = ($value->status == 1)?'<span class="badge badge-success">Active</span>':'<span class="badge badge-danger">In-Active</span>'; 
					$mainData['created_at'] = date('d M Y',strtotime($value->created_at));
					$mainData['action'] = "";   
					$mainData['action'] .= ' 
					<a href="'.url('shipment/packaging/edit',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon mr-1"> <i class="mdi mdi-pencil"></i> </a>';
					
					$mainData['action'] .= '<a href="'.url('shipment/packaging/delete',$value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="" onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'; 
					
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
		
		public function shipmentPackagingAdd()
		{
			return view('shipping.packaging.add');
		}
		
		public function shipmentPackagingStore(Request $request)
		{
			$user_id = Auth::user()->id;
			
			$data = $request->except('_token','images');
			$data['user_id'] = $user_id;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			
			$path_avatar = "storage/app/public/packaging";
			
			if(!File::isDirectory($path_avatar)){
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			
			if(!empty($request->file('images')))
			{
				$images = $request->file('images');
				$string = "";
				foreach($images as $key => $image)
				{ 
					$getAvatar = time().rand(111111,999999) . '.' . $image->getClientOriginalExtension();
					$filename = $path_avatar."/".$getAvatar;
					$image->move($path_avatar, $getAvatar);  
					$string .= $getAvatar.',';
				}
				$data['images'] = rtrim($string,',');
			} 
			
			try
			{
				Packaging::insert($data);
				return response()->json(['status'=>'success','msg'=>'The packaging has been successfully added.']);
			}
			catch(\Exception $e)
			{
				return response()->json(['status'=>'error','msg'=>$e->getMessage()]);
			} 
			
		}
		
		public function shipmentPackagingEdit($id)
		{
			$package = Packaging::whereId($id)->first();
			return view('shipping.packaging.edit',compact('package')); 
		}
		
		public function shipmentPackagingUpdate(Request $request)
		{
			$user_id = Auth::user()->id;
			$id = $request->id;
			$data = $request->except('_token','images','id');
			$data['user_id'] = $user_id; 
			$data['updated_at'] = date('Y-m-d H:i:s');
			
			$path_avatar = "storage/app/public/packaging";
			
			if(!File::isDirectory($path_avatar)){
				File::makeDirectory($path_avatar, 0777, true, true);
			}
			
			if(!empty($request->file('images')))
			{
				$images = $request->file('images');
				$string = "";
				foreach($images as $key => $image)
				{ 
					$getAvatar = time().rand(111111,999999) . '.' . $image->getClientOriginalExtension();
					$filename = $path_avatar."/".$getAvatar;
					$image->move($path_avatar, $getAvatar);  
					$string .= $getAvatar.',';
				}
				$data['images'] = rtrim($string,',');
			} 
			
			try
			{
				Packaging::whereId($id)->update($data);
				return response()->json(['status'=>'success','msg'=>'The packaging has been successfully updated.']);
			}
			catch(\Exception $e)
			{
				return response()->json(['status'=>'error','msg'=>$e->getMessage()]);
			} 
		}
		
		public function shipmentPackagingDelete($id)
		{ 
			try
			{ 
				$user = Packaging::find($id);
				$user->delete();
				return redirect()->route('shipment.packaging')->with('success','The product category has been successfully deleted.'); 
			}
			catch(\Exception $e)
			{ 
				return redirect()->route('shipment.packaging')->with('error','Something went wrong.'); 
			}  
		}
		
		public function rateCalculator()
		{ 
		    $user = Auth::user();
			return view('rate-calculator', compact('user'));
		}
		
		public function rateCalculatorShow(Request $request)
		{   
			$user = Auth::user();
			$role = $user->role;
			$charge = $user->charge;
			$charge_type = $user->charge_type;
			
			$shippingCompanies = ShippingCompany::whereStatus(1)->get();
			$couriers = [];
			 
			foreach($shippingCompanies as $shippingCompany)
			{ 
				if ($shippingCompany->id == 1) { 
					
					$pincodeServiceData = $this->shipMozo->pincodeService($request, $shippingCompany);
					 
					if (!($pincodeServiceData['success'] ?? false) || 
					(isset($pincodeServiceData['response']['result']) && $pincodeServiceData['response']['result'] == 0)) {
						continue;
					}
					
					$response = $this->shipMozo->rateCaculator($request->all(), $shippingCompany); 
					if (!($response['success'] ?? false) || 
						(isset($response['response']['result']) && $response['response']['result'] == 0)) {
						continue;
					}
					
					$responseDetails = $response['response']['data'] ?? []; 
					if (!$responseDetails) {
						continue;
					}
					
					foreach($responseDetails as  $responseData){ 
						$totalCharges = $responseData['total_charges'];  
						$beforeTax = $responseData['before_tax_total_charges'];  
						$gst = $responseData['gst'];  

						$couriers[] = [  
							'shipping_charge' => $beforeTax, 
							'tax' => $gst, 
							'shipping_company_id' => $shippingCompany->id,
							'courier_id' 	=> $responseData['id'],
							'shipping_company_name' => $responseData['name'],
							'shipping_company_logo' => $responseData['image'], 
							'courier_name' => $responseData['name'], 
							'total_charges' => $totalCharges,
							'estimated_delivery' => $responseData['estimated_delivery'] ?? 'N/A', 
							'chargeable_weight' => $responseData['minimum_chargeable_weight'] ?? 0,
							'applicable_weight' => $request->weight ?? 0,
							'percentage_amount' => 0,
							'responseData' => $responseData
						];
					}
				} 
			}  
		     
			$view = view('shipping-cost', compact('couriers'))->render();
			return response()->json(['status'=>'success','view'=>$view]); 
		} 
		
		public function rateFreightBreakup(Request $request)
		{	
			$responseData = $request->data ? json_decode($request->data ?? [], true) : []; 
			if(!$responseData['responseData'])
			{
				return response()->json(['error'=>'success', 'msg'=> 'Something went wrong.']); 
			}
			 
			$view = view('freight-breakup-modal', compact('responseData'))->render();
			return response()->json(['status'=>'success', 'view'=>$view]); 	
		}
		
		public function ratePincodeServiciable($pincode)
		{
			$shippingCompanies = ShippingCompany::whereStatus(1)->get();
		 
			foreach ($shippingCompanies as $shippingCompany)
			{ 
				$pincodeServiceData = $this->delhiveryService->pincodeService($pincode ?? '', $shippingCompany);
				if (!($pincodeServiceData['success'] ?? false) || 
					(isset($pincodeServiceData['response']['success']) && !$pincodeServiceData['response']['success'])) {
					return response()->json(['status'=>'error', 'msg' => "pin: {$pincode} is not serviceable."]); 
				}  
				
				$city = $pincodeServiceData['response']['data']['pincode_serviceability_data'][0]['city'] ?? '';
				return response()->json(['status'=>'success', 'msg' => $city]); 
			}
		}
	}
