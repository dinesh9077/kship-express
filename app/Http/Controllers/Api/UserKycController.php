<?php
	
	namespace App\Http\Controllers\Api;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller; 
	use App\Models\UserKyc; 
	use DB,Auth,File;
	use Illuminate\Support\Facades\Http;
	use App\Traits\ApiResponse;   
	
	class UserKycController extends Controller
	{
		use ApiResponse;

		public function kycUserPancardUpdate(Request $request)
		{
			$user = Auth::user();

			try {
				$response = Http::withOptions(['verify' => false])
				->withHeaders([
					'Content-Type' => 'application/json',
				])->post('https://api.quickekyc.com/api/v1/pan/pan', [
					'key' => 'd57238fd-1474-40a4-a058-2c20f1ab5247',
					'id_number' => $request->input('pancard'),
				]);

				if ($response->failed()) {
					return $this->errorResponse('PAN verification failed. Please try again later.'); 
				}

				$data = $response->json();

				if (!isset($data['status']) || strtolower($data['status']) !== 'success') {
					return $this->errorResponse($data['message'] ?? 'PAN verification unsuccessful. Please check your details.'); 
				}

				// Update or create KYC record
				$userKyc = UserKyc::updateOrCreate(
					['user_id' => $user->id],
					[
						'pancard' => $data['data']['pan_number'],
						'pan_full_name' => $data['data']['full_name'],
						'pancard_category' => $data['data']['category'],
						'pancard_status' => 1,
						'pancard_text' => $data,
					]
				);

				// Update user's overall KYC status if both PAN and Aadhar verified
				if ($userKyc->pancard_status && $userKyc->aadhar_status) {
					$user->kyc_status = 1;
					$user->save();
				}
				return $this->successResponse($userKyc->refresh(), 'Your PAN details have been verified successfully.'); 

			} catch (\Throwable $e) {
				return $this->errorResponse('Something went wrong while verifying PAN: ' . $e->getMessage());	 
			}
		}

		public function kycUserAadharOtp(Request $request)
		{
			try {
				$response = Http::withOptions(['verify' => false])
				->withHeaders([
					'Content-Type' => 'application/json',
				])->post('https://api.quickekyc.com/api/v1/aadhaar-v2/generate-otp', [
					'key' => 'd57238fd-1474-40a4-a058-2c20f1ab5247',
					'id_number' => $request->aadhar,
				]);

				$data = $response->json();

				if ($response->failed() || (strtolower($data['status'] ?? '') !== 'success')) { 
					return $this->errorResponse($data['message'] ?? 'Unable to send OTP. Please try again.');
				}

				return $this->successResponse(['request_id' => $data['request_id'] ?? null], 'OTP has been sent successfully to your registered mobile number linked with Aadhar.');
			 
			} catch (\Throwable $e) { 
				return $this->errorResponse('Something went wrong while sending OTP.');
			}
		}

		public function kycUserAadharUpdate(Request $request)
		{
			$user = Auth::user();

			try {
				$response = Http::withOptions(['verify' => false])
				->withHeaders([
					'Content-Type' => 'application/json',
				])->post('https://api.quickekyc.com/api/v1/aadhaar-v2/submit-otp', [
					'key' => 'd57238fd-1474-40a4-a058-2c20f1ab5247',
					'request_id' => $request->input('request_id'),
					'otp' => $request->input('otp'),
				]);

				if ($response->failed()) {
					return $this->errorResponse('Aadhar verification failed. Please try again later.'); 
				}

				$data = $response->json();

				if (!isset($data['status']) || strtolower($data['status']) !== 'success') {
					return $this->errorResponse($data['message'] ?? 'Aadhar verification unsuccessful. Please check your details.'); 
				}

				$address = $data['data']['address'];
				$profileImage = $data['data']['profile_image'];

				if ($profileImage) {
					// Remove base64 prefix if it exists
					$profileImage = preg_replace('/^data:image\/\w+;base64,/', '', $profileImage);

					// Decode base64
					$imageData = base64_decode($profileImage);

					// Generate unique filename
					$fileName = 'profile_' . time() . '.png';

					$filePath = 'public/kyc/aadhar_profile/' . $fileName;
					\Storage::put($filePath, $imageData);

					$imageUrl = \Storage::url($filePath);

				}

				// Combine all non-empty address parts into one string
				$fullAddress = implode(', ', array_filter([
					$address['house'] ?? '',
					$address['street'] ?? '',
					$address['landmark'] ?? '',
					$address['loc'] ?? '',
					$address['po'] ?? '',
					$address['vtc'] ?? '',
					$address['subdist'] ?? '',
					$address['dist'] ?? '',
					$address['state'] ?? '',
					$address['country'] ?? ''
				]));

				// Update or create KYC record
				$userKyc = UserKyc::updateOrCreate(
					['user_id' => $user->id],
					[
						'aadhar_front' => $imageUrl,
						'aadhar' => $data['data']['aadhaar_number'],
						'aadhar_full_name' => $data['data']['full_name'],
						'aadhar_address' => $fullAddress,
						'aadhar_dob' => $data['data']['dob'],
						'aadhar_gender' => $data['data']['gender'],
						'aadhar_zip' => $data['data']['zip'],
						'aadhar_status' => 1,
						'aadhar_text' => $data,
					]
				);

				// Update user's overall KYC status if both PAN and Aadhar verified
				if ($userKyc->pancard_status && $userKyc->aadhar_status) {
					$user->kyc_status = 1;
					$user->save();
				}
				$userKyc->aadhar_front = asset($imageUrl); 
				return $this->successResponse($userKyc, 'Your Aadhar details have been verified successfully.'); 

			} catch (\Throwable $e) {
				return $this->errorResponse('Something went wrong while verifying Aadhar: ' . $e->getMessage()); 
			}
		}
		
		public function kycUserGSTUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'gst_image', 'gst');
		}
		
		public function kycUserBankUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'bank_passbook', 'bank_passbook');
		}
		
		private function kycUserDocumentUpdate(Request $request, $imageFields, $prefix)
		{
			$user_id = Auth::id();
			$data = $request->except('_token');
			$path_avatar = storage_path("app/public/kyc/{$user_id}");
			
			try {
				if (!File::exists($path_avatar)) {
					File::makeDirectory($path_avatar, 0777, true, true);
				}
				
				$imageFields = (array) $imageFields;
				foreach ($imageFields as $field) {
					if ($request->hasFile($field)) {
						$photo_image = $request->file($field);
						$getAvatar = "{$prefix}_" . time() . rand(111111, 999999) . '.' . $photo_image->getClientOriginalExtension();
						$filename = "$path_avatar/$getAvatar";
						
						if ($photo_image->getSize() > 2 * 1024 * 1024) {
							$sourceImage = imagecreatefromjpeg($photo_image->getRealPath());
							if ($sourceImage) {
								imagejpeg($sourceImage, $filename, 75);
								imagedestroy($sourceImage);
								} else {
								throw new \Exception("Invalid image format. Only JPEG supported for compression.");
							}
							} else {
							$photo_image->move($path_avatar, $getAvatar);
						}
						$data[$field] = $getAvatar;
					}
				}
				
				DB::beginTransaction();
				
				if (UserKyc::where('user_id', $user_id)->exists()) {
					UserKyc::where('user_id', $user_id)->update($data);
					} else {
					$data['user_id'] = $user_id;
					UserKyc::create($data);
				}
				
				DB::commit(); 
				return $this->successResponse([], 'The KYC request has been successfully sent to the admin.');
			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update KYC request.');
			}
		}
		 
	}
