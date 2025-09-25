@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Recharge History')
@section('header_title','Recharge History')
@section('content')
<div class="content-page">
	<div class="content">

		<!-- Start Content-->
		<div class="container-fluid">
			<div class="main-order-page-1">
				<div class="main-order-001">
					<div class="main-create-order">
						<div class="mb-3">
							<div class="header-11">
								<div class="row" style="row-gap: 10px;">
									<div class="from-group col-lg-2 col-sm-6">
										<select name="status" id="status">
											<option value="5">All</option>
											<option value="0">Pending</option>
											<option value="1">Approved</option>
											<option value="2">Rejected</option>
										</select>
									</div>
									<div class="from-group col-lg-2 col-sm-6">
										<select name="transaction_type" id="transaction_type1">
											<option value="">All</option>
											<option value="Online">Online</option>
											<option value="Offline">Offline</option>
										</select>
									</div>
								</div>
							</div>
							<div class="main-btn0main-1"> </div>
						</div>

						<div class="main-data-teble-1 table-responsive">
							<table id="recharge-datatable" class="" style="width:100%">
								<thead>
									<tr>
										<th> SR.No </th>
										<th> User Name</th>
										<th> Transaction Type </th>
										<th> Amount </th>
										<th> Reciept </th>
										<th> Note </th>
										<th> Payment status </th>
										<th> Created At </th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
	var dataTable = $('#recharge-datatable').DataTable({
		processing: true,
		"language": {
			'loadingRecords': '&nbsp;',
			'processing': 'Loading...'
		},
		serverSide: true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		bInfo: true,
		iDisplayLength: 25,
		order: [
			[0, 'desc']
		],
		bAutoWidth: false,
		"ajax": {
			"url": "{{ route('recharge.list.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function(d) {
				d._token = "{{csrf_token()}}";
				d.search = $('input[type="search"]').val();
				d.status = $('#status').val();
				d.transaction_type = $('#transaction_type1').val();
			}
		},
		"columns": [{
				"data": "id"
			},
			{
				"data": "name"
			},
			{
				"data": "transaction_type"
			},
			{
				"data": "amount"
			},
			{
				"data": "payment_receipt"
			},
			{
				"data": "note"
			},
			{
				"data": "status"
			},
			{
				"data": "created_at"
			}
		]
	});

	$('#transaction_type1,#status').change(function() {
		dataTable.draw()
	})
	dataTable.columns(4).visible(false);
	@if(Auth::user()-> role != "admin")
	dataTable.columns(1).visible(false);
	@endif
</script>
@endpush