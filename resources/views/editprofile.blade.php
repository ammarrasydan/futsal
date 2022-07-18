@extends('layouts.app')

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">Edit Profile</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<label for="username">Username</label>
		<input autocomplete="off" spellcheck="false" autocorrect="off" id="username" name="username" class="form-control" type="text" readonly required>
	</div>
</div>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<p class="text-secondary">Change Password</p>
		<form id="FormEditProfile">
			<div class="form-group">
				<label for="oldpassword">Old Password</label>
				<div class="input-group">
					<input autocapitalize="none" autocomplete="off" spellcheck="false" autocorrect="off" type="password" id="oldpassword" name="oldpassword" class="form-control" required>
					<a href="#" class="input-group-append btnShowHidePassword" tabindex="-1">
						<span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
					</a>
				</div>
			</div>
			<div class="form-group">
				<label for="newpassword">New Password</label>
				<div class="input-group">
					<input autocapitalize="none" autocomplete="off" spellcheck="false" autocorrect="off" type="password" id="newpassword" name="newpassword" class="form-control" required>
					<a href="#" class="input-group-append btnShowHidePassword" tabindex="-1">
						<span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
					</a>
				</div>
			</div>
			<div class="form-group">
				<label for="confirmpassword">Confirm Password</label>
				<div class="input-group">
					<input autocapitalize="none" autocomplete="off" spellcheck="false" autocorrect="off" type="password" id="confirmpassword" name="confirmpassword" class="form-control" required>
					<a href="#" class="input-group-append btnShowHidePassword" tabindex="-1">
						<span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
					</a>
				</div>
			</div>
			<div class="form-group">
				<button class="btn btn-primary" type="submit">Submit</button>
				<button class="btn btn-light" id="btnFormReset" type="button">Reset</button>
			</div>
		</form>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function Pageload() {
		$('.pre-loader').show();
		$('#FormEditProfile')[0].reset();
		$('#FormEditProfile input').removeClass(function(index, className) {
			return (className.match(/(^|\s)is-\S+/g) || []).join(' ');
		});
		$('#FormEditProfile').find('.invalid-feedback, .form-control-feedback').remove();

		$('#FormEditProfile input').closest('.form-group').removeClass(function(index, className) {
			return (className.match(/(^|\s)has-\S+/g) || []).join(' ');
		});
		$('#FormEditProfile').find('.input-group-append').removeClass('d-none');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareEditProfile',
			type: 'POST',
			dataType: 'JSON',
			success: function(data) {
				console.log(data);

				if (data.status == 'success') {
					$('#username').val(data.username);
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

		$(document).on('submit', '#FormEditProfile', function(e) {
			e.preventDefault();

			$('.pre-loader').show();

			var form = $(this);
			var _SubmitButton = form.find('button[type="submit"]');

			form.find('.invalid-feedback').remove();
			_SubmitButton.attr('disabled', true);

			setTimeout(function() {
				var formData = new FormData(form[0]);
				formData.append('additional_field_for_testing', '');

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/ChangePassword',
					type: "POST",
					async: false,
					data: formData,
					dataType: 'json',
					contentType: false,
					processData: false,
					cache: false,
					success: function(data) {
						console.log(data);

						Swal.fire({
							icon: data.status,
							title: data.message
						});

						if (data.status == 'success') {
							Pageload();
						} else {
							if (data.error) {
								$.each(data.error, function(k, v) {
									form.find('#' + k).closest('.form-group').append('<div class="invalid-feedback d-block">' + v + '</div>');
								});
							}

							$('.pre-loader').hide();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						$('.pre-loader').hide();
						console.log('Error');
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