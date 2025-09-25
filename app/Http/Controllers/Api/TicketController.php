<?php
	
	namespace App\Http\Controllers\Api;
	
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	use App\Models\Ticket;
	use App\Models\User;
	use App\Models\TicketRemark;
	use App\Models\Notification;
	use Auth, DB, Str;
	use App\Mail\TicketMail;
	use Mail;
	use App\Traits\ApiResponse;
	 
	class TicketController extends Controller
	{
		use ApiResponse;  
		public function index(Request $request)
		{   
			$start  = $request->get("offset", 0);
			$limit  = $request->get("limit", 10);  
			$search = $request->input('search') ?? '';
			
			$role = Auth::user()->role;
			$id   = Auth::user()->id;
 
			$query = Ticket::query();

			if (!empty($ticket_id)) {
				$query->where('id', $ticket_id);
			}

			if ($role !== "admin") {
				$query->where('user_id', $id);
			}
 
			if ($search) {
				$query->where(function ($q) use ($search) {
					$q->where('contact_name', 'LIKE', "%{$search}%")
					  ->orWhere('contact_phone', 'LIKE', "%{$search}%")
					  ->orWhere('awb_number', 'LIKE', "%{$search}%")
					  ->orWhere('ticket_no', 'LIKE', "%{$search}%")
					  ->orWhere('status', 'LIKE', "%{$search}%")
					  ->orWhereDate('created_at', $search);
				});
			} 
			$values = $query->orderByDesc('id')
				->offset($start)
				->limit($limit)
				->get();
				
			return $this->successResponse($values, 'list fetched successfully.');
		}
  
		public function ticketStore(Request $request)
		{ 
			DB::beginTransaction();
			
			try { 
				$user = Auth::user(); 
			
				if (Ticket::where('awb_number', $request->awb_number)->where('status', 'Open')->exists()) {
					return $this->errorResponse('The AWB number you provided already has an open ticket.');   
				}
			
				$ticket = Ticket::create([
					'user_id'    => $user->id,
					'ticket_no'  => strtoupper(Str::uuid()->toString()), // more unique than uniqid
					'awb_number' => $request->awb_number,
					'status'     => 'Open',
					'contact_name' => $request->contact_name,
					'text' => $request->text,
				]);
 
				TicketRemark::create([
					'ticket_id' => $ticket->id,
					'user_id'   => $user->id,
					'role'      => $user->role,
					'remark'    => $request->text,
					'images'    => null, // only set when available
				]);
 
				$notifications = [
					[
						'user_id' => null,
						'task_id' => $ticket->id,
						'type'    => 'New Ticket',
						'role'    => 'admin',
						'text'    => "{$request->contact_name} created a new ticket (No: {$ticket->ticket_no})",
						'created_at' => now(),
						'updated_at' => now(),
					],
					[
						'user_id' => $user->id,
						'task_id' => $ticket->id,
						'type'    => 'New Ticket',
						'role'    => 'user',
						'text'    => "Your ticket has been generated (No: {$ticket->ticket_no})",
						'created_at' => now(),
						'updated_at' => now(),
					]
				];
				Notification::insert($notifications);
 
				try {
					Mail::to($user->email)->send(new TicketMail([
						'name'       => $request->contact_name,
						'ticket_no'  => $ticket->ticket_no,
						'awb_number' => $ticket->awb_number,
					]));
				} catch (\Exception $mailException) {
					\Log::warning("Ticket email failed for ticket {$ticket->id}: ".$mailException->getMessage());
				}
				DB::commit();
				return $this->successResponse([] , 'The ticket has been successfully generated.'); 

			} catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('Something went wrong while creating the ticket.');  
			}
		}
		 
		public function ticketView($id)
		{
			try{ 
				$ticket = Ticket::with(['remarks' => function ($q) {
					$q->orderBy('id');
				}])->findOrFail($id);

				return $this->successResponse($ticket , 'ticket fetched successfully.'); 
			} catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('Something went wrong.');  
			}
		}
 
		public function remarkStore(Request $request)
		{     
			DB::beginTransaction();
			try {
				$ticketId = $request->ticket_id;
				$imagePaths = []; 
				if ($request->hasFile('images')) {
					foreach ($request->file('images') as $image) {
						 
						// Generate a unique filename
						$filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

						// Store the file in the "weight_descrepency" folder within "storage/app/public"
						$path = $image->storeAs('ticket-remark/'.$ticketId, $filename, 'public');

						// Save the file path in an array
						$imagePaths[] = $path;
					}
				} 
				
				$remark = TicketRemark::create([
					'ticket_id' => $ticketId,
					'role' =>  auth()->user()->role,
					'user_id' => auth()->id(),
					'remark' => $request->remark,
					'images' => $imagePaths,
				]);
				
				$now = now();  
				$ticket = Ticket::find($ticketId);
				
				if(auth()->user()->role == "user")
				{
    				Notification::insert([
    					'task_id'    => $ticketId,
    					'type'       => 'Ticket Remark',
    					'role'       => 'admin',
    					'text'       => $ticket->contact_name . ' has created a new ticket reamrk (Ticket No: ' . $ticket->ticket_no . ').',
    					'created_at' => $now,
    					'updated_at' => $now
    				]); 
				}
				else {
				     
    				Notification::insert([
    					'user_id'    => $ticket->user_id,
    					'task_id'    => $ticketId,
    					'type'       => 'Ticket Remark',
    					'role'       => 'user',
    					'text'       => auth()->user()->name . ' has created a new ticket reamrk (Ticket No: ' . $ticket->ticket_no . ').',
    					'created_at' => $now,
    					'updated_at' => $now
    				]);
    				
				}
				DB::commit(); 
				return $this->successResponse([], 'Remark submitted successfully.');    
			} catch (\Exception $e) {
				DB::rollback();
				return $this->errorResponse('Failed to submit remark.');   
			}
		} 
	}
