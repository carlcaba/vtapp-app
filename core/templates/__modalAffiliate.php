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
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_BASIC"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_basic_rate ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_basic" data-rate-value="<?= $user_affiliate_basic_rate ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_basic_rate ?>" readonly>

															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-basic">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_1"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_allied_company ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_1" data-rate-value="<?= $user_affiliate_allied_company ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_allied_company ?>">

															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-1">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_2"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_company_users ?></td>
															<td class="align-middle p-3">
																<input type="number" name="number_users_rate_2" data-rate-value="<?= $user_affiliate_company_users ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_company ?>">
															</td>
															<td class="text-right font-weight-semibold align-middle p-3">$<span class="number-users-total-rate-2">0</span></td>

														</tr>

														<tr>
															<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_3"] ?></td>
															<td class="text-right font-weight-semibold align-middle p-3">$<?= $user_affiliate_delivery_allied ?></td>
															<td class="align-middle p-3">

																<input type="number" name="number_users_rate_3" data-rate-value="<?= $user_affiliate_delivery_allied ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_delivery_allied ?>">

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
								<h5 class="card-header bg-info">Tus Datos de Facturación</h5>
								<div class="card-body">
									<form>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputNombre">RAZÓN SOCIAL</label>
												<input type="text" class="form-control" id="inputNombre" placeholder="Nombre">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="inputApellido">NIT</label>
												<input type="text" class="form-control" id="inputApellido" placeholder="Apellido">
											</div>
											<div class="form-group col-md-6">
												<label for="inputTelefono">TELÉFONO PRINICIPAL</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-phone"></i></div>
													</div>
													<input type="text" class="form-control" id="inputTelefono" placeholder="Teléfono">
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputEmail">DIRECCIÓN PRINCIPAL</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-map"></i></div>
													</div>
													<input type="email" class="form-control" id="inputEmail" placeholder="Correo Electrónico">
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputDireccion">NOMBRE REPRESENTANTE LEGAL</label>
												<input type="text" class="form-control" id="inputDireccion" placeholder="Dirección">
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="card">
								<h5 class="card-header bg-info">Detalles de la tarjeta</h5>
								<div class="card-body">
									<form>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputNombre">NÚMERO DE TARJETA</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-cc-mastercard"></i></div>
													</div>
													<input type="text" class="form-control" id="inputTelefono" placeholder="Teléfono">
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-12">
												<label for="inputApellido">NOMBRE EN TARJETA</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-user"></i></div>
													</div>
													<input type="text" class="form-control" id="inputTelefono" placeholder="Teléfono">
												</div>
											</div>
										</div>
										<div class="form-row">

											<div class="form-group col-md-6">
												<label for="inputTelefono">FECHA DE VENCIMIENTO</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-calendar-times-o"></i></div>
													</div>
													<input type="text" class="form-control" id="inputTelefono" placeholder="Teléfono">
												</div>
											</div>

											<div class="form-group col-md-6">
												<label for="inputEmail">CVC</label>
												<div class="input-group mb-2 mr-sm-2">
													<div class="input-group-prepend">
														<div class="input-group-text"><i class="fa fa-cc"></i></div>
													</div>
													<input type="email" class="form-control" id="inputEmail" placeholder="Correo Electrónico">
												</div>
											</div>
										</div>
										
										<!-- <?= $user->showField("BUSINESS_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?> -->
									</form>
								</div>
							</div>
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