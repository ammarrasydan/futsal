@extends('layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/cropper.css') }}">
<style>
	img {
		max-width: 100%;
	}

	.img-container {
		margin: 0 auto;
		width: 400px;
		height: 300px;
		padding: 1px 2px;
		text-align: center;
	}

	@media (max-width: 500px) {
		.img-container {
			width: 350px;
			height: 262.5px;
		}
	}

	@media (max-width: 400px) {
		.img-container {
			width: 320px;
			height: 240px;
		}
	}

	@media (max-width: 375px) {
		.img-container {
			width: 300px;
			height: 225px;
		}
	}

	@media (max-width: 360px) {
		.img-container {
			width: 300px;
			height: 225px;
		}
	}

	@media (max-width: 350px) {
		.img-container {
			width: 240px;
			height: 180px;
		}
	}
</style>
@endpush

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item"><a href="/users/list">List Users</a></li>
	<li class="breadcrumb-item active">Edit User</li>
</ol>
<div class="login-wrap my-3">
	<div class="bg-white box-shadow p-3 rounded mx-auto">
		<form id="FormEditUser">
			<div class="form-group">
				<label for="user_id">ID</label>
				<input autocomplete="off" spellcheck="false" autocorrect="off" id="user_id" name="user_id" value="{{$id}}" class="form-control" type="text" required readonly>
			</div>
			<div class="form-group">
				<label for="username">User ID (MSVG)</label>
				<input type="text" id="username" name="username" required="" class="form-control ">
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
			<div class="form-group">
				<label for="email">Email</label>
				<input autocomplete="off" spellcheck="false" autocorrect="off" id="email" name="email" class="form-control" type="email" required>
			</div>
			<div class="form-group ">
				<label for="user_status">Status</label>
				<select id="user_status" name="user_status" class="form-control" required>
					<option selected disabled>Select an option</option>
					<option value="Pending">Pending</option>
					<option value="Active">Active</option>
				</select>
			</div>
			<hr />
			<div class="form-group">
				<label for="password">New Password</label>
				<div class="input-group">
					<input autocapitalize="none" autocomplete="off" spellcheck="false" autocorrect="off" type="password" id="password" name="password" class="form-control">
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
		<hr />
		<div class="form-group">
			<label class="fw-bold">ID Front Page</label>
			<div id="hasimgbox1" class="img-container border border-2 rounded shadow d-none" style="">
				<div class="float-right" style="margin:-30px 0 0 0;">
					<button type="button" id="btnDeleteImage1" class="btn btn-link text-danger p-0 m-0">Delete</button>
				</div>
				<img src="" id="hasimage1" alt="ID Front Page Image">
			</div>
			<div id="noimgbox1" class="d-none mt-2">
				<label id="btnSelectImage1" class="w-100 text-center p-5 border border-2 rounded shadow" for="inputimage1" title="Upload image file" style="cursor:pointer;">
					<input type="file" class="sr-only" id="inputimage1" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
					<span class="docs-tooltip" data-toggle="tooltip" title="Import image with Blob URLs">
						<span class="fa fa-camera"></span>
					</span>
					<span class="text-muted">Upload ID Front Page</span>
				</label>
				<div class="img-container d-none mb-3" id="imagepreview1" style="">
					<img src="" id="image1" alt="ID Front Page Preview">
				</div>
				<button type="button" id="btnUploadImage1" class="btn btn-primary">Submit</button>
				<div class="float-right">
					<button type="button" id="btnCancelImage1" class="btn btn-link text-danger p-0 m-0">Cancel</button>
				</div>
			</div>
		</div>
		<hr />
		<div class="form-group">
			<label class="fw-bold">Bank Card Front</label>
			<div id="hasimgbox2" class="img-container border border-2 rounded shadow d-none" style="">
				<div class="float-right" style="margin:-30px 0 0 0;">
					<button type="button" id="btnDeleteImage2" class="btn btn-link text-danger p-0 m-0">Delete</button>
				</div>
				<img src="" id="hasimage2" alt="Bank Card Front Image">
			</div>
			<div id="noimgbox2" class="d-none mt-2">
				<label id="btnSelectImage2" class="w-100 text-center p-5 border border-2 rounded shadow" for="inputimage2" title="Upload image file" style="cursor:pointer;">
					<input type="file" class="sr-only" id="inputimage2" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
					<span class="docs-tooltip" data-toggle="tooltip" title="Import image with Blob URLs">
						<span class="fa fa-camera"></span>
					</span>
					<span class="text-muted">Upload Bank Card Front</span>
				</label>
				<div class="img-container d-none mb-3" id="imagepreview2" style="">
					<img src="" id="image2" alt="Bank Card Front Preview">
				</div>
				<button type="button" id="btnUploadImage2" class="btn btn-primary">Submit</button>
				<div class="float-right">
					<button type="button" id="btnCancelImage2" class="btn btn-link text-danger p-0 m-0">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cropper.min.js') }}"></script>
