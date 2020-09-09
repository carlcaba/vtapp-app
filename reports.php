<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	$rep = "report1";
	$link = "reports.php";
	//Captura las variables
    if(empty($_POST['rep'])) {
        //Verifica el GET
        if(!empty($_GET['rep'])) {
            $rep = $_GET['rep'];
			$link .= "?rep=$rep"; 
        }
    }
    else {
        $rep = $_POST['rep'];
		$link .= "?rep=$rep"; 
    }

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($link);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($link,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	$menuSel = $inter->getMenuInformation($_SESSION["menu_id"]);
	$icon = $menuSel["icon"];
	
	require_once("core/classes/$rep.php");
	require_once("core/classes/configuration.php");
	
	$report = new $rep();
	$move = $rep->movement;
	
	$conf = new configuration("ORDER_PREFIX");
	$prefix =  $conf->verifyValue();	

	$conf = new configuration("COMPANY_NAME");
	$company =  $conf->verifyValue();	

	$conf = new configuration("CURRENCY");
	$currency =  $conf->verifyValue();	

	$leng = "en";
	if($_SESSION["LANGUAGE"] != 1)
		$leng = $_SESSION["LANGUAGE"] == 2 ? "es-es" : "de-de";

?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- DataTables -->
	<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">
	<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap4.min.css">
	<!-- daterange picker -->
	<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="plugins/select2/select2.css">
	  <!-- iCheck for checkboxes and radio inputs -->
	  <link rel="stylesheet" href="plugins/iCheck/all.css">
</head>
<body class="hold-transition sidebar-mini <?= $skin[2] ?>">
	<div class="wrapper">
<?
	include("core/templates/__toparea.tpl");
?>
		<!-- Main Sidebar Container -->
		<aside class="main-sidebar  elevation-4 <?= $skin[1] ?>">
<?
	include("core/templates/__appname.tpl");
?>
			<!-- Sidebar -->
			<div class="sidebar">
<?
	include("core/templates/__userinfo.tpl");
	include("core/templates/__menu.tpl");
?>
			<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><i class="<?= $icon ?>"></i> <?= $_SESSION["REPORT"] . " " . $menuSel["title"] ?></h1>
						</div>
						<!-- /.col -->
<?
	include("core/templates/__breadcum.tpl");
?>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="callout callout-warning">
								<h5><i class="fa fa-warning"></i> <?= $_SESSION["NOTE"] ?>:</h5>
								<?= $_SESSION["NOTE_FILTER"] ?>
							</div>
							<!-- Main content -->
							<div class="card card-info collapsed-card card-outline">
								<div class="card-header">
									<h5><i class="fa fa-filter"></i> <?= $_SESSION["FILTER"] ?></h5>
<?= $report->showFilterForm() ?>
									<div class="card-tools">
										<div class="btn-group float-right">
											<button type="button" class="btn btn-success" title="<?= $_SESSION["APPPLY_FILTER"] ?>" id="btnApply" name="btnApply" data-widget="collapse">
												<i class="fa fa-exclamation"></i>
												<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["APPPLY_FILTER"] ?></div>
											</button>
											<button id="btnRefresh" name="btnRefresh" class="btn btn-warning">
												<i class="fa fa-refresh"></i> 
												<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["CLEAR_FILTER"] ?></div>
											</button>							
										</div>
									</div>
								</div>
								<div class="card-body">
									<h5><i class="fa fa-list-ol"></i> <?= $_SESSION["RESULTS"] ?>:</h5>
									<table id="tableReport" class="table table-bordered table-striped dt-responsive nowrap">
										<thead>
											<tr>
<?= $report->showHeaders() ?>												
											</tr>
										</thead>
										<tbody id="tableBodyReport">
										</tbody>
									</table>
								</div>
								<div class="card-footer">
									<div class="btn-group float-right">
										<button id="btnPrint" name="btnPrint" class="btn btn-default disabled">
											<i class="fa fa-print"></i> 
											<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["_PRINT"] ?></div>
										</button>							
										<button class="btn btn-success disabled" id="btnExport" name="btnExport">
											<i class="fa fa-file-excel-o"></i> 
											<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["GENERATE_EXCEL"] ?></div>
										</button>
									</div>
								</div>
							</div>
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["REPORT"];
	$icon = "<i class=\"fa fa-print\"></i>";
	$noEdit = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
?>

    <!-- Data Table JS
		============================================ -->
	<!-- DataTables -->
	<script src="plugins/datatables/jquery.dataTables.js"></script>
	<script src="plugins/datatables/dataTables.bootstrap4.js"></script>
    <script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- date-range-picker -->
	<script src="plugins/moment/moment.min.js"></script>
	<script src="plugins/daterangepicker/daterangepicker.js"></script>
	<!-- iCheck 1.0.1 -->
	<script src="plugins/iCheck/icheck.min.js"></script>
	<!-- Select2 -->
	<script src="plugins/select2/select2.full.js"></script>
	
<?
	if($_SESSION["LANGUAGE"] != 1) {
?>
    <script src="plugins/select2/i18n/<?= $_SESSION["LANGUAGE"] ?>.js"></script>
<?
	}
?>		
	
	<script>
		$(function () {
			$('.select2-id').select2();
			$('.select2-id').on('select2:select', function (e) {
				console.log("select");
				$("#btnApply").trigger("click");
			});
			$('.chkICheck').iCheck({
				checkboxClass: 'icheckbox_flat',
				radioClass: 'iradio_flat'
			});			
<?
	if($_SESSION["LANGUAGE"] != "1") {
?>
			$.getJSON("plugins/daterangepicker/lang/<?= $_SESSION["LANGUAGE"] ?>.json", function(json) { 
				$('#MOVE_DATE').daterangepicker({"locale": json});
				$('#daterange-btn').daterangepicker({
					ranges   : {
						'<?= $_SESSION["TODAY_FILTER"] ?>'       : [moment(), moment()],
						'<?= $_SESSION["YESTERDAY_FILTER"] ?>'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'<?= $_SESSION["LAST_7_FILTER"] ?>' : [moment().subtract(6, 'days'), moment()],
						'<?= $_SESSION["LAST_30_FILTER"] ?>': [moment().subtract(29, 'days'), moment()],
						'<?= $_SESSION["THIS_MONTH_FILTER"] ?>'  : [moment().startOf('month'), moment().endOf('month')],
						'<?= $_SESSION["LAST_MONTH_FILTER"] ?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
					startDate: moment().subtract(29, 'days'),
					endDate  : moment(),
					locale: json
				},
				function (start, end) {
					$('#MOVE_DATE').data('daterangepicker').setStartDate(start.format('YYYY-MM-DD'));
					$('#MOVE_DATE').data('daterangepicker').setEndDate(end.format('YYYY-MM-DD'));
				});
			});
<?
	}
	else {
?>		
			$('#MOVE_DATE').daterangepicker();
			$('#daterange-btn').daterangepicker({
				ranges   : {
					'<?= $_SESSION["TODAY_FILTER"] ?>'       : [moment(), moment()],
					'<?= $_SESSION["YESTERDAY_FILTER"] ?>'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'<?= $_SESSION["LAST_7_FILTER"] ?>' : [moment().subtract(6, 'days'), moment()],
					'<?= $_SESSION["LAST_30_FILTER"] ?>': [moment().subtract(29, 'days'), moment()],
					'<?= $_SESSION["THIS_MONTH_FILTER"] ?>'  : [moment().startOf('month'), moment().endOf('month')],
					'<?= $_SESSION["LAST_MONTH_FILTER"] ?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				},
				startDate: moment().subtract(29, 'days'),
				endDate  : moment()
			},
			function (start, end) {
				$('#MOVE_DATE').data('daterangepicker').setStartDate(start.format('YYYY-MM-DD'));
				$('#MOVE_DATE').data('daterangepicker').setEndDate(end.format('YYYY-MM-DD'));
			});
			
<?
	}
?>
			$("#btnRefresh").on("click", function(e) {
				$('.select2-id').val("*").trigger('change');
				$('#MOVE_DATE').val('');
				$("#btnPrint").addClass("disabled");
				$("#btnExport").addClass("disabled");
				var colCount = 0;
				$('#tableReport tr:nth-child(1) td').each(function () {
					if ($(this).attr('colspan')) {
						colCount += +$(this).attr('colspan');
					} 
					else {
						colCount++;
					}
				});				
				var newTR = "<tr><td colspan=\"" + colCount + "\" align=\"center\"><strong><?= $_SESSION["NO_DATA"] ?></strong></td></tr>";
				$("#tableBodyReport").empty().append(newTR);
				$('.select2-id').focus();
			});
			$("#btnPrint").on("click", function(e) {
				var $frm = $("#frmFilter");
				var datas = JSON.stringify($frm.serializeObject());
				window.open("report-print.php?strModel=" + datas + "&rep=<?= $rep ?>");
			});
			$("#btnExport").on("click", function(e) {
				var $frm = $("#frmFilter");
				var datas = JSON.stringify($frm.serializeObject());
				window.open("core/actions/_save/__exportReport.php?strModel=" + datas + "&rep=<?= $rep ?>");
			});
			$("#btnApply").on("click", function(e) {
				var form = document.getElementById('frmFilter');
				var noty;
				if (form.checkValidity() === false) {
					window.event.preventDefault();
					window.event.stopPropagation();
					notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
					return false;
				}
				var $frm = $("#frmFilter");
				var datas = JSON.stringify($frm.serializeObject());
				$.ajax({
					url: "core/actions/_load/__loadReport.php",
					data: { 
						strModel: datas,
						txtClass: "<?= $rep ?>"
					},
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						if(data.success) {
							$("#btnPrint").removeClass("disabled");
							$("#btnExport").removeClass("disabled");
							$("#tableBodyReport").empty();
							$.each(data.datas, function(key,value) {
								var newTR = "<tr>";
								$.each(value, function(innerKey,innerValue) {
									newTR += "<td>" + innerValue + "</td>";
								});
								newTR += "</tr>";
								$("#tableBodyReport").append(newTR);
							});
						}
						else {
							$("#btnPrint").addClass("disabled");
							$("#btnExport").addClass("disabled");
							var newTR = "<tr><td colspan=\"" + data.columns + "\" align=\"center\"><strong>" + data.message + "</strong></td></tr>";
							$("#tableBodyReport").empty().append(newTR);
						}
					}
				});
			});
		});
	</script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>