@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Report')
@section('header_title','Report')
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
               <div class="main-filter-weight">                    
                    <div class="row row-re">
                         @if(Auth::user()->role == "admin")
                         <div class="col-lg-2 col-sm-6">
                              <div class="main-selet-11">
                                   <select name="user" id="user_id">
                                        
                                        @foreach ($users as $user)
                                             <option value="{{$user->id}}"> {{$user->name}}  </option>
                                        @endforeach
                                   </select>
                              </div>
                         </div>
                         @endif
                         <div class="col-lg-2 col-sm-6">
                              <div class="main-selet-11">
                                   <input type="text" class="form-control datepicker" name="fromdate" <?php echo (isset($_GET['fromdate']))?$_GET['fromdate']:''; ?> id="fromdate" placeholder="From Date">
                              </div>
                         </div>
                         <div class="col-lg-2 col-sm-6">
                              <div class="main-selet-11">
                                   <input type="text" class="form-control datepicker" name="todate" <?php echo (isset($_GET['todate']))?$_GET['todate']:''; ?> id="todate" placeholder="To Date">
                              </div>
                         </div>
                         <div class="col-lg-2 col-sm-6">
                              <div class="main-selet-11">
                                   <button class="btn-main-1 search_user">Search</button>
                              </div>
                         </div>
                    </div>                    
               </div>
                    <ul id="tab">                             
                      <li class="active">
                           <div class="main-calander-11"> 
                                <div class="main-data-teble-1 table-responsive">
                                     <table id="wallet_datatable" class="" style="width:100%">
                                          <thead>
                                               <tr>
                                                    <th> Sr.No</th>
                                                    <th> User Name </th>
                                                    <th> Billing details </th>
                                                    <th> Transaction Details </th>
                                                    <th> Credit </th>
                                                    <th> Debit </th>
                                                    <th> Balance </th>
                                                    {{-- <th> Amount </th> --}}
                                                    <th> Description </th>
                                                    <th> Date </th>
                                               </tr>
                                          </thead> 
                                     </table>
                                </div>
                           </div>
                      </li> 
                 </ul>
                    </div>
               </div>
          </div>
     </div>
</div>
@endsection
@push('js')  
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
     $('ul.tabs-001 li').click(function() {
         var tab_id = $(this).attr('data-tab');
         $('ul.tabs-001 li').removeClass('current');
         $('.tab-content11').removeClass('current');
         $(this).addClass('current');
         $("#" + tab_id).addClass('current');
       })
  })
  
  
  
  var dataTable = $('#wallet_datatable').DataTable({
       processing:true,
       "language": {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
       },
       dom: 'Bflitp', // Include the 'B' button container in the layout
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-outline-primary'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-outline-primary'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-outline-success'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-outline-danger'
                },
                {
                    extend: 'print',
                    className: 'btn btn-outline-secondary'
                }
            ],
       serverSide:true,
       bLengthChange: true,
       searching: true,
       bFilter: true,
       bInfo: true,
       iDisplayLength: 25,
    //   lengthMenu: [[10, 25, 50, 100, 10000000], [ 10, 25, 50, 100, "All"]],
       order: [[0, 'desc'] ],
       bAutoWidth: false,			 
       "ajax":{
            "url": "{{ route('daily_wallet.dailywalletAjax') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
                 d._token   = "{{csrf_token()}}";
                 d.search   = $('input[type="search"]').val();
                 d.user_id   = $('#user_id').val(); 
                 d.fromdate   = $('#fromdate').val(); 
                 d.todate   = $('#todate').val(); 
            }
       },
       "columns": [
       { "data": "id" }, 
       { "data": "name" }, 
       { "data": "billing_type" }, 
       { "data": "transaction_type" }, 
       { "data": "credit" }, 
       { "data": "debit" }, 
       { "data": "balance" }, 
     //   { "data": "amount" }, 
       { "data": "note" }, 
       { "data": "created_at" }
       ]
  }); 
  $('.search_user').click(function (){
			dataTable.draw();	
	});
  $('#wallet_datatable').on('draw.dt', function() {
       $('[data-toggle="tooltip"]').tooltip({
            position: {
                 my: "left bottom", // the "anchor point" in the tooltip element
                 at: "left top", // the position of that anchor point relative to selected element
            }
       });
	@if(Auth::user()->role != "admin") 
	dataTable.columns(1).visible(false); 
	@endif 
  });
</script>
@endpush