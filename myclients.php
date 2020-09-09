<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	
	$filename = "myclients.php";
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	//Load Google Maps Information
	require_once("core/classes/configuration.php");
	
	$conf = new configuration("MAPS_API_URL");
	$map_url = $conf->verifyValue();
	$conf = new configuration("MAPS_API_KEY");
	$map_api = $conf->verifyValue();
	$map_url = $map_url . $map_api;
	$conf = new configuration("MAPS_DEFAULT_ZOOM");
	$map_zoom = $conf->verifyValue();
	$conf = new configuration("MAPS_API_CALLBACK_LOCATION");
	$location_callback = $conf->verifyValue();

	require_once("core/classes/partner_client.php");
	require_once("core/classes/users.php");
	
	$usua = new users($_SESSION["vtappcorp_userid"]);
	
	$party = new partner_client();
	$party->setPartner($usua->REFERENCE);

?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- DataTables -->
	<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">
	<link rel="stylesheet" href="plugins/datatables/select.dataTables.min.css">
	<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap4.min.css">
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- JqueryUI -->
	<link rel="stylesheet" href="plugins/jQueryUI/jquery-ui.css">	
	<!-- iCheck -->
	<link rel="stylesheet" href="plugins/iCheck/minimal/green.css">	
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
							<h1 class="m-0 text-dark"><i class="fa fa-briefcase"></i> <?= $_SESSION["MYCLIENTS"] ?></h1>
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
									<?= $_SESSION["MYCLIENTS_TITLE_PAGE"] ?>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableMyClients" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="20%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_1"] ?></th>
											<th width="15%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_2"] ?></th>
											<th width="10%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_3"] ?></th>
											<th width="10%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_4"] ?></th>
											<th width="10%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_5"] ?></th>
											<th width="10%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_6"] ?></th>
											<th width="10%"><?= $_SESSION["MYCLIENTS_TABLE_TITLE_7"] ?></th>
											<th width="15%"><?= $_SESSION["ACTIONS_TABLE_TITLE"] ?></th>
										</tr>
									</thead>		
									<tbody>
<?
	$party->showMyClients();
?>
									</tbody>                                       
								</table>
								<input type="hidden" id="hfClientId" name="hfClientId" value="<?= $client ?>" />
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
	
?>

	<div class="modal" tabindex="-1" role="dialog" id="modalService">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-gift"></i> <?= $_SESSION["ASSIGN_SERVICE"] ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="frmAssignation" name="frmAssignation">
						<table id="tableServices" class="table table-bordered table-striped dt-responsive" style="width:100%">
							<thead>
								<tr>
									<th width="5%"></th>
									<th width="25%"><?= $_SESSION["REQUEST_ADDRESS_TITLE"] ?></th>
									<th width="25%"><?= $_SESSION["DELIVER_ADDRESS_TITLE"] ?></th>
									<th width="15%"><?= $_SESSION["DELIVER_NAME_TITLE"] ?></th>
									<th width="10%"><?= $_SESSION["TYPE_TITLE"] ?></th>
									<th width="10%"><?= $_SESSION["FRAGILE_TITLE"] ?></th>
									<th width="10%"><?= $_SESSION["ROUND_TRIP_TITLE"] ?></th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>				
						<input type="hidden" id="hfCounterData" name="hfCounterData" value="" />
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnAssignation" name="btnAssignation" disabled><?= $_SESSION["SAVE"] ?></button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $_SESSION["CANCEL"] ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" tabindex="-1" role="dialog" id="modalEmployees">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-user-times"></i> <?= $_SESSION["EMPLOYEE_ASSIGNED"] ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table id="tableEmployees" class="table table-bordered table-striped dt-responsive" style="width:100%">
						<thead>
							<tr>
								<th width="40%"><?= $_SESSION["ASSIGNED_EMPLOYEE_TITLE_1"] ?></th>
								<th width="20%"><?= $_SESSION["ASSIGNED_EMPLOYEE_TITLE_2"] ?></th>
								<th width="15%"><?= $_SESSION["ASSIGNED_EMPLOYEE_TITLE_3"] ?></th>
								<th width="15%"><?= $_SESSION["ASSIGNED_EMPLOYEE_TITLE_4"] ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>				
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $_SESSION["CANCEL"] ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" tabindex="-1" role="dialog" id="modalEmployee">
		<div class="modal-dialog modal-warning" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-motorcycle"></i> <?= $_SESSION["ASSIGN_TO"] ?></h5>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label><?= $_SESSION["EMPLOYEE_AVAILABLE"] ?></label>
						<select class="form-control" id="cbEmployees" name="cbEmployees">
							<option value="" selected></option>
