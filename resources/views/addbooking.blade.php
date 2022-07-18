@extends('layouts.app')

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">Add Booking</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<form id="FormBooking">
			<div class="form-group">
				<label for="fullname">Full Name</label>
				<input type="text" id="fullname" name="fullname" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="phonenumber">Phone Number</label>
				<div class="input-group">
					<span class="input-group-text p-0 bg-white">
						<select id="phonecode" name="phonecode" class="form-control border-0"></select>
					</span>
					<input type="tel" id="phonenumber" name="phonenumber" required="" class="form-control ">
				</div>
			</div>
			<div class="form-group">
				<label for="date">Date</label>
				<input type="date" id="date" name="date" required="" class="form-control">
			</div>
			<div class="form-group">
				<label for="time">Time Start</label>
				<input type="time" id="time_start" name="time_start" required="" class="form-control">
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
				<label for="booking_status">Status</label>
				<select id="booking_status" name="booking_status" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Pending">Pending</option>
					<option value="Active">Active</option>
					<option value="Approved">Approved</option>
					<option value="Rejected">Rejected</option>
					<option value="Cancelled">Cancelled</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="pricetype">Price</label>
				<select id="pricetype" name="pricetype" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Normal">Normal</option>
					<option value="Student-Collage">Student-Collage</option>
					<option value="Student-Secondary">Student-Secondary</option>
					<option value="Student-Primary">Student-Primary</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="type">Type</label>
				<select id="type" name="type" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Standby">Standby</option>
					<option value="Reconfirmed">Reconfirmed</option>
					<option value="Cancelled">Cancelled</option>
					<option value="Cancelled(Absent)">Cancelled(Absent)</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="payment">Payment</label>
				<select id="payment" name="payment" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Paid">Paid</option>
					<option value="Unpaid">Unpaid</option>
				</select>
			</div>
			<div class="form-group ">
				<label for="amount">Actual Amount (RM)</label>&nbsp;&nbsp;
				<input type="text" name="amount" id="amount" /><br><br>
			<div>
			<div class="form-group"><br>
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
		$('#FormBooking')[0].reset();

		$('#FormBooking input').removeClass(function(index, className) {
			return (className.match(/(^|\s)is-\S+/g) || []).join(' ');
		});
		$('#FormBooking').find('.invalid-feedback, .form-control-feedback').remove();

		$('#FormBooking input').closest('.form-group').removeClass(function(index, className) {
			return (className.match(/(^|\s)has-\S+/g) || []).join(' ');
		});
		$('#FormBooking').find('.input-group-append').removeClass('d-none');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareNewBooking',
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

		$(document).on('submit', '#FormBooking', function(e) {
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
					url: '/create',
					type: "POST",
					data: formData,
					dataType: 'json',
					contentType: false,
					processData: false,
					cache: false,
					success: function(data) {
						console.log('xxx', data);

						Swal.fire({
							icon: data.status,
							title: data.message,
						});

						if (data.status == 'success') {
							if(data.url){
							    window.open(data.url);
							}

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