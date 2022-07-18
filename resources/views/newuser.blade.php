@extends('layouts.app')

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">New User</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<form id="FormRegister">
			<div class="form-group">
				<label for="useridmsvg">User ID (MSVG)</label>
				<input type="text" id="useridmsvg" name="useridmsvg" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="fullname">Full Name</label>
				<input type="text" id="fullname" name="fullname" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="email">Email Address</label>
				<input type="email" id="email" name="email" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="phonenumber">Phone Number</label>
				<div class="input-group">
					<span class="input-group-text p-0 bg-white">
						<select id="phonecode" name="phonecode" class="form-control border-0"></select>
					</span>
					<input type="tel" id="phonenumber" name="phonenumber" required="" class="form-control ">
				</div>
				<small class="text-info">Phone Number will be used for login</small>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="confirmpassword">Confirm Password</label>
				<input type="password" id="confirmpassword" name="confirmpassword" required="" class="form-control ">
			</div>
			<div class="form-group ">
				<label for="user_status">Status</label>
				<select id="user_status" name="user_status" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Pending">Pending</option>
					<option value="Active">Active</option>
				</select>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function Pageload() {
		$('#phonecode').html('');
		$('#FormRegister')[0].reset();

		$('#FormRegister input').removeClass(function(index, className) {
			return (className.match(/(^|\s)is-\S+/g) || []).join(' ');
		});
		$('#FormRegister').find('.invalid-feedback, .form-control-feedback').remove();

		$('#FormRegister input').closest('.form-group').removeClass(function(index, className) {
			return (className.match(/(^|\s)has-\S+/g) || []).join(' ');
		});
		$('#FormRegister').find('.input-group-append').removeClass('d-none');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareNewUser',
			type: 'POST',
			dataType: 'JSON',
			success: function(data) {
				console.log(data);

				if (data.status == 'success') {
					if (data.phonecode) {
						if (data.phonecode.length > 0) {
							$.each(data.phonecode, function(k, v) {
								$('#phonecode').append('<option value="' + v + '">' + v + '</option>');
							});
						}
					}
				}

				$('.pre-loader').hide();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log('Error', xhr);
				$('.pre-loader').hide();
			},
			complete: function(c) {

			}
		});
	}

	$(document).ready(function() {

		Pageload();

		$(document).on('click', '#btnFormReset', function(e) {
			Pageload();
		})

		$(document).on('submit', '#FormRegister', function(e) {
			e.preventDefault();

			$(".pre-loader").fadeIn();

			var form = $(this);
			var _SubmitButton = form.find('button[type="submit"]');

			form.find('.invalid-feedback').remove();
			_SubmitButton.attr('disabled', true);

			setTimeout(function() {
				var formData = new FormData(form[0]);

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/NewUser',
					type: "POST",
					data: formData,
					dataType: 'json',
					contentType: false,
					processData: false,
					cache: false,
					success: function(data) {
						console.log(data);

						Swal.fire({
							icon: data.status,
							title: data.message,
						});

						if (data.status == 'success') {
							Pageload();
						} else {
							if (data.error) {
								form.find('.invalid-feedback').remove();
								if (data.error.message) {
									_SubmitButton.before('<p class="invalid-feedback d-block">' + data.error.message + '</p>');
								}
								$.each(data.error, function(k, v) {
									form.find('#' + k).closest('.form-group').append('<div class="invalid-feedback">' + v + '</div>');
								});
								form.find('.invalid-feedback').css({
									'display': 'block'
								});
							}
							$(".pre-loader").fadeOut();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log('Error', xhr);
						$(".pre-loader").fadeOut();
					},
					complete: function(c) {
						setTimeout(function() {
							_SubmitButton.attr('disabled', false);
						}, 800);
					}
				});
			}, 800);
		});

	})
</script>
@endpush