<?
	echo $party->showEmployees();
?>
						</select>
						<input type="hidden" id="hfCounter" name="hfCounter" value="" />
					</div>					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnAssign" name="btnAssign" disabled><?= $_SESSION["ASSIGN"] ?></button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $_SESSION["CANCEL"] ?></button>
				</div>
			</div>
		</div>
	</div>

    <!-- Data Table JS
		============================================ -->
	<!-- DataTables -->
	<script src="plugins/datatables/jquery.dataTables.js"></script>
	<script src="plugins/datatables/dataTables.bootstrap4.js"></script>
	<script src="plugins/datatables/dataTables.select.min.js"></script>
    <script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.js"></script>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- iCheck -->
	<script src="plugins/iCheck/icheck.min.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
        var table = $('#tableMyClients').DataTable({
			"columns": [
				{ "target": 0, "searchable": true, "responsivePriority": 2, "class": "details-control" },
				{ "target": 1, "searchable": false, "responsivePriority": 8 },
				{ "target": 2, "searchable": true, "responsivePriority": 7 },
				{ "target": 3, "searchable": true, "responsivePriority": 6 },
				{ "target": 4, "searchable": false, "responsivePriority": 5 },
				{ "target": 5, "searchable": true, "responsivePriority": 4 },
				{ "target": 6, "searchable": true, "responsivePriority": 3 },
				{ "target": 7, "searchable": false, "responsivePriority": 1, "sortable": false }
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
		$('input[aria-controls="tableMyClients"').unbind();
		$('input[aria-controls="tableMyClients"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function assign(id,type) {
		$.ajax({
			url: "core/actions/_load/__loadServices.php",
			data: { id: id },
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				if($.fn.DataTable.isDataTable('#tableServices')) {
					$('#tableServices').DataTable().clear().draw();
					$('#tableServices').DataTable().destroy();
				}
				var table = $('#tableServices').DataTable({
					"columns": [
						{ "searchable": true, "visible": true, "responsivePriority": 1 },
						{ "searchable": true, 
							"responsivePriority": 2, 
							"render": function ( data, type, item ) {
								var link = $(data);
								var txt = link.html();
								if (type === 'display' && txt.length > 15) {
									txt = txt.substr(0,15) +'…';
								}
								link.html(txt);
								return link[0].outerHTML;
							} 
						},
						{ "searchable": true, 
							"responsivePriority": 3, 
							"render": function ( data, type, item ) {
								var link = $(data);
								var txt = link.html();
								if (type === 'display' && txt.length > 15) {
									txt = txt.substr(0,15) +'…';
								}
								link.html(txt);
								return link[0].outerHTML;
							} 
						},
						{ "searchable": true, "responsivePriority": 4 },
						{ "searchable": true, "responsivePriority": 5 },
						{ "searchable": true, "responsivePriority": 6 },
						{ "searchable": true, "responsivePriority": 7 },
						{ "searchable": true, "visible": false },
						{ "searchable": true, "visible": false }
					],					
					columnDefs: [{	
						orderable: false,
						className: 'select-checkbox',
						targets: 0
					}],
					select: {
						style:    'os',
						selector: 'td:first-child'
					},
					"autoWidth": false,
					"processing": false,
					"serverSide": false,
					"responsive": true,
					"pageLength": 10
				});
				table.on('select', function ( e, dt, type, indexes ) {
					var id = indexes[0];
					$("#hfCounter").val(indexes[0]);
					if($("#hfIdEmployee_" + indexes[0]).val() != "") {
						notify("", "danger", "", "<?= $_SESSION["SERVICE_ALREADY_ASSIGNED"] ?>", "", false);												
						table.rows().deselect();
						return false;
					}
					$("#cbEmployees").change(function () {
						var _id = $("#hfCounter").val();
						$("#hfIdEmployee_" + _id).val($(this).val());
						$("#btnAssign").attr("disabled", $(this).val() == "");
					});
					$("#cbEmployees").val("");
					$("#cbEmployees").trigger("change");
					$("#btnAssign").click(function() {
						table.rows().deselect();
						$("#btnAssignation").attr("disabled", false);
						$("#modalEmployee").modal("hide");
					});
					$("#modalEmployee").modal("toggle");
				});
				$("#btnAssignation").click(function () {
					var title = "<?= $_SESSION["ASSIGN"] ?>";
					var url = "core/actions/_save/__assignService.php";
					var $frm = $("#frmAssignation");
					var datasObj = $frm.serializeObject();
					var datas = JSON.stringify(datasObj);
					$("#spanTitle").html(title);
					$("#spanTitleName").html("");
					$("#modalBody").html("<?= $_SESSION["MSG_ASSIGN_SERVICE"] ?>");
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
								if(data.success)
									location.href = data.link;
							}
						});
					});
					$("#divActivateModal").modal("toggle");			
					
				});
				if(data.success) {
					var counter = 0;
					$.each(data.data,function(key,value) {
						var addr1 = "<a href=\"#\" title=\"<?= $_SESSION["VIEW_MAP"] ?>\" onclick=\"showMap(" + value.lat_ini + "," + value.lng_ini + ",'" + value.title + "','" + value.from + "',true);\">" + value.from + "</a>";
						var addr2 = "<a href=\"#\" title=\"<?= $_SESSION["VIEW_MAP"] ?>\" onclick=\"showMap(" + value.lat_end + "," + value.lng_end + ",'" + value.title + "','" + value.to + "',false);\">" + value.to + "</a>";
						var deliver = "<a href=\"#\" title=\"<?= $_SESSION["VIEW_ROUTE"] ?>\" onclick=\"showRoute(" + value.lat_ini + "," + value.lng_ini + "," + value.lat_end + "," + value.lng_end + ",'" + value.deliver_to + "');\">" + value.deliver_to + "</a>";
						var fragile = "<div align=\"center\"><i class=\"fa fa-" + (value.fragile == "1" ? "check-square" : "square-o") + "\"></i></div>";
						var round = "<div align=\"center\"><i class=\"fa fa-" + (value.roundtrip == "1" ? "check-square" : "square-o") + "\"></i></div>";
						var check = "<div align=\"center\"><input type=\"checkbox\" class=\"form-control iCheckClass\" name=\"chk_" + counter + "\" id=\"chk_" + counter + "\" /></div>";
						var data = "<input type=\"hidden\" id=\"hfIdService_" + counter + "\" name=\"hfIdService_" + counter + "\" value=\"" + value.id + "\" />" +
									"<input type=\"hidden\" id=\"hfIdEmployee_" + counter + "\" name=\"hfIdEmployee_" + counter + "\" value=\"\" />";
						table.row.add([
							"",
							addr1,
							addr2,
							deliver,
							value.type,
							fragile, 
							round + data,
							value.zone_ini,
							value.zone_end
						]).draw(false);
						$("#hfCounterData").val(counter);
						counter++;
					});
				}
			}
		});
		$("#modalService").modal("toggle");
	}
	var marker, map, infoWindow;
	function showAssign(id) {
		$.ajax({
			url: "core/actions/_load/__loadEmployees.php",
			data: { id: id },
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				if($.fn.DataTable.isDataTable('#tableEmployees')) {
					$('#tableEmployees').DataTable().clear().draw();
					$('#tableEmployees').DataTable().destroy();
				}
				var table = $('#tableEmployees').DataTable({
					"columns": [
						{ "searchable": true, "visible": true, "responsivePriority": 1 },
						{ "searchable": true, "responsivePriority": 2 },
						{ "searchable": true, "responsivePriority": 3 },
						{ "searchable": true, "responsivePriority": 4 }
					],					
					"autoWidth": false,
					"processing": false,
					"serverSide": false,
					"responsive": true,
					"pageLength": 10
				});
				if(data.success) {
					$.each(data.data,function(key,value) {
						table.row.add([
							value.name,
							value.services,
							value.registered_on,
							value.registered_by
						]).draw(false);
					});
				}
			}
		});
		$("#modalEmployees").modal("toggle");
	}
	function showMap(lat,lng,title,address,origin) {
		$('#divMapModal').modal('toggle');
		$('#divMapModal').find('.modal-body').css({
			width:'auto', //probably not needed
			height: '600px',
			'max-height': '100%'
		});
		title += " - " + (origin ? "origen" : "destino");
		title += " (" + address + ")";
		$("#divTitle").html(title);
		var location = {lat: lat, lng: lng};
		map = new google.maps.Map(document.getElementById('modalMap'), {zoom: <?= $map_zoom ?>, center: location});
		marker = new google.maps.Marker({position: location, map: map});
	}
	
	function showRoute(lat1,lng1,lat2,lng2,title) {
		title = "<?= $_SESSION["SHOW_ROUTE"] ?> " + title;
		$("#divTitle").html(title);
		map = new google.maps.Map(document.getElementById('modalMap'), {
			zoom: 15,
			center: {lat: lat1, lng: lng1},
			mapTypeId: 'terrain'
        });		
		$('#divMapModal').modal('toggle');
		$('#divMapModal').find('.modal-body').css({
			width:'auto', //probably not needed
			height: '600px',
			'max-height': '100%'
		});
		var routeCoordinates = [
			{
				lat: lat1, 
				lng: lng1 
			},
			{
				lat: lat2, 
				lng: lng2
			}
		];
        var routePath = new google.maps.Polyline({
			path: routeCoordinates,
			geodesic: true,
			strokeColor: '#FF0000',
			strokeOpacity: 1.0,
			strokeWeight: 2
        });

        routePath.setMap(map);
	}
	function initMap() {
		map = new google.maps.Map(document.getElementById('modalMap'), {
			center: {
				lat: -34.397, 
				lng: 150.644
			},
			zoom: <?= $map_zoom ?>
		});
		infoWindow = new google.maps.InfoWindow;		
		// Try HTML5 geolocation.
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};

				infoWindow.setPosition(pos);
				infoWindow.setContent('<?= $_SESSION["LOCATION_FOUND"] ?>');
				infoWindow.open(map);
				map.setCenter(pos);
			}, function() {
				handleLocationError(true, infoWindow, map.getCenter());
			});
		} 
		else {
			// Browser doesn't support Geolocation
			handleLocationError(false, infoWindow, map.getCenter());
		}
	}
    </script>

	<style>
		#modalMap {
			height: 100%;
		}
	</style>

	<!-- Modal Map -->
	<div class="modal fade" id="divMapModal" tabindex="-1" role="dialog" aria-labelledby="h5ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalLabel"><i class="fa fa-map-marker"></i> <span id="divTitle"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>					
				</div>
				<div class="modal-body"><div id="modalMap"></div></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- MAPS -->
    <script src="<?= $map_url . $location_callback ?>" async defer></script>
	
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
