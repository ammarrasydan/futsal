@extends('layouts.app')

@section('content')
<div class="login-box bg-white box-shadow p-3 rounded center">
	<h2 class="text-center mb-3">Admin Login</h2>
	<form id="FormLogin">
		<div class="form-group">
			<label for="username">Username</label>
			<input autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="none" id="username" name="username" class="form-control" type="text" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="none" id="password" name="password" class="form-control" type="password" required>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<button class="btn btn-primary  btn-block" type="submit">Sign In</button>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {

		$(document).on('submit', '#FormLogin', function(e) {
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
					url: '/Login',
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
							title: data.message
						});

						if (data.status == 'success') {
							setTimeout(function() {
								window.location.replace("/");
							}, 1000);
						} else {
							if (data.error) {
								if (data.error.message) {
									_SubmitButton.before('<p class="invalid-feedback d-block">' + data.error.message + '</p>');
								}
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

		$(".pre-loader").fadeOut();
	})
</script>
@endpush