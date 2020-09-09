<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId("outputs.php");
	
	require_once("core/__check-session.php");
	
	$result = checkSession("outputs.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	//Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
			$inter->redirect($result["link"]);
        }
        else {
            $id = $_GET['id'];
        }
    }
    else {
        $id = $_POST['id'];
    }
	
	require_once("core/classes/movement_detail.php");
	require_once("core/classes/configuration.php");
	
	$move = new movement_detail();
	$move->setMovement($id);
	
	//Verifica si existe
	if($move->nerror > 0) {
		$_SESSION["vtappcorp_user_alert"] = $move->error;
		$inter->redirect($result["link"]);
	}

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
    <!-- datapicker CSS -->
    <link rel="stylesheet" href="plugins/datapicker/datepicker3.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="plugins/select2/select2.css">
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
							<h1 class="m-0 text-dark"><i class="fa fa-paper-plane"></i> <?= $_SESSION["VIEW"] . " " . $_SESSION["OUTPUT"] . " " . $move->movement->INTERNAL_NUMBER ?></h1>
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
							<div class="callout callout-info">
								<h5><i class="fa fa-info"></i> <?= $_SESSION["NOTE"] ?>:</h5>
								<?= $_SESSION["NOTE_PRINT"] ?>
							</div>
							<!-- Main content -->
							<div class="invoice p-3 mb-3">
								<!-- title row -->
								<div class="row">
									<div class="col-12">
										<h4>
											<i class="fa fa-globe"></i> <?= $company ?>
											<small class="float-right"><?= $_SESSION["OUTPUT_TABLE_TITLE_3"] ?>: <?= date("d/m/Y", strtotime($move->movement->MOVE_DATE)) ?></small>
										</h4>
									</div>
									<!-- /.col -->
								</div>
								<!-- info row -->
								<div class="row invoice-info">
									<div class="col-sm-4 invoice-col">
										<?= $_SESSION["FROM"] ?>
										<address>
											<strong><?= $move->movement->employee->FIRSTNAME . " " . $move->movement->employee->LASTNAME ?></strong><br>
											<?= $move->movement->employee->COSTCENTER . " - " . $move->movement->employee->CODE ?><br>
											<?= $move->movement->employee->area->COSTCENTER . " - " . $move->movement->employee->area->getResource() ?><br>
											<?= $_SESSION["PHONE"] . ": " ?><br>
											<?= $_SESSION["EMAIL"] . ": " . $move->movement->employee->EMAIL ?>
										</address>
									</div>
									<!-- /.col -->
									<div class="col-sm-4 invoice-col">
										<!--
										<?= $_SESSION["TO"] ?>
										<address>
											<strong><?= $move->movement->employee->FIRSTNAME . " " . $move->movement->employee->LASTNAME ?></strong><br>
											<?= $move->movement->employee->COSTCENTER . " - " . $move->movement->employee->CODE ?><br>
											<?= $move->movement->employee->area->COSTCENTER . " - " . $move->movement->employee->area->getResource() ?><br>
											<?= $_SESSION["PHONE"] . ": " ?><br>
											<?= $_SESSION["EMAIL"] . ": " . $move->movement->employee->EMAIL ?>
										</address>
										-->
									</div>
									<!-- /.col -->
									<div class="col-sm-4 invoice-col">
										<b><?= $_SESSION["ORDER"] . " #" . $move->movement->INTERNAL_NUMBER ?></b><br>
										<br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_5"] . ":</b> " . $_SESSION["OUTPUT"] ?><br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_6"] . ":</b> " . date("d/m/Y", strtotime($move->movement->REGISTERED_ON)) ?><br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_7"] . ":</b> " . $move->movement->REGISTERED_BY ?>
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->
								
								<!-- Table row -->
								<div class="row">
									<div class="col-12 table-responsive">
										<table class="table table-striped">
											<thead>
											<tr>
												<th width="5%"><?= $_SESSION["ORDER_TABLE_TITLE_1"] ?></th>
												<th width="5%"><?= $_SESSION["ORDER_TABLE_TITLE_2"] ?></th>
												<th width="30%"><?= $_SESSION["ORDER_TABLE_TITLE_3"] ?></th>
												<th width="5%"><?= $_SESSION["ORDER_TABLE_TITLE_4"] ?></th>
												<th width="5%"><?= $_SESSION["ORDER_TABLE_TITLE_5"] ?></th>
												<th width="15%"><?= $_SESSION["ORDER_TABLE_TITLE_6"] ?></th>
												<th width="20%"><?= $_SESSION["ORDER_TABLE_TITLE_7"] ?></th>
											</tr>
											</thead>
											<tbody>
<?= $move->listTable(0,	$currency) ?>										
											</tbody>
										</table>
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->

								<div class="row">
									<!-- accepted payments column -->
									<div class="col-6">
										<!--
										<p class="lead">Payment Methods:</p>
										<img src="img/credit/visa.png" alt="Visa">
										<img src="img/credit/mastercard.png" alt="Mastercard">
										<img src="img/credit/american-express.png" alt="American Express">
										<img src="img/credit/paypal2.png" alt="Paypal">
										<p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
											Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem
											plugg
											dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
										</p>
										-->
									</div>
									<!-- /.col -->
									<div class="col-6">
										<p class="lead"><?= $_SESSION["AMOUNT_DUE"] . " " . date("d/m/Y", strtotime($move->movement->MOVE_DATE)) ?></p>
										<div class="table-responsive">
											<table class="table">
												<tr>
													<th style="width:50%"><?= $_SESSION["SUBTOTAL"] ?>:</th>
													<td><?= $currency ?> $ <?= number_format($move->TOTAL,2,".",",") ?></td>
												</tr>
												<tr>
													<th><?= $_SESSION["TAX"] ?> (0 %)</th>
													<td><?= $currency ?> $ 0.0</td>
												</tr>
												<tr>
													<th><?= $_SESSION["SHIPPING"] ?>:</th>
													<td><?= $currency ?> $ 0.00</td>
												</tr>
												<tr>
													<th><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?>:</th>
													<td><?= $currency ?> $ <?= number_format($move->TOTAL,2,".",",") ?></td>
												</tr>
											</table>
										</div>
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->

								<!-- this row will not appear when printing -->
								<div class="row no-print">
									<div class="col-12">
										<a href="output-print.php?id=<?= $id ?>" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> <?= $_SESSION["_PRINT"] ?></a>
										<!--
										<button type="button" class="btn btn-success float-right" <?= $applied ?>><i class="fa fa-credit-card"></i> <?= $_SESSION["APPLY_STOCK"] ?></button>
										-->
										<button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
											<i class="fa fa-download"></i> <?= $_SESSION["GENERATE_PDF"] ?>
										</button>
									</div>
								</div>
							</div>
							<!-- /.invoice -->
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

<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>