<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Staff</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="editStaffForm" action="{{ route('staff.update', ['id' => $staff->id]) }}" method="post" enctype="multipart/form-data">
				 <div class="modal-body row">
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Company Name{{-- <span class="text-danger">*</span>--}}</label>
						<input type="text" class="form-control new-border-popups" id="company_name" name="company_name" value="{{ $staff->company_name }}" required>
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Name{{-- <span class="text-danger">*</span>--}}</label>
						<input type="text" class="form-control new-border-popups" id="name" name="name" value="{{ $staff->name }}">
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Mobile{{-- <span class="text-danger">*</span>--}}</label>
						<input type="text" class="form-control new-border-popups" id="mobile" name="mobile" value="{{ $staff->mobile }}"  oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Email{{-- <span class="text-danger">*</span>--}}</label>
						<input type="text" class="form-control new-border-popups" id="email" name="email" value="{{ $staff->email }}">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Password{{-- <span class="text-danger">*</span>--}}</label>
						<input type="text" class="form-control new-border-popups" id="password" name="password" value="" autocomplete="off">
					</div> 
					<div class="mb-3 col-md-6"> 
						<label for="recipient-name" class="form-label">Geneder{{-- <span class="text-danger">*</span>--}}</label>
						<select autocomplete="off" class="form-control new-border-popups" name="gender" id="gender" Required>
							<option value=""> Select Gender </option>
							<option value="Male"  {{ $staff->gender == 'Male' ? 'selected' : '' }}> Male </option>
							<option value="Female"  {{ $staff->gender == 'Female' ? 'selected' : '' }}> Female</option>
							<option value="Other"  {{ $staff->gender == 'Other' ? 'selected' : '' }}> Other</option>
						</select> 
					</div>
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status{{-- <span class="text-danger">*</span>--}}</label>
						<select class="form-control new-border-popups" id="status" name="status">
							<option value="1" {{ $staff->status == 1 ? 'selected' : '' }}> Active </option>
							<option value="0" {{ $staff->status == 0 ? 'selected' : '' }}> In-Active </option>
						</select>
					</div>   
				</div>
				<div class="modal-footer" style="padding-top: 0px; border-top : 0px;">
					<button type="submit" class="btn new-submit-popup-btn">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>  
	
	$('#editStaffForm').submit(function(event) 
	{
		event.preventDefault();   
		
		$(this).find('button').prop('disabled',true);    
		var formData = new FormData(this);  
		formData.append('_token', "{{ csrf_token() }}");
		 
		$.ajax({ 
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			processData: false, 
			contentType: false,  
			cache: false, 
			dataType: 'Json', 
			success: function (res) 
			{ 
				$('#editStaffForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.msg);  
					$('#editStaffModal').modal('hide');
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $('#' + key);
						var errorSpan = $('<span>')
						.addClass('error_msg text-danger') 
						.attr('id', key + 'Error')
						.text(value[0]);  
						inputField.parent().append(errorSpan);
					});
				}
				else
				{  
					toastrMsg(res.status,res.msg); 
				}
			} 
		});
	});	
	 
</script>  