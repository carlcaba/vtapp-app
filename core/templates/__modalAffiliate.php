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
				<div id="stepperCompanyUserAffiliation" class="bs-stepper">
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
							<h3><?= $_SESSION["AFFILIATION_RATE_STEP2_H2"] ?> </h3>
							<div class="container px-3 my-5 clearfix">
								<!-- Shopping cart table -->
								<div class="card">
									<div class="card-header">
										<h4><?= $_SESSION["AFFILIATION_RATE_STEP2_H4"] ?></h4>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<form id="frmAffiliateRates">
												<table class="table table-bordered m-0">
													<thead>
														<tr>
															<!-- Set columns width -->
															<th class="text-center py-3 px-4" style="min-width: 300px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL1"] ?></th>
															<th class="text-right py-3 px-4" style="width: 100px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL2"] ?></th>
															<th class="text-center py-3 px-4" style="width: 120px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL3"] ?></th>
															<th class="text-right py-3 px-4" style="width: 150px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL4"] ?></th>

														</tr>
													</thead>
													<tbody>

														<tr>
															<td class="p-2">
																<div class="media align-items-center">

																	<div class="media-body">
																		<p class="d-block text-dark"><?= $_SESSION["AFFILIATION_RATE_NAME_1"] ?></p>
																	</div>
																</div>
															</td>
															<td class="text-right font-weight-semibold align-middle p-4">$<?= $user_affiliate_rate_value ?></td>
															<td class="align-middle p-4">

																<input type="number" name="number_users_rate_1" class="form-control text-center number-users-affiliation " min="0" value="0" max="<?= $max_users_affiliation_rate_1 ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-4">$<span class="number-users-total-rate-1">0</span></td>

														</tr>

														<tr>
															<td class="p-2">
																<div class="media align-items-center">

																	<div class="media-body">
																		<p class="d-block text-dark"><?= $_SESSION["AFFILIATION_RATE_NAME_2"] ?></p>
																	</div>
																</div>
															</td>
															<td class="text-right font-weight-semibold align-middle p-4">$<?= $user_affiliate_rate_value ?></td>
															<td class="align-middle p-4"><input type="number" name="number_users_rate_2" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_rate_2 ?>"></td>
															<td class="text-right font-weight-semibold align-middle p-4">$<span class="number-users-total-rate-2">0</span< /td>

														</tr>

														<tr>
															<td class="p-2">
																<div class="media align-items-center">

																	<div class="media-body">
																		<p class="d-block text-dark"><?= $_SESSION["AFFILIATION_RATE_NAME_3"] ?></p>
																	</div>
																</div>
															</td>
															<td class="text-right font-weight-semibold align-middle p-4">$<?= $user_affiliate_rate_value ?></td>
															<td class="align-middle p-4">

																<input type="number" name="number_users_rate_3" class="form-control text-center number-users-affiliation " min="0" value="0" max="<?= $max_users_affiliation_rate_3 ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-4">$<span class="number-users-total-rate-3">0</span< /td>

														</tr>

													</tbody>
												</table>
											</form>
										</div>
										<!-- / Shopping cart table -->

										<div class="d-flex flex-wrap justify-content-between align-items-center pb-4">
											<div class="mt-4">

											</div>
											<div class="d-flex">
												<div class="text-right mt-4 mr-5">

												</div>
												<div class="text-right mt-4">
													<label class="text-muted font-weight-normal m-0"><?= $_SESSION["AFFILIATION_RATE_STEP2_LB_TOTAL_VALUE"] ?></label>
													<div class="text-large"><strong>$</strong><strong class="total-membership-value">0</strong></div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div id="test-l-3" class="content">
							<h2>Step 3 Content</h2>
							<!-- Aquí va el contenido del paso 3 -->
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btnCancelActivate" name="btnCancelActivate"><?= $_SESSION["CLOSE"] ?></button>
				<button class="btn btn-secondary" id="previousBtn"><?= $_SESSION["AFFILIATION_RATE_PREVIOUS_BUTTON"] ?></button>
				<button class="btn btn-primary" id="nextBtn"><?= $_SESSION["AFFILIATION_RATE_NEXT_BUTTON"] ?></button>

			</div>
		</div>
	</div>
</div>