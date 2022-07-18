@extends('layouts.app')

@push('styles')
<style>
	#TableOrderDetail tr th:nth-child(3),
	#TableOrderDetail tr td:nth-child(3) {
		text-align: right;
	}

	#TableOrderDetail tr th:nth-child(1),
	#TableOrderDetail tr td:nth-child(1) {
		padding: 10px 0 0 0;
	}

	/* .paymentstatus.success {
		color: #28a745;
		border: 2px solid #28a745 !important;
	}

	.paymentstatus.danger {
		color: #dc3545;
		border: 2px solid #dc3545 !important;
	} */

	.cartitemimage {
		height: 100px;
		background-position: 50% center;
		background-size: cover;
		width: 90px;
		float: left;
		margin: 5px 8px 0 0;
	}

	.listwithstyle ul {
		padding-left: 20px;
		list-style: disc;
		text-align: left !important;
	}
</style>
@endpush

@section('content')
<ol class="breadcrumb bg-white">
	<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
	<li class="breadcrumb-item"><a href="/orders/list">List Orders</a></li>
	<li class="breadcrumb-item active">Edit Order</li>
</ol>

<div class="bg-white p-3 shadow my-3">
	<h4>Order Detail</h4>
	<h4 class="my-3 font-weight-bold">Invoice</h4>
	<p class="mt-3 mb-0">Status'; ?>:</p>
	<p class="paymentstatus font-weight-bolder">-</p>
	<p class="mt-3 mb-0">Order Number'; ?>:</p>
	<span class="orderno font-weight-bolder">-</span>

	<p class="mt-3 mb-1">Buyer'; ?>:</p>
	<div class="card">
		<div class="card-body">
			<span class="orderfor" style="white-space: pre-wrap;">-</span>
		</div>
	</div>

	<div class="table-responsive mt-4">
		<table class="table table-borderless" id="TableOrderDetail">
			<thead>
				<tr>
					<th><span>Product</span></th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr class="border-top border-bottom">
					<th class="pb-2" style="font-weight: 500;"><span>Total</span><span class="float-right order-total">-</span></th>
				</tr>
				<tr>
					<th class="" style="font-weight: 500;">
						<p class="mb-0">Paid Amount</span><span class="float-right paid-total">-</p>
						<p class="mb-0 text-info">', '']); ?></span><span class="float-right convert-total">-</p>
					</th>
				</tr>
				<tr class="">
					<th class="pb-2" style="font-weight: 500;"><span>Balance</span>
						<div class="float-right text-right">
							<span class="balance-total">-</span>
						</div>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	function Pageload() {
		$('#TableOrderDetail tbody').html('');
		$('#btnPayment').addClass('d-none');
		$('.balance-total, .paymentstatus').removeClass('text-danger text-success text-warning');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/PrepareEditOrder',
			data: {
				'orderno': '{{$orderno}}'
			},
			type: "POST",
			dataType: 'json',
			success: function(data) {
				console.log(data);

				if (data.status == 'success') {
					$('.orderno').text(data.orderno);
					$('#btnInquiry').attr('data-orderno', data.orderno);
					$('.paymentstatus').text(data.paymentstatus).addClass(data.paymentstatusclass);
					$('.orderfor').html(data.orderfor);
					$('.shippingdetails').text(data.shippingdetails);
					$('.billingdetails').text(data.billingdetails);
					if (data.products) {
						if (data.products.length > 0) {
							$.each(data.products, function(k, v) {
								$('#TableOrderDetail tbody').append('<tr>\
									<td class="border-top mt-2">\
										<h5 class="mb-2 font-weight-bold">' + v.name + '</h5>\
										<h5 class="mb-0 font-weight-bold">' + v.shortdescription + '</h5>\
										<div class="mb-0 listwithstyle">' + v.description + '</div>\
										<div class="text-right float-right">\
											<p class="mt-3 mb-0">' + v.pricetext + '</p>\
											<p class="mb-0">Quantity'; ?>: ' + v.quantity + '</p>\
											<p class="itemtotalpricetext">' + v.itemtotalprice + '</p>\
										</div>\
									</td>\
								</tr>');
							});
							$('.order-subtotal').text(data.ordersubtotal);
							$('.shipping-total').text(data.shippingtotal);
							$('.order-total').text(data.ordertotal);
							$('.paid-total').text(data.paidtotal);
							$('.convert-total').text(data.converttotal);
							$('.balance-total').text(data.balancetotaltext);

							if (data.balancetotal > 0) {
								$('.balance-total').addClass('text-danger');
								$('#btnPayment').removeClass('d-none');
								$('#btnPayment').attr('data-orderno', data.orderno);
							} else {
								$('.paid-total').addClass(data.paymentstatusclass);
							}
						}
					}
				} else {
					var modals = [];

					modals.push({
						icon: data.status,
						title: data.message
					});

					Swal.queue(modals);
				}

				$(".pre-loader").fadeOut();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log('Error', xhr);
				$(".pre-loader").fadeOut();
			},
			complete: function(c) {

			}
		});
	}

	$(document).ready(function() {
		Pageload();

	})
</script>
@endpush