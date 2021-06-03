<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	
	$source = "";
	if(!empty($_GET['src'])) {
		$source = $_GET['src'];
	}

	$filename = basename(__FILE__) . ($source == "" ? "" : "?src=" . $source);	
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/service.php");
	
	$service = new service();
	$qty = $service->loadCount();
	$btncompl = "btn-default";
	$btnicon = "fa-info-circle";
	$btnbadge = "";
	if($qty > 0) {
		$btncompl = "btn-warning";
		$btnicon = "fa-exclamation-triangle fa-lg";
		$btnbadge = "<span class=\"badge badge-danger\">$qty</span>";
	}
	
	$template = "services.xlsx";
	
	$disabled = "disabled";
	if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
		$disabled = "";
	}
	else if($_SESSION["vtappcorp_useraccess"] == "GOD") {
		$disabled = "";
	}
	
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
							<h1 class="m-0 text-dark"><i class="fa fa-motorcycle"></i> <?= $_SESSION["SERVICES"] ?></h1>
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
								</p>
								<div class="btn-group float-right">
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewService" name="btnNewService" class="btn btn-primary pull-right" onclick="show('','new');">
										<i class="fa fa-plus-circle"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
									</button>
<?
	include("core/templates/__buttons.tpl");
?>
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["COMPLETE_SERVICES"] ?>" id="btnComplete" name="btnComplete" class="btn <?= $btncompl ?> pull-right" onclick="location.href = 'services-complete.php';">
										<i class="fa <?= $btnicon ?>"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["COMPLETE_SERVICES"] ?></span>
										<?= $btnbadge ?>
									</button>
								</div>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableService" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_1"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_2"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_3"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_4"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_5"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_6"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_7"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_8"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_9"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_10"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_TABLE_TITLE_11"] ?></th>
											<th width="1%">Icon</th>
											<th width="1%">Notif</th>
											<th width="1%">Payed</th>
											<th width="10%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
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
	$title = $_SESSION["SERVICES"];
	$icon = "<i class=\"fa fa-motorcycle\"></i>";
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
	
	$titleUpload = $_SESSION["UPLOAD_SERVICES"];
	$textUpload = str_replace("{__filename__}","services",$_SESSION["LOAD_TITLE_2"]);
	$parameters = "?class=service&link=services.php&file=service";
	$saveUpload = "core/actions/_save/__saveUploadedServices.php";
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
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	var fields = ["SERVICE_ID", "CLIENT_NAME", "REQUESTED_BY", "REQUESTED_ADDRESS", "ZONE_NAME_REQUEST", "DELIVER_TO", "DELIVER_ADDRESS", "ZONE_NAME_DELIVERY", 
				"DELIVERY_TYPE_NAME", "PRICE", "SERVICE_STATE_NAME", "NOTIFIED", "PAYED", "ICON_STATE", "ID_STATE"];
	$(document).ready(function() {
		$('#divActivateModal').on('shown.bs.modal', function (e) {
			if($("#hfTextButton").val() != "")
				$("#btnActivate").html($("#hfTextButton").val());
			else 
				$("#btnActivate").html($("#hfDefaultTextButton").val());
		});
		$('[data-toggle="tooltip"]').tooltip();		
        table = $('#tableService').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "service",
					"field": fields.join()
				}
			},
			"columns": [
				{ "data": "SERVICE_ID", "searchable": false, "visible": false, "responsivePriority": 9 },
				{ "data": "CLIENT_NAME", "responsivePriority": 1 },
				{ "data": "REQUESTED_BY", "responsivePriority": 2 },
				{ "data": "REQUESTED_ADDRESS", "responsivePriority": 3 },
				{ "data": "ZONE_NAME_REQUEST", "responsivePriority": 11 },
				{ "data": 	"DELIVER_TO", 
							"responsivePriority": 4, 
							"render": function ( data, type, item ) {
								var text = "<button type='button' class='btn btn-light btn-block text-left' title='" + item.SERVICE_STATE_NAME + "'><i class='fa " + item.ICON_STATE + "'></i>&nbsp;" + data + "</button>";
								return text;
							}  
				},
				{ "data": "DELIVER_ADDRESS", "responsivePriority": 5 },
				{ "data": "ZONE_NAME_DELIVERY", "responsivePriority": 10 },
				{ "data": "DELIVERY_TYPE_NAME", "responsivePriority": 6 },
				{ "data": "PRICE", "responsivePriority": 7, "render": $.fn.dataTable.render.number(',', '.', 2, '') },
				{ "data": "SERVICE_STATE_NAME", "responsivePriority": 8 },
				{ "data": "NOTIFIED", "searchable": false, "responsivePriority": 12, "sortable": false, visible: false },
				{ "data": "PAYED", "searchable": false, "responsivePriority": 13, "sortable": false, visible: false },
				{ "data": "ICON_STATE", "searchable": false, "responsivePriority": 4, "sortable": false, visible: false },
				{ "data": "ID_STATE", "searchable": false, "responsivePriority": 1, "sortable": false }
			],
			"autoWidth": false,
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
		$('input[aria-controls="tableService"').unbind();
		$('input[aria-controls="tableService"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function show(id, action) {
		if(action != "new")
			location.href = "service-management.php?id=" + id + "&action=" + action;
		else 
			location.href = "service-management.php?action=" + action;
	}
	function information(id) {
		if(id != "")
			location.href = "service-log.php?id=" + id;
		else 
			notify("", 'danger', "", "<?= $_SESSION["NO_INFORMATION"] ?>", "");
		}
	function activate(id,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["SERVICE"] ?> ";
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
					txtClass: "service", 
					txtLink: "services.php",
					txtPre: "SERVICE"
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
	function markAsPayed(id) {
		var _msg = "<?= $_SESSION["PAY"] ?>";
		$("#spanTitle").html(_msg);
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__saveMarkAsPayed.php',
				dataType: "json",
				data: {
					service: id
				},
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data) {
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					if(data.success) {
						if($("#bidBtn" + id).attr("disabled")) $("#bidBtn" + id).attr("disabled", false);
						if($("#assBtn" + id).attr("disabled")) $("#assBtn" + id).attr("disabled", false);
						$("#payBtn" + id).attr("disabled", true);						
					}
				}
			});
		});
		$("#divActivateModal").modal("toggle");
	}
	function startBid(id) {
		$.ajax({
			url:'core/actions/_load/__getOnlineEmployees.php',
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data) {
				noty.close();
				if(data.success) {
					var _msg = data.message;
					$("#spanTitle").html(_msg);
					$("#spanTitleName").html(name);
					$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
					$("#hfTextButton").val(data.btnText);
					$("#btnActivate").unbind("click");
					$("#btnActivate").bind("click", function() {
						var noty;
						$.ajax({
							url:'core/actions/_save/__startBid.php',
							data: { 
								users: JSON.stringify(data.data),
								id: id
							},
							dataType: "json",
							beforeSend: function (xhrObj) {
								var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
								noty = notify("", "dark", "", message, "", false);												
							},
							success:function(data){
								$("#hfTextButton").val("");
								noty.close();
								notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
								if(data.success) {
									$("#bidBtn" + id).attr("disabled", true);
									$("#assBtn" + id).attr("disabled", true);
								}
							}
						});
					});
					$("#divActivateModal").modal("toggle");
				}
				else {
					notify("", 'info', "", data.message, "");
				}
			}
		});
	}
	function assign(id) {
		var noty;
		$.ajax({
			url:'core/actions/_load/__loadToAssign.php',
			data: { 
				id: id,
				src: "<?= $source ?>"
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				if(data.success) {
					$("#h5ModalLabel").html(data.message.icon + data.message.title);
					$("#modalForm").html(data.message.form);
					$("#btnSave").show();
					$("#btnSave").unbind();
					$('#btnSave').bind("click", function(e) {
						var form = document.getElementById('frmAssignService');
						var noty;
						if (form.checkValidity() === false) {
							window.event.preventDefault();
							window.event.stopPropagation();
							notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
							return false;
						}
						var title = "<?= $_SESSION["MENU_NEW"] . " " . $_SESSION["ASSIGN_SERVICE"] ?>";
						var url = $("#hfLinkAction").val();
						var $frm = $("#frmAssignService");
						var datas = $frm.serializeObject();
						if(!datas.hasOwnProperty("cbPartner")) {
							datas["cbPartner"] = $("#cbPartner option:selected").val()
						}
						if(!datas.hasOwnProperty("cbEmployee")) {
							datas["cbEmployee"] = $("#cbEmployee option:selected").val()
						}
						if(!datas.hasOwnProperty("cbVehicle")) {
							datas["cbVehicle"] = $("#cbVehicle option:selected").val()
						}
						datas["hfIdVehicle"] = $("#cbEmployee option:selected").data("vehicleid");
						datas = JSON.stringify(datas);
						$("#spanTitle").html(title);
						$("#spanTitleName").html("");
						$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
						$("#btnActivate").unbind("click");
						$("#btnActivate").bind("click", function() {
							var noty;
							$.ajax({
								url: url,
								data: { 
									strModel: datas,
									link: "services.php"
								},
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
					$.getScript("js/resources.js");
					$('#divEditModal').modal('toggle');
					$("#cbPartner").on("change",function (e) {
						var value = $(this).find("option:selected").val();
						var vehicle = $("#cbVehicle option:selected").val();
						$("#cbEmployee").find("option[data-partnerid!='" + value + "']").attr("disabled","disabled");
						$("#cbEmployee").find("option[data-partnerid='" + value + "']").removeAttr("disabled");
						if($("#hfNoVehicle").val() != vehicle) {
							$("#cbEmployee option:enabled").each(function () {
								var val = $(this).data("vehicletype");
								if(val != vehicle) {
									$(this).attr("disabled","disabled");
								}
							});
						}
						$("#cbEmployee").find("option[value='']").removeAttr("disabled");
						$("#cbEmployee").val("");
						$("#hfVehicleTypeId").val("");
						$("#cbEmployee").trigger("change");
					});
					$("#cbEmployee").on("change",function (e) {
						var value = $(this).find("option:selected").val();
						var type = $(this).find("option:selected").data("vehicletype");
						if(value != "" && $("#hfChangeVehicle").val() == "true") {
							$("#hfVehicleTypeId").val(type);
						}
					});
					$("#cbPartner").trigger("change");
				}
				else {
					notify("", 'danger', "", data.message, "");
				}
			}
		});
	}
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
