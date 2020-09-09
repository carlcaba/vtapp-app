<?
/*
	require_once("core/classes/movement_detail.php");
	$move = new movement_detail();
	*/
?>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title"><?= $_SESSION["GRAPH_1_TITLE"] ?></h5>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-widget="collapse">
											<i class="fa fa-minus"></i>
										</button>
										<!--
										<div class="btn-group">
											<button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
												<i class="fa fa-wrench"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" role="menu">
												<a href="#" class="dropdown-item">Action</a>
												<a href="#" class="dropdown-item">Another action</a>
												<a href="#" class="dropdown-item">Something else here</a>
												<a class="dropdown-divider"></a>
												<a href="#" class="dropdown-item">Separated link</a>
											</div>
										</div>
										-->
										<button type="button" class="btn btn-tool" data-widget="remove">
											<i class="fa fa-times"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<div class="row">
										<div class="col-md-8">
											<p class="text-center">
												<strong><? //$move->getChartTitle() ?></strong>
											</p>
											<div class="chart">
												<!-- Sales Chart Canvas -->
												<canvas id="salesChart" height="180" style="height: 180px;"></canvas>
											</div>
											<!-- /.chart-responsive -->
										</div>
										<!-- /.col -->
										<div class="col-md-4">
											<p class="text-center">
												<strong><?= $_SESSION["WAREHOUSE_TITLE_PROGRESS"] ?></strong>
											</p>
<? //$move->showWarehouseProgress() ?>
										</div>
										<!-- /.col -->
									</div>
									<!-- /.row -->
								</div>
								<!-- ./card-body -->
								<div class="card-footer">
									<div class="row">
										<div class="col-sm-3 col-6">
											<div class="description-block border-right">
												<span class="description-percentage text-success"><i class="fa fa-caret-up"></i> 0%</span>
												<h5 class="description-header">$0.00</h5>
												<span class="description-text"><?= $_SESSION["TOTAL_REVENUE"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
										<!-- /.col -->
										<div class="col-sm-3 col-6">
											<div class="description-block border-right">
												<span class="description-percentage text-warning"><i class="fa fa-caret-left"></i> 0%</span>
												<h5 class="description-header">$0.00</h5>
												<span class="description-text"><?= $_SESSION["TOTAL_COST"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
										<!-- /.col -->
										<div class="col-sm-3 col-6">
											<div class="description-block border-right">
												<span class="description-percentage text-success"><i class="fa fa-caret-up"></i> 0%</span>
												<h5 class="description-header">$0.00</h5>
												<span class="description-text"><?= $_SESSION["TOTAL_PROFIT"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
										<!-- /.col -->
										<div class="col-sm-3 col-6">
											<div class="description-block">
												<span class="description-percentage text-danger"><i class="fa fa-caret-down"></i> 0%</span>
												<h5 class="description-header">0</h5>
												<span class="description-text"><?= $_SESSION["GOAL_COMPLETIONS"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
									</div>
									<!-- /.row -->
								</div>
								<!-- /.card-footer -->
							</div>
							<!-- /.card -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->

	<!-- ChartJS 1.0.2 -->
	<script src="plugins/chartjs-old/Chart.js"></script>
