<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use App\Models\Ticket;
	use App\Models\User;
	use App\Models\Notification;
		use App\Models\Permission;
	use Auth, DB, Log;
	class TicketController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function index()
		{ 
			return view('ticket.index');
		}
		
		public function ticketListAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get('start');
			$limit = $request->get('length');
			$orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
			$orderDirection = $request->get('order')[0]['dir'] ?? 'asc';
			$orderColumn = $request->get('columns')[$orderColumnIndex]['data'] ?? 'id';
			$searchValue = $request->get('search') ?? '';
			$ticket_id = $request->post('ticket_id');
			$user = Auth::user();

			if ($orderColumn === 'customer_name') {
				$orderColumn = 'id';
			}

			$query = Ticket::select('tickets.*', 'users.staff_id')
				->join('users', 'users.id', '=', 'tickets.user_id');

			if (!empty($ticket_id)) {
				$query->where('tickets.id', $ticket_id);
			}
			
			if ($user->role == "staff") {
				$query->where('users.staff_id', $user->id);
			} elseif ($user->role != "admin") {
				$query->where('tickets.user_id', $user->id);
			}

			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->where('tickets.contact_name', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.contact_phone', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.awb_number', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.created_at', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.ticket_no', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.status', 'LIKE', "%{$searchValue}%");
				});
			}

			$totalData = $query->count();
			$values = $query->orderBy("tickets." . $orderColumn, $orderDirection)
				->offset($start)
				->limit($limit)
				->get();

			$data = [];
			$i = $start + 1;
			foreach ($values as $value) {
				$data[] = [
					'id' => $i++,
					'ticket_no' => $value->ticket_no,
					'awb_number' => $value->awb_number,
					'contact_name' => $value->contact_name,
					'contact_phone' => $value->contact_phone,
					'revert' => $value->revert ?: 'Wait for revert',
					'status' => $value->status == 'Close' 
						? '<span class="badge badge-success">Close</span>' 
						: '<span class="badge badge-danger">Open</span>',
					'created_at' => date('d M Y', strtotime($value->created_at)),
					'action' => '<a href="' . url('ticket/delete', $value->id) . '" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip" title="Delete User" onClick="deleteRecord(this,event);"> <i class="mdi mdi-trash-can-outline"></i> </a>'
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalData,
				"aaData" => $data
			]);
		}
 
		public function ticketAdd()
		{
			return view('ticket.add');
		}
		
		public function ticketStore(Request $request)
		{
			$ticket_no = strtoupper(uniqid());
			$user = Auth::user();
			$email = $user->email;

			$data = $request->except('_token');
			$data['user_id'] = $user->id;
			$data['ticket_no'] = $ticket_no;
			$data['status'] = 'Open';
			$data['created_at'] = now();
			$data['updated_at'] = now();

			DB::beginTransaction();
			try {
				$ticket = Ticket::create($data);
				$awb_number = $data['awb_number'];

				Notification::insert([
					['user_id' => null, 'task_id' => $ticket->id, 'type' => 'New Ticket', 'role' => 'admin', 'text' => $request->contact_name . ' added a new ticket with ticket no. ' . $ticket_no, 'created_at' => now(), 'updated_at' => now()],
					['user_id' => $user->id, 'task_id' => $ticket->id, 'type' => 'New Ticket', 'role' => 'user', 'text' => 'Your ticket has been generated with ticket no. ' . $ticket_no, 'created_at' => now(), 'updated_at' => now()]
				]);

				DB::commit();

				try {
					\Mail::to($email)->send(new \App\Mail\TicketMail([
						'name' => $request->contact_name,
						'ticket_no' => $ticket_no,
						'awb_number' => $awb_number
					]));
				} catch (\Exception $e) { 
				}

				return response()->json(['status' => 'success', 'msg' => 'The ticket has been successfully generated.']);
			} catch (\Exception $e) {
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
			}
		} 
		
		public function ticketDelete($id)
		{ 
			DB::beginTransaction();
			try { 
				$deleted = Ticket::whereId($id)->delete();

				if ($deleted) {
					DB::commit();
					return back()->with('success', 'The ticket has been successfully deleted.');
				} else {
					DB::rollBack();
					return back()->with('error', 'Ticket not found or already deleted.');
				}
			} catch (\Exception $e) { 
				DB::rollBack();
				Log::error('Ticket Deletion Error: ' . $e->getMessage());
				return back()->with('error', 'Something went wrong.');
			}  
		}
 
		public function ticketList()
		{
			if(isset($_GET['task_id']))
			{
				Notification::whereId($_GET['notify_id'])->update(['read_at'=>1]);
			}
			$ticket_id = (isset($_GET['task_id']))?$_GET['task_id']:'';
			return view('ticket.list', compact('ticket_id'));
		}
		
		public function ticketAllListAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length");
			$orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
			$orderColumn = $request->get('columns')[$orderColumnIndex]['data'] ?? 'id';
			$orderDirection = $request->get('order')[0]['dir'] ?? 'asc';
			$searchValue = $request->get('search') ?? '';
			$ticket_id = $request->post('ticket_id');
			$user = Auth::user();

			$query = Ticket::select('tickets.*', 'users.staff_id')
				->join('users', 'users.id', '=', 'tickets.user_id');

			if (!empty($ticket_id)) {
				$query->where('tickets.id', $ticket_id);
			}
			if ($user->role == "staff") {
				$query->where('users.staff_id', $user->id);
			} elseif ($user->role != "admin") {
				$query->where('tickets.user_id', $user->id);
			}

			if (!empty($searchValue)) {
				$query->where(function ($q) use ($searchValue) {
					$q->where('tickets.contact_name', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.contact_phone', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.awb_number', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.created_at', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.ticket_no', 'LIKE', "%{$searchValue}%")
					  ->orWhere('tickets.status', 'LIKE', "%{$searchValue}%");
				});
			}

			$totalData = $query->count();
			$values = $query->orderBy("tickets." . $orderColumn, $orderDirection)
				->offset($start)
				->limit($limit)
				->get();

			$data = [];
			$i = $start + 1;
			  
			foreach ($values as $value) {
				$action = '';
				if (config('permission.ticket_request.edit')) { 
					$action .= '<a href="'.url('ticket/revert_ticket', $value->id).'" ><button type="button" class="btn-main-1">Revert Ticket</button></a>'; 
				} 
				if($value->status != "Close" && config('permission.ticket_request.delete')) 
				{
					$action .= '<a href="'.url('ticket/close', $value->id).'" onclick="closeTicket(this,event);"><button type="button" class="btn-main-1">Close Ticket</button></a>';  
				}
				if (config('permission.ticket_request.delete')) { 
					$action .= '<a href="'.url('ticket/delete', $value->id).'" class="btn btn-icon waves-effect waves-light action-icon" data-toggle="tooltip"  title="Delete User"  onClick="deleteRecord(this,event);"> <button type="button" class="btn btn-danger">Delete</button> </a>'; 
				}
				 

				$data[] = [
					'id' => $i++,
					'ticket_no' => $value->ticket_no,
					'awb_number' => $value->awb_number,
					'contact_name' => $value->contact_name,
					'contact_phone' => $value->contact_phone,
					'description' => $value->text,
					'status' => $value->status == 'Close' ? '<span class="badge badge-success">Close</span>' : '<span class="badge badge-danger">Open</span>',
					'created_at' => date('d M Y', strtotime($value->created_at)),
					'action' => $action
				];
			}

			return response()->json([
				"draw" => intval($draw),
				"iTotalRecords" => $totalData,
				"iTotalDisplayRecords" => $totalData,
				"aaData" => $data
			]);
		}
 
		public function revert_ticket($ticketId)
		{
			$ticket = Ticket::find($ticketId); 
			return view('ticket.revert', compact('ticket'));
		}

		public function ticketUpdate(Request $request, $ticketId)
		{	
			DB::beginTransaction();
			try { 
				$data = $request->except('_token', 'id');
				$ticket = Ticket::findOrFail($ticketId);
				$ticket->update($data);
				
				Notification::create([
					'user_id' => $ticket->user_id,
					'task_id' => $ticket->id,
					'type' => 'Revert Ticket', 
					'role' => 'user', 
					'text' => 'Your ticket revert msg with ticket no. ' . $ticket->ticket_no, 
					'created_at' => now(), 
					'updated_at' => now()
				]);
				
				DB::commit();
				return response()->json(['status' => 'success', 'msg' => 'The ticket has been successfully reverted.']); 
			} catch (\Exception $e) { 
				DB::rollBack(); 
				return response()->json(['status' => 'error', 'msg' => 'Something went wrong.']);
			} 
		}

		public function ticketClose($id)
		{ 
			DB::beginTransaction();
			try { 
				$ticket = Ticket::findOrFail($id);
				$user = User::findOrFail($ticket->user_id);

				$ticket->update(['status' => 'Close']); 
				
				Notification::insert([
					'user_id' => $user->id,
					'task_id' => $ticket->id,
					'type' => 'Close Ticket', 
					'role' => 'user', 
					'text' => 'Your ticket has been close with ticket no. ' . $ticket->ticket_no, 
					'created_at' => now(), 
					'updated_at' => now()
				]);
 
				DB::commit();

				$details = [
					'name' => $ticket->contact_name,
					'ticket_no' => $ticket->ticket_no,
					'awb_number' => $ticket->awb_number
				];
			
				try {
					\Mail::to($user->email)->send(new \App\Mail\TicketCloseMail($details));
				} catch (\Exception $e) { 
				}

				return back()->with('success', 'The ticket has been successfully closed.'); 

			} catch (\Exception $e) { 
				DB::rollBack(); 
				return back()->with('error', 'Something went wrong.'); 
			}  
		}
  
	}
