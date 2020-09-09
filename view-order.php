<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId("orders.php");
	
	require_once("core/__check-session.php");
	
	$result = checkSession("orders.php",true);

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
	
	require_once("core/classes/employee.php");
	require_once("core/classes/configuration.php");
	require_once("core/classes/order_detail.php");
	
	$employee = new employee();
	$employee->CODE = "0022";
	$employee->getInformationByOtherInfo("CODE");
	
	$orden = new order_detail();
	$orden->setOrder($id);
	
	//Verifica si existe
	if($orden->nerror > 0) {
		$_SESSION["vtappcorp_user_alert"] = $orden->error;
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
							<h1 class="m-0 text-dark"><i class="fa fa-file"></i> <?= $_SESSION["VIEW"] . " " . $_SESSION["ORDER"] . " " . $orden->_order->INTERNAL_NUMBER ?></h1>
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
											<small class="float-right"><?= $_SESSION["OUTPUT_TABLE_TITLE_3"] ?>: <?= date("d/m/Y", strtotime($orden->_order->REGISTERED_ON)) ?></small>
										</h4>
									</div>
									<!-- /.col -->
								</div>
								<!-- info row -->
								<div class="row invoice-info">
									<div class="col-sm-4 invoice-col">
										<?= $_SESSION["FROM"] ?>
										<address>
											<strong><?= $company ?></strong><br>
											<?= $employee->FIRSTNAME . " " . $employee->LASTNAME ?><br>
											<?= $employee->area->COSTCENTER . " - " . $employee->area->getResource() ?><br>
											<?= $_SESSION["PHONE"] . ": " ?><br>
											<?= $_SESSION["EMAIL"] . ": " . $employee->EMAIL ?>
										</address>
									</div>
									<!-- /.col -->
									<div class="col-sm-4 invoice-col">
										<?= $_SESSION["TO"] ?>
										<address>
											<strong><?= $orden->_order->client->COMPANY_NAME ?></strong><br>
											<?= $orden->_order->client->CONTACT_NAME_1 ?><br>
											<?= $orden->_order->client->ADDRESS ?><br>
											<?= $orden->_order->client->city->country->COUNTRY ?><br>
											<?= $_SESSION["PHONE"] . ": " . $orden->_order->client->PHONE_1 ?><br>
											<?= $_SESSION["EMAIL"] . ": " . $orden->_order->client->EMAIL_1 ?>
										</address>
									</div>
									<!-- /.col -->
									<div class="col-sm-4 invoice-col">
										<b><?= $_SESSION["ORDER"] . " #" . $orden->_order->INTERNAL_NUMBER ?></b><br>
										<br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_5"] ?>: </b><?= $_SESSION["ORDER"] ?><br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_6"] ?>: </b><?= date("d/m/Y", strtotime($orden->_order->REGISTERED_ON)) ?><br>
										<b><?= $_SESSION["OUTPUT_TABLE_TITLE_7"] ?>: </b><?= $orden->_order->REGISTERED_BY ?><br>
										<b><?= $_SESSION["STATE"] ?>: </b>
											<span class="<?= $orden->_order->state->BADGE ?>">
												<?= $orden->_order->state->getResource() ?>
											</span>
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
<?= $orden->listTable(0,$currency) ?>										
											</tbody>
										</table>
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->

								<div class="row">
									<!-- accepted payments column -->
									<div class="col-6">
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
									</div>
									<!-- /.col -->
									<div class="col-6">
										<p class="lead"><?= $_SESSION["AMOUNT_DUE"] . " " . date("d/m/Y", strtotime($orden->_order->MOVE_DATE)) ?></p>
										<div class="table-responsive">
											<table class="table">
												<tr>
													<th style="width:50%"><?= $_SESSION["SUBTOTAL"] ?>:</th>
													<td><?= $currency ?> $ <?= number_format($orden->TOTAL,2,".",",") ?></td>
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
													<td><?= $currency ?> $ <?= number_format($orden->TOTAL,2,".",",") ?></td>
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
										<a href="#" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> <?= $_SESSION["_PRINT"] ?></a>
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