							<!-- MAP & BOX PANE -->
							<div class="card">
								<div class="card-header">
									<h3 class="card-title"><?= $_SESSION["VISITORS_REPORT"] ?></h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-widget="collapse">
											<i class="fa fa-minus"></i>
										</button>
										<button type="button" class="btn btn-tool" data-widget="remove">
											<i class="fa fa-times"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body p-0">
									<div class="d-md-flex">
										<div class="p-1 flex-1" style="overflow: hidden">
											<!-- Map will be created here -->
											<div id="world-map-markers" style="height: 325px; overflow: hidden"></div>
										</div>
										<div class="card-pane-right bg-success pt-2 pb-2 pl-4 pr-4">
											<div class="description-block mb-4">
												<div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
												<h5 class="description-header">8390</h5>
												<span class="description-text"><?= $_SESSION["VISITORS"] ?></span>
											</div>
											<!-- /.description-block -->
											<div class="description-block mb-4">
												<div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
												<h5 class="description-header">30%</h5>
												<span class="description-text"><?= $_SESSION["REFERRALS"] ?></span>
											</div>
											<!-- /.description-block -->
											<div class="description-block">
												<div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
												<h5 class="description-header">70%</h5>
												<span class="description-text"><?= $_SESSION["ORGANIC"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
										<!-- /.card-pane-right -->
									</div>
									<!-- /.d-md-flex -->
								</div>
								<!-- /.card-body -->
							</div>