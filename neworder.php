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

	require_once("core/classes/order_detail.php");
	require_once("core/classes/configuration.php");
	
	$order = new order_detail();
	$conf = new configuration("ORDER_PREFIX");
	$prefix =  $conf->verifyValue();	

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
							<h1 class="m-0 text-dark"><i class="fa fa-file-text-o"></i> <?= $_SESSION["MENU_NEW"] . " " . $_SESSION["ORDER"] ?></h1>
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
								<h5 class="card-title">
									<?= $_SESSION["BASIC_DATA"] ?>
								</h5>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
<?= $order->_order->showForm("new") ?>
								<div class="form-group">
									<label><?= $_SESSION["TOTAL_ORDER"] ?></label>						
									<div class="input-group mb-2">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fa fa-money-bill-1"></i></div>
										</div>
										<input id="txtTOTALORDER" class="form-control" type="text" name="txtTOTALORDER" readonly="readonly" value="$ 0">
									</div>
								</div>
								<input id="hfMovement" type="hidden" name="hfMovement" value="2" />
							</div>
							<div class="card-footer">
								<div class="btn-group float-right">
									<button id="btnNewItem" name="btnNewItem" class="btn btn-warning" onclick="newItem();">
										<i class="fa fa-plus-circle"></i> 
										<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] . " " . $_SESSION["ITEM"] ?></div>
									</button>							
									<button class="btn btn-success float-right" id="btnSaveOutput" name="btnSaveOutput">
										<i class="fa fa-floppy-o"></i>
										<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["SAVE_CHANGES"] ?></div>
									</button>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<table id="tableMovement" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th width="5%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_1"] ?></th>
											<th width="20%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_2"] ?></th>
											<th width="10%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_3"] ?></th>
											<th width="15%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_4"] ?></th>
											<th width="10%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_5"] ?></th>
											<th width="15%"><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?></th>
											<th width="20%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>										
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["ORDER"];
	$icon = "<i class=\"fa fa-file-text-o\"></i>";
	$noEdit = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
?>
	<!-- Invoice area End-->

	<!-- Modal New Item -->
	<div class="modal fade" id="divNewItem" role="dialog" aria-labelledby="h5ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalLabel"><i class="fa fa-plus-square-o"></i> <span id="spAction"></span> <?= $_SESSION["NEW_ITEM"] ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>					
				</div>
				<div class="modal-body" id="itemForm">
					<form id="frmNewItem" name="frmNewItem" role="form">
						<div class="form-group">
							<label><?= $_SESSION["PRODUCT"] ?> <span class="required">*</span></label>
							<select class="form-control" id="cbProduct" name="cbProduct" style="width: 100%;">
<?= $order->product->showOptionList(8,0,0,true) ?>								
							</select>
							<input id="hfID" type="hidden" name="hfID" value="" />
							<input id="hfCODE" type="hidden" name="hfCODE" value="" />
							<input id="hfUNIT" type="hidden" name="hfUNIT" value="" />
							<input id="txtEXISTENCE" type="hidden" name="txtEXISTENCE" value="0">
						</div>
						<div class="form-group">
							<label><?= $_SESSION["PRODUCT_TABLE_TITLE_6"] ?> <span class="required">*</span></label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<div class="input-group-text"><span id="unitDiv"></span></div>
								</div>
								<input id="txtQUANTITY" class="form-control" placeholder="<?= $_SESSION["PRODUCT_TABLE_TITLE_6"] ?>" type="number" name="txtQUANTITY" required="required" value="0">
							</div>
						</div>
						<div class="form-group">
							<label><?= $_SESSION["PRODUCT_TABLE_TITLE_7"] ?></label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-money-bill-1"></i></span>
								</div>
								<input id="txtPRICE" class="form-control" placeholder="<?= $_SESSION["PRODUCT_TABLE_TITLE_7"] ?>" type="text" name="txtPRICE" readonly="readonly" value="">
								<div class="input-group-append">
									<span class="input-group-text" id="money-bill-1TypePrice"></span>
								</div>
							</div>
						</div>						
						<div class="form-group">
							<label><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?></label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-money-bill-1"></i></span>
								</div>
								<input id="txtTOTAL" class="form-control" placeholder="<?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?>" type="text" name="txtTOTAL" readonly="readonly" value="">
								<div class="input-group-append">
									<span class="input-group-text" id="money-bill-1TypeTotal"></span>
								</div>
							</div>
						</div>						
						<input type="hidden" name="hfFactor" id="hfFactor" value="">
						<input type="hidden" name="hfmoney-bill-1Factor" id="hfmoney-bill-1Factor" value="">
					</form>				
				</div>
				<div class="modal-footer">
					<div class="btn-group">
						<button type="button" class="btn btn-danger" data-dismiss="modal">
							<i class="fa fa-times-circle"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["CLOSE"] ?></span>
						</button>
						<button type="button" class="btn btn-primary" id="btnSave" name="btnSave">
							<i class="fa fa-plus-circle"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["ADD"] ?></span>
						</button>
					</div>
					<input type="hidden" name="hfAction" id="hfAction" value="">
					<input type="hidden" name="hfRow" id="hfRow" value="">
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="h5ModalScanLabel" id="divScanCamera">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalScanLabel"><i class="fa fa-barcode"></i> <?= $_SESSION["READ_QR_CODE"] ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>					
				</div>
				<div class="modal-body">
					<div class="card">
						<div class="card-body" id="itemScan">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
					<input type="hidden" id="hfIdScan" name="hfIdScan" value="" />
				</div>
			</div>
		</div>
	</div>	

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
    <!-- mobile-detect JS -->
    <script src="plugins/mobile-detect/mobile-detect.js"></script>
	<!-- date-picker -->
	<script src="plugins/moment/moment.min.js"></script>
    <!-- datapicker JS -->
    <script src="plugins/datapicker/bootstrap-datepicker.js"></script>
	<!-- Select2 -->
	<script src="plugins/select2/select2.full.js"></script>
