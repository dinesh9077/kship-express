
@foreach($notifications as $row)
	@if($row->type == "New User")
		@if($row->role == "admin")
			<a href="{{route('users')}}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{$row->text}}<small class="text-muted">{{Helper::posted($row->created_at)}}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{$row->text}}<small class="text-muted">{{Helper::posted($row->created_at)}}</small></p>
			</a>
		@endif
	@endif
	@if($row->type == "Low Balnace")
		@if($row->role == "admin")
			<a href="{{route('users')}}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@endif
	@endif
	@if($row->type == "Kyc Pending")
		@if($row->role == "admin")
			<a href="{{ url('kyc/request') }}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-info"><i class="mdi mdi-account-plus"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@endif
	@endif
	@if($row->type == "New Ticket")
		@if($row->role == "admin")
			<a href="{{route('ticket.admin')}}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{$row->text}}<small class="text-muted">{{Helper::posted($row->created_at)}}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{$row->text}}<small class="text-muted">{{Helper::posted($row->created_at)}}</small></p>
			</a>
		@endif
	@endif
	@if($row->type == "Revert Ticket")
		@if($row->role == "admin")
			<a href="{{ route('ticket.admin') }}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@endif
	@endif
	@if($row->type == "Close Ticket")
		@if($row->role == "admin")
			<a href="{{ route('ticket.admin') }}?notify_id={{$row->id}}&task_id={{$row->task_id}}" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@else
			<a href="javascript:;" class="dropdown-item notify-item">
				<div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
				<p class="notify-details">{{ $row->text }}<small class="text-muted">{{ Helper::posted($row->created_at) }}</small></p>
			</a>
		@endif
	@endif
@endforeach