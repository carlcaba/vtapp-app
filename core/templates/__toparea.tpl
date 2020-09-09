<?
	require_once("core/classes/language.php");
	require_once("core/classes/notification.php");
	require_once("core/classes/directchat.php");
	$lang = new language();
	$noti = new notification();
	$chat = new directchat($_SESSION['vtappcorp_userid']);
	
	$totalNoti = $noti->getTotalCount();
	$totalChat = $chat->getTotalCount();
	$badgeNoti = ($totalNoti == 0) ? "" : "<span class=\"badge badge-warning navbar-badge\">$totalNoti</span>\n";
	$badgeChat = ($totalChat == 0) ? "" : "<span class=\"badge badge-danger navbar-badge\">$totalChat</span>\n";
?>
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand <?= $skin[0] ?> border-bottom">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="dashboard.php" class="nav-link"><?= $_SESSION["MENU_1"] ?></a>
				</li>
			</ul>
			<!-- SEARCH FORM -->
			<form class="form-inline ml-3">
				<div class="input-group input-group-sm">
					<input class="form-control form-control-navbar" type="search" placeholder="<?= $_SESSION["SEARCH"] ?>" aria-label="<?= $_SESSION["SEARCH"] ?>">
					<div class="input-group-append">
						<button class="btn btn-navbar" type="submit">
							<i class="fa fa-search"></i>
						</button>
					</div>
				</div>
			</form>
			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<!-- Messages Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#">
						<i class="fa fa-comments-o"></i>
						<div id="directChatCount"><?= $badgeChat ?></div>
					</a>
<?= $chat->showPanel() ?>
				</li>	
				<!-- Notifications Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#">
						<i class="fa fa-bell-o"></i>
						<div id="notificationCount"><?= $badgeNoti ?></div>
					</a>
<?= $noti->showPanel() ?>
				</li>
				<!-- Change language Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#" title="<?= $_SESSION["LANGUAGES"] ?>">
						<i class="fa fa-language"></i>
					</a>
<?
	echo $lang->showLanguages();
?>									
				</li>
				<!-- Loguot -->
				<li class="nav-item">
					<a class="nav-link" href="core/__exit.php" title="<?= $_SESSION["LOGOUT"] ?>"><i class="fa fa-sign-out"></i></a>
				</li>
			</ul>
		</nav>
		<!-- /.navbar -->
		
	<script>
		function changeLanguage(lang) {
			var noty;
			$.ajax({
				type: "POST",
				url: "core/__change-language.php",
				data: {
					lng: lang
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CHANGING_LANGUAGE"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				error: function () {
					var message = "<?= $_SESSION["AN_ERROR_OCCURRED"] ?>";
					notify("", "danger", "", message, "", false);												
					return {success: false, message: ""};
				},
				success: function (data) {
					noty.close();
					if (!data.success) {
						notify("", 'info', "", data.message, "");
					}
					else {
						location.reload();
					}
				}
			});
		}	
	</script>
    <!-- End Header Top Area -->