<?
	$lang = "en";
	if($_SESSION["LANGUAGE"] != 1) {
		$lang = $_SESSION["LANGUAGE"] == 2 ? "es" : "de";
?>
    <script src="plugins/datapicker/locale/bootstrap-datepicker.<?= $_SESSION["LANGUAGE"] ?>.js"></script>
    <script src="plugins/select2/i18n/<?= $_SESSION["LANGUAGE"] ?>.js"></script>
<?
	}
?>		
	
    <script>
	var counter = 0;
	var table;
	var msg1 = "<?= $_SESSION["SEARCHING_FOR_QR_CODE"] ?>",
		msg2 = "<?= $_SESSION["PRICE_WILL_BE_CALCULATED"] ?>",
		msg3 = "<?= $_SESSION[""] ?>",
		msg4 = "<?= $_SESSION[""] ?>",
		msg5 = "<?= $_SESSION[""] ?>",
		msg6 = "<?= $_SESSION[""] ?>",
		msg7 = "<?= $_SESSION["CONVERSION_FACTOR_ENABLED"] ?>",
		msg8 = "<?= $_SESSION["money-bill-1_CONVERT_REQUIRED"] ?>",
		msg9 = "<?= $_SESSION["PRIZE_NOT_REGISTERED"] ?>";
	
	
	$(document).ready(function() {
		$('.form-control.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true,
<?= ($_SESSION["vtappcorp_useraccessid"] > 50) ? "" : "startDate: \"0d\"," ?>
			endDate: "0d",
			format: "yyyy-mm-dd"
		});
		$("#cbProduct").select2();
        table = $('#tableMovement').DataTable({
            "responsive": true,
			"columnDefs": [
				{ responsivePriority: 1, targets: -1 },
				{ responsivePriority: 2, targets: 1 },
				{ responsivePriority: 3, targets: 2 },
				{ responsivePriority: 4, targets: 4 },
				{ responsivePriority: 2, targets: 5 }
			],
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
		$('input[aria-controls="tableMovement"').unbind();
		$('input[aria-controls="tableMovement"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
		$("#btnSave").on("click", function(e) {
			var quant = parseFloat($("#txtQUANTITY").val());			
			if(isNaN(quant)) { quant = 0; }
			if(quant <= 0) {
				notify("", "danger", "", "<?= $_SESSION["QUANTITY_GREATER_THAN"] ?>", "");
				$("#txtQUANTITY").focus();
				return false;
			}
			var form = document.getElementById('frmNewItem');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = "<?= $_SESSION["ADD"] . " " . $_SESSION["ITEM"] ?>";
            $frm = $("#frmNewItem");
			var datas = JSON.stringify($frm.serializeObject());
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var buttons = "<div class=\"btn-toolbar\" role=\"toolbar\">";
				var bagdeSpan = "",
					badgeSpan2 = "";
				if($("#hfAction").val() == "new")
					counter++;
				var ids = ($("#hfAction").val() == "new") ? counter : parseInt($("#hfRow").val());
				buttons += "<div class=\"btn-group\">";
				buttons += "<button type=\"button\" class=\"btn btn-default\" title=\"<?= $_SESSION["VIEW"] ?>\" onclick=\"show(" + ids + ",'view');\"><i class=\"fa fa-eye\"></i></button>";
				buttons += "<button type=\"button\" class=\"btn btn-default\" title=\"<?= $_SESSION["EDIT"] ?>\" onclick=\"show(" + ids + ",'edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
				buttons += "<button type=\"button\" class=\"btn btn-default\" title=\"<?= $_SESSION["DELETE"] ?>\" onclick=\"deleteItem(" + ids  + ");\"><i class=\"fa fa-trash\"></i></button>";
				buttons += "<input type=\"hidden\" name=\"hfRow_" + ids + "\" id=\"hfRow_" + ids + "\" value='" + datas + "' />";
				buttons += "</div></div>";
				if(!isNaN(parseFloat($("#hfFactor").val())) && parseFloat($("#hfFactor").val()) != 1)
					bagdeSpan = " <span class=\"badge bg-warning\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"<?= $_SESSION["CONVERSION_MSG"] ?>\"><?= $_SESSION["CONVERSION_ABBRV"]?></span>";
				if(!isNaN(parseFloat($("#hfmoney-bill-1Factor").val())) && parseFloat($("#hfmoney-bill-1Factor").val()) != 1)
					bagdeSpan = " <span class=\"badge bg-primary\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"<?= $_SESSION["TRM_MSG"] ?>\"><?= $_SESSION["TRM_ABBRV"]?></span>";
				var text = $("#cbProduct option:selected").text() + bagdeSpan + badgeSpan2;
				if(($("#hfAction").val() == "new")) {
					table.row.add( [
						ids,
						$("#hfCODE").val(),
						text,
						$("#hfUNIT").val(),
						"$ " + FormatNumber($("#txtPRICE").val(),2),
						$("#txtQUANTITY").val(),
						"$ " + FormatNumber($("#txtTOTAL").val(),2),
						buttons
					] ).draw( false );
				}
				else {
					var id = (ids - 1);
					table.row(id).data( [
						ids,
						$("#hfCODE").val(),
						text,
						$("#hfUNIT").val(),
						"$ " + FormatNumber($("#txtPRICE").val(),2),
						$("#txtQUANTITY").val(),
						"$ " + FormatNumber($("#txtTOTAL").val(),2),
						buttons
					] ).draw( false );
				}
				var totalorder = 0;
				$('[id^=hfRow_]').each(function() {
					var data = JSON.parse($(this).val());
					var val = parseFloat(data.txtTOTAL);
					if(isNaN(val)) { val = 0; }
					totalorder += val;
				});
				$('[data-toggle="tooltip"]').tooltip();		
				$('#txtTOTALORDER').val("$ " + FormatNumber(totalorder, 2));
				$('#divNewItem').modal('toggle');
			});
			$("#divActivateModal").modal("toggle");			
		});
		$('#txtINTERNAL_NUMBER').on('input',function(e){
			var prefix = "<?= $prefix ?>";
			var val = $(this).val();
			if(val.indexOf(prefix) == -1) {
				$(this).val(prefix + val);
			}
		});
		$("#btnSaveOutput").click(function() {
			if($("#txtINTERNAL_NUMBER").val() == "") {
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				$("#txtINTERNAL_NUMBER").focus();
				return false;
			}
			if($("#txtREGISTERED_ON").val() == "") {
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
				$("#txtREGISTERED_ON").focus();
				return false;
			}
			if($("#cbEmployee").val() == "") {
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
				$("#cbEmployee").focus();
				return false;
			}
			var count = 0;
			var obj = [];
			$('[id^=hfRow_]').each(function() {
				var data = JSON.parse($(this).val());
				var objData = {
					Id: data.hfID,
					Code: data.hfCODE,
					Unit: data.hfUNIT,
					Existence: data.txtEXISTENCE,
					Quantity: data.txtQUANTITY,
					Price: data.txtPRICE,
					Total: data.txtTOTAL,
					Factor: data.hfFactor,
					money-bill-1Factor: data.hfmoney-bill-1Factor,
					Counter: (count + 1)
				};
				obj.push(objData);
				count++;
			});			
			if(count == 0) {
				notify("", "danger", "", "<?= $_SESSION["NO_ITEMS_TO_ADD"] ?>", "");							
				return false;
			}
			var datas = { 
				txtINTERNAL_NUMBER: $("#txtINTERNAL_NUMBER").val(),
				txtREGISTERED_ON: $("#txtREGISTERED_ON").val(),
				cbClient: $("#cbClient").val(),
				hfMovement: $("#hfMovement").val(),
				cbEmployee: $("#cbEmployee").val(),
				Items: obj
			}
			var title = "<?= $_SESSION["ADD_NEW_OUTPUT"] ?>";
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: "core/actions/_save/__newOrder.php",
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
		$("#txtINTERNAL_NUMBER").focus();
	});
	function newItem() {
		clearItems();
		$("#hfAction").val("new");
		$("#spAction").html("<?= $_SESSION["ADD"] ?>");
		$("#hfRow").val("");
		$('#divNewItem').modal('toggle');
		$('#cbProduct').on('select2:select', function (e, clickedIndex, isSelected, previousValue) {
			var selected = e.params.data;
			var data = $('#cbProduct option[value="' + selected.id + '"]').data();
			var focus = "txtQUANTITY";
			data.productPrice = parseFloat(data.productPrice);
			data.factormoney-bill-1 = parseFloat(data.factormoney-bill-1);
			if(data.factor != 1) {
				notify("", 'warning', "", msg7, "");
			}
			if(data.factormoney-bill-1 != 1) {
				notify("", 'warning', "", msg8, "");
			}
			if(data.productPrice == 0) {
				notify("", 'warning', "", msg2, "");
			}
			$("#btnSave").attr("disabled", data.productQuantity == 0);
			$("#money-bill-1TypePrice").html(data.productmoney-bill-1type);
			$("#money-bill-1TypeTotal").html(data.factormoney-bill-1conversion);
			$("#unitDiv").html(data.unit);
			$("#txtPRICE").val(data.productPrice);
			$("#txtEXISTENCE").val(data.productQuantity);
			$("#hfCODE").val(data.code);
			$("#hfID").val($(this).val());
			$("#hfFactor").val(data.factor);
			$("#hfmoney-bill-1Factor").val(data.factormoney-bill-1);
			$("#txtQUANTITY").css("background-color", (data.factor != 1 ? "LightYellow" : ""));
			$("#hfUNIT").val(data.unit);
			$('#txtQUANTITY').val("");
			$("#txtTOTAL").val(0 * data.productPrice * data.factormoney-bill-1);
			$('#' + focus).focus();
		});		
		$('#txtQUANTITY').on('input',function(e){
			var prize = parseFloat($("#txtPRICE").val());
			var existence = parseFloat($("#txtEXISTENCE").val());
			var quant = parseFloat($(this).val());
			var factor = parseFloat($("#hfFactor").val());
			var factormoney-bill-1 = parseFloat($("#hfmoney-bill-1Factor").val());
			if(isNaN(prize)) { prize = 0; }
			if(isNaN(factor)) { factor = 1; }
			if(isNaN(factormoney-bill-1)) { factormoney-bill-1 = 1; }
			if(isNaN(existence)) { existence = 0; }
			$("#txtTOTAL").val(quant * factor * prize * factormoney-bill-1);
		});		
	}
	function clearItems() {
		$("#money-bill-1TypePrice").html("");
		$("#money-bill-1TypeTotal").html("");
		$("#txtPRICE").val(0);
		$("#txtEXISTENCE").val(0);
		$("#txtQUANTITY").val(0);
		$("#txtTOTAL").val(0);
		$("#unitDiv").html("");
		$("#hfCODE").val("");
		$("#hfID").val("");
		$("#hfFactor").val("");
		$("#hfmoney-bill-1Factor").val("");
		$("#txtQUANTITY").css("background-color", "");
		$("#hfUNIT").val("");
		$('#cbProduct').val("");
		$('#cbProduct').trigger("change");
		$('#cbProduct').focus();
	}
	function show(id, action) {
		var info = $("#hfRow_" + id).val();
		var data = JSON.parse(info);
		var disabled = action == "view";
		$("#btnScan").attr("disabled", disabled);
		$("#btnLoadScan").attr("disabled", disabled);
		$("#btnSave").attr("disabled", disabled);
		$("#spAction").html(action == "view" ? "<?= $_SESSION["VIEW"] ?>" : "<?= $_SESSION["EDIT"] ?>");
		$("#txtQUANTITY").attr("disabled", disabled);
		$("#btnSave").attr("disabled", disabled);
		$('#cbProduct').val(data.cbProduct);
		$('#cbProduct').trigger('change');
		$("#cbProduct").attr("disabled", disabled);
		$("#txtQUANTITY").val(data.txtQUANTITY);
		$("#hfRow").val(id);
		$("#hfAction").val(action);
		$('#divNewItem').modal('toggle');
		if(action == "edit")
			$('#txtQUANTITY').focus();
		else
			$('#cbProduct').focus();
	}
	function deleteItem(id) {
		$("#spanTitle").html("<?= "Información" ?>");
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["SORRY_NOT_IMPLEMENTED"] ?>");
		$("#btnActivate").html("<?= $_SESSION["ACCEPT"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			$("#btnActivate").html("<?= $_SESSION["SAVE_CHANGES"] ?>");
			$("#divActivateModal").modal("toggle");
		});
		$("#divActivateModal").modal("toggle");		
	}
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>