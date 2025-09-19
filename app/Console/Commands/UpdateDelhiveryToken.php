<?php
	namespace App\Console\Commands;

	use Illuminate\Console\Command;
	use App\Models\ShippingCompany;
	use Carbon\Carbon;
	use App\Services\DelhiveryService;
	use Illuminate\Support\Facades\Log;

	class UpdateDelhiveryToken extends Command
	{
		protected $signature = 'update:delhivery-token';
		protected $description = 'Update Delhivery token every night at 11 PM';
		
		protected $delhiveryService;

		public function __construct(DelhiveryService $delhiveryService)
		{
			parent::__construct();
			$this->delhiveryService = $delhiveryService;
		}

		public function handle()
		{
			try {
				// Fetch the token from Delhivery API
				$data = $this->delhiveryService->getToken();

				// Validate response structure
				if (!isset($data['success']) || !$data['success'] || (isset($data['response']['success']) && !$data['response']['success'])) {
					return;
				}

				 
				// Extract token
				$token = $data['response']['data']['jwt'] ?? null;
				
				// Find the Delhivery shipping company record
				$delhivery = ShippingCompany::find(2);
				 
				if (!$delhivery) { 
					return;
				}

				// Update token in database
				$delhivery->update([
					'api_key' => $token,
					'updated_at' => Carbon::now(),
				]);

				$this->info("Delhivery token updated successfully.");
			} catch (\Exception $e) {
				$this->info($e->getMessage());
			}
		}
	}
