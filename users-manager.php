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
	
	$user = new users($_SESSION["vtappcorp_userid"]);
	$titleUsr = $source == "" ? $_SESSION["MENU_7"] : $_SESSION["USER_" . strtoupper($source)];
	
	if($user->REFERENCE != "") {
		$source .= ";" . $user->REFERENCE;
	}

	require_once("core/classes/configuration.php");
	$conf = new configuration("INIT_PASSWORD");

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
	<!-- iCheck for checkboxes and radio inputs -->
	<link rel="stylesheet" href="plugins/iCheck/all.css">	
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
							<h1 class="m-0 text-dark"><i class="fa fa-users"></i> <?= $titleUsr ?></h1>
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
<?
	if($user->ACCESS_ID == 40 || $user->ACCESS_ID == 80 || $user->ACCESS_ID >= 90) {
?>
									<div class="btn-group float-right">
										<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewUser" name="btnNewUser" class="btn btn-primary pull-right" onclick="show('','new');">
											<i class="fa fa-user-plus"></i>
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
										</button>
									</div>
<?
	}
?>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<table id="tableUser" class="table table-bordered table-striped dt-responsive nowrap">
									<thead>
										<tr>
											<th width="10%"><?= $_SESSION["USERS_TABLE_TITLE_1"] ?></th>
											<th width="30%"><?= $_SESSION["USERS_TABLE_TITLE_2"] ?></th>
											<th width="15%"><?= $_SESSION["USERS_TABLE_TITLE_3"] ?></th>
											<th width="5%"><?= $_SESSION["USERS_TABLE_TITLE_4"] ?></th>
											<th width="5%"><?= $_SESSION["USERS_TABLE_TITLE_5"] ?></th>
											<th width="5%"><?= $_SESSION["USERS_TABLE_TITLE_6"] ?></th>
											<th width="5%"><?= $_SESSION["USERS_TABLE_TITLE_6"] ?></th>
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
	$title = $_SESSION["USER"];
	$icon = "<i class=\"fa fa-user\"></i>";
	$userModal = true;
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
	<!-- iCheck 1.0.1 -->
	<script src="plugins/iCheck/icheck.min.js"></script>	
	
    <script>
	var fields = ["USER_ID", "FULL_NAME", "EMAIL", "ACCESS_NAME", "CHANGE_PASSWORD", "IS_BLOCKED", "ACCESS_ID", "PREFIX"];
	var orTitle = '<?= $title ?>';
	var orIcon = '<?= $icon ?>';
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
        table = $('#tableUser').DataTable({
			"ajax": { 
				"url": "core/actions/_load/__loadSummary.php",
				"data": {
					"class": "users",
					"field": fields.join(),
					"options": "<?= $source ?>"
				}
			},
			"fnInitComplete": function(oSettings, json) {
				$('[data-toggle="tooltip"]').tooltip();		
			},			
			"columns": [
				{ "data": "USER_ID", "searchable": false, "responsivePriority": 2 },
				{ "data": "FULL_NAME", "responsivePriority": 3 },
				{ "data": "EMAIL", "responsivePriority": 4 },
				{ "data": "ACCESS_NAME", "searchable": false, "responsivePriority": 5 },
				{ "data": "CHANGE_PASSWORD", "searchable": false, "responsivePriority": 6 },
				{ "data": "IS_BLOCKED", "searchable": false, "responsivePriority": 7  },
				{ "data": "ACCESS_ID", "searchable": false, "sortable": false, "responsivePriority": 1 },
				{ "data": "PREFIX", "searchable": false, "sortable": false, "responsivePriority": 1 }
			],
			"autoWidth": false,
			"processing": true,
			"serverSide": true,
			"responsive": true,
			"pageLength": 50,
			"order": [[ 1, 'asc' ]],
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
		$('input[aria-controls="tableUser"').unbind();
		$('input[aria-controls="tableUser"').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();	
			}
		});
	});
	function show(id, action) {
		if(action != "new")
			location.href = "user-management.php?id=" + id + "&action=" + action + "&src=<?= $source ?>";
		else 
			location.href = "user-management.php?action=" + action + "&src=<?= $source ?>";
	}
	function activate(user,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["USER"] ?> ";
		$("#spanTitle").html(_msg);
		$("#spanTitleName").html(name);
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__activateData.php',
				data: { 
					txtId: name,
					activate: activate,
					txtClass: "users", 
					txtLink: "users.php",
					txtPre: "USER"
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
    function resetPassword(id) {
		var _msg = "<?= $_SESSION["RESET_PASSWORD_TEXT"] . "<br />" . str_replace("{0}",$conf->verifyValue(),$_SESSION["USER_RESET_PASSWORD"]) ?>";
		_msg += "<br /><br /><div class=\"fm-checkbox\"><label><input type=\"checkbox\" name=\"chkConfirm\" id=\"chkConfirm\" value=\"true\" class=\"i-checks\"/> <i></i> <?= $_SESSION["SEND_EMAIL_TO_USER"] ?></label></div>";
		$("#spanTitle").html("<?= $_SESSION["RESET_PASSWORD"] ?>");
		$("#spanTitleName").html("");
		$("#modalBody").html(_msg);
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__resetPassword.php',
				data: { 
					txtUser: id,
					sendMail: $('#chkConfirm').iCheck('update')[0].checked
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");							
				}
			});
		});		
		$("#divActivateModal").modal("toggle");
    }
	function addFunds(id) {
		var noty;
		$.ajax({
			url:'core/actions/_load/__loadAddFunds.php',
			data: { 
				source: "USER",
				id: "<?= $source ?>",
				user: id
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
						var form = document.getElementById('frmAddFunds');
						var noty;
						if (form.checkValidity() === false) {
							window.event.preventDefault();
							window.event.stopPropagation();
							notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
							return false;
						}
						var title = "<?= $_SESSION["MENU_NEW"] . " " . $_SESSION["ADD_FUNDS"] ?>";
						var url = $("#hfLinkAction").val();
						var $frm = $("#frmAddFunds");
						var datas = $frm.serializeObject();
						if(!datas.hasOwnProperty("cbClient")) {
							datas["cbClient"] = $("#cbClient option:selected").val()
						}
						if(!datas.hasOwnProperty("cbArea")) {
							datas["cbArea"] = $("#cbArea option:selected").val()
						}
						if(!datas.hasOwnProperty("cbUser")) {
							datas["cbUser"] = $("#cbUser option:selected").val()
						}
						if(!datas.hasOwnProperty("cbQuota")) {
							datas["cbQuota"] = $("#cbQuota option:selected").val()
						}
						if(!datas.hasOwnProperty("cbBlocked")) {
							datas["cbBlocked"] = $("#cbBlocked").is(':checked');
						}
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
									link: "users-manager.php"
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
					$.getScript( "js/resources.js");
					$('#divEditModal').modal('toggle');
					$("#cbQuota").on("change",function (e) {
						var max = $(this).find("option:selected").data("max");
						$("#txtAMOUNT").attr("max", max);
						$("#txtAMOUNT").attr("min", 10);
						$("#txtAMOUNT").val(0);
					});
					$("#cbQuota").trigger("change");
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
	include("core/templates/__modalAffiliateStep4.php");
?>

</body>
</html>
