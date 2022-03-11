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
	
	$result = checkSession("payments.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/configuration.php");
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_TOKEN");
	$urlToken = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_CHARGE");
	$urlCharge = $conf->verifyValue();
	
	require_once("core/classes/quota.php");
	
	require_once("core/classes/configuration.php");
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_TOKEN");
	$urlToken = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_CHARGE");
	$urlCharge = $conf->verifyValue();
	
	$buttonText = $action == "new" ? $_SESSION["PAY"] : $_SESSION["ADD_FUNDS"];
	
	$gate = $conf->verifyValue("PAYMENT_GATEWAY");
	$accTok = 0;
	$err = 0;
	
	//Verifica la pasarela
	if($gate == "WOMPI") {
		//Libreria requerida
		require_once("core/actions/_save/__wompiGatewayFunctions.php");

		$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
		$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
		
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
							<h1 class="m-0 text-dark"><i class="fa fa-money-check-dollar"></i> <?= $_SESSION["MENU_27"] ?></h1>
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
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tablePayment" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="1%">id</th>
											<th width="20%"><?= $_SESSION["PAYMENT_TABLE_TITLE_9"] ?></th>
											<th width="25%"><?= $_SESSION["PAYMENT_TABLE_TITLE_10"] ?></th>
											<th width="20%"><?= $_SESSION["PAYMENT_TABLE_TITLE_11"] ?></th>
											<th width="10%"><?= $_SESSION["PAYMENT_TABLE_TITLE_12"] ?></th>
											<th width="15%"><?= $_SESSION["PAYMENT_TABLE_TITLE_13"] ?></th>
											<th width="1%">Icon</th>
											<th width="10%"><?= $_SESSION["PAYMENT_TABLE_TITLE_14"] ?></th>
											<th width="1%">Notif</th>
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
	$title = $_SESSION["QUOTA_USED_SERVICES"];
	$icon = "<i class=\"fa fa-motorcycle\"></i>";
	$isTable = true;
	$tableColumns = [$_SESSION["QUOTA_USED_TABLE_TITLE_1"], $_SESSION["QUOTA_USED_TABLE_TITLE_2"], $_SESSION["QUOTA_USED_TABLE_TITLE_3"], $_SESSION["QUOTA_USED_TABLE_TITLE_4"], $_SESSION["QUOTA_USED_TABLE_TITLE_5"], $_SESSION["QUOTA_USED_TABLE_TITLE_6"], $_SESSION["QUOTA_USED_TABLE_TITLE_7"], $_SESSION["QUOTA_USED_TABLE_TITLE_8"]];
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
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	var fields = ["PAYMENT_ID", "CLIENT_NAME", "DELIVER_ADDRESS", "REQUESTED_BY", "REQUESTED_DATE", "DELIVER_TO", "SERVICE_STATE_NAME", "PRICE", "ICON"];
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
        var table = $('#tablePayment').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "payment",
					"field": fields.join(),
					"options": "<?= $_SESSION["vtappcorp_referenceid"] ?>"
				}
			},
			"fnInitComplete": function(oSettings, json) {
				$('[data-toggle="tooltip"]').tooltip();		
			},			
			"columns": [
				{ "data": "PAYMENT_ID", "searchable": false, "visible": false, "responsivePriority": 10 },
				{ "data": "CLIENT_NAME", "responsivePriority": 2 },
				{ "data": "DELIVER_ADDRESS", "sClass": "text-center" , "responsivePriority": 4 },
				{ "data": "REQUESTED_BY", "sClass": "text-center" , "responsivePriority": 5 },
				{ "data": "REQUESTED_DATE", "sClass": "text-center" , "responsivePriority": 5 },
				{ "data": "DELIVER_TO", 
							"responsivePriority": 2, 
							"render": function ( data, type, item ) {
								var text = "<button data-toggle='tooltip' data-placement='top' data-original-title='" + item.SERVICE_STATE_NAME + "' type='button' class='btn btn-light btn-block text-left' title='" + item.SERVICE_STATE_NAME + "'><i class='" + item.ICON + "'></i>&nbsp;" + data + "</button>";
								return text;
							}  
				},
				{ "data": "SERVICE_STATE_NAME", "responsivePriority": 8, "visible": false },
				{ "data": "PRICE", 
							"sClass": "text-center" , 
							"responsivePriority": 6, 
							"render": function (data, type, row) { 
								if(data < 10)
									return FormatNumber(data,0) + " notif"; 
								else 
									return "$" + FormatNumber(data,2); 
							} 
				},
				{ "data": "ICON", "searchable": false, "responsivePriority": 4, "sortable": false, "visible": false }
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
		$('input[aria-controls="tablePayment"').unbind();
		$('input[aria-controls="tablePayment"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function show(id, action) {
		if(action != "new")
			location.href = "quota-management.php?id=" + id + "&action=" + action;
		else 
			location.href = "quota-management.php?action=" + action;
	}
	function list(id) {
		$('#divTableModal').on('show.bs.modal', function (e) {
			if($.fn.DataTable.isDataTable('#tableOnModal')) {
				$('#tableOnModal').DataTable().clear().draw();
				$('#tableOnModal').DataTable().destroy();
			}
			var tableQU = $('#tableOnModal').DataTable({
				"ajax": { 
					"url": "core/actions/_load/__loadUsedQuota.php",
					"data": {
						"id": id
					}
				},
				"columns": [
					{ "data": "id", "searchable": false, "responsivePriority": 8 },
					{ "data": "requested_by", "responsivePriority": 7 },
					{ "data": "deliver_to", "responsivePriority": 1, "render": function (data, type, row) { return "<a href=\"service-log.php?id=" + row.id + "\" target=\"_blank\" title=\"<?= $_SESSION["SERVICE_DETAILS"] ?>\">" + data + "</a>";} },
					{ "data": "deliver_address", "responsivePriority": 2 },
					{ "data": "payment_value", "sClass": "text-center", "responsivePriority": 3, "render": function (data, type, row) { return FormatNumber(data,2); } },
					{ "data": "payed_on", "sClass": "text-center", "responsivePriority": 5 },
					{ "data": "user_id", "sClass": "text-center", "responsivePriority": 6 },
					{ "data": "service_state_name", "responsivePriority": 4,
							"render": function ( data, type, item ) {
								var text = "<button type='button' class='btn btn-light btn-block text-left' title='" + data + "'><i class='fa " + item.icon + "'></i>&nbsp;" + data + "</button>";
								return text;
							}  
					}
				],				
				"autoWidth": false,
				"processing": true,
				"serverSide": false,
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
		});		
		$("#divTableModal").modal("toggle");			
	}
	
	function payment(id, amont) {
		var noty;
		var title = "<?= $_SESSION["GO_TO_PAY"] ?>";
		var url = "core/actions/_save/__processPayment.php";
		var bodyHtml = "<?= $_SESSION["MSG_PROCESS_PAYMENT"] ?> ";
		bodyHtml = bodyHtml.replace("{0}", "$" + FormatNumber(amont,2));
		if(<?= ($accTok && !$err) ?>) {
			var link = "<?= str_replace("{0}", $accTokData->data->presigned_acceptance->permalink, $_SESSION["ACCEPTANCE_TOKEN_TEXT"]) ?>";
			bodyHtml += "<br><br><div class=\"form-check\"><input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"chkAccToken\" name=\"chkAccToken\"><label class=\"form-check-label\" for=\"chkAccToken\">" + link + "</label></div>";
		}
		$("#spanTitle").html(title);
		$("#spanTitleName").html("");
		$("#modalBody").html(bodyHtml);
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			if(<?= ($accTok && !$err) ?>) {
				if(!$("#chkAccToken").is(':checked')) {  
					notify("", "danger", "", "<?= $_SESSION["ERROR_ACCEPT_TOKEN_TEXT"] ?>", "");
					return false;
				}  				
			}
			var noty;
			$.ajax({
				url: url,
				data: { 
					id: id,
					gate: "<?= $gate ?>",
					token: "<?= $accTokData->data->presigned_acceptance->acceptance_token ?>"
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					if(!data.success) {
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					}
					else {
						if(data.continue) {
							var id = data.message.id;
							var day = data.message.dex.split('/');
							var amount = parseFloat(data.message.amount);
							var diferred = parseInt(data.message.df)
							var objCard = {
								name: data.message.cn,
								number: data.message.cc.split(' ').join(''),
								expiryMonth: day[0],
								expiryYear: day[1],
								cvv: data.message.cv
							};
							var objData = {
								card: objCard,
								totalAmount: amount,
								currency: "COP"
							};
							var settings = {
								"async": true,
								"crossDomain": true,
								"url": "<?= $urlToken ?>",
								"method": "POST",
								"headers": {
									"public-merchant-id": "<?= $merchId ?>",
									"content-type": "application/json"
								},
								"processData": false,
								"data": JSON.stringify(objData),
								"dataType": "json",
								"beforeSend": function (xhrObj) {
									var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["PROCESSING_PAYMENT"] ?>";
									noty = notify("", "dark", "", message, "", false);												
								}
							}

							$.ajax(settings).done(function (response) {
								if(response.token != "") {
									var token = response.token;
									var objAmount = {
										subtotalIva: 0,
										subtotalIva0: amount,
										ice: 0,
										iva: 0,
										currency: "COP"
									};
									var objDeferred = {
										graceMonths: "00",
										creditType: "01",
										months: diferred
									};
									var objMeta = {
										contractID: id
									};
									var objData = {
										token: token,
										amount: objAmount,
										deferred: objDeferred,
										metadata: objMeta,
										fullResponse: true
									};
								}
								var settings = {
									"async": true,
									"crossDomain": true,
									"url": "<?= $urlCharge ?>",
									"method": "POST",
									"headers": {
										"private-merchant-id": "<?= $merchId ?>",
										"content-type": "application/json"
									},
									"processData": false,
									"data": JSON.stringify(objData),
									"dataType": "json",
									"error": function (jqXHR, textStatus) {
										var response = jqXHR.responseJSON;
										var msg = "<?= $_SESSION["ERROR_ON_PAYMENT"] ?><br />" + response.code + ": " + response.message;
										notify("", "danger", "", msg, "");
									},
									"always": function() {
										noty.close();
									}				
								}
								$.ajax(settings).done(function (response) {
									if(response.ticketNumber != "") {
										$.ajax({
											url: "core/actions/_save/__newPayment.php",
											data: { 
												strModel: JSON.stringify(response),
												payment: "true"
											},
											dataType: "json",
											beforeSend: function (xhrObj) {
												var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
												noty = notify("", "dark", "", message, "", false);												
											},
											success:function(data){
												noty.close();
												notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
												if(data.success)
													location.href = data.link;
											}
										});
									}
									else {
										var msg = "<?= $_SESSION["ERROR_ON_PAYMENT"] ?><br />" + response.code + ": " + response.message;
										notify("", "danger", "", msg, "");
									}
								});
							});
						}
						else {
							notify("", 'info', "", data.message, "");
							setTimeout(function() { location.href = data.link; }, 5000);
						}
					}
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
