<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\KycPendingAlert;

class KycPendingCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kyc:pending-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users with pending KYC and send reminder emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users with pending KYC
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            $userKyc = UserKyc::where('user_id', $user->id)->first();

            if (!$userKyc) {
                continue; // Skip if no KYC record exists
            }

            // KYC checks
            $kycChecks = [
                'pancard_status' => 'Pan Card',
                'aadhar_status' => 'Aadhar Card',
                'bank_status' => 'Bank Details',
            ];

            $pendingKycTypes = [];

            foreach ($kycChecks as $field => $kycType) {
                if ($userKyc->$field == 0) {
                    $pendingKycTypes[] = $kycType;
                }
            }

            // If the user has pending KYC steps, send an email
            if (!empty($pendingKycTypes)) {
                try {
                    DB::beginTransaction();

                    // Email Message
                    $message = "Dear {$user->name},<br><br>"
                        . "Your KYC verification is still incomplete. Please complete the following pending steps:<br>"
                        . implode(', ', $pendingKycTypes)
                        . "<br><br>Complete your KYC as soon as possible to enjoy our full services.<br>Regards,<br>"
                        . config('setting.company_name');

                    // Send Email
                    try {
                        Mail::to($user->email)->send(new KycPendingAlert($user, $message));
                    } catch (\Exception $e) { 
						$this->info("Failed to send KYC pending email: " . $e->getMessage());
                    }
					
					Notification::insert([
						'user_id' => $user->id,
						'task_id' => $user->id,
						'type' => 'Kyc Pending', 
						'role' => 'user', 
						'text' => strip_tags($message), 
						'created_at' => now(), 
						'updated_at' => now()
					]);
				
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->info("Error processing KYC pending check: " . $e->getMessage());
                }
            }
        }

        $this->info("KYC pending check completed.");
    }
}
