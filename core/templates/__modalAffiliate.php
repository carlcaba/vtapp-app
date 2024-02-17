<!-- Modal Reset Password / Activate user -->
<div class="modal fade bd-example-modal-lg" id="divActivateModalAffiliateUsers" tabindex="-1" role="dialog" aria-labelledby="h5Modal2Label" aria-hidden="true" style="z-index: 99998 !important;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="h5Modal2Label"><i class="fa fa-exclamation-triangle"></i> <span id="spanTitle"></span> <span id="spanTitleName"></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalBody">
				<div id="stepper1" class="bs-stepper">
					<div class="bs-stepper-header">
						<div class="step" data-target="#test-l-1">
							<button type="button" class="btn step-trigger">
								<span class="bs-stepper-circle">1</span>
								<span class="bs-stepper-label">First step</span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-2">
							<button type="button" class="btn step-trigger">
								<span class="bs-stepper-circle">2</span>
								<span class="bs-stepper-label">Second step</span>
							</button>
						</div>
						<div class="line"></div>
						<div class="step" data-target="#test-l-3">
							<button type="button" class="btn step-trigger">
								<span class="bs-stepper-circle">3</span>
								<span class="bs-stepper-label">Third step</span>
							</button>
						</div>
					</div>
					<div class="bs-stepper-content">
						<div id="test-l-1" class="content">
							<p class="text-center">test 1</p>
							<button class="btn btn-primary" onclick="stepper1.next()">Next</button>
						</div>
						<div id="test-l-2" class="content">
							<p class="text-center">test 2</p>
							<button class="btn btn-primary" onclick="stepper1.next()">Next</button>
						</div>
						<div id="test-l-3" class="content">
							<p class="text-center">test 3</p>
							<button class="btn btn-primary" onclick="stepper1.next()">Next</button>
							<button class="btn btn-primary" onclick="stepper1.previous()">Previous</button>
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