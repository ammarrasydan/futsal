@extends('layouts.app')

@push('styles')
<style>
	#TableBookings {
		font-size: 14px;
	}

	#TableBookings tbody tr td p {
		margin: 0;
	}

	#TableBookings tbody tr td:not(:first-child) {
		cursor: pointer;
	}

	#TableBookings tbody tr.standby td {
		padding-top: 2px;
		padding-bottom: 2px;
		color: #888888;
	}

	#TableBookings thead tr th {
		text-align: center;
	}

	.bookingdata {
		padding: 5px 10px 5px 10px;
		border-radius: 5px;
		cursor: pointer;
	}

	.bookingdata .halfhour {
		height: 10px;
		width: 10px;
		border-radius: 50%;
		background-color: #ffff00;
		border: 1.5px solid #888888;
	}
</style>
@endpush

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">Table Booking</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">

		<h5 class="mb-3">View Booking</h5>
		<div class="form-group">
			<label for="date">Date</label>
			<input type="date" id="searchdate" name="searchdate" required="">
			<button type="button" class="btn btn-primary btn-sm btnsearch">Search</button>
		</div>
		<button type="button" class="btn btn-outline-primary btnyesterday">Yesterday</button>
		<button type="button" class="btn btn-outline-primary btntoday">Today</button>
		<button type="button" class="btn btn-outline-primary btntomorrow">Tomorrow</button>

		<hr />

		<h5 class="mb-2">Booking Date: <span class="searchdate"></span></h5>
		<div class="table-responsive">
			<table id="TableBookings" class="table table-hover table-bordered table-striped text-nowrap table-dark">
				<thead>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

		</hr>
		<!-- <table border="5" cellspacing="0" align="center">
			<tr><br><br>
				<td align="center:50%" height="50" width="120"><br>
					<b>Times</b></br>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 1</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 2</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 3</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 4</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 5</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 6</b>
				</td>
				<td align="center" height="50" width="120">
					<b><br>Court 7</b>
				</td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>8am - 9am</b></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>9am - 10am</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>10am - 11am</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>11am -12pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>12pm - 1pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>1pm - 2pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>2pm - 3pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>3pm - 4pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>4pm - 5pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>5pm - 6pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>6pm - 7pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>7pm - 8pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>8pm - 9pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>Standby</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
			<tr>
				<td align="center" height="50">
					<b>9pm - 10pm</b>
				</td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
				<td align="center" height="50"></td>
			</tr>
		</table> -->
	</div>
</div>

<!-- Modal Header -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" data-target="myModal" data-id="booking" aria-labelledby="myModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalCenterTitle">Add Booking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

<!-- Modal Body -->
      <div class="modal-body">
	  <form id="FormEditBooking">
	        <div class="form-group">
				<label for="booking_id">Booking ID</label>
				<input autocomplete="off" spellcheck="false" autocorrect="off" id="booking_id" name="booking_id" class="form-control" type="text" required readonly>
			</div>
			<div class="form-group">
				<label for="fullname">Full Name</label>
				<input type="text" id="fullname" name="fullname" required="" class="form-control ">
			</div>
			<div class="form-group">
				<label for="phonenumber">Phone Number</label>
				<div class="input-group">
					<span class="input-group-text p-0 bg-white">
						<select id="phonecode" name="phonecode" class="form-control border-0" required>
					    <option value="86">86</option>
					    <option value="852">852</option>
					    <option value="91">91</option>
					    <option value="62">62</option>
					    <option value="81">81</option>
					    <option value="82">82</option>
						<option value="60">60</option>
					    <option value="65">65</option>
					    <option value="66">66</option>
				        </select>
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
				<select id="court" name="court" value="court" class="form-control" required>
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
				<label for="pricetype">Price Type</label>
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

<!-- Modal Footer -->
      <div class="modal-footer">
	    <button type="submit" class="btn btn-primary">Submit</button>
        <button type="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/cropper.min.js') }}"></script>
