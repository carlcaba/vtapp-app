<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId(basename($_SERVER['REQUEST_URI']));
	
	require_once("core/__check-session.php");
	
	$result = checkSession("products.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/product.php");
	
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
							<h1 class="m-0 text-dark"><i class="fa fa-shopping-cart"></i> <?= $_SESSION["PRODUCTS"] ?></h1>
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
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewProduct" name="btnNewProduct" class="btn btn-primary pull-right" onclick="show('','new');">
										<i class="fa fa-plus-circle"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
									</button>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableProduct" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="10%"><?= $_SESSION["PRODUCT_TABLE_TITLE_1"] ?></th>
											<th width="25%"><?= $_SESSION["PRODUCT_TABLE_TITLE_2"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_3"] ?></th>
											<th width="10%"><?= $_SESSION["PRODUCT_TABLE_TITLE_4"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_5"] ?></th>
											<th width="10%"><?= $_SESSION["PRODUCT_TABLE_TITLE_6"] ?></th>
											<th width="10%"><?= $_SESSION["PRODUCT_TABLE_TITLE_7"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_8"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_9"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_10"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_11"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_12"] ?></th>
											<th width="5%"><?= $_SESSION["PRODUCT_TABLE_TITLE_13"] ?></th>
											<th width="20%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
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
	$title = $_SESSION["PRODUCTS"];
	$icon = "<i class=\"fa fa-shopping-cart\"></i>";
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
	
    <script>
	var fields = ["PRODUCT_ID", "PRODUCT_NAME", "CODE" , "SPECIFICATION", "TRADE", "QUANTITY", "PRICE", "money-bill-1TYPE", "OBSERVATION", "FIELD", "AREA_NAME", "CATEGORY_NAME", "REGISTERED_ON", "REGISTERED_BY", "BLOCKED", "LANGUAGE_ID"];
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();		
        table = $('#tableProduct').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "product",
					"field": fields.join()
				}
			},
			"fnInitComplete": function(oSettings, json) {
				$('[data-toggle="tooltip"]').tooltip();		
			},			
			"columns": [
				{ "data": "PRODUCT_ID", "searchable": false, "visible": false },
				{ "data": "PRODUCT_NAME", "responsivePriority": 2 },
				{ "data": "CODE", "visible": false },
				{ "data": "SPECIFICATION", "visible": false },
				{ "data": "TRADE" },
				{ "data": "QUANTITY", "sClass": "text-center" , "render": $.fn.dataTable.render.number(",", ".", 0, '') },
				{ "data": "PRICE", "sClass": "text-center" , "render": function (data, type, row) { return "$" + FormatNumber(data,2) + " " + row.money-bill-1TYPE; } },
				{ "data": "money-bill-1TYPE", "visible": false },
				{ "data": "OBSERVATION" },
				{ "data": "FIELD" },
				{ "data": "AREA_NAME" },
				{ "data": "CATEGORY_NAME" },
				{ "data": "BLOCKED", "searchable": false  },
				{ "data": "LANGUAGE_ID", "searchable": false, "responsivePriority": 1, "sortable": false }
			],
			"processing": true,
			"serverSide": true,
			"responsive": true,
			"pageLength": 50,
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
        }).columns.adjust().responsive.recalc();
		$('input[aria-controls="tableProduct"').unbind();
		$('input[aria-controls="tableProduct"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
		$('#divEditModal').on('shown.bs.modal', function () {
			var id = 0;
			if($("#hfAction").val() != "new")
				id = 1;
			$('#txt' + fields[id]).focus();
		});
		$("#btnSave").on("click", function(e) {
			var form = document.getElementById('frmProduct');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["PRODUCT"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmProduct");
			var datas = JSON.stringify($frm.serializeObject());
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: url,
					data: { strModel: datas },
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#divEditModal").modal('hide');
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success)
							location.href = data.link;
					}
				});
			});
			$("#divActivateModal").modal("toggle");			
		});
	});
	function QRCode(id, name) {
		$("#spanTitle").html("<?= $_SESSION["QR_CODE"] ?> - ");
		$("#spanTitleName").html(name);
		$("#modalBody").html('<img src="core/actions/_load/__showQRCode.php?txtId=' + id +'&type=QR" align="center" class="center-block" />');
		$("#btnActivate").html("<?= $_SESSION["ACCEPT"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			$("#btnActivate").html("<?= $_SESSION["SAVE_CHANGES"] ?>");
			$("#divActivateModal").modal("toggle");
		});
		$("#divActivateModal").modal("toggle");
	}
	function BarCode(id, name) {
		$("#spanTitle").html("<?= $_SESSION["QR_CODE"] ?> - ");
		$("#spanTitleName").html(name);
		$("#modalBody").load("core/actions/_load/__showQRCode.php?txtId=" + id + "&type=BAR");
		$("#btnActivate").html("<?= $_SESSION["ACCEPT"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			$("#btnActivate").html("<?= $_SESSION["SAVE_CHANGES"] ?>");
			$("#divActivateModal").modal("toggle");
		});
		$("#divActivateModal").modal("toggle");
	}
	function show(id, action) {
		var noty;
		$.ajax({
			url:'core/actions/_load/__showFormData.php',
			data: { 
				txtId: id,
				txtAction: action,
				txtClass: "product"
			},
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				$("#modalForm").html(data);
				$("#actionId").html($("#hfAction").val());
				if(action == "view")
					$("#btnSave").hide();
				else {
					$("#btnSave").show();
				}
				$.getScript( "js/resources.js");
				$('#divEditModal').modal('toggle');
			}
		});
	}
	function activate(id,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["PRODUCT"] ?> ";
		$("#spanTitle").html(_msg);
		$("#spanTitleName").html(name);
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__activateData.php',
				data: { 
					txtId: id,
					activate: activate,
					txtClass: "product", 
					txtLink: "products.php",
					txtPre: "PRODUCT"
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					table.ajax.reload();
				}
			});
		});
		$("#divActivateModal").modal("toggle");
	}
	
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