<script type="text/javascript">
	function Pageload() {
	
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

		$('#imagepreview1, #btnCancelImage1, #hasimgbox1, #noimgbox1').addClass('d-none');
		$('#imagepreview2, #btnCancelImage2, #hasimgbox2, #noimgbox2').addClass('d-none');

		$('#btnSelectImage1').removeClass('d-none');
		$('#btnSelectImage2').removeClass('d-none');

		if (cropper1) {
			cropper1.destroy();
		}
		$('#image1')[0].src = "";
		$('#inputimage1').val('');

		if (cropper2) {
			cropper2.destroy();
		}
		$('#image2')[0].src = "";
		$('#inputimage2').val('');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareEditUser',
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
						$('#username').val(data.user.username);
						$('#fullname').val(data.user.fullname);
						$('#email').val(data.user.email);
						$('#referralcode').val(data.user.referralcode);
						$('#referralby').val(data.user.referralby);
						$("#user_status").val(data.user.status);

						// $("#user_status option").filter(function() {
						// 	return $(this).text() == data.user.status;
						// }).prop('selected', true);

						if (data.user.image1 == '') {
							$('#noimgbox1').removeClass('d-none');
						} else {
							$('#noimgbox1').addClass('d-none');
							$('#hasimgbox1').removeClass('d-none');
							$('#hasimgbox1 > img').attr('src', data.user.image1);
						}

						if (data.user.image2 == '') {
							$('#noimgbox2').removeClass('d-none');
						} else {
							$('#noimgbox2').addClass('d-none');
							$('#hasimgbox2').removeClass('d-none');
							$('#hasimgbox2 > img').attr('src', data.user.image2);
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

	var URL = window.URL || window.webkitURL;

	var cropper1;
	var cropper2;
	var image1;
	var image2;

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
					url: '/EditUser',
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

		$(document).on('change', '#inputimage1', function(e) {
			console.log('change');
			image1 = $('#image1')[0];

			$('#btnSelectImage1').removeClass('d-none');
			$('#imagepreview1, #btnCancelImage1').addClass('d-none');

			if (URL) {
				var files = this.files;
				var file;

				if (files && files.length) {
					$('#btnSelectImage1').addClass('d-none');
					$('#imagepreview1, #btnCancelImage1').removeClass('d-none');
					file = files[0];

					if (/^image\/\w+/.test(file.type)) {
						uploadedImageType = file.type;
						uploadedImageName = file.name;

						$(image1)[0].src = URL.createObjectURL(file);

						cropper1 = new Cropper(image1, options);
						$(this).value = null;
					} else {
						window.alert('Please choose an image file.');
					}
				}
			}
		});

		$(document).on('change', '#inputimage2', function(e) {
			console.log('change');
			image2 = $('#image2')[0];

			$('#btnSelectImage2').removeClass('d-none');
			$('#imagepreview2, #btnCancelImage2').addClass('d-none');

			if (URL) {
				var files = this.files;
				var file;

				if (files && files.length) {
					$('#btnSelectImage2').addClass('d-none');
					$('#imagepreview2, #btnCancelImage2').removeClass('d-none');
					file = files[0];

					if (/^image\/\w+/.test(file.type)) {
						uploadedImageType = file.type;
						uploadedImageName = file.name;

						$(image2)[0].src = URL.createObjectURL(file);

						cropper2 = new Cropper(image2, options);
						$(this).value = null;
					} else {
						window.alert('Please choose an image file.');
					}
				}
			}
		});

		$(document).on('click', '#btnCancelImage1', function(e) {
			if (cropper1) {
				cropper1.destroy();
			}
			$('#image1')[0].src = "";
			$('#inputimage1').val('');
			$('#btnSelectImage1').removeClass('d-none');
			$('#imagepreview1, #btnCancelImage1').addClass('d-none');
		});

		$(document).on('click', '#btnCancelImage2', function(e) {
			if (cropper2) {
				cropper2.destroy();
			}
			$('#image2')[0].src = "";
			$('#inputimage2').val('');
			$('#btnSelectImage2').removeClass('d-none');
			$('#imagepreview2, #btnCancelImage2').addClass('d-none');
		});

		$(document).on('click', '#btnUploadImage1', function(e) {
			$("#preloader").show();

			var _SubmitButton = $(this);
			var imgurl = cropper1.getCroppedCanvas().toDataURL();
			var img = document.createElement("img");
			img.src = imgurl;

			console.log('imgurl', imgurl);
			console.log('img', img);
			cropper1.getCroppedCanvas().toBlob(function(blob) {
				var formData = new FormData();
				formData.append('croppedImage', blob);
				formData.append('user_id', '{{$id}}');

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/UploadImage1',
					type: "POST",
					dataType: 'json',
					data: formData,
					processData: false,
					contentType: false,
					success: function(data) {
						console.log(data);

						var modals = [];

						modals.push({
							icon: data.status,
							title: data.message
						});

						Swal.queue(modals);

						if (data.status == 'success') {
							if (cropper1) {
								cropper1.destroy();
							}
							$('#image1')[0].src = "";
							$('#inputimage1').val('');
							$('#btnSelectImage1').removeClass('d-none');
							$('#imagepreview1, #btnCancelImage1').addClass('d-none');
							Pageload();
						} else {
							$("#preloader").fadeOut();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log('Error', xhr);
						$("#preloader").fadeOut();
					},
					complete: function(c) {
						setTimeout(function() {
							swal.close();
							_SubmitButton.attr('disabled', false);
						}, 800);
					}
				});
			});
		});

		$(document).on('click', '#btnUploadImage2', function(e) {
			$("#preloader").show();
			var _SubmitButton = $(this);
			var imgurl = cropper2.getCroppedCanvas().toDataURL();
			var img = document.createElement("img");
			img.src = imgurl;

			console.log('imgurl', imgurl);
			console.log('img', img);
			cropper2.getCroppedCanvas().toBlob(function(blob) {
				var formData = new FormData();
				formData.append('croppedImage', blob);
				formData.append('user_id', '{{$id}}');

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '/UploadImage2',
					type: "POST",
					dataType: 'json',
					data: formData,
					processData: false,
					contentType: false,
					success: function(data) {
						console.log(data);

						var modals = [];

						modals.push({
							icon: data.status,
							title: data.message
						});

						Swal.queue(modals);

						if (data.status == 'success') {
							if (cropper2) {
								cropper2.destroy();
							}
							$('#image2')[0].src = "";
							$('#inputimage2').val('');
							$('#btnSelectImage2').removeClass('d-none');
							$('#imagepreview2, #btnCancelImage2').addClass('d-none');
							Pageload();
						} else {
							$("#preloader").fadeOut();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log('Error', xhr);
						$("#preloader").fadeOut();
					},
					complete: function(c) {
						setTimeout(function() {
							swal.close();
							_SubmitButton.attr('disabled', false);
						}, 800);
					}
				});
			});
		});

		$(document).on('click', '#btnDeleteImage1', function(e) {
			$("#preloader").show();

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/DeleteImage1',
				type: "POST",
				data: {
					'user_id': '{{$id}}'
				},
				dataType: 'json',
				success: function(data) {
					console.log(data);

					var modals = [];

					modals.push({
						icon: data.status,
						title: data.message
					});

					Swal.queue(modals);

					if (data.status == 'success') {
						if (cropper1) {
							cropper1.destroy();
						}
						$('#image2')[0].src = "";
						$('#inputimage2').val('');
						$('#btnSelectImage2').removeClass('d-none');
						$('#imagepreview2, #btnCancelImage2').addClass('d-none');
						Pageload();
					} else {
						$("#preloader").fadeOut();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					console.log('Error', xhr);
					$("#preloader").fadeOut();
				},
				complete: function(c) {}
			});
		});

		$(document).on('click', '#btnDeleteImage2', function(e) {
			$("#preloader").show();

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/DeleteImage2',
				type: "POST",
				data: {
					'user_id': '{{$id}}'
				},
				dataType: 'json',
				success: function(data) {
					console.log(data);

					var modals = [];

					modals.push({
						icon: data.status,
						title: data.message
					});

					Swal.queue(modals);

					if (data.status == 'success') {
						if (cropper2) {
							cropper2.destroy();
						}
						$('#image2')[0].src = "";
						$('#inputimage2').val('');
						$('#btnSelectImage2').removeClass('d-none');
						$('#imagepreview2, #btnCancelImage2').addClass('d-none');
						Pageload();
					} else {
						$("#preloader").fadeOut();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					console.log('Error', xhr);
					$("#preloader").fadeOut();
				},
				complete: function(c) {}
			});
		});

	})
</script>
@endpush