<script type="text/javascript">

	var courts = [];

	function getColIndex(coltext) {
		var th_index = null;

		$.each($('#TableBookings thead tr th'), function(k, v) {
			if ($(this).text() == coltext) {
				th_index = k
				return
			}
		});

		return th_index
	}

	function Pageload(searchdatetype = '') {
		$('#TableBookings tbody').html('');
		$('#TableBookings thead').html('<tr><th>Times</th></tr>');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/GetBookingTable',
			data: {
				"searchdatetype": searchdatetype,
				"searchdate": $('#searchdate').val()
			},
			type: 'POST',
			dataType: 'JSON',
			success: function(data) {
				console.log(data);

				if (data.status == 'success') {
					var _courts = '';

					if (data.courts) {
						if (data.courts.length > 0) {
							courts = data.courts;
							$.each(data.courts, function(k, v) {
								_courts += '<td></td>';
								$('#TableBookings thead tr').append('<th>' + v + '</th>');
							});
						}
					}

					if (data.tabletimes) {
						if (data.tabletimes.length > 0) {
							$.each(data.tabletimes, function(k, v) {
								$('#TableBookings tbody').append('<tr class="' + v.timefrom + '" data-time="' + v.timeformat + 
								'"><td>' + v.timefrom + ' - ' + v.timeto + '</td>' + _courts + '</tr>');
								$('#TableBookings tbody').append('<tr class="' + v.timefrom + '_standby standby"><td>Standby</td>' 
								+ _courts + '</tr>');
							});
						}
					}

					if (data.bookings) {
						if (data.bookings.length > 0) {
							$.each(data.bookings, function(k, v) {
								$('#TableBookings tbody tr.' + v.tablestarttime + ' td:eq(' + getColIndex(v.court) + ')').append('<div class="bookingdata" data-id="' + v.id + '"><div>' + v.fullname + ' (' + v.bookingstarttime + ' - ' + v.bookingendTime + ')</div><div>' + v.currentstarttime + ' - ' + v.currentendtime + ' <div class="d-inline-block ' + (v.halfhour ? 'halfhour' : '') + '"></div></div></div>');
							});
						}
					}
					
					$('#searchdate').val(data.searchdate)
					$('.searchdate').text(data.searchdate)
					$('#court').val(data.court)
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

		$(document).on('click', '.btnsearch', function(e) {
			Pageload();
		})

		$(document).on('click', 'td', function(e) {

		    $('#FormEditBooking')[0].reset();
			$('#FormEditUser input').removeClass(function(index, className) {
			return (className.match(/(^|\s)is-\S+/g) || []).join(' ');
		});
		    $('#FormEditUser').find('.invalid-feedback, .form-control-feedback').remove();

	     	$('#FormEditUser input').closest('.form-group').removeClass(function(index, className) {
			return (className.match(/(^|\s)has-\S+/g) || []).join(' ');
		});
	    	$('#FormEditUser').find('.input-group-append').removeClass('d-none');

			var starttime = $(this).closest('tr').data('time');
			var col_index = $(this).parent().children().index($(this));
			var court = $('#TableBookings thead tr th:eq(' + col_index + ')').text();
			var isvalidcourt = courts.indexOf(court);

			if (isvalidcourt > -1) {
			    $("#time_start").val(starttime)
			    $("#court").val(court)
   
                $("#myModal").modal('show');
				
		    } else {
			    console.log('No court selected');
		    }
	})

		$(document).on('click', '.btntoday', function(e) {
			Pageload('today');
		})

		$(document).on('click', '.btnyesterday', function(e) {
			Pageload('yesterday');
		})

		$(document).on('click', '.btntomorrow', function(e) {
			Pageload('tomorrow');
		})

		$(document).on('click', '.bookingdata', function(e) {
		  
			var data_id = $(this).attr('data-id');

			$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/GetBookingData',
			data: {
				"dataid": data_id
			},
			type: 'POST',
			dataType: 'JSON',
			success: function(data) {
				console.log(data);

				if (data.status == 'success') {
					$('#booking_id').val(data.booking.id);
					$('#fullname').val(data.booking.fullname);
					$('#phonecode').val(data.booking.phonecode).change();
					$('#phonenumber').val(data.booking.phonenumber);
					$('#time_start').val(data.booking.timestart);
					$('#date').val(data.booking.date);
					$('#duration').val(data.booking.duration).change();
					$('#court').val(data.booking.court).change();
					$('#booking_status').val(data.booking.status).change();
					$('#payment').val(data.booking.payment).change();
					$('#pricetype').val(data.booking.pricetype);
					$('#amount').val(data.booking.amount);
					$('#type').val(data.booking.type).change();
				
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
	})

	    $(document).on('mouseenter', '.bookingdata', function(e) {
			var _this = $(this);

			$('.bookingdata[data-id="' + _this.data('id') + '"]').css({
				'background-color': '#62676c'
			})

		})

     	$(document).on('mouseleave', '.bookingdata', function(e) {
			var _this = $(this);

			$('.bookingdata[data-id="' + _this.data('id') + '"]').css({
				'background-color': ''
			})
		})


	$(document).on('submit', '#FormEditBooking', function(e) {
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
					url: '/EditBookingData',
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
		});
	})
})
</script>

@endpush
