<!-- Modal Reset Password / Activate user -->
<script>
	$(document).ready(function() {
		//TODO Nativapps
		var subscribed_customer = "<?= $_GET["subscribed_customer"] ?>";
		if (subscribed_customer) {
			$("#divActivateModalAffiliateUsersStep4").modal("toggle");
			$('#divActivateModalAffiliateUsersStep4').on('hidden.bs.modal', function(event) {
				console.log("hidden.bs.modal");
				// Obtener la URL actual
				var url = window.location.href;

				// Crear un objeto URLSearchParams con los parámetros de la URL
				var searchParams = new URLSearchParams(new URL(url).search);

				// Eliminar el parámetro "subscribed_customer"
				searchParams.delete('subscribed_customer');

				// Reconstruir la URL con los parámetros actualizados
				var newUrl = window.location.origin + window.location.pathname + (searchParams.toString() ? "?" + searchParams.toString() : "");

				// Reemplazar la URL sin recargar la página
				window.history.replaceState({
					path: newUrl
				}, '', newUrl);
			})
		}
		console.log(subscribed_customer);
		//////////////////////////////
	});
</script>

<div class="modal fade bd-example-modal-lg" id="divActivateModalAffiliateUsersStep4" tabindex="-1" role="dialog" aria-labelledby="h5Modal2Label" aria-hidden="true"  style="z-index: 99998 !important;">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="h5Modal2Label"><i class="fa fa-check"></i> <span id="spanTitle"></span> <span id="spanTitleName"></span><?= $_SESSION["AFFILIATION_RATE_STEP4_WELCOME_TO_UBIO"] ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalBody">
				<h1 class="my-3"><?= $_SESSION["AFFILIATION_RATE_STEP4_WELCOME_TO_UBIO"] ?></h1>
				<!-- <h5>¡Tu suscripción ha sido exitosa!</h5>
				<p>Ahora podrás configurar tus usuarios, empresa aliada y comenzar a gestionar tus envíos de mensajería de manera inteligente.</p>
				<p>Comienza por registrar tu empresa aliada y tus usuarios.
					Recuerda que también podrás realizar estos pasos desde el menú lateral en la opción GESTIÓN XXX</p> -->

				<div>
					<?= $_SESSION["AFFILIATION_RATE_STEP4_DESCRIPTION"] ?>
				</div>

				<div class="mt-2">
					<h4><?= $_SESSION["AFFILIATION_RATE_STEP4_WHAT_TO_DO_FIRST"] ?></h4>
					<a href="/partner-management.php?action=new" class="btn btn-outline-success"><i class="fa fa-building" aria-hidden="true"></i> <?= $_SESSION["AFFILIATION_RATE_STEP4_REGISTER_ALLIED_COMPANY"] ?></a>
					<a href="/user-management.php?action=new&src=cli" class="btn btn-outline-primary"><i class="fa fa-user-plus" aria-hidden="true"></i> <?= $_SESSION["AFFILIATION_RATE_STEP4_ADD_MY_USERS"] ?></a>
				</div>

				<div class="mt-3">
					<h4><?= $_SESSION["AFFILIATION_RATE_STEP4_OTHER_OPTIONS"] ?></h4>
					<a href="/client-management.php?action=new" class="btn btn-outline-dark"><i class="fa fa-cart-plus" aria-hidden="true"></i> <?= $_SESSION["AFFILIATION_RATE_STEP4_ACQUIRE_MEMBERSHIPS"] ?></a>
					<a href="#" class="btn btn-outline-info"><i class="fa fa-eye" aria-hidden="true"></i> <?= $_SESSION["AFFILIATION_RATE_STEP4_VIEW_MY_SUBSCRIPTION"] ?></a>
					<a href="#" class="btn btn-outline-danger"><i class="fa fa-ban" aria-hidden="true"></i> <?= $_SESSION["AFFILIATION_RATE_STEP4_CANCEL_MY_SUBSCRIPTION"] ?></a>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelActivate" name="btnCancelActivate"><?= $_SESSION["CLOSE"] ?></button>

			</div>
		</div>
	</div>
</div>