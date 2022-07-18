@extends('layouts.app')

@push('styles')
<style>

</style>
@endpush

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">Product Discount Percentage</li>
</ol>

<div class="row">
	<div class="col-12 col-md-6 col-xl-4">
		<div class="card mb-4">
			<div class="card-body">
				<form id="FormProductDiscountRate">
					<div class="mb-3">
						<h5>Update Percentage</h5>
					</div>
					<div class="form-group">
						<label for="paymentmethod">Payment Method</label>
						<select id="paymentmethod" name="paymentmethod" class="form-control"></select>
					</div>
					<div class="form-group">
						<label for="percentage">Percentage</label>
						<div class="input-group custom ">
							<div class="input-group-prepend custom">
								<select id="percentageoperator" name="percentageoperator" class="form-control input-group-text bg-white" required>
									<option>+</option>
									<option>-</option>
								</select>
							</div>
							<input autocomplete="off" spellcheck="false" autocorrect="off" id="percentage" name="percentage" step="any" placeholder="0.0000" class="form-control" type="number">
							<div class="input-group-append custom">
								<span class="input-group-text">%</span>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<button class="btn btn-primary" type="button" id="btnEditProductDiscountRate">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-body">
		<div class="filter">
			<p class="font-weight-bold">Search</p>
			<div class="d-inline-block">
				<label for="searchpaymentmethod">Payment Method</label>
				<input type="text" class="form-control" id="searchpaymentmethod" name="searchpaymentmethod">
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
						<th>Payment Method</th>
						<th>Percentage</th>
						<th>Full Name</th>
						<th>Status</th>
						<th>Created Date</th>
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
		$('#FormProductDiscountRate')[0].reset();
		$('#paymentmethod').html('<option selected></option>');
		$('.invalid-feedback').remove();

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
					filename: 'List Rates'; ?>',
					title: 'List Rates'; ?>' + moment(new Date()).format("YYYY-MM-DD hh:mm:ss A"),
					exportOptions: {
						columns: ':not(.notexport)'
					}
				}],
				ajax: {
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					"url": "/GetProductDiscountRate",
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

						d.searchpaymentmethod = $("#searchpaymentmethod").val();
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
					"className": 'details-control',
					"data": 'paymentmethod',
					render: function(data, type, row) {
						return row.paymentmethod;
					}
				}, {
					"className": 'details-control',
					"data": 'rate',
					render: function(data, type, row) {
						return row.rate;
					}
				}, {
					"className": '',
					data: 'fullname',
					render: function(data, type, row) {
						return row.fullname;
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
						return '';
					}
				}],
				"drawCallback": function(data) {
					console.log('drawCallback', data);

					$('#searchpaymentmethod').val('');

					var filters = data.json;

					if (filters.searchpaymentmethod) {
						$('#searchpaymentmethod').val(filters.searchpaymentmethod);
					}

					if (filters.paymentmethods) {
						if (filters.paymentmethods.length > 0) {
							$.each(filters.paymentmethods, function(k, v) {
								$('#paymentmethod').append('<option>' + v.name + '</option>');
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

		$(document).on('click', '#btnEditProductDiscountRate', function(e) {
			console.log('btnEditProductDiscountRate');

			$('.invalid-feedback').remove();

			var percentagebox = $('#percentage');

			if (percentagebox.val() != '') {
				Swal.fire({
					title: 'Update Percentage'; ?>',
					text: "Confirm update percentage?'; ?>",
					icon: 'info',
					showCancelButton: true,
					confirmButtonText: 'Submit'; ?>'
				}).then((result) => {
					if (result.isConfirmed) {
						console.log('proceed');

						$(".pre-loader").fadeIn();

						var form = $('#FormProductDiscountRate');
						var formData = new FormData(form[0]);

						$.ajax({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							url: '/EditProductPricePercentage',
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
								}

								$('.pre-loader').hide();
							},
							error: function(xhr, ajaxOptions, thrownError) {
								$('.pre-loader').hide();
								console.log('Error', xhr);
							},
							complete: function(c) {

							}
						});
					}
				})
			} else {
				percentagebox.closest('.form-group').append('<div class="invalid-feedback d-block">Percentage is required</div>');
			}

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

		$(document).on('submit', '#FormProductDiscountRate', function(e) {
			e.preventDefault();

			$('.invalid-feedback').remove();

			var percentagebox = $('#percentage');

			if (percentagebox.val() != '') {
				Swal.fire({
					title: 'Update Percentage'; ?>',
					text: "Confirm update percentage?'; ?>",
					icon: 'info',
					showCancelButton: true,
					confirmButtonText: 'Submit'; ?>'
				}).then((result) => {
					if (result.isConfirmed) {
						console.log('proceed');

						$(".pre-loader").fadeIn();

						// $('#FormProductDiscountRate').trigger('submit');
						var form = $('#FormProductDiscountRate');
						var formData = new FormData(form[0]);

						$.ajax({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							url: '/EditProductPricePercentage',
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
									InitDataTable();
								}

								$('.pre-loader').hide();
							},
							error: function(xhr, ajaxOptions, thrownError) {
								$('.pre-loader').hide();
								console.log('Error', xhr);
							},
							complete: function(c) {

							}
						});
					}
				})
			} else {
				percentagebox.closest('.form-group').append('<div class="invalid-feedback d-block">Percentage is required</div>');
			}
		});

	});
</script>
@endpush