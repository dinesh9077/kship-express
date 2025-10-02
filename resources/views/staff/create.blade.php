<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">create New staff</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="addStaffForm" action="{{ route('staff.store') }}" method="post" enctype="multipart/form-data">
				<div class="modal-body row">
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Company Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control  new-border-popups" id="company_name" name="company_name" required>
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control  new-border-popups" id="name" name="name" required>
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Mobile<span class="text-danger">*</span></label>
						<input type="text" class="form-control  new-border-popups" id="mobile" name="mobile" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required>
					</div>  
					 
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Email<span class="text-danger">*</span></label>
						<input type="text" class="form-control  new-border-popups" id="email" name="email" required>
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Password<span class="text-danger">*</span></label>
						<input type="password" class="form-control  new-border-popups" id="password" name="password" required>
					</div> 
					
					<div class="mb-3 col-md-6"> 
						<label for="recipient-name" class="form-label">Geneder<span class="text-danger">*</span></label>
						<select autocomplete="off" class="form-control  new-border-popups" name="gender" id="gender" Required>
							<option value=""> Select Gender </option>
							<option value="Male"> Male </option>
							<option value="Female"> Female</option>
							<option value="Other"> Other</option>
						</select> 
					</div>
									
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Roles<span class="text-danger">*</span></label>
						<select class="form-control  new-border-popups" id="role" name="role" required>
							<option value=""> Select Roles </option> 
							@foreach($roles as $role)
								@if($role->name != 'user')
									<option value="{{ $role->name }}" data-role-id="{{ $role->id }}"> {{ $role->name }} </option> 
								@endif
							@endforeach
						</select>
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status<span class="text-danger">*</span></label>
						<select class="form-control  new-border-popups" id="status" name="status">
							<option value="1"> Active </option>
							<option value="0"> In-Active </option>
						</select>
					</div>  
					 
				</div>
				<div class="roles-table-main permission_show row col-md-12 m-0"> </div>
				
				
				<div class="modal-footer"  style="border-top: 0px; padding-top : 0px;">
					<button type="submit" class="btn new-submit-popup-btn">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script> 
		
	$('#addStaffForm').submit(function(event) 
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
				$('#addStaffForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.msg);  
					$('#addStaffModal').modal('hide');
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
	
	$('#role').change(function ()
	{  
		$('.permission_show').html(''); 
		const role = $(this).val(); 
		const roleId = $(this).find(':selected').attr('data-role-id'); 
 
		if(role != "admin")
		{
			$.get("{{ url('roles/groups') }}/"+roleId, function(res)
			{  
				$('.permission_show').html(res.view);  
			},'Json');
		}  
	})
		
	$('.selectAllModule').on('click', function()
	{
		if (this.checked) {
			$('tbody').find('input[type="checkbox"]').prop('checked', true);
		} else {
			$('tbody').find('input[type="checkbox"]').prop('checked', false);
		}
	});
	
	$('.selectAll').on('click',function()
	{ 
		var num = $(this).val();   
		if(this.checked)
		{
			$('.checkbox_'+num).each(function(){
				this.checked = true;
			});
		}
		else
		{
			$('.checkbox_'+num).each(function(){
				this.checked = false;
			});
		}
	});
</script>  