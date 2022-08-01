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
	
	$result = checkSession("faqs.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/faq.php");
	$faqs = new faq();

?>
<!DOCTYPE html>
<html>
<head>
	<link href="css/faqs.css" rel="stylesheet">
<?
	include("core/templates/__header.tpl");
?>
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
							<h1 class="m-0 text-dark"><i class="fa fa-circle-question"></i> <?= $_SESSION["FAQS"] ?></h1>
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
				<div class="container-fluid">
					<!-- Row -->
					<div class="row">
						<div class="col-xl-12 pa-0">
							<div class="mt-sm-60 mt-30">
								<div class="hk-row">
									<div class="col-xl-3">
										<div class="card">
											<h4 class="card-header"><?= $_SESSION["CATEGORY"] ?></h4>
<?
	$faqs->listCategories();
?>
										</div>
									</div>
									<div class="col-xl-9">
<?
	$faqs->listFAQS();
?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Row -->
				</div>
				<!-- END TICKET -->
				<input type="hidden" name="hfAnswer" id="hfAnswer" value="false" />
				<input type="hidden" name="hfQId" id="hfQId" value="0" />
				<div class="row">
					<div class="col-12 mt-3 text-center">
						<p class="lead">
							<a href="#" data-toggle="modal" data-target="#modalFAQ"><?= $_SESSION["CLICK_HERE"] ?></a>,
							<?= $_SESSION["FAQ_NOT_FOUND"] ?><br />
						</p>
					</div>
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["FAQS"];
	$icon = "<i class=\"fa fa-circle-question\"></i>";
	include("core/templates/__modals.tpl");
	include("core/templates/__modalFaq.tpl");
	include("core/templates/__footer.tpl");
?>

    <script>
		$(document).ready(function() {
			var searchTerm, panelContainerId;
			$.expr[":"].containsCaseInsensitive = function (n, i, m) {
				return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
					/*|| 
					jQuery(n).html().toUpperCase().indexOf(m[3].toUpperCase()) >= 0 || 
					jQuery(n).textContent().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; */
			};
			$('#txtSearchFAQ').bind('enterKey', function () {
				searchTerm = $(this).val();
				$("div[id^='acc'] > .card").each(function () {
					panelContainerId = $(this).attr('id');
					$(panelContainerId + ":not(:containsCaseInsensitive(" + searchTerm + "))").hide("slow");
					$(panelContainerId + ":containsCaseInsensitive(" + searchTerm + ")").show();
				});
			});
			$('#txtSearchFAQ').on('change keyup', function (e) {
				if(e.keyCode == 13)	{
					$(this).trigger("enterKey");
				}
			});				
			$(".lst-category").on("click", function (e) {
				var data = $(this).data("category");
				$(this).addClass('active').siblings('li').removeClass('active');
				$(".card.card-lg:visible").hide("slow");
				$('.card.card-lg[data-category="' + data + '"]').show();
			});
			$("div[id^='acc']").on('shown.bs.collapse', function (e) {
				var id = $(document).find("[href='#" + $(e.target).attr('id') + "']").data("id");
				$.ajax({
					url:'core/actions/_save/__newFaqView.php',
					data: { 
						id: id
					},
					dataType: "json",
					method: "POST",
					success:function(data) {
						if(!data.success) 
							notify("", 'danger', "", data.message, "");
					}
				});
			});
			$('#modalFAQ').on('hidden.bs.modal', function (e) {
				$("#hfAnswer").val("false");
				$("#hfQId").val(0);
			});
			$('#modalFAQ').on('show.bs.modal', function (e) {
				var show = $("#hfAnswer").val() == "true";
				var id = $("#hfQId").val();
				if(show) {
					$("#pTextFAQ").html($("#txtQuestion_" + id).val());
					$("#txtQuestion").attr("placeholder","<?= $_SESSION["ANSWER_PLACEHOLDER"] ?>");
				}
				else {
					$("#pTextFAQ").html("<?= $_SESSION["YOUR_QUESTION"] ?>");
					$("#txtQuestion").attr("placeholder","<?= $_SESSION["QUESTION_PLACEHOLDER"] ?>");					
				}
				$("#txtQuestion").val("");
				$("#txtQuestion").focus();
			});
		});
		function Save() {
			var title = "<?= $_SESSION["SAVE_CHANGES"] ?>";
			var url, params;
			var show = $("#hfAnswer").val() == "true";
			if($("#txtQuestion").val() == "") {
				notify("", 'danger', "", "<?= $_SESSION["NO_DATA_FOR_VALIDATE"] ?>", "");
				return false;
			}
			if(show) {
				url = "core/actions/_save/__editFAQ.php";
				params = { id: $("#hfQId").val(), strFAQ: $("#txtQuestion").val() };
			}
			else {
				url = "core/actions/_save/__newFAQ.php";
				params = { strFAQ: $("#txtQuestion").val() };
			}
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: url,
					data: params,
					dataType: "json",
					method: "POST",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					error: function( jqxhr, textStatus, error ) {
						var err = textStatus + ", " + error;
						notify("", 'danger', "", "Request Failed: " + err , "");
					},
					success:function(data){
						noty.close();
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success)
							location.reload();
					}
				});
			});
			$("#divActivateModal").modal("toggle");			
		}
		function Answer(id) {
			$("#hfAnswer").val("true");
			$("#hfQId").val(id);
		}
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
