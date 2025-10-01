@extends('layouts.backend.app')
@section('title',config('setting.company_name').' - Shipping Companies')
@section('header_title','Shipping Companie')
@section('content') 

<div class="content-page">
    <div class="content">
		
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="main-order-page-1">                    
               <div class="main-order-001"> 
                    <div class="main-create-order">
                        <div class="main-disolay-felx" style="margin-top: 0 !important;">
							@if(config('permission.shipping_company.add'))	
								<div class="main-btn0main-1">
									<button  class="btn btn-primary btn-main-1" onclick="createShipping(event)">  Create Shipping Company </button>
								</div>
							@endif
        				</div>
        				
                        <div class="main-data-teble-1 table-responsive">
							<table id="shipping-datatable" class="table table-striped" style="width:100%">
								<thead>
									<tr>
										<th>Sr.No.</th>
										<th>Company Logo</th>
										<th>Company Name</th> 
										<th>Url</th> 
										<th>Mode</th>
										<th>Status</th>
										<th>Created At</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($shippings as $key => $shipping)
										<tr>
											<td>{{ $key + 1 }}</td>
											<td>
												@if($shipping->logo)
													<img class="pan1" src="{{ url('storage/shipping-logo/' . $shipping->logo) }}" alt="{{ $shipping->name }} Logo">
												@else
													<span>No Logo</span>
												@endif
											</td>
											<td>{{ $shipping->name }}</td> 
											<td>{{ $shipping->url }}</td> 
											<td>
												<span class="badge {{ $shipping->mode == 1 ? 'badge-success' : 'badge-warning' }}">
													{{ $shipping->mode == 1 ? 'Live' : 'TEST' }}
												</span>
											</td>
											<td>
												<span class="badge {{ $shipping->status == 1 ? 'badge-success' : 'badge-danger' }}">
													{{ $shipping->status == 1 ? 'Active' : 'In-Active' }}
												</span>
											</td>
											<td>{{ $shipping->created_at->format('d M Y') }}</td>
											<td>
												@if(config('permission.shipping_company.edit'))	
													<a href="javascript:;" onclick="editShipping(this,event)"
													   data-id="{{ $shipping->id }}"
													   data-name="{{ $shipping->name }}"
													   data-status="{{ $shipping->status }}" 
													   data-api_key="{{ $shipping->api_key }}"
													   data-secret_key="{{ $shipping->secret_key }}"
													   data-mode="{{ $shipping->mode }}"
													   data-url="{{ $shipping->url }}"
													   data-email="{{ $shipping->email }}"
													   data-password="{{ $shipping->password }}"
													   class="btn class-pencil mr-1">
														<i class="mdi mdi-pencil " style="color: #1C21DE;"></i>
													</a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
 
        			</div>
    			</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="createShippingModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Create Shipping Company </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>  
			<form id="shippingStoreForm" method="post" action="{{ route('shipping.company.store') }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
                    <div class="row">
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Shipping Company Logo </label>
								<input type="file" class="form-control" name="logo" accept="image/png, image/jpe, image/jpeg">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Shipping Company Name </label>
								<input type="text" class="form-control" name="name" placeholder="Shipping Company Name" required>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Api Key/Token</label>
								<input type="text" class="form-control" name="api_key" placeholder="Api Key/Token">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Secret Key</label>
								<input type="text" class="form-control" name="secret_key" placeholder="Secret Key">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Email</label>
								<input type="text" class="form-control" name="email" placeholder="Email">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Password</label>
								<input type="text" class="form-control" name="password" placeholder="Password">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Url</label>
								<input type="url" class="form-control" name="url" placeholder="Url" Required>
							</div>
						</div>
						<div class="col-lg-6" style="display:none;">
							<div class="from-group my-2">
								<label for="order-id">Tax</label>
								<input type="number" class="form-control" name="tax" placeholder="tax" value="0">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id"> Environment Mode</label>
								<select class="form-control" name="mode" id="mode" required>
									<option value="1"> Live </option>
									<option value="2"> TEST </option>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id"> Status </label>
								<select class="form-control" name="status" id="status" required>
									<option value="1"> Active </option>
									<option value="2"> In-Active </option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="border-top: none; padding-top : 0px;"> 
					<button type="submit" class="btn new-submit-popup-btn"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="editShippingModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Update Shipping Company </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<form id="shippingEditForm" method="post" action="{{ route('shipping.company.update') }}"  enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Shipping Company Logo </label>
								<input type="file" class="form-control" name="logo" accept="image/png, image/jpe, image/jpeg">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Shipping Company Name </label>
								<input type="text" class="form-control" name="name" id="edit_name" placeholder="Shipping Company Name" required>
							</div>
							<input type="hidden" id="edit_id" name="id">
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Api Key/Token</label>
								<input type="text" class="form-control" id="edit_api_key" name="api_key" placeholder="Api Key/Token">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Secret Key</label>
								<input type="text" class="form-control" id="edit_secret_key" name="secret_key" placeholder="Secret Key">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Email</label>
								<input type="text" class="form-control" id="edit_email" name="email" placeholder="Email">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Password</label>
								<input type="text" class="form-control" id="edit_password" name="password" placeholder="Password">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Url</label>
								<input type="url" class="form-control" id="edit_url" name="url" placeholder="Url" Required>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id">Tax</label>
								<input type="number" class="form-control" id="edit_tax" name="tax" placeholder="tax" value="0">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id"> Environment Mode</label>
								<select class="form-control" name="mode" id="edit_mode" required>
									<option value="1"> Live </option>
									<option value="2"> TEST </option>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="from-group my-2">
								<label for="order-id"> Status </label>
								<select class="form-control" name="status" id="edit_status" required>
									<option value="1"> Active </option>
									<option value="2"> In-Active </option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer " style="border-top: none; padding-top: 0px;"> 
					<button type="submit" class="btn new-submit-popup-btn"> Save </button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('js') 
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script> 
	$('#shipping-datatable').DataTable(); 
	
	function createShipping(event) {
		event.preventDefault();
		$('#shippingStoreForm')[0].reset();
		$('#createShippingModal').modal('show');
	}
 
	function editShipping(obj, event)
	{
		event.preventDefault();  
 
		var $obj = $(obj);
		
		var id = $obj.data('id');
		var name = $obj.data('name');
		var status = $obj.data('status');
		var tax = $obj.data('tax');
		var apiKey = $obj.data('api_key');
		var secretKey = $obj.data('secret_key');
		var email = $obj.data('email');
		var password = $obj.data('password');
		var url = $obj.data('url');
		var mode = $obj.data('mode');

		// Set the values in the form
		$('#edit_id').val(id);
		$('#edit_name').val(name);
		$('#edit_api_key').val(apiKey);
		$('#edit_secret_key').val(secretKey);
		$('#edit_email').val(email);
		$('#edit_password').val(password);
		$('#edit_url').val(url);
		$('#edit_mode').val(mode);
		$('#edit_tax').val(tax);
		$('#edit_status').val(status);

		// Show the modal
		$('#editShippingModal').modal('show');
	}

</script>
@endpush