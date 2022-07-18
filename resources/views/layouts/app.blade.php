<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>CRM</title>

	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
	<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
	<link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

	<!-- CSS -->
	<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/responsive.bootstrap4.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('fonts/font-awesome/css/all.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css?ver=1.0') }}" />
	<link rel="stylesheet" href="{{ asset('css/jquery.orgchart.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/ekko-lightbox.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css?ver=1.0') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css?ver=1.0') }}">
	@stack('styles')
</head>

<body class="sb-nav-fixed">
	<div class="pre-loader"></div>
	@guest
	@yield('content')
	@else
	<div class="header clearfix">
		<div class="header-right">
			<div class="brand-logo">
				<a href="/">Admin Panel</a>
			</div>
			<!-- <div class="d-inline-block m-3">
				<select name="ddlChangeLanguage" id="ddlChangeLanguage">
					<option value="en" selected>English</option>
					<option value="cn">中文</option>
				</select>
			</div> -->
			<div class="menu-icon">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<div class="left-side-bar">
		<div class="brand-logo">
			<a href="/">Admin Panel</a>
		</div>
		<div class="menu-block customscroll">
			<div class="sidebar-menu">
				<ul id="accordion-menu">
					<li>
						<a href="/dashboard" class="dropdown-toggle no-arrow">
							<span class="fa fa-home icon"></span><span class="mtext">Home</span>
						</a>
					</li>
					<li>
						<a href="/profile/edit" class="dropdown-toggle no-arrow">
							<span class="fa fa-user-cog icon"></span><span class="mtext">Edit Profile</span>
						</a>
					</li>
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="fas fa-users-cog icon"></span><span class="mtext">Manage Users</span>
						</a>
						<ul class="submenu">
							<li><a href="/users/list">List Users</a></li>
							<li><a href="/user/new">New User</a></li>
						</ul>
					</li>
						<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="fas fa-users-cog icon"></span><span class="mtext">Manage Booking</span>
						</a>
						<ul class="submenu">
							<li><a href="/booking/new">New Booking</a></li>
							<li><a href="/booking/list">List Booking</a></li>
							<li><a href="/booking/table/{id}">Table Booking</a></li>

                    </i>
                </a>
            </li>
						</ul>
					</li>
					<!-- <li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="fas fa-clipboard-list icon"></span><span class="mtext">Manage Orders</span>
						</a>
						<ul class="submenu">
							<li><a href="/orders/list">List Orders</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle">
							<span class="fas fa-cogs icon"></span><span class="mtext">Settings</span>
						</a>
						<ul class="submenu">
							<li><a href="/settings/productdiscountrate">Product Price Percentage</a></li>
						</ul>
					</li> 
					<li>
						<a href="/activity/logs" class="dropdown-toggle no-arrow">
							<span class="fas fa-clipboard-list icon"></span><span class="mtext">Activity Logs</span>
						</a>
					</li> -->
					<li class="divider"></li>
					<li>
						<a href="/logout" class="dropdown-toggle no-arrow">
							<span class="fa fa-sign-out-alt icon"></span><span class="mtext">Log Out</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="main-container">
		<div class="container-fluid py-3">
			@yield('content')
		</div>
	</div>
	@endguest
	<script src="{{ asset('js/jquery-3.5.1.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/popper.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js?ver=1.0') }}"></script>

	<!-- <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
	<script src="{{ asset('js/responsive.bootstrap4.min.js') }}"></script> -->
	<script src="{{ asset('DataTables/datatables.min.js?ver=1.0') }}"></script>

	<script src="{{ asset('js/bootstrap-select.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/sweetalert2@10.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/moment.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/tempusdominus-bootstrap-4.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.ui.widget.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/load-image.all.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/canvas-to-blob.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.iframe-transport.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.fileupload.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.fileupload-process.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.fileupload-image.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery.orgchart.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/ekko-lightbox.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/jquery-ui.min.js?ver=1.0') }}"></script>
	<script src="{{ asset('js/custom.js?ver=1.0') }}"></script>
	<script>
		function downloadURI(uri, name) {
			console.log('uri', uri);
			console.log('name', name);
			var link = document.createElement("a");
			link.download = name;
			link.href = uri;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
			delete link;
		}

		$(document).ready(function() {
			$(document).on('click', '.btnShowHidePassword', function(e) {
				e.preventDefault();
				var parent = $(this).closest('.input-group');
				var input = parent.find('input');
				var icon = $(this).find('i');

				if (input.attr("type") == "text") {
					input.attr('type', 'password');
					icon.addClass("fa-eye-slash");
					icon.removeClass("fa-eye");
				} else if (input.attr("type") == "password") {
					input.attr('type', 'text');
					icon.removeClass("fa-eye-slash");
					icon.addClass("fa-eye");
				}
			});

			$(document).on('click', '[data-toggle="lightbox"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});
		});
	</script>
	@stack('scripts')
</body>

</html>