<!-- Modal Reset Password / Activate user -->
<div class="modal fade bd-example-modal-lg" id="divActivateModalAffiliateUsers" tabindex="-1" role="dialog" aria-labelledby="h5Modal2Label" aria-hidden="true" style="z-index: 99998 !important;">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="h5Modal2Label"><i class="fa fa-exclamation-triangle"></i> <span id="spanTitle"></span> <span id="spanTitleName"></span>Pasos para la afiliación</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalBody">
				<div id="stepper1" class="bs-stepper">
					<div class="bs-stepper-header">
						<div class="step" data-target="#test-l-1">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">1</span>
								<span class="bs-stepper-label">Bienvenido</span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-2">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">2</span>
								<span class="bs-stepper-label">Rellena tu plan</span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-3">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">3</span>
								<span class="bs-stepper-label">Confirmar compra</span>
							</button>
						</div>
					</div>
					<div class="bs-stepper-content">
						<div id="test-l-1" class="content">
							<h2>Desde aquí podrás gestionar tu afiliación</h2>
							<p>Recuerda que deberás adquirir un servicio de afiliación para tu empresa, cada una de las empresas aliadas que trabajen contigo y adquirir membresías para usuarios con una base mensual.</p>

							<div class="form-check">
								<input class="form-check-input" id="exampleRadios1" name="exampleRadios1" type="checkbox" data-toggle="toggle" data-on="Si" data-off="No" data-onstyle="success">
								<label class="form-check-label" for="exampleRadios1">
									* Acepta los términos y condiciones de tu plan Vincula tu Aliado
								</label>
							</div>

						</div>
						<div id="test-l-2" class="content">
							<h2>Step 2 Content</h2>
							<!-- Aquí va el contenido del paso 2 -->
						</div>
						<div id="test-l-3" class="content">
							<h2>Step 3 Content</h2>
							<!-- Aquí va el contenido del paso 3 -->
						</div>
						<div class="row justify-content-between mt-3">
							<div class="col-auto">
								<button class="btn btn-secondary" id="previousBtn">previous</button>
							</div>
							<div class="col-auto">
								<button class="btn btn-primary" id="nextBtn">Next</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelActivate" name="btnCancelActivate"><?= $_SESSION["CLOSE"] ?></button>
				<!-- <button type="button" class="btn btn-primary" id="btnActivate" name="btnActivate" data-dismiss="modal"><?= $btnText ?></button>
				<input type="hidden" name="hfDefaultTextButton" id="hfDefaultTextButton" value="<?= $btnText ?>" />
				<input type="hidden" name="hfTextButton" id="hfTextButton" value="" /> -->
			</div>
		</div>
	</div>
</div>