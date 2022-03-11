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
	
	$result = checkSession("resourcesman.php",true);
	
	if(!$result["success"]) 
		$inter->redirect($result["link"]);
	
	$user = new users($_SESSION["vtappcorp_userid"]);

	$lang = new language();
	
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
    <!-- Switchery -->
    <link href="plugins/switchery/switchery.min.css" rel="stylesheet">
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
							<h1 class="m-0 text-dark"><i class="fa fa-language"></i> <?= $_SESSION["RESOURCES"] ?></h1>
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
									<div class="btn-group pull-right">
										<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewResource" name="btnNewResource" class="btn btn-primary pull-right" onclick="show('','new');">
											<i class="fa fa-plus-square"></i>
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
										</button>
										<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["LOAD_RESOURCES"] ?>" id="btnLoadResources" name="btnLoadResources" class="btn btn-default pull-right" onclick="upload();">
											<i class="fa fa-cloud-upload"></i>
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["LOAD_RESOURCES"] ?></span>
										</button>
										<div class="btn-group">
											<button type="button" class="btn btn-default dropdown-toggle" title="<?= $_SESSION["DOWNLOAD_RESOURCES"] ?>" data-toggle="dropdown">
												<i class="fa fa-cloud-download"></i>												
												<span class="caret"></span>
											</button>
											<div class="dropdown-menu">
<?= $lang->showOptionLanguages() ?>										
											</div>
										</div>
									</div>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableResource" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="5%"><?= $_SESSION["RESOURCES_TABLE_TITLE_6"] ?></th>
											<th width="10%"><?= $_SESSION["RESOURCES_TABLE_TITLE_1"] ?></th>
											<th width="30%"><?= $_SESSION["RESOURCES_TABLE_TITLE_2"] ?></th>
											<th width="15%"><?= $_SESSION["RESOURCES_TABLE_TITLE_3"] ?></th>
											<th width="5%"><?= $_SESSION["RESOURCES_TABLE_TITLE_4"] ?></th>
											<th width="5%"><?= $_SESSION["RESOURCES_TABLE_TITLE_5"] ?></th>
											<th width="30%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
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
	$title = $_SESSION["RESOURCE"];
	$icon = "<i class=\"fa fa-language\"></i>";
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
	
	$titleUpload = $_SESSION["UPLOAD_RESOURCES"];
	$textUpload = str_replace("{__filename__}","resources",$_SESSION["LOAD_TITLE_2"]);
	$parameters = "";
	$saveUpload = "core/actions/_save/__saveUploadedResources.php";
	include("core/templates/__modalUpload.tpl");
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
	<!-- Switchery -->
	<script src="plugins/switchery/switchery.min.js"></script>

    <script>
	var fields = ["ID", "RESOURCE_NAME", "RESOURCE_TEXT", "LANGUAGE_NAME", "IS_SYSTEM", "IS_BLOCKED"];
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
        table = $('#tableResource').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "resources",
					"field": fields.join()
				}
			},
			"fnInitComplete": function(oSettings, json) {
				$('[data-toggle="tooltip"]').tooltip();		
			},			
			"columns": [
				{ "data": "ID", "searchable": false, "responsivePriority": 6 },
				{ "data": "RESOURCE_NAME", "responsivePriority": 2 },
				{ "data": "RESOURCE_TEXT", "responsivePriority": 3 },
				{ "data": "LANGUAGE_NAME", "responsivePriority": 4 },
				{ "data": "IS_SYSTEM", "searchable": false, "responsivePriority": 5  },
				{ "data": "IS_BLOCKED", "searchable": false, "responsivePriority": 7 },
				{ "data": "", "searchable": false, "responsivePriority": 1 }
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
        }).columns.adjust().responsive.recalc();
		$('input[aria-controls="tableResource"').unbind();
		$('input[aria-controls="tableResource"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
		$('#divEditResource').on('shown.bs.modal', function () {
			$('#txtTBL_SYSTEM_RESOURCE_RESOURCE_NAME').focus();
		});
		$("#btnSave").on("click", function(e) {
			var form = document.getElementById('frmResource');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["RESOURCES"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmResource");
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
	function show(id, action) {
		var noty;
		$.ajax({
			url:'core/actions/_load/__showFormData.php',
			data: { 
				txtId: id,
				txtAction: action,
				txtClass: "resources"
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
				if ($(".js-switch")[0]) {
					var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
					elems.forEach(function (html) {
						var switchery = new Switchery(html, {
							color: '#F15A31'
						});
					});
				}
				$.getScript( "js/resources.js");
				$('#divEditModal').modal('toggle');
			}
		});		
	}
	function downloadCSV(id) {
		var noty;
		var url = 'core/actions/_load/__downloadResources.php';
		$.ajax({
			url: url,
			data: { 
				txtId: id
			},
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);
			},
			success:function(data){
				noty.close();
				window.location = url + '?txtId=' + id;
			}
		});
	}
	function activate(id,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["RESOURCE"] ?> ";
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
					txtClass: "resources", 
					txtLink: "resourceman.php",
					txtPre: "RESOURCE"
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
