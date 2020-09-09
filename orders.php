<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId(basename(__FILE__));
	
	require_once("core/__check-session.php");
	
	$result = checkSession("orders.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/order.php");
	
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
	<link rel="stylesheet" href="plugins/select2/select2.min.css">
	
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
							<h1 class="m-0 text-dark"><i class="fa fa-folder-open"></i> <?= $_SESSION["ORDERS"] ?></h1>
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
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<p class="card-title">
									<?= $_SESSION["TITLE_PAGE"] ?>
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewOrder" name="btnNewOrder" class="btn btn-primary pull-right" onclick="location.href = 'neworder.php';">
										<i class="fa fa-plus-square"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
									</button>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableOrder" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="5%"><?= $_SESSION["ORDERS_TABLE_TITLE_1"] ?></th>
											<th width="15%"><?= $_SESSION["ORDERS_TABLE_TITLE_2"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_3"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_4"] ?></th>
											<th width="5%"><?= $_SESSION["ORDERS_TABLE_TITLE_5"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_6"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_7"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_8"] ?></th>
											<th width="10%"><?= $_SESSION["ORDERS_TABLE_TITLE_9"] ?></th>
											<th><?= $_SESSION["ORDERS_TABLE_TITLE_9"] ?></th>
											<th width="15%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
										</tr>
									</thead>		
									<tbody>
									</tbody>
								</table>
							</div>
							<!-- /.card-body -->
						</div>
						<!-- /.card -->
					</div>
					<!-- /.col -->
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["ORDERS"];
	$icon = "<i class=\"fa fa-folder-open\"></i>";
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
	<!-- Select2 -->
	<script src="plugins/select2/select2.full.min.js"></script>
	

    <script>
	var fields = ["ORDER_ID", "INTERNAL_NUMBER", "COMPANY_NAME", "COUNTRY", "STATE_NAME", "REGISTERED_ON", "REGISTERED_BY", "ITEMS", "TOTAL", "ID_STATE", "BADGE"];
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();		
        table = $('#tableOrder').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "order",
					"field": fields.join()
				}
			},
			"columns": [
				{ "data": "ORDER_ID", "searchable": false, "visible": false },
				{ "data": "INTERNAL_NUMBER", "responsivePriority": 2 },
				{ "data": "COMPANY_NAME", "responsivePriority": 3 },
				{ "data": "COUNTRY" },
				{ "data": "STATE_NAME" },
				{ "data": "REGISTERED_ON" },
				{ "data": "REGISTERED_BY" },
				{ "data": "ITEMS", "responsivePriority": 4, "sClass": "text-center" },
				{ "data": "TOTAL", "sClass": "text-center" , "responsivePriority": 5, "render": function (data, type, row) { return "$" + FormatNumber(data,2); } },				
				{ "data": "ID_STATE", "searchable": false, "visible": false },
				{ "data": "BADGE", "visible": true, "searchable": false, "responsivePriority": 1, "sortable": false }
			],
			"processing": true,
			"serverSide": true,
			"pageLength": 50,
            "responsive": true,
			"order": [[ 2, 'asc' ]],
            "columnDefs": [{
                "targets": 0,
				"orderable": false 
            }],
            "select": {
                style:    'os',
                selector: 'td:first-child'
            }
<?
	if($_SESSION["LANGUAGE"] != "1") {
?>
			, language: {
				url: 'plugins/datatables/lang/<?= $_SESSION["LANGUAGE"] ?>.json'
			}
<?
	}
?>
        }); //.columns.adjust().responsive.recalc();
		$('input[aria-controls="tableOrder"').unbind();
		$('input[aria-controls="tableOrder"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function show(id, link) {
		window.open(link + "?id=" + id,'_self');
	}
	
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
