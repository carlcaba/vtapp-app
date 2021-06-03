<?
	require_once("core/classes/logs.php");
	$log = new logs();

	$parArr = ["ALO","ALU","ALA"];
	$cliArr = ["VIS","CLI","CLU","CLA"];
	$empArr = ["ALE"];
?>
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
											<!--
											<div id="world-map-markers" style="height: 325px; overflow: hidden"></div>
											-->
											<div class="container" id="mapVisitors">
												<div class="map">Map couldn't be loaded!</div>
											</div>											
										</div>
										<div class="card-pane-right bg-success pt-2 pb-2 pl-4 pr-4">
											<div class="description-block mb-4">
												<div class="sparkbar pad" data-color="#fff"><?= implode(",",$parArr) ?></div>
												<h5 class="description-header"><?= $log->LoginStats($parArr) ?></h5>
												<span class="description-text"><?= $_SESSION["PARTNERS"] ?></span>
											</div>
											<!-- /.description-block -->
											<div class="description-block mb-4">
												<div class="sparkbar pad" data-color="#fff"><?= implode(",",$cliArr) ?></div>
												<h5 class="description-header"><?= $log->LoginStats($cliArr) ?></h5>
												<span class="description-text"><?= $_SESSION["CLIENTS"] ?></span>
											</div>
											<!-- /.description-block -->
											<div class="description-block">
												<div class="sparkbar pad" data-color="#fff"><?= implode(",",$empArr) ?></div>
												<h5 class="description-header"><?= $log->LoginStats($empArr) ?></h5>
												<span class="description-text"><?= $_SESSION["MESSENGER"] ?></span>
											</div>
											<!-- /.description-block -->
										</div>
										<!-- /.card-pane-right -->
									</div>
									<!-- /.d-md-flex -->
								</div>
								<!-- /.card-body -->
							</div>
							<script>
								$("#mapVisitors").mapael({
									map: {
										name : "bogota",
										defaultArea: {
											attrsHover: {
												fill: "#343434",
												stroke: "#5d5d5d",
												"stroke-width": 1,
												"stroke-linejoin": "round"
											}
										}										
									},
									plots: {
										"my-place": {
											value: "1",
											latitude: 4.69604261499948,
											longitude: -74.1213146119605,
											href: "#",
											tooltip: {
												content: "<span style=\"font-weight:bold;\">Bogotá (111311)</span><br />Population : Here I am"
											}
										},
										"place2": {
											value: "2",
											latitude: 4.654971826685302, 
											longitude: -74.07282324539098,
											href: "#",
											tooltip: {
												content: "<span style=\"font-weight:bold;\">Bogotá (111311)</span><br />Population : Another point"
											}
										}
									}
								});							
							</script>