<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	
	$filename = "services.php";
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/service.php");
	
	$service = new service();
	
	$qty = $service->loadCount();
	
	if($qty < 1) {
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["NO_SERVICES_TO_COMPLETE"];		
		$inter->redirect("services.php");
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
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- JqueryUI -->
	<link rel="stylesheet" href="plugins/jQueryUI/jquery-ui.css">	
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
									<?= $_SESSION["SERVICE_COMPLETE_TITLE_PAGE"] ?>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableServiceComplete" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="15%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_1"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_2"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_4"] ?></th>
											<th width="15%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_5"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_6"] ?></th>
											<th width="5%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_8"] ?></th>
											<th width="5%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_9"] ?></th>
											<th width="5%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_10"] ?></th>
											<th width="10%"><?= $_SESSION["SERVICE_COMPLETE_TABLE_TITLE_11"] ?></th>
											<th width="10%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
										</tr>
									</thead>		
									<tbody>
<?
	$service->showLoaded();
?>
									</tbody>
								</table>
								<input type="hidden" id="hfClientId" name="hfClientId" value="<?= $client ?>" />
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<div class="float-left">
									<p><small><?= $_SESSION["PRICE_CALCULATED_MESSAGE"] ?></small></p>
								</div>
								<div class="btn-group float-right">
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["GO_TO_PAY"] ?>" id="btnPayment" name="btnPayment" class="btn btn-warning pull-right" onclick="payment();">
										<i class="fa fa-money"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["GO_TO_PAY"] ?></span>
									</button>
								</div>
							</div>
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
	
	if($payment) {
		require_once("core/classes/configuration.php");
		$conf = new configuration("PAYMENT_MERCHANT_ID");
		$merchId = $conf->verifyValue();

		include("core/templates/__modalPayment.tpl");
	}
	
