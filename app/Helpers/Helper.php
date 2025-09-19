<?php  
	namespace App\Helpers;  
	use App\Models\Order;
	use App\Models\OrderActivity;
	use Ixudra\Curl\Facades\Curl;
	use App\Models\ShippingCompany;
		use App\Models\VendorAddress;
	use App\Models\Vendor;
	
	class Helper
	{  
		public static  function posted($time)
		{
			// Calculate difference between current
			// time and given timestamp in seconds
			$time = strtotime($time);
			$diff     = time() - $time;
			
			// Time difference in seconds
			$sec     = $diff;
			
			// Convert time difference in minutes
			$min     = round($diff / 60 );
			
			// Convert time difference in hours
			$hrs     = round($diff / 3600);
			
			// Convert time difference in days
			$days     = round($diff / 86400 );
			
			// Convert time difference in weeks
			$weeks     = round($diff / 604800);
			
			// Convert time difference in months
			$mnths     = round($diff / 2600640 );
			
			// Convert time difference in years
			$yrs     = round($diff / 31207680 );
			
			// Check for seconds
			if($sec <= 60) {
				$ret = "$sec seconds ago";
			}
			
			// Check for minutes
			else if($min <= 60) {
				if($min==1) {
					$ret =  "one minute ago";
				}
				else {
					$ret =  "$min minutes ago";
				}
			}
			
			// Check for hours
			else if($hrs <= 24) {
				if($hrs == 1) { 
					$ret =  "an hour ago";
				}
				else {
					$ret =  "$hrs hours ago";
				}
			}
			
			// Check for days
			else if($days <= 7) {
				if($days == 1) {
					$ret =  "Yesterday";
				}
				else {
					$ret =  "$days days ago";
				}
			}
			
			// Check for weeks
			else if($weeks <= 4.3) {
				if($weeks == 1) {
					$ret =  "a week ago";
				}
				else {
					$ret =  "$weeks weeks ago";
				}
			}
			
			// Check for months
			else if($mnths <= 12) {
				if($mnths == 1) {
					$ret =  "a month ago";
				}
				else {
					$ret =  "$mnths months ago";
				}
			}
			
			// Check for years
			else {
				if($yrs == 1) {
					$ret =  "one year ago";
				}
				else {
					$ret =  "$yrs years ago";
				}
			}
			
			return $ret;
		}
		
		public static function dateDiffInDays($date1, $date2) 
		{ 
			$diff = strtotime($date2) - strtotime($date1);  
			return abs(round($diff / 86400))+1;
		}
		public static function AmountInWords(float $amount)
		{
			$amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
			// Check if there is any number after decimal
			$amt_hundred = null;
			$count_length = strlen($num);
			$x = 0;
			$string = array();
			$change_words = array(0 => '', 1 => 'One', 2 => 'Two',
			3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
			7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
			10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
			13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
			16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
			19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
			40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
			70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
			$here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
			while( $x < $count_length ) {
				$get_divider = ($x == 2) ? 10 : 100;
				$amount = floor($num % $get_divider);
				$num = floor($num / $get_divider);
				$x += $get_divider == 10 ? 1 : 2;
				if ($amount) {
					$add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
					$amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
					$string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
					'.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
					'.$here_digits[$counter].$add_plural.' '.$amt_hundred;
				}
				else $string[] = null;
			}
			$implode_to_Rupees = implode('', array_reverse($string));
			$get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
			" . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
			return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
		}
		
		public static function decimal_number($number)
		{
			return number_format($number,2);
		}
		
		public static function decimalsprint($number)
		{
			return sprintf("%0.2f", $number);
		} 
		
		public static function getOrderPrefix()
		{ 
			$order = Order::latest('id')->pluck('id')->first();
			return $order + 1; 
		}
		  
		public static function getChargeableWeight($id)
		{
			$order = Order::whereId($id)->first();
			$volumatric_weight = $order->length * $order->width * $order->height / 5000;
			$applicable_wt = $volumatric_weight;
			if($order->weight > $volumatric_weight)
			{
				$applicable_wt = $order->weight;
			}
			return ['dead_wt'=>$order->weight,'volumetric_wt'=>$volumatric_weight,'applicable_wt'=>$applicable_wt]; 
		}
		
		public static function orderActivity($id,$activity_name)
		{
			return  OrderActivity::insert(['order_id'=>$id,'activity_name'=>$activity_name,'date'=>date('Y-m-d'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]); 
		}
		
		public static function xpressBeesToken($id)
		{
			$shipping = ShippingCompany::find($id);
			$token = $shipping->api_key;
			if($shipping->id == 1)
			{
				if(empty($shipping->expired_at) || $shipping->hasExpired())
				{ 
					$response = Curl::to($shipping->url.'api/users/login')
					->withData(array('email' => $shipping->email,'password' =>$shipping->password))
					->asJson(true)
					->post(); 
					ShippingCompany::whereId($id)->update(['api_key'=>$response['data'],'expired_at'=>date('Y-m-d H:i:s')]);
					$token = $response['data']; 
				}
				else
				{
					$token = $shipping->api_key;
				}
				
			}
			if($shipping->id == 4)
			{
				if(empty($shipping->expired_at) || $shipping->hasExpired())
				{ 
					$response = Curl::to($shipping->url.'auth/login')
					->withData(array('email' => $shipping->email,'password' =>$shipping->password,'vendorType' =>"SELLER"))
					->asJson(true)
					->post(); 
					 
					ShippingCompany::whereId($id)->update(['api_key'=>$response['data']['accessToken'],'expired_at'=>date('Y-m-d H:i:s')]);
					$token = $response['data']['accessToken']; 
				}
				else
				{
					$token = $shipping->api_key;
				}
				
			} 
			return $token;
		}
		
		public static function callCurlApi($url,$dataArray,$token)
		{  
			$response = Curl::to($url)
			->withData($dataArray)
			->asJson(true)
			->withBearer($token) 
			->post();
			return $response;
		}
		
		public static function callCurlApiPut($url,$dataArray,$token)
		{  
			$response = Curl::to($url)
			->withData($dataArray)
			->asJson(true)
			->withBearer($token) 
			->put();
			return $response;
		}
		
		public static function callCurlGetApi($url,$token)
		{  
			$response = Curl::to($url) 
			->withBearer($token)
			->get();
			return $response;
		}
		
		public static function callGetUrl($url)
		{  
			$response = Curl::to($url)->asJson(true)->get(); 
			return $response;
		}
		public static function callGetUrlAuthorize($url,$token)
		{  
			$response = Curl::to($url)->asJson(true)->withAuthorization('Token '.$token)->get(); 
			return $response;
		}
		public static function callPostUrlAuthorize($url,$dataArray,$token)
		{  
			$response = Curl::to($url)->withData($dataArray)->asJson(true)->withAuthorization('Token '.$token)->get(); 
			return $response;
		} 
		
		public static function DownloadLable($url,$token,$awb_number,$id)
		{
			
			// $token = $shippingcomp->api_key;
			$labelurl = $url.'fulfillment/public/seller/order/download/label-invoice?awbNumber='.$awb_number.'';
			
			$curl = curl_init(); 
			curl_setopt_array($curl, array(
			CURLOPT_URL => $labelurl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
			'Authorization: Token '.$token.'',
			'Cookie: sessionid=bg71z30eiahrpwfasedox06td1arwx4p'
			),
			));
			
			$response = curl_exec($curl); 
			curl_close($curl);
			$response = json_decode($response,true);
			
			if($response['status'] == 200)
			{ 
				
				Order::whereId($id)->update(['label'=>$response['data'][0]['shippingLabelUrl']]);
				return $response['data'][0]['shippingLabelUrl'];
			}
		}
		
		public static function generateDeliveryLable($shippingcomp,$id,$awb_number)
		{
			$token = $shippingcomp->api_key;
			$url = $shippingcomp->url.'api/p/packing_slip?wbns='.$awb_number.'&pdf=true';
			$curl = curl_init(); 
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
			'Authorization: Token '.$token.'',
			'Cookie: sessionid=bg71z30eiahrpwfasedox06td1arwx4p'
			),
			));
			
			$response = curl_exec($curl); 
			curl_close($curl);
			$response = json_decode($response,true);
			
			if(count($response['packages']) > 0)
			{
				Order::whereId($id)->update(['label'=>$response['packages'][0]['pdf_download_link']]);
				return $response['packages'][0]['pdf_download_link'];
			}
		}
		
		public static function postCurl($url,$dataArray,$token)
		{
			$curl = curl_init(); 
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>$dataArray,
			CURLOPT_HTTPHEADER => array(
			'Authorization: Token '.$token.'',
			'Content-Type: application/json', 
			),
			));
			
			$response = curl_exec($curl); 
			curl_close($curl);
			return json_decode($response,true);
		}
		
		public static function vendor_address_id($id)
		{
			$vendor = Vendor::where('user_id',$id)->first();
			$vendors = VendorAddress::whereVendor_id($vendor->id)->first();
			
			return $vendors;
		}

		
	}	
	
	
	
	
	
