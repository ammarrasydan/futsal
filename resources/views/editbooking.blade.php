@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/cropper.css') }}">

@endpush

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item"><a href="/booking/list">List Booking</a></li>
	<li class="breadcrumb-item active">Edit Booking</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<form id="FormEditUser">
			<div class="form-group">
				<label for="user_id">ID</label>
				<input autocomplete="off" spellcheck="false" autocorrect="off" id="user_id" name="user_id" value="{{$id}}" class="form-control" type="text" required readonly>
			</div>
			<div class="form-group">
				<label for="phonenumber">Phone Number</label>
				<div class="input-group">
					<span class="input-group-prepend p-0 bg-white">
						<select id="phonecode" name="phonecode" class="form-control" required></select>
					</span>
					<input type="tel" id="phonenumber" name="phonenumber" required="" class="form-control ">
				</div>
			</div>
			<div class="form-group">
				<label for="fullname">Full Name</label>
				<input autocomplete="off" spellcheck="false" autocorrect="off" id="fullname" name="fullname" class="form-control" type="text" required>
			</div>
			<div class="form-group ">
				<label for="duration">Duration</label>
				<select id="duration" name="duration" class="form-control" required>
				<option selected disabled>Select an option</option>
					<option value="30">00 hours 30 mins</option>
					<option value="60">01 hours 00 mins</option>
					<option value="90">01 hours 30 mins</option>
					<option value="120">02 hours 00 mins</option>
					<option value="150">02 hours 30 mins</option>
					<option value="180">03 hours 00 mins</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="court">Court</label>
				<select id="court" name="court" class="form-control" required>
				<option selected disabled>Select an option</option>
					<option value="Court 1">Court 1</option>
					<option value="Court 2">Court 2</option>
					<option value="Court 3">Court 3</option>
					<option value="Court 4">Court 4</option>
					<option value="Court 5">Court 5</option>
					<option value="Court 6">Court 6</option>
					<option value="Court 7">Court 7</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="status">Status</label>
				<select id="status" name="status" class="form-control" required>
				<option selected disabled>Select an option</option>
					<option value="Pending">Pending</option>
					<option value="Active">Active</option>
					<option value="Approved">Approved</option>
					<option value="Rejected">Rejected</option>
					<option value="Cancelled">Cancelled</option>
				</select>
			</div>
			<hr />
			<div class="form-group">
				<button class="btn btn-primary" type="submit">Submit</button>
				<button class="btn btn-light" id="btnFormReset" type="button">Reset</button>
			</div>
		</form>
		<hr />
		
			</div>
		</div>
		<hr />
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cropper.min.js') }}"></script>
<script type="text/javascript">
	function Pageload() {
		$('#phonecode').html('');
		$('.pre-loader').show();
		$('#FormEditUser')[0].reset();
		$('#FormEditUser input').removeClass(function(index, className) {
			return (className.match(/(^|\s)is-\S+/g) || []).join(' ');
		});
		$('#FormEditUser').find('.invalid-feedback, .form-control-feedback').remove();

		$('#FormEditUser input').closest('.form-group').removeClass(function(index, className) {
			return (className.match(/(^|\s)has-\S+/g) || []).join(' ');
		});
		$('#FormEditUser').find('.input-group-append').removeClass('d-none');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareEditBooking',
			data: {
				'user_id': '{{$id}}'
			},
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

					if (data.user) {
						$('#user_id').val(data.user.id);

						$("#phonecode").filter(function() {
							return $(this).text() == data.user.phonecode;
						}).prop('selected', true);

						$('#phonecode').val(data.user.phonecode);
						$('#phonenumber').val(data.user.phonenumber);
						$('#fullname').val(data.user.fullname);
						$('#duration').val(data.user.duration);
						$('#court').val(data.user.court);
						$('#referralcode').val(data.user.referralcode);
						$('#referralby').val(data.user.referralby);
						$("#status").val(data.user.status);

						// $("#status option").filter(function() {
						// 	return $(this).text() == data.user.status;
						// }).prop('selected', true);

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

	var options = {
		aspectRatio: 4 / 3,
		initialAspectRatio: 4 / 3,
		viewMode: 0,
		autoCropArea: 2,
		dragMode: 'move',
		cropBoxResizable: false,
		cropBoxMovable: false,
		movable: true,
		rotatable: false,
		scalable: false,
		toggleDragModeOnDblclick: false,
		ready: function(e) {
			console.log(e.type);
		},
		cropstart: function(e) {
			console.log(e.type, e.detail.action);
		},
		cropmove: function(e) {
			console.log(e.type, e.detail.action);
		},
		cropend: function(e) {
			console.log(e.type, e.detail.action);
		},
		crop: function(e) {
			var data = e.detail;

			console.log(e.type);
		},
		zoom: function(e) {
			console.log(e.type, e.detail.ratio);
		}
	};

	$(document).ready(function() {

		Pageload();

		$(document).on('click', '#btnFormReset', function(e) {
			Pageload();
		})

		$(document).on('submit', '#FormEditUser', function(e) {
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
					url: '/EditBooking',
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