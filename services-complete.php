<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();
	
	$filename = "services.php";
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/users.php");
	$uscli = new users($_SESSION["vtappcorp_userid"]);

	require_once("core/classes/service.php");
	
	$service = new service();
	
	$qty = $service->loadCount();
	
	if($qty < 1) {
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["NO_SERVICES_TO_COMPLETE"];		
		$inter->redirect("services.php");
	}		

	$payment = !(substr($uscli->access->PREFIX,0,2) == "AL");

	$gate = $conf->verifyValue("PAYMENT_GATEWAY");
	$accTok = 0;
	$err = 0;

	//Verifica la pasarela
	if($gate == "WOMPI") {
		//Libreria requerida
		require_once("core/actions/_save/__wompiGatewayFunctions.php");

		$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
		$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
		$urlReturn = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $conf->verifyValue("PAYMENT_WOMPI_REDIRECT");
		
		$accTok = 1;
		
		//Obtiene el acceptance token
		$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

		//Si no es null
		if($accTokRet["token"] != null) {
			$accTokData = $accTokRet["token"];
		}
		else {
			$err = 1;
		}
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
	$count = $service->showLoaded();
?>
									</tbody>
								</table>
								<input type="hidden" id="hfSQL" name="hfSQL" value="<?= $service->sql ?>" />
								<input type="hidden" id="hfClientId" name="hfClientId" value="<?= $client ?>" />
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<div class="float-left">
									<p><small><?= $_SESSION["PRICE_CALCULATED_MESSAGE"] ?></small></p>
								</div>
								<div class="btn-group float-right">
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["GO_TO_PAY"] ?>" id="btnPayment" name="btnPayment" class="btn btn-warning pull-right" onclick="payment();">
										<i class="fa fa-money-bill-1"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["GO_TO_PAY"] ?></span>
									</button>
									<input type="hidden" name="hfTtl2Py" id="hfTtl2Py" value="0" />
									<input type="hidden" name="hfTotalRegsToComplete" id="hfTotalRegsToComplete" value="<?= $count ?>" />
									<input type="hidden" name="hfTotalAmount" id="hfTotalAmount" value="0" />
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
	
	if($payment) 
		include("core/templates/__modalPayment.tpl");
	
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
	var url = null;
	var ky = null;
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
				if($("#hfLng" + ReqDel + "_" + id).val() == "") {
					$("#hfLng" + ReqDel + "_" + id).val(parseFloat(data.lng));
				}
				if($("#hfLat" + ReqDel + "_" + id).val() == "") {
					$("#hfLat" + ReqDel + "_" + id).val(parseFloat(data.lat));
				}
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
		url = getResourceValue("MAGLO", "<?= $_SESSION["MSG_PROCESSING"] ?>");
		var noty;
		$.ajax({
			url: "core/actions/_load/__getValue.php",
			data: { 
				value: "MKLM"
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				if(data.success) {
					ky = data.message;
					$.getScript("https://maps.googleapis.com/maps/api/js?key=" + data.message)
					.done(function( script, textStatus ) {
						console.log( textStatus );
					})
					.fail(function( jqxhr, settings, exception ) {
						$( "div.log" ).text( "Triggered ajaxError handler." );
					});	
				}
				else {
					notify("", 'danger', "", data.message, "");
					return "";
				}
			}
		});
		var ttl = 0;
		for(i=0;i<parseInt($("#hfTotalRegsToComplete").val());i++) {
			if($("#hfPayed_" + i).val() == "false" && $("#hfSaved_" + i).val() == "true") {
				ttl += parseFloat($("#hfPrice_" + i).val());
			}
		}
		$("#hfTotalAmount").val(ttl);
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
			if(typeof distance !== 'undefined')
				if(distance == 0)
					distance = setDistance(id);
		}
		else {
			distance = setDistance(id);
		}
		var noty;
		$.ajax({
			url: "core/actions/_load/__checkRate.php",
			data: { 
				distance: distance,
				round: $("#cbRoundTrip_" + id).is(':checked'),
				select: false
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CALCULATING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				if(data.success) {
					$("#hfPrice_" + id).val(data.max);
					$("#spPrice_" + id).html(data.max);
					if($(".dtr-data > span#spPrice_" + id).is(":visible")) {
						$(".dtr-data > span#spPrice_" + id).html(FormatNumber(parseFloat(data.max),2,3));
					}
					$("#hfTtl2Py").val(parseFloat($("#hfTtl2Py").val()) + data.max);
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
	function completeLocation(id) {
		var address = $("#tdREQUESTED_ADDRESS_" + id).html();
		var address2 = $("#tdDELIVER_ADDRESS_" + id).html();
		var calc1 = false;
		var calc2 = false;
		try {
			var geocoder = new google.maps.Geocoder();
			if (geocoder) {
				geocoder.geocode({ 'address': address }, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						$("#hfLatReq_" + id).val(results[0].geometry.location.lat());
						$("#hfLngReq_" + id).val(results[0].geometry.location.lng());
						var sele = "";
						var neigh = "";
						var place = results[0];
						for (var i = 0; i < place.address_components.length; i++) {
							if(place.address_components[i].types.indexOf("neighborhood") > -1) {
								neigh = removeAccents(place.address_components[i].long_name.toUpperCase());
							}
							else if(place.address_components[i].types.indexOf("sublocality_level_1") > -1) {
								sele = removeAccents(place.address_components[i].long_name.toUpperCase());
							}
						}
						if(neigh != "" && sele != "")
							$("#txtZONE_REQUEST_" + id).val(neigh + " (" + sele + ")");
						$("#hfZonReq_" + id).val(neigh + "," + sele);
						calc1 = true;
						geocoder.geocode({ 'address': address2 }, function (results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								$("#hfLatDel_" + id).val(results[0].geometry.location.lat());
								$("#hfLngDel_" + id).val(results[0].geometry.location.lng());
								var sele = "";
								var neigh = "";
								var place = results[0];
								for (var i = 0; i < place.address_components.length; i++) {
									if(place.address_components[i].types.indexOf("neighborhood") > -1) {
										neigh = removeAccents(place.address_components[i].long_name.toUpperCase());
									}
									else if(place.address_components[i].types.indexOf("sublocality_level_1") > -1) {
										sele = removeAccents(place.address_components[i].long_name.toUpperCase());
									}
								}
								if(neigh != "" && sele != "")
									$("#txtZONE_DELIVER_" + id).val(neigh + " (" + sele + ")");
								$("#hfZonDel_" + id).val(neigh + "," + sele);
								calc2 = true;
								$("#txtZONE_REQUEST_" + id).attr("disabled", calc1);
								$("#txtZONE_DELIVER_" + id).attr("disabled", calc2);
								$("#tdREQUESTED_ADDRESS_" + id).trigger("click");
								if(calc1 && calc2) {
									$("#btnLocate_" + id).attr("disabled", true);
									var distance = setDistance(id);
									if(distance > 0) {
										calculate(id,distance);
									}
								}
							}
							else {
								notify("", 'danger', "", "Geocoding failed REQ: " + status, "");
								console.log("Geocoding failed REQ: " + status);
								calc2 = false;
							}
						});
					}
					else {
						notify("", 'danger', "", "Geocoding failed REQ: " + status, "");
						console.log("Geocoding failed REQ: " + status);
						calc1 = false;
					}
				});
			}    		
		}
		catch (error) {
			console.log(error);
		}
	}
	
	function payment() {
		var total = 0;
		var counter = 0;
		var uid = "<?= uniqid() ?>";
		var datser = {};
		var datas = { 
			id: uid,
			services: []
		};
		for(i=0;i<parseInt($("#hfTotalRegsToComplete").val());i++) {
			if($("#hfPayed_" + i).val() == "false" && $("#hfSaved_" + i).val() == "true") {
				$("#hfToPay_" + i).val(uid);
				total += parseFloat($("#hfPrice_" + i).val());
				counter++;
				datser = { 
					id: $("#hfId_" + i).val(),
					indx: i,
					price: parseFloat($("#hfPrice_" + i).val()),
					user: $("#hfUserId_" + i).val(),
					payed: false,
					quota: false,
					qid: "",
					datapayment: {}
				};
				console.log(datser);
				datas["services"].push(datser);
			}
		}
		if(total <= 0) {
			notify("", 'danger', "", "<?= $_SESSION["NO_SERVICES_COMPLETED_TO_PAY"] ?>", "");
			return false;
		}
		if(counter <= 0) {
			notify("", 'danger', "", "<?= $_SESSION["NO_SERVICES_COMPLETED_TO_PAY"] ?>", "");
			return false;
		}
		console.log(datas);
		console.log(total);
		console.log(counter);
		$("#hfTotalAmount").val(total);
		$.ajax({
			url: "core/actions/_save/__saveUserQuotaMultiple.php",
			data: { 
				datas: JSON.stringify(datas)
			},
			method: "POST",
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success: function(data) {
				noty.close();
				if(data.success) {
					var amount = 0;
					var qty = 0;
					for(i=0;i<counter;i++) {
						if(!data.objPay[i].payed) {
							amount+=data.objPay[i].price; 
							qty++;
						}
					}
					datas.services = data.objPay;
					console.log(datas);
					$("#hfTotalAmount").val(amount);
					if(amount > 0 && <?= ($accTok && !$err) ?>) {
						$.getScript("<?= $script ?>", function( data, textStatus, jqxhr ) {
							var checkout = new WidgetCheckout({
								currency: 'COP',
								amountInCents: Math.ceil(parseFloat($("#hfTotalAmount").val()) * 100),
								reference: datas.id,
								publicKey: '<?= $pubkey ?>'
								//,redirectUrl: '<?= $urlReturn ?>'
							});
							checkout.open(function ( result ) {
								var transaction = result.transaction
								if(transaction.status == "APPROVED") {
									notify("", 'success', "", "<?= $_SESSION["PAYMENT_REGISTERED"] ?>", "");
									var url = "core/actions/_save/__processMultiplePayFromGateway.php";
									var datasObj = JSON.stringify(datas.services);
									var payObj = JSON.stringify(transaction);
									console.log(datasObj);
									console.log(transaction);
									var noty;
									$.ajax({
										url: url,
										data: { 
											datas: datasObj,
											payment: payObj,
											gate: "<?= $gate ?>",
											ref: uid
										},
										dataType: "json",
										method: "POST",
										beforeSend: function (xhrObj) {
											var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
											noty = notify("", "dark", "", message, "", false);												
										},
										success:function(data) {
											noty.close();
											notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
											if(data.success) {
												window.onbeforeunload = function () { }
												location.href = data.link
											}
										}
									});
								}
								else {
									notify("", 'danger', "", "<?= $_SESSION["ERROR_ON_PAYMENT"] ?> <br />State: " + transaction.status + "<br />Err:" + transaction.statusMessage, "");
								}
							});						
						});
					}
					else {
						$("#frmPayment").attr("action", 'core/actions/_save/__newCheckout.php');
						$("#frmPayment").empty();
						$("#frmPayment").append('<input type="hidden" name="serviceData" value="' + $("#frmPayment").serialize() + '">');
						notify("", 'danger', "", data.message, "");
						var kushki = new KushkiCheckout({
							form: "frmPayment",
							merchant_id: "<?= $merchId ?>",
							amount: $("#hfPRICE").val(),
							currency: "COP", 
							is_subscription: false,
							inTestEnvironment: true,
							regional: false 
						});					
						$("#divPayment").modal("toggle");			
					}
				}
			}
		});
	}
	
	function save(id) {
		if($("#hfZonReq_" + id).val() == "" && $("#txtZONE_REQUEST_" + id).val() == "") {
			notify("", 'danger', "", "<?= $_SESSION["ZONE_REQUEST_NOT_DEFINED"] ?>", "");
			$("#txtZONE_REQUEST_" + id).focus();
			return false;
		}
		if($("#hfZonDel_" + id).val() == "" && $("#txtZONE_DELIVER_" + id).val() == "") {
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
				method: "POST",
				data: { strModel: datas },
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					console.log(data);
					var objPay = typeof data.data_payment !== 'undefined' ?  data.data_payment : {};
					$("#hfSaved_" + data.counter).val(data.success);
					$("#hfPayed_" + data.counter).val(data.payment);
					$("#hfObjPay_" + data.counter).val(JSON.stringify(objPay));
					$("#btnSave_" + data.counter).attr("disabled", data.success);
					$("#txtZONE_REQUEST_" + data.counter).attr("disabled", data.success);
					$("#txtZONE_DELIVER_" + data.counter).attr("disabled", data.success);
					$("#btnLocate_" + data.counter).attr("disabled", data.success);
					$("#btnDelete_" + data.counter).attr("disabled", data.success);
					var enpay = true;
					for(i=0;i<parseInt($("#hfTotalRegsToComplete").val());i++) {
						if($("#hfPayed_" + i).val() == "false") {
							enpay = false;
							break;
						}
					}
					$("#btnPayment").attr("disabled", enpay);
				}
			});
		});
		$("#divActivateModal").modal("toggle");			
	}
	window.onbeforeunload = function(e) {
		return "Si sale de la página puede perder información ya completada. ¿Está seguro?";
	};
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
