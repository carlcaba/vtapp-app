<?
	require_once("core/classes/configuration.php");

	$script = $payment ? "PAYMENT_CHECKOUT" : "PAYMENT_MERCHANT_SCRIPT";

	$conf = new configuration($script);
	$script = $conf->verifyValue();
	
	$titlePayment = $payment ? $_SESSION["CHECKOUT_TITLE"] : $_SESSION["PAYMENT_TITLE"];
?>
		<!-- Modal Payment -->
		<div class="modal fade" id="divPayment" tabindex="-1" role="dialog"  aria-labelledby="myModalPaymentLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="myModalPaymentLabel"><i class="fa fa-money"></i> <?= $titlePayment ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="paymentForm">
						<p></p>
						<form id="frmPayment" name="frmPayment" method="post">
							<input type="hidden" name="cart_id" value="123">							
						</form>
					</div>
				</div>
			</div>
		</div>
	<!-- Payment Gateway Script -->
    <script src="<?= $script ?>" charset="utf-8"></script>
