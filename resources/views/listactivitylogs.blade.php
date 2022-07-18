@extends('layouts.app')

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item active">List Activity Logs</li>
</ol>

<div class="card mb-4">
	<div class="card-body">
		<div class="filter">
			<p class="font-weight-bold">Search</p>
			<div class="d-inline-block">
				<label for="searchfullname">Admin Full Name</label>
				<input type="text" class="form-control" id="searchfullname" name="searchfullname">
			</div>
			<div class="d-inline-block">
				<label for="searchactivity">Activity</label>
				<input type="text" class="form-control" id="searchactivity" name="searchactivity">
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
						<th>Admin Full Name</th>
						<th>Activity</th>
						<th>Created Date</th>
						<th class="notexport">Actions</th>
					</tr>
				</thead>
			</table>
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
					filename: 'List Users'; ?>',
					title: 'List Users'; ?>' + moment(new Date()).format("YYYY-MM-DD hh:mm:ss A"),
					exportOptions: {
						columns: ':not(.notexport)'
					}
				}],
				ajax: {
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					"url": "/GetActivities",
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

						d.searchfullname = $("#searchfullname").val();
						d.searchactivity = $("#searchactivity").val();
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
					data: 'user_name',
					render: function(data, type, row) {
						return row.user_name;
					}
				}, {
					"className": '',
					data: 'content',
					render: function(data, type, row) {
						return row.content;
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

					$('#searchfullname, #searchactivity').val('');
					$('#searchstatus').html('<option></option>');

					var filters = data.json;

					if (filters.searchfullname) {
						$('#searchfullname').val(filters.searchfullname);
					}

					if (filters.searchactivity) {
						$('#searchactivity').val(filters.searchactivity);
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

	});
</script>
@endpush