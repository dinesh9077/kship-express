<?php
	
	namespace App\Http\Controllers\Api;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\User;
	use App\Models\Role;
	use App\Models\Permission;
	use App\Models\Billing;
	use App\Models\UserKyc;
	use App\Models\Notification;
	use App\Models\UserWallet;
	use DB,Auth,File,Hash;
	use Illuminate\Support\Facades\Http;
	use App\Traits\ApiResponse;   
	
	class UserKycController extends Controller
	{
		use ApiResponse;  
		  
		public function kycUserPancardUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, 'pancard_image', 'pancard');
		}
		
		public function kycUserAadharUpdate(Request $request)
		{
			return $this->kycUserDocumentUpdate($request, ['aadhar_front', 'aadhar_back'], 'aadhar');
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
