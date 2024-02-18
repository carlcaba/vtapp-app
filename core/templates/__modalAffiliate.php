<!-- Modal Reset Password / Activate user -->
<div class="modal fade bd-example-modal-lg" id="divActivateModalAffiliateUsers" tabindex="-1" role="dialog" aria-labelledby="h5Modal2Label" aria-hidden="true" data-backdrop="static" style="z-index: 99998 !important;">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="h5Modal2Label"><i class="fa fa-exclamation-triangle"></i> <span id="spanTitle"></span> <span id="spanTitleName"></span><?= $_SESSION["AFFILIATION_RATE_TITLE_MODAL"] ?></h5>
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
								<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_1"] ?></span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-2">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">2</span>
								<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_2"] ?></span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-3">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">3</span>
								<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_3"] ?></span>
							</button>
						</div>
					</div>
					<div class="bs-stepper-content">
						<div id="test-l-1" class="content">
							<h2><?= $_SESSION["AFFILIATION_RATE_STEP1_H2"] ?></h2>
							<p><?= $_SESSION["AFFILIATION_RATE_STEP1_P"] ?></p>

							<div class="form-check">
								<input class="form-check-input form-control" id="acceptTermsConditionsId" name="acceptTermsConditions" type="checkbox" data-toggle="toggle" data-on="Si" data-off="No" data-onstyle="success">
								<label class="form-check-label" for="acceptTermsConditions">
									<?= $_SESSION["AFFILIATION_RATE_ACCEPT_TERMS_CONDITIONS"] ?>
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
								<button class="btn btn-secondary" id="previousBtn"><?= $_SESSION["AFFILIATION_RATE_PREVIOUS_BUTTON"] ?></button>
							</div>
							<div class="col-auto">
								<button class="btn btn-primary" id="nextBtn"><?= $_SESSION["AFFILIATION_RATE_NEXT_BUTTON"] ?></button>
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