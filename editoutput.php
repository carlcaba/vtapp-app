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
	
	$applied = $move->isApplied() ? "disabled=\"disabled\"" : "";
	$items = $move->getTotalItems();

	$conf = new configuration("ORDER_PREFIX");
	$prefix =  $conf->verifyValue();	
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
							<h1 class="m-0 text-dark"><i class="fa fa-pencil-square"></i> <?= $_SESSION["MENU_EDIT"] . " " . $_SESSION["OUTPUT"] ?></h1>
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
<?
	$form = $move->movement->showForm("edit",5,true);
	echo $form["form"];
?>
								<div class="form-group">
									<label><?= $_SESSION["TOTAL_ORDER"] ?></label>						
									<div class="input-group mb-2">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fa fa-money"></i></div>
										</div>
										<input id="txtTOTALORDER" class="form-control" type="text" name="txtTOTALORDER" readonly="readonly" value="$ 0">
									</div>
								</div>
								<input id="hfMovement" type="hidden" name="hfMovement" value="2" />
								<input id="hfIdOutput" type="hidden" name="hfIdOutput" value="<?= $id ?>" />
							</div>
							<div class="card-footer">
								<div class="btn-group float-right">
									<button id="btnNewItem" name="btnNewItem" class="btn btn-warning" onclick="newItem();">
										<i class="fa fa-plus-square"></i> 
										<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["ADD"] . " " . $_SESSION["MENU_NEW"] . " " . $_SESSION["ITEM"] ?></div>
									</button>
									<button class="btn btn-danger" id="btnApplyStock" name="btnApplyStock" <?= $applied ?> onclick="applyToStock();">
										<i class="fa fa-exclamation-circle"></i>
										<div class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["APPLY_STOCK"] ?></div>
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
<?= $form["table"] ?>									
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
	$title = $_SESSION["OUTPUT"];
	$icon = "<i class=\"fa fa-pencil-square\"></i>";
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
<?= $move->product->showOptionList(8,0,0,true) ?>								
							</select>
							<input id="hfID" type="hidden" name="hfID" value="" />
							<input id="hfCODE" type="hidden" name="hfCODE" value="" />
							<input id="hfUNIT" type="hidden" name="hfUNIT" value="" />
						</div>
						<div class="form-group">
							<label><?= $_SESSION["EXISTENCE"] ?></label>
							<input id="txtEXISTENCE" class="form-control" placeholder="<?= $_SESSION["EXISTENCE"] ?>" type="number" name="txtEXISTENCE" readonly="readonly" value="0">
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
									<span class="input-group-text"><i class="fa fa-money"></i></span>
								</div>
								<input id="txtPRICE" class="form-control" placeholder="<?= $_SESSION["PRODUCT_TABLE_TITLE_7"] ?>" type="text" name="txtPRICE" readonly="readonly" value="">
								<div class="input-group-append">
									<span class="input-group-text" id="moneyTypePrice"></span>
								</div>
							</div>
						</div>						
						<div class="form-group">
							<label><?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?></label>
							<div class="input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-money"></i></span>
								</div>
								<input id="txtTOTAL" class="form-control" placeholder="<?= $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] ?>" type="text" name="txtTOTAL" readonly="readonly" value="">
								<div class="input-group-append">
									<span class="input-group-text" id="moneyTypeTotal"></span>
								</div>
							</div>
						</div>						
						<input type="hidden" name="hfFactor" id="hfFactor" value="">
						<input type="hidden" name="hfMoneyFactor" id="hfMoneyFactor" value="">
						<input type="hidden" name="hfIdMove" id="hfIdMove" value="<?= $id ?>">
						<input type="hidden" name="hfIdMoveDetail" id="hfIdMoveDetail" value="">
					</form>				
				</div>
				<div class="modal-footer">
					<div class="btn-group">
						<button type="button" class="btn btn-danger" data-dismiss="modal">
							<i class="fa fa-times-circle"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["CLOSE"] ?></span>
						</button>
						<button type="button" class="btn btn-default" id="btnScan" name="btScan" ttitle="<?= $_SESSION["SCAN_QR_CODE"] ?>" onclick="loadScanCamera();">
							<i class="fa fa-barcode"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["SCAN_QR_CODE"] ?></span>
						</button>
						<button type="button" class="btn btn-default" id="btnLoadScan" name="btLoadScan" title="<?= $_SESSION["LOAD_IMAGE"] ?>" onclick="loadScanCamera(true);">
							<i class="fa fa-folder-open"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["LOAD_IMAGE"] ?></span>
						</button>
						<button type="button" class="btn btn-primary" id="btnSave" name="btnSave">
							<i class="fa fa-plus-circle"></i>
							<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block" id="btnSaveLabel" name="btnSaveLabel"><?= $_SESSION["ADD"] ?></span>
						</button>
					</div>
					<input type="hidden" name="hfAction" id="hfAction" value="">
					<input type="hidden" name="hfRow" id="hfRow" value="<?= $items ?>">
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
	var counter = parseInt($("#hfRow").val());
	if(isNaN(counter)) counter = 0;
	var table;
	var msg1 = "<?= $_SESSION["SEARCHING_FOR_QR_CODE"] ?>",
		msg2 = "<?= $_SESSION["QR_CODE_NOT_VALID"] ?>",
		msg3 = "<?= $_SESSION["QR_CODE_NOT_FOUND"] ?>",
		msg4 = "<?= $_SESSION["QR_CODE_VALID"] ?>",
		msg5 = "<?= $_SESSION["NOT_ENOUGH_EXISTENCE"] ?>",
		msg6 = "<?= $_SESSION["CAMERAS"] ?>",
		msg7 = "<?= $_SESSION["CONVERSION_FACTOR_ENABLED"] ?>",
		msg8 = "<?= $_SESSION["MONEY_CONVERT_REQUIRED"] ?>";
	
	
	$(document).ready(function() {
		$('.form-control.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true,
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
			var preDatas = $frm.serializeObject();
			if($("#hfAction").val() == "edit")
				preDatas.cbProduct = $("#cbProduct").val();
			var datas = JSON.stringify(preDatas);
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
				buttons += "<button type=\"button\" class=\"btn btn-default\" title=\"<?= $_SESSION["EDIT"] ?>\" onclick=\"show(" + ids + ",'edit');\"><i class=\"fa fa-pencil-square-o\"></i></button>";
				buttons += "<button type=\"button\" class=\"btn btn-default\" title=\"<?= $_SESSION["DELETE"] ?>\" onclick=\"deleteItem(" + ids  + ");\"><i class=\"fa fa-trash\"></i></button>";
				buttons += "<input type=\"hidden\" name=\"hfRow_" + ids + "\" id=\"hfRow_" + ids + "\" value='" + datas + "' />";
				buttons += "</div></div>";
				if(!isNaN(parseFloat($("#hfFactor").val())) && parseFloat($("#hfFactor").val()) != 1)
					bagdeSpan = " <span class=\"badge bg-warning\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"<?= $_SESSION["CONVERSION_MSG"] ?>\"><?= $_SESSION["CONVERSION_ABBRV"]?></span>";
				if(!isNaN(parseFloat($("#hfMoneyFactor").val())) && parseFloat($("#hfMoneyFactor").val()) != 1)
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
		$("#btnSaveOutput").click(function() {
			if($("#txtINTERNAL_NUMBER").val() == "") {
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				$("#txtINTERNAL_NUMBER").focus();
				return false;
			}
			if($("#txtMOVE_DATE").val() == "") {
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
				$("#txtMOVE_DATE").focus();
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
					MoneyFactor: data.hfMoneyFactor,
					IdMove: data.hfIdMove,
					IdDetail: data.hfIdMoveDetail,
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
				txtID: $("#hfIdOutput").val(),
				txtINTERNAL_NUMBER: $("#txtINTERNAL_NUMBER").val(),
				txtMOVE_DATE: $("#txtMOVE_DATE").val(),
				hfMovement: $("#hfMovement").val(),
				cbEmployee: $("#cbEmployee").val(),
				Items: obj
			}
			var title = "<?= $_SESSION["EDIT_OUTPUT"] ?>";
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: "core/actions/_save/__editOutput.php",
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
		$("#btnSaveLabel").html("<?= $_SESSION["ADD"] ?>");
		enableControls(false);
		$("#hfRow").val("");
		$('#divNewItem').modal('toggle');
		$('#cbProduct').on('select2:select', function (e, clickedIndex, isSelected, previousValue) {
			var selected = e.params.data;
			var data = $('#cbProduct option[value="' + selected.id + '"]').data();
			var focus = "txtQUANTITY";
			$("#txtQUANTITY").attr("disabled", data.productQuantity == 0);
			if (data.productQuantity == 0) {
				notify("", 'danger', "", msg5, "");
				focus = "cbProduct";
			}
			if(data.factor != 1) {
				notify("", 'warning', "", msg7, "");
			}
			if(data.factormoney != 1) {
				notify("", 'warning', "", msg8, "");
			}
			$("#btnSave").attr("disabled", data.productQuantity == 0);
			$("#moneyTypePrice").html(data.productMoneytype);
			$("#moneyTypeTotal").html(data.factormoneyconversion);
			$("#unitDiv").html(data.unit);
			$("#txtPRICE").val(data.productPrice);
			$("#txtEXISTENCE").val(data.productQuantity);
			$("#hfCODE").val(data.code);
			$("#hfID").val($(this).val());
			$("#hfFactor").val(data.factor);
			$("#hfMoneyFactor").val(data.factormoney);
			$("#hfIdMove").val("<?= $id ?>");
			$("#hfIdMoveDetail").val("0");
			$("#txtQUANTITY").css("background-color", (data.factor != 1 ? "LightYellow" : ""));
			$("#hfUNIT").val(data.unit);
			$('#txtQUANTITY').val("");
			$("#txtTOTAL").val(0 * data.productPrice * data.factormoney);
			$('#' + focus).focus();
		});		
		$('#txtQUANTITY').on('input',function(e){
			var prize = parseFloat($("#txtPRICE").val());
			var existence = parseFloat($("#txtEXISTENCE").val());
			var quant = parseFloat($(this).val());
			var factor = parseFloat($("#hfFactor").val());
			var factorMoney = parseFloat($("#hfMoneyFactor").val());
			if(isNaN(prize)) { prize = 0; }
			if(isNaN(factor)) { factor = 1; }
			if(isNaN(factorMoney)) { factorMoney = 1; }
			if(isNaN(existence)) { existence = 0; }
			
			if((quant * factor) > existence) {
				notify("", 'danger', "", msg5, "");
				$("#txtQUANTITY").val(existence);
				quant = existence;
			}
			$("#txtTOTAL").val(quant * factor * prize * factorMoney);
		});		
	}
	function clearItems() {
		$("#moneyTypePrice").html("");
		$("#moneyTypeTotal").html("");
		$("#txtPRICE").val(0);
		$("#txtEXISTENCE").val(0);
		$("#txtQUANTITY").val(0);
		$("#txtTOTAL").val(0);
		$("#unitDiv").html("");
		$("#hfCODE").val("");
		$("#hfID").val("");
		$("#hfFactor").val("");
		$("#hfMoneyFactor").val("");
		$("#txtQUANTITY").css("background-color", "");
		$("#hfUNIT").val("");
		$('#cbProduct').val("");
		$('#cbProduct').trigger("change");
		$('#cbProduct').focus();
	}
	function enableControls(disabled) {
		$("#btnScan").attr("disabled", disabled);
		$("#btnLoadScan").attr("disabled", disabled);
		$("#btnSave").attr("disabled", disabled);
		$("#txtQUANTITY").attr("disabled", disabled);
		$("#btnSave").attr("disabled", disabled);
		$("#cbProduct").attr("disabled", disabled);		
	}
	function show(id, action) {
		var info = $("#hfRow_" + id).val();
		var data = JSON.parse(info);
		var disabled = action == "view";
		$("#spAction").html(action == "view" ? "<?= $_SESSION["VIEW"] ?>" : "<?= $_SESSION["EDIT"] ?>");
		$("#btnSaveLabel").html(action == "view" ? "<?= $_SESSION["ADD"] ?>" : "<?= $_SESSION["UPDATE"] ?>");
		$('#cbProduct').val(data.cbProduct);
		$('#cbProduct').trigger('change');
		$("#txtQUANTITY").val(data.txtQUANTITY);
		if(action == "edit") {
			var dataProd = $('#cbProduct option[value="' + data.cbProduct + '"]').data();
			$("#moneyTypePrice").html(dataProd.productMoneytype);
			$("#moneyTypeTotal").html(dataProd.factormoneyconversion);
			$("#unitDiv").html(dataProd.unit);
			$("#txtPRICE").val(data.txtPRICE);
			$("#txtEXISTENCE").val(data.txtEXISTENCE);
			$("#hfCODE").val(data.hfCODE);
			$("#hfID").val(data.hfId);
			$("#hfFactor").val(data.hfFactor);
			$("#hfMoneyFactor").val(data.MoneyFactor);
			$("#hfIdMove").val("<?= $id ?>");
			$("#hfIdMoveDetail").val(data.hfIdMoveDetail);
			$("#txtQUANTITY").css("background-color", (data.hfFactor != 1 ? "LightYellow" : ""));
			$("#hfUNIT").val(dataProd.unit);
			$("#txtTOTAL").val(data.txtTOTAL);
			$('#txtQUANTITY').on('input',function(e){
				var prize = parseFloat($("#txtPRICE").val());
				var existence = parseFloat($("#txtEXISTENCE").val());
				var quant = parseFloat($(this).val());
				var factor = parseFloat($("#hfFactor").val());
				var factorMoney = parseFloat($("#hfMoneyFactor").val());
				if(isNaN(prize)) { prize = 0; }
				if(isNaN(factor)) { factor = 1; }
				if(isNaN(factorMoney)) { factorMoney = 1; }
				if(isNaN(existence)) { existence = 0; }
				
				if((quant * factor) > existence) {
					notify("", 'danger', "", msg5, "");
					$("#txtQUANTITY").val(existence);
					quant = existence;
				}
				$("#txtTOTAL").val(quant * factor * prize * factorMoney);
			});					
		}
		enableControls(disabled);
		$("#hfRow").val(id);
		$("#hfAction").val(action);
		$('#divNewItem').modal('toggle');
		$("#hfIdMoveDetail").val(data.hfIdMoveDetail);
		if(action == "edit") {
			$("#cbProduct").attr("disabled", true);
			$('#txtQUANTITY').focus();
		}
		else
			$('#cbProduct').focus();
	}
	function loadScanCamera(load = false) {
		var url = "core/templates/__cameraScanQ.tpl";
		if (location.protocol != 'https:') {
			var md = new MobileDetect(window.navigator.userAgent);
			url = md.mobile() != null ? "core/templates/__loadScanCodeQ.tpl" : url;
			if(load) 
				url = "core/templates/__loadScanCodeQ.tpl";
		}
		$.get(url, function (data) {
			$("#itemScan").html(data);
			$("#divScanCamera").modal('show');
			$('#divScanCamera').on('hidden.bs.modal', function () {
				var content = $("#hfIdScan").val();
				var guid = true;
				if(content != "") {
					var patt = new RegExp(/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i);
					var res = patt.test(content);
					var prod;
					if(!res) {
						guid = false;
						patt = new RegExp(/^\d+$/);
						res = patt.test(content);
						if(!res) {
							//No es codigo válido
							return false;
						}
					}
					if(!guid)
						prod = $("#cbProduct").find("[data-code='" + content + "']");
					else 
						prod = $('#cbProduct option[value="' + content + '"]');
					
					if(prod.length) {
						content = prod.val();
						try {
							$("#hfIdScan").val(content);
							$('#cbProduct').val(content);
							$('#cbProduct').trigger("change");
							var data = $('#cbProduct option[value="' + content + '"]').data();
							var focus = "txtQUANTITY";
							$("#txtQUANTITY").attr("disabled", data.productQuantity == 0);
							if (data.productQuantity == 0) {
								notify("", 'danger', "", msg5, "");
								focus = "cbProduct";
							}
							if(data.factor != 1) {
								notify("", 'warning', "", msg7, "");
							}
							if(data.factormoney != 1) {
								notify("", 'warning', "", msg8, "");
							}
							$("#btnSave").attr("disabled", data.productQuantity == 0);
							$("#moneyTypePrice").html(data.productMoneytype);
							$("#moneyTypeTotal").html(data.factormoneyconversion);
							$("#unitDiv").html(data.unit);
							$("#txtPRICE").val(data.productPrice);
							$("#txtEXISTENCE").val(data.productQuantity);
							$("#hfCODE").val(data.code);
							$("#hfID").val(content);
							$("#hfFactor").val(data.factor);
							$("#hfMoneyFactor").val(data.factormoney);
							$("#hfUNIT").val(data.unit);
							$('#txtQUANTITY').val("");
							$("#txtQUANTITY").css("background-color", (data.factor != 1 ? "LightYellow" : ""));
							$("#txtTOTAL").val(0 * data.productPrice * data.factormoney);
							$('#' + focus).focus();
						}
						catch(e) {
							console.log(e);
							$('#txtQUANTITY').focus();
						}
						$('#txtQUANTITY').focus();
					}
					else {
						notify("", 'warning', "", msg3, "");
					}
				}
			});
		});
	}
	function deleteItem(id) {
		$("#spanTitle").html("<?= $_SESSION["INFORMATION"] ?>");
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
	function applyToStock() {
		$("#spanTitle").html("<?= $_SESSION["INFORMATION"] ?>");
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["APPLY_TO_STOCK_MESSAGE"] . " <br/><br /> " . $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__applyToStock.php',
				data: { 
					txtId: $("#hfIdOutput").val()
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					location.reload();
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