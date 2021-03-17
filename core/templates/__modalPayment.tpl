<?
	$script = "";
	require_once("core/classes/configuration.php");
	$conf = new configuration();
	if(!$accTok) {
		$script = $payment ? "PAYMENT_CHECKOUT" : "PAYMENT_MERCHANT_SCRIPT";
		$script = $conf->verifyValue($script);
	}
	else {
		$script = $conf->verifyValue("PAYMENT_WOMPI_WIDGET");
	}
	$titlePayment = $payment ? $_SESSION["CHECKOUT_TITLE"] : $_SESSION["PAYMENT_TITLE"];
?>
		<script type="text/javascript" src="<?= $widget ?>"></script>
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
<? 
	if(!($accTok && !$err)) {
?>
						<form id="frmPayment" name="frmPayment" method="post">
							<input type="hidden" name="cart_id" value="123">							
						</form>
<?
	}
?>
					</div>
				</div>
			</div>
		</div>