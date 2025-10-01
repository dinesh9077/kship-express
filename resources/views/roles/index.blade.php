@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Role')
@section('header_title','Role')
@section('content') 

<style>
	.tooltip .tooltiptext 
	{ 
	text-align: left !important; 
	padding: 5px 0 5px 5px !important;
	}	
	td p {
	margin-bottom: 0;
    }
</style>
<div class="content-page">
     <div class="content">
          <!-- Start Content-->
          <div class="container-fluid">
               <div class="ordr-main-001">   
                   <div class="main-disolay-felx" style="margin-top: 0 !important;">
				   @if(config('permission.roles.add'))
					   <div class="main-btn0main-1"> 
						   <button class="btn-main-1" onclick="addRoles(this, event)"><span class="mdi mdi-plus"></span> Create New Role</button>
					   </div>
				   @endif
                   </div>
						<div class="main-calander-11"> 
							<div class="main-data-teble-1 table-responsive">
								 <table id="roleDatatable" class="" style="width:100%">
									  <thead>
										   <tr>
												<th> Sr.No</th>
												<th> Name </th>
												<th> Status </th>
												<th> Created </th>
												<th> Action </th>
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
	var dataTable = $('#roleDatatable').DataTable({ 
		processing:true,
		"language": {
			'loadingRecords': '&nbsp;',
			'processing': 'Loading...'
		},
		serverSide:true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		responsive:false,
		bInfo: true,
		iDisplayLength: 10,
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
			"url": "{{ route('roles.ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}"; 
			}
		},
		"columns": [
		{ "data": "id" },    
		{ "data": "name" },   
		{ "data": "status" },  
		{ "data": "created_at" }, 
		{ "data": "action" }
		]
	}); 
	
	function addRoles(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{ route('roles.create') }}", function(res)
			{ 
				$('body').find('#modal-view-render').html(res.view);
				$('#addRoleModal').modal('show');  
			});
		} 
	}
	
	function editRoles(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(obj, function(res)
			{ 
				$('body').find('#modal-view-render').html(res.view);
				$('#editRoleModal').modal('show');  
			});
		} 
	} 
</script>
@endpush				