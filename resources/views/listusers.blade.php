@extends('layouts.app')

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">List Users</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<div class="filter">
			<p class="font-weight-bold">Search</p>
			<div class="d-inline-block">
				<label for="searchusername">User ID (MSVG)</label>
				<input type="text" id="searchusername" name="searchusername" class="form-control ">
			</div>
			<div class="d-inline-block">
				<label for="searchfullname">Full Name</label>
				<input type="text" class="form-control" id="searchfullname" name="searchfullname">
			</div>
			<div class="d-inline-block">
				<label for="searchphonenumber">Phone Number</label>
				<input type="text" class="form-control" id="searchphonenumber" name="searchphonenumber">
			</div>
			<div class="d-inline-block">
				<label for="searchstatus">Status</label>
				<select id="searchstatus" name="searchstatus" class="form-control" data-style="btn-outline-primary" required></select>
			</div>
			<div class="d-inline-block form-group">
				<label for="transactiondatefrom">Date Start From</label>
				<div class="input-group custom ">
					<input id="transactiondatefrom" name="transactiondatefrom" class="form-control datetimepicker-input date-only" data-toggle="datetimepicker" data-target="#transactiondatefrom" type="text" readonly>
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
			<div class="d-inline-block form-group">
				<label for="transactiontimefrom">Time Start From</label>
				<div class="input-group custom ">
					<input id="transactiontimefrom" name="transactiontimefrom" class="form-control datetimepicker-input time-only" data-toggle="datetimepicker" data-target="#transactiontimefrom" type="text" readonly>
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="fa fa-clock" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
			<div class="d-inline-block form-group">
				<label for="transactiondateto">Date Start To</label>
				<div class="input-group custom ">
					<input id="transactiondateto" name="transactiondateto" class="form-control datetimepicker-input date-only" data-toggle="datetimepicker" data-target="#transactiondateto" type="text" readonly>
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
			<div class="d-inline-block form-group">
				<label for="transactiontimeto">Time Start To</label>
				<div class="input-group custom ">
					<input id="transactiontimeto" name="transactiontimeto" class="form-control datetimepicker-input time-only" data-toggle="datetimepicker" data-target="#transactiontimeto" type="text" readonly>
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="fa fa-clock" aria-hidden="true"></i></span>
					</div>
				</div>
			</div>
			<div class="d-inline-block">
				<button class="btn btn-success" type="button" id="btnSearchUser">Search</button>
				<button class="btn btn-link text-secondary" type="button" id="btnClearSearch">Clear</button>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-sm table-bordered table-striped table-hover text-nowrap" id="TableUsers" style="width:100%;" cellspacing="0">
				<thead>
					<tr>
						<th>ID</th>
						<th>User ID (MSVG)</th>
						<th>Full Name</th>
						<th>Phone Number</th>
						<th>Password</th>
						<th>Status</th>
						<th>Date Join</th>
						<th class="notexport">Actions</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalDeleteUser" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteUserTitle" aria-hidden="true" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="overflow: visible;">
				<form id="FormDeleteUser">
					<p>Confirm delete user?</p>
					<input autocomplete="off" spellcheck="false" autocorrect="off" id="user_id" name="user_id" class="form-control d-none" type="text" readonly required>
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<button class="btn btn-danger" type="submit">Delete</button>
								<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function InitDataTable() {
		$('#TableUsers').closest('.dataTables_wrapper').removeClass('d-none');

		if (!$.fn.DataTable.isDataTable('#TableUsers')) {
			console.log('Is not datatable, initializing now...');

			TableUsers = $('#TableUsers').DataTable({
				processing: true,
				serverSide: true,
				// responsive: true,
				searching: false,
				columnDefs: [{
					orderable: false,
					targets: [-1]
				}],
				"order": [
					[0, 'desc']
				],
				cache: false,
				"dom": "<'row'<'col-sm-12 col-md-6'Bi><'col-sm-12 col-md-6 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				lengthMenu: [
					[10, 25, 50, 100, -1],
					['10 rows', '25 rows', '50 rows', '100 rows', 'All']
				],
				buttons: [{
					extend: 'excel',
					text: 'Excel',
					filename: 'List Users',
					title: 'List Users' + moment(new Date()).format("YYYY-MM-DD hh:mm:ss A"),
					exportOptions: {
						columns: ':not(.notexport)'
					}
				}],
				ajax: {
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					"url": "/GetUsers",
					"type": "POST",
					"data": function(d) {
						var datestartfrom = $('#transactiondatefrom').val();
						var datestartto = $('#transactiondateto').val();
						var timestartfrom = $('#transactiontimefrom').val();
						var timestartto = $('#transactiontimeto').val();

						if (datestartfrom == '') {
							datestartfrom = '2020-01-01';
						}
						if (datestartto == '') {
							datestartto = '2099-12-31';
						}
						if (timestartfrom == '') {
							timestartfrom = '12:00:00 AM';
						}
						if (timestartto == '') {
							timestartto = '11:59:59 PM';
						}

						d.searchusername = $("#searchusername").val();
						d.searchfullname = $("#searchfullname").val();
						d.searchphonenumber = $("#searchphonenumber").val();
						d.searchstatus = $("#searchstatus option:selected").text();
						d.transactiondatefrom = datestartfrom + ' ' + timestartfrom;
						d.transactiondateto = datestartto + ' ' + timestartto;
					},
					complete: function(c) {
						// console.log('completed', c);
					}
				},
				columns: [{
					"className": 'details-control',
					"data": 'id',
					render: function(data, type, row) {
						return row.id;
					}
				}, {
					"className": '',
					data: 'username',
					render: function(data, type, row) {
						return row.username;
					}
				}, {
					"className": '',
					data: 'fullname',
					render: function(data, type, row) {
						return row.fullname;
					}
				}, {
					"className": '',
					data: 'phonenumber',
					render: function(data, type, row) {
						return row.phonenumber;
					}
				}, {
					"className": '',
					data: 'password',
					render: function(data, type, row) {
						return row.password;
					}
				}, {
					"className": '',
					data: 'status',
					render: function(data, type, row) {
						return row.status;
					}
				}, {
					"className": '',
					data: 'created_date',
					render: function(data, type, row) {
						return row.createddate;
					}
				}, {
					"className": '',
					data: null,
					render: function(data, type, row) {
						return '<a href="/user/edit/' + row.id + '" class="btn badge badge-primary">Edit</a> <button type="button" class="btnDeleteUser btn badge badge-danger" data-id="' + row.id + '">Delete</button>';
					}
				}],
				"drawCallback": function(data) {
					console.log('drawCallback', data);

					$('#searchfullname, #searchusername, #searchphonenumber').val('');
					$('#searchstatus').html('<option></option>');

					var filters = data.json;

					if (filters.searchfullname) {
						$('#searchfullname').val(filters.searchfullname);
					}

					if (filters.searchusername) {
						$('#searchusername').val(filters.searchusername);
					}

					if (filters.searchphonenumber) {
						$('#searchphonenumber').val(filters.searchphonenumber);
					}

					if (filters.statuses) {
						if (filters.statuses.length > 0) {
							$.each(filters.statuses, function(k, v) {
								var selected = '';
								if (filters.searchstatus == v.name) {
									selected = 'selected';
								}
								$("#searchstatus").append('<option value="' + v.value + '" ' + selected + '>' + v.name + '</option>');
							});
						}
					}

					$(".pre-loader").fadeOut();
				}
			});
		} else {
			TableUsers.ajax.reload(function(json) {
				console.log('json', json);
			}, false);
		}
	}

	$(document).ready(function() {
		$('.datetimepicker-input.date-only').datetimepicker({
			format: "YYYY-MM-DD",
			ignoreReadonly: true
		})

		$('.datetimepicker-input.time-only').datetimepicker({
			format: "hh:mm:ss A",
			ampm: true,
			ignoreReadonly: true
		})

		InitDataTable();

		$(document).on('click', '#btnSearchUser', function(e) {
			InitDataTable();
		});

		$(document).on('click', '#btnClearSearch', function(e) {
			$('.filter input').val('');
			$('.invalid-feedback').remove();
		});

		$(document).on('click', '.btnExportAllUsers', function(e) {
			e.preventDefault();

			$('#form-message').text('').removeClass('text-danger, text-success');

			$('.pre-loader').show();

			var _SubmitButton = $(this);

			_SubmitButton.attr('disabled', true);

			setTimeout(function() {
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/ExportAllUsersToExcel',
					type: "POST",
					success: function(data) {
						console.log(data);

						Swal.fire({
							icon: data.status,
							title: data.message
						});

						if (data.status == 'success') {
							downloadURI(data.downloadpath, data.filename);
						}

						$('.pre-loader').hide();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						$('.pre-loader').hide();
						console.log('Error', xhr);
					},
					complete: function(c) {
						setTimeout(function() {
							_SubmitButton.attr('disabled', false);
						}, 800);
					}
				});
			}, 800);
		});

		$(document).on('click', '.btnDeleteUser', function(e) {
			e.preventDefault();

			var _id = $(this).data('id');

			$('#FormDeleteUser #user_id').val(_id);

			$('#ModalDeleteUser').modal('show');
		});

		$(document).on('click', '#btnUpdateUserRanks', function(e) {
			e.preventDefault();
			$('#ModalUpdateUserRanks').modal('show');
		});

		$(document).on('submit', '#FormDeleteUser', function(e) {
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
					url: '/DeleteUser',
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
							$('#ModalDeleteUser').modal('hide');
							InitDataTable();
						} else {
							if (data.error) {
								form.find('.invalid-feedback').remove();
								if (data.error.message) {
									_SubmitButton.before('<p class="invalid-feedback d-block">' + data.error.message + '</p>');
								}
								$.each(data.error, function(k, v) {
									form.find('#' + k).closest('.form-group').append('<div class="invalid-feedback d-block">' + v + '</div>');
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

	});
</script>
@endpush