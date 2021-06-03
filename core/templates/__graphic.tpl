<?
	require_once("core/classes/service.php");
	$serv = new service();
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
												<strong><?= $_SESSION["GRAPH_1_SUBTITLE"] ?></strong>
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
												<strong><?= $_SESSION["GRAPH_1_SUBTITLE_2"] ?></strong>
											</p>
<?= $serv->DashboardSummaryGraph() ?>
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
	<script src="plugins/chart.js/Chart.js"></script>
	<script>
		$(document).ready(function() {
			var beer;
			// Get context with jQuery - using jQuery's .get() method.
			var salesChartCanvas = document.getElementById('salesChart').getContext('2d')
			var salesChartOptions = {
				maintainAspectRatio : false,
				responsive : true,
				legend: {
					display: false
				},
				scales: {
					xAxes: [
						{
							gridLines : {
							display : false,
						}
					}],
					yAxes: [
						{
							gridLines : {
								display : false,
							}
					}]
				}
			}
			$.getJSON("core/actions/_load/__loadGraphicData.php",function(json){
				var salesChartData = json.data;
				/*
				{
					labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
					datasets: [
						{
							label               : 'Digital Goods',
							backgroundColor     : 'rgba(60,141,188,0.9)',
							borderColor         : 'rgba(60,141,188,0.8)',
							pointRadius          : false,
							pointColor          : '#3b8bba',
							pointStrokeColor    : 'rgba(60,141,188,1)',
							pointHighlightFill  : '#fff',
							pointHighlightStroke: 'rgba(60,141,188,1)',
							data                : [28, 48, 40, 19, 86, 27, 90]
						},
						{
							label               : 'Electronics',
							backgroundColor     : 'rgba(210, 214, 222, 1)',
							borderColor         : 'rgba(210, 214, 222, 1)',
							pointRadius         : false,
							pointColor          : 'rgba(210, 214, 222, 1)',
							pointStrokeColor    : '#c1c7d1',
							pointHighlightFill  : '#fff',
							pointHighlightStroke: 'rgba(220,220,220,1)',
							data                : [65, 59, 80, 81, 56, 55, 40]
						},
						{
							label               : 'Electronics',
							backgroundColor     : 'rgba(234, 213, 20, 1)',
							borderColor         : 'rgba(234, 213, 20, 1)',
							pointRadius         : false,
							pointColor          : 'rgba(234, 213, 20, 1)',
							pointStrokeColor    : '##959AA1',
							pointHighlightFill  : '#fff',
							pointHighlightStroke: 'rgba(220,220,220,1)',
							data                : [65, 59, 80, 81, 56, 55, 40]
						}
					]
				}
				*/
				var salesChart = new Chart(salesChartCanvas, { 
					type: 'line', 
					data: salesChartData, 
					options: salesChartOptions
				});
			});         
			
		});
	</script>