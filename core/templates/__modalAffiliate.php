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
						<div class="line"></div>
						<div class="step" data-target="#test-l-4">
							<button type="button" class="step-trigger">
								<span class="bs-stepper-circle">4</span>
								<span class="bs-stepper-label">Fin</span>
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
							<h2><?= $_SESSION["AFFILIATION_RATE_STEP2_H4"] ?></h2>
							<div class="container my-4 clearfix">
								<!-- Shopping cart table -->
								<div class="card">
									<div class="card-body">
										<div class="table-responsive">
											<form id="frmAffiliateRates">
												<table class="table table-bordered m-0">
													<thead>
														<tr>
															<!-- Set columns width -->
															<th class="text-center py-3 px-4" style="min-width: 200px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL1"] ?></th>
															<th class="text-right py-3 px-4" style="width: 100px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL2"] ?></th>
															<th class="text-center py-3 px-4" style="width: 120px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL3"] ?></th>
															<th class="text-right py-3 px-4" style="width: 150px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL4"] ?></th>

														</tr>
													</thead>
													<tbody>

														<tr>
															<td class="font-weight-semibold align-middle p-3">
																<div class="rate-name-basic"><?= $_SESSION["AFFILIATION_RATE_NAME_BASIC"] ?></div>
															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_basic_rate ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_basic" data-resource-name="AFFILIATION_RATE_NAME_BASIC" data-rate-value="<?= $user_affiliate_basic_rate ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_basic_rate ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-basic">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_1"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_allied_company ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_1" data-resource-name="AFFILIATION_RATE_NAME_1" data-rate-value="<?= $user_affiliate_allied_company ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_allied_company ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-1">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_2"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_company_users ?></td>
															<td class="align-middle p-3">
																<input type="number" name="number_users_rate_2" data-resource-name="AFFILIATION_RATE_NAME_2" data-rate-value="<?= $user_affiliate_company_users ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_company ?>">
															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-2">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_3"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_delivery_allied ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_3" data-resource-name="AFFILIATION_RATE_NAME_3" data-rate-value="<?= $user_affiliate_delivery_allied ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_delivery_allied ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-3">0</span></td>

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
							<div class="card">
								<h5 class="card-header bg-info"><?= $_SESSION["AFFILIATION_RATE_STEP3_TITLE_BILLING_DATA"] ?></h5>
								<div class="card-body">
									<form id="frmBillingData">
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="business_name"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME"] ?></label>
												<input type="text" class="form-control" id="business_name" name="business_name" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME"] ?>" disabled>
											</div>
											<input type="hidden" name="client_id" id="client_id" />
										</div>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="nit"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_NIT"] ?></label>
												<input type="text" class="form-control" id="nit" name="nit" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_NIT"] ?>" disabled>
											</div>
											<div class="form-group col-md-6">
												<label for="main_phone"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE"] ?></label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-phone"></i></div>
													</div>
													<input type="text" class="form-control" id="main_phone" name="main_phone" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE"] ?>" disabled>
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="main_address"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS"] ?></label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-map"></i></div>
													</div>
													<input type="email" class="form-control" id="main_address" name="main_address" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS"] ?>" disabled>
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="legal_representative"><?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[1] ?> *</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-user"></i></div>
													</div>
													<input type="<?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[0] ?>" class="form-control" id="legal_representative" name="legal_representative" placeholder="<?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[2] ?>" required>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="card">
								<h5 class="card-header bg-info"><?= $_SESSION["AFFILIATION_RATE_STEP3_TITLE_CARD_DETAILS"] ?></h5>
								<div class="card-body">
									<form id="frmCardDetails">
										<div class="form-row">
											<div class="form-group col-md-12">
												<?= $affiliate_subscription->showField("CREDIT_CARD_NUMBER", $as_dataForm["tabs"], "fa fa-credit-card-alt", "", false, "", false, "9,9,12", '') ?>
											</div>
											<input type="hidden" name="hfValidCard" id="hfValidCard" value="false" />
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<?= $affiliate_subscription->showField("CREDIT_CARD_NAME", $as_dataForm["tabs"], "fa fa-user", "", false, "", false, "9,9,12", '') ?>
											</div>
										</div>
										<div class="form-row">

											<div class="form-group col-md-6">
												<?= $affiliate_subscription->showField("DATE_EXPIRATION", $as_dataForm["tabs"], "fa fa-calendar-times-o", "", false, "", false, "9,9,12", '') ?>
											</div>

											<div class="form-group col-md-6">
												<?= $affiliate_subscription->showField("VERIFICATION_CODE", $as_dataForm["tabs"], "fa fa-cc", "", false, "", false, "9,9,12", '') ?>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div id="test-l-4" class="content">
							

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