?>

    <!-- Data Table JS
		============================================ -->
	<!-- DataTables -->
	<script src="plugins/datatables/jquery.dataTables.js"></script>
	<script src="plugins/datatables/dataTables.bootstrap4.js"></script>
    <script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.js"></script>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$('[data-widget="pushmenu"]').trigger("click");
		$('[data-toggle="tooltip"]').tooltip();
		$("input[name^='txtZONE_']").autocomplete({
			source: "core/actions/_load/__loadZones.php",
			minLength: 3,
			select: function( event, ui ) {
				var control = event.target.name;
				var id = control.split("_")[2];
				var ReqDel = control.indexOf("REQUEST") > -1 ? "Req" : "Del";
				var data = ui.item;
				$("#hfLng" + ReqDel + "_" + id).val(parseFloat(data.lng));
				$("#hfLat" + ReqDel + "_" + id).val(parseFloat(data.lat));
				$("#hfZon" + ReqDel + "_" + id).val(data.id);
				var distance = setDistance(id);
				if(distance > 0) {
					calculate(id,distance);
				}
				$('#cbClient_' + id).trigger("change");
			}
		});
		$("select[name^='cbClient_']").change(function() {
			var id = this.id.split("_")[1];
			$("#hfClientId_" + id).val($(this).val());
		});
		$("input[name^='cbRoundTrip_']").change(function() {
			var id = this.id.split("_")[1];
			calculate(id,0,true);
		});
        table = $('#tableServiceComplete').DataTable({
			"columns": [
				{ "target": 0, "searchable": true, "responsivePriority": 2, "class": "details-control" },
				{ "target": 1, "searchable": false, "responsivePriority": 3 },
				{ "target": 2, "searchable": true, "responsivePriority": 7 },
				{ "target": 3, "searchable": true, "responsivePriority": 4 },
				{ "target": 4, "searchable": false, "responsivePriority": 5 },
				{ "target": 5, "searchable": true, "responsivePriority": 9 },
				{ "target": 6, "searchable": true, "responsivePriority": 8 },
				{ "target": 7, "searchable": false, "responsivePriority": 6 },
				{ "target": 8, "searchable": false, "responsivePriority": 10 },
				{ "target": 9, "searchable": false, "responsivePriority": 1, "sortable": false }
			],
			"autoWidth": false,
			"processing": false,
			"serverSide": false,
			"responsive": true,
			"pageLength": 50,
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
		$('input[aria-controls="tableServiceComplete"').unbind();
		$('input[aria-controls="tableServiceComplete"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function setDistance(id) {
		var distance;
		var orig = {
			lat: parseFloat($("#hfLatReq_" + id).val()),
			lng: parseFloat($("#hfLngReq_" + id).val())
		};
		var dest = {
			lat: parseFloat($("#hfLatDel_" + id).val()),
			lng: parseFloat($("#hfLngDel_" + id).val())
		};
		if(isNaN(orig.lat) || isNaN(orig.lng) || isNaN(dest.lat) || isNaN(dest.lng)) 
			distance = 0;
		else 
			distance = getDistance(orig, dest);
		$("#hfDistance_" + id).val(distance);
		return distance;
	}
	function calculate(id, distance, recalculate = false) {
		if(!recalculate) {
			var value = parseFloat($("#spPrice_" + id).html());
			if(!isNaN(value) && value > 0) {
				$(".dtr-data > span#spPrice_" + id).html(FormatNumber(value,2,3));
				return false;
			}
			distance = typeof distance !== 'undefined' ?  distance : setDistance(id);
		}
		else {
			distance = setDistance(id);
		}
		var noty;
		$.ajax({
			url: "core/actions/_load/__checkRate.php",
			data: { 
				distance: distance,
				round: $("#cbRoundTrip_" + id).is(':checked')
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CALCULATING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				if(data.success) {
					$("#hfPrice_" + id).val(data.message);
					$("#spPrice_" + id).html(data.message);
					if($(".dtr-data > span#spPrice_" + id).is(":visible")) {
						$(".dtr-data > span#spPrice_" + id).html(FormatNumber(parseFloat(data.message),2,3));
					}
				}
				else 
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
				noty.close();
			},
			always: function() {
				noty.close();
			}
		});
	}
	function save(id) {
		if($("#txtZONE_REQUEST_" + id).val() == "") {
			notify("", 'danger', "", "<?= $_SESSION["ZONE_REQUEST_NOT_DEFINED"] ?>", "");
			$("#txtZONE_REQUEST_" + id).focus();
			return false;
		}
		if($("#txtZONE_DELIVER_" + id).val() == "") {
			notify("", 'danger', "", "<?= $_SESSION["ZONE_DELIVER_NOT_DEFINED"] ?>", "");
			$("#txtZONE_DELIVER_" + id).focus();
			return false;
		}
		if($("#hfPrice_" + id).val() == "") {
			notify("", 'danger', "", "<?= $_SESSION["PRICE_NOT_DEFINED"] ?>", "");
			return false;
		}
		if(parseFloat($("#hfPrice_" + id).val()) == 0) {
			notify("", 'danger', "", "<?= $_SESSION["PRICE_NOT_DEFINED"] ?>", "");
			return false;
		}
		var title = "<?= $_SESSION["UPDATE_LOADED_SERVICE"] ?>";
		var url = "core/actions/_save/__updateService.php";
		var datasObj = {
			id: $("#hfId_" + id).val(),
			zone_req: $("#hfZonReq_" + id).val(),
			zone_del: $("#hfZonDel_" + id).val(),
			lat_req: $("#hfLatReq_" + id).val(),
			lng_req: $("#hfLngReq_" + id).val(),
			lat_del: $("#hfLatDel_" + id).val(),
			lng_del: $("#hfLngDel_" + id).val(),
			price: $("#hfPrice_" + id).val(),
			client: $("#hfClientId_" + id).val(),
			changeclient: $("#hfAskClient_" + id).val(),
			counter: id
		};
		var datas = JSON.stringify(datasObj);
		$("#spanTitle").html(title);
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["TEXT_UPDATE_LOADED_SERVICE"] ?><br /><br /><?= $_SESSION["MSG_CONFIRM"] ?>");
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
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					var objPay = typeof data.data_payment !== 'undefined' ?  data.data_payment : {};
					$("#hfSaved_" + data.counter).val(data.success);
					$("#hfPayed_" + data.counter).val(!data.payment);
					$("#hfObjPay_" + data.counter).val(JSON.stringify(objPay));
					$("#btnSave_" + data.counter).attr("disabled", data.success);
					var enpay = true;
					$("input[name^='hfPayed_']").each(function() {
						if($(this).val() == "false") {
							enpay = false;
							return false;
						}
					});
					$("#btnPayment").attr("disabled", enpay);
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
