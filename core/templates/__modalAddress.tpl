<?
	function getTitleFromComments($str) {
		$arrStr = explode(",",$str);
		return count($arrStr) > 1 ? $arrStr[1] : $str;
	}

	require_once("core/classes/user_address.php");
	$usradd = new user_address();
	$dfUsrAdd = $usradd->dataForm("new");
	
?>	
		<!-- DataTables -->
		<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">
		<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap4.min.css">
		<!-- Modal Answer Challenge -->
		<div class="modal fade" id="mdlAddress" tabindex="-1" role="dialog" aria-labelledby="mdlAddress" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header bg-success text-dark">
						<h5 class="modal-title" id="mdlAddressTitle"><i class="fa fa-map"></i> <?= $_SESSION["OWN_ADDRESSES"] ?> <small id="typeAddress"></small></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body text-dark text-left">
						<table id="tableAddresses" class="table table-bordered table-striped dt-responsive nowrap">
							<thead>
								<tr>
									<th width="30%"><?= $_SESSION["ADDRESS_NAME"] ?></th>
									<th width="30%"><?= $_SESSION["ADDRESS"] ?></th>
									<th width="30%"><?= $_SESSION["ZONE"] ?></th>
									<th width="10%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
								</tr>
							</thead>		
							<tbody id="tableAddressesBody"></tbody>
						</table>
						<hr />
						<h5 class="m-0 text-dark"><a href="javascript: $('#frmNewAddress').toggle('slow');"><i class="fa fa-plus-circle"></i> <?= $_SESSION["NEW_ADDRESS"] ?></a></h5>
						<hr />
						<form name="frmNewAddress" id="frmNewAddress" style="display: none;">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtADDRESS_NAME"><?= getTitleFromComments($usradd->arrColComments["ADDRESS_NAME"]) ?> <span class="required">*</span></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-id-badge"></i></span>
											</div>
											<input id="txtADDRESS_NAME" class="form-control" placeholder="Nombre dirección" type="text" name="txtADDRESS_NAME" required="required" value="" autocomplete="off">
										</div>
									</div>								
								</div>
								<div class="col-md-6">
									<label for="cbCity"><?= getTitleFromComments($usradd->arrColComments["CITY_ID"]) ?> <span class="required">*</span></label>
									<select class="form-control" id="cbCity" name="cbCity">
										<?= $usradd->city->showOptionList(9,"0") ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<label><?= getTitleFromComments($usradd->arrColComments["ADDRESS"]) ?> <span class="required">*</span></label>
									<select class="form-control" id="cbTypeAddress" name="cbTypeAddress">
										<?= $usradd->type->showOptionList(9,"") ?>
									</select>
								</div>
								<div class="col-md-3">
									<label for="txtAddress01">&nbsp;</label>
									<input type="text" id="txtAddress01" name="txtAddress01" class="form-control" placeholder="104 Bis" required="required" value="" autocomplete="off" />
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="txtAddress02">&nbsp;</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">#</span>
											</div>
											<input type="text" id="txtAddress02" name="txtAddress02" class="form-control" placeholder="35 A" required="required" value="" autocomplete="off"/>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="txtAddress03">&nbsp;</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">-</span>
											</div>
											<input type="text" id="txtAddress03" name="txtAddress03" class="form-control" placeholder="78" required="required" value="" autocomplete="off"/>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="txtAddress04">&nbsp;</label>
										<input type="text" id="txtAddress04" name="txtAddress04" class="form-control" placeholder="Adicional" value="" autocomplete="off"/>
									</div>
								</div>
							</div>
							<div class="row" id="divLocations">
								<div class="col-md-6">
									<label for="cbZone"><?= getTitleFromComments($usradd->arrColComments["ZONE_ID"]) ?> <span class="required">*</span></label>
									<select class="form-control ZoneSelection" id="cbZone" name="cbZone">
										<?= $usradd->zone->showOptionList(9,"0") ?>
									</select>
								</div>
								<div class="col-md-6">
									<label for="cbZoneSub"><?= $_SESSION["SUB_ZONE_NAME"] ?></label>
									<select class="form-control ZoneSelection" id="cbZoneSub" name="cbZoneSub">
										<?= $usradd->zone->showOptionList(9,"",0,false) ?>
									</select>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?= $_SESSION["CANCEL"] ?></button>
						<button type="button" class="btn btn-primary" id="btnSelect" name="btnSelect"><i class="fa fa-plus-circle"></i> <?= $_SESSION["SAVE_AND_SELECT"] ?></button>
						<input type="hidden" id="hfDestinyField" name="hfDestinyField" value="" />
						<input type="hidden" id="hfLinkNewAddress" name="hfLinkNewAddress" value="<?= $dfUsrAdd->link ?>" />
						<input type="hidden" id="hfLATITUDE_USER_ADDRESS" name="hfLATITUDE_USER_ADDRESS" value="" />
						<input type="hidden" id="hfLONGITUDE_USER_ADDRESS" name="hfLONGITUDE_USER_ADDRESS" value="" />
					</div>
				</div>
			</div>
		</div>	
		<!-- DataTables -->
		<script src="plugins/datatables/jquery.dataTables.js"></script>
		<script src="plugins/datatables/dataTables.bootstrap4.js"></script>
		<script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
		<script>
			$(function() {
				$(".ZoneSelection").on("change", function () {
					var value = $(this).val();
					var name = $(this).attr("id");
					var data = $(this).data();
					$("#hfLATITUDE_USER_ADDRESS").val(data.latitude);
					$("#hfLONGITUDE_USER_ADDRESS").val(data.longitude);
					var sub = name + "Sub";
					$("#" + sub).removeAttr("disabled");
					$("#" + sub).find("option[data-parent='" + value + "']").removeAttr("disabled");
					$("#" + sub).find("option[data-parent!='" + value + "']").attr("disabled","disabled");
					$('#' + sub + ' option:not([disabled]):first').attr('selected', 'selected');
					$('#' + sub).trigger("change");
				});
				$("#cbCity").change(function (e){
					if($(this).val() == 1) {
						$("#divLocations").fadeIn();						
						$(".ZoneSelection").trigger("change");
					}
					else {
						$("#divLocations").fadeOut();						
					}
				});
				$("#cbCity").trigger("change");
				$("#btnSelect").click(function () {
					var form = document.getElementById('frmNewAddress');
					var noty;
					if (form.checkValidity() === false) {
						window.event.preventDefault();
						window.event.stopPropagation();
						notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
						return false;
					}					
					var address = ($("#cbTypeAddress option:selected").text() + " " + $("#txtAddress01").val() + " # " + $("#txtAddress02").val() + " - " + $("#txtAddress03").val() + " " + $("#txtAddress04").val()).trim() + ", " + $("#cbCity option:selected").text() + ", Colombia";
					var field = $("#hfDestinyField").val();
					var hfField = field.replace("txt","_");
					var reference = field.split("_")[0];
					if(reference == "txtREQUESTED") 
						reference = "Request";
					else 
						reference = "Deliver";
					$("#" + field).val(address);
					var title = "<?= $_SESSION["SAVE_USER_ADDRESS"] ?>";
					var url = "core/actions/_save/__newAddress.php";
					var $frm = $("#frmNewAddress");
					var datasObj = $frm.serializeObject();
					datasObj = checkSerializedAddressObject(datasObj);
					var datas = JSON.stringify(datasObj);
					if(datasObj.ZoneVisible) {
						$("#cbZone" + reference).val($("#cbZone").val());
						$("#cbZone" + reference + "Sub").val($("#cbSubZone").val());
					}
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
								notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
								if(data.success) {
									eval(data.link);
								}
							}
						});
					});
					$("#divActivateModal").modal("toggle");			
				});
			});
			function checkSerializedAddressObject(datasObj) {
				if(!datasObj.hasOwnProperty("AddressType")) {
					datasObj["AddressType"] = $("#cbTypeAddress option:selected").text();
				}
				if(!datasObj.hasOwnProperty("ZoneVisible")) {
					datasObj["ZoneVisible"] = $("#divLocations").is(":visible");
				}
				if(!datasObj.hasOwnProperty("IsOrigin")) {
					datasObj["IsOrigin"] = $("#hfDestinyField").val().indexOf("REQUESTED_ADDRESS") > -1;
				}
				datasObj["txtAddress02"] = $("#txtAddress02").val().toUpperCase();
				datasObj["txtAddress04"] = $("#txtAddress04").val().toUpperCase();
				return datasObj;	
			}
			
			function makeTableAddress() {
				$('#tableAddresses').DataTable({
					"autoWidth": false,
					"responsive": true,
					"pageLength": 5,
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
				$(".btnSelectAddress").click(function () {
					var datas = $(this).data();
					var field = $("#hfDestinyField").val();
					var hfField = field.replace("txt","_");
					var reference = field.split("_")[0];
					if(reference == "txtREQUESTED") 
						reference = "Request";
					else 
						reference = "Deliver";
					$("#" + field).val(datas.address);
					$("#hfLATITUDE" + hfField).val(datas.latitude);
					$("#hfLONGITUDE" + hfField).val(datas.longitude);
					if(datas.parentzone != "") {
						$("#cbZone" + reference).val(datas.parentzone);
						$("#cbZone" + reference).trigger("change");
						$("#cbZone" + reference + "Sub").val(datas.zone);
						$("#cbZone" + reference + "Sub").trigger("change");
					}
					else {
						$("#cbZone" + reference).val(datas.zone);
						$("#cbZone" + reference).trigger("change");
					}
					console.log(datas);
					$("#mdlAddress").modal("hide");
				});
				
			}
		</script>