<?
	$image = "img/logo/only_logo.png";
	$title = $_SESSION["vtappcorp_appname"];
	$appname = strtolower(APP_NAME);
	if(substr($_SESSION['vtappcorp_useraccess'],0,2) == "AL") {
		require_once("core/classes/partner.php");
		$partner = new partner();
		$partner->ID = $_SESSION['vtappcorp_referenceid'];
		$partner->__getInformation();
		if($partner->nerror == 0) {
			if(file_exists("img/partners/" . $_SESSION['vtappcorp_referenceid'] . ".png")) {
				$image = "img/partners/" . $_SESSION['vtappcorp_referenceid'] . ".png";
			}
			$title = $partner->PARTNER_NAME;
			$appname = explode(" ",$appname)[0];
		}
	}
	else if(substr($_SESSION['vtappcorp_useraccess'],0,2) == "CL") {
		require_once("core/classes/partner_client.php");
		$partner = new partner_client();
		$partner->setClient = $_SESSION['vtappcorp_referenceid'];
		$partner->getInformationByClient();
		if($partner->nerror == 0) {
			if(file_exists("img/partners/" . $partner->partner->ID . ".png")) {
				$image = "img/partners/" . $partner->partner->ID . ".png";
			}
			$title = $partner->partner->PARTNER_NAME;
			$appname = explode(" ",$appname)[0];
		}
	}
	else {
		$title = substr($title,0,5);
	}
	$appname .= "\n</span><span class=\"brand-text font-weight-light\"><small class=\"brand-text small-brand-text\"> &nbsp;$title</small></span>\n";
	$skn = explode(" ",$skin[0]);
	if(count($skn) == 3)
		$skn[1] = $skn[2];
?>
			<!-- Brand Logo -->
			<a href="dashboard.php" class="brand-link <?= $skn[1] ?>">
				<img src="<?= $image ?>" alt="<?= APP_NAME ?>" class="brand-image elevation-3" style="opacity: .8" />
				<span class="ubio-brand">
					<?= $appname ?>
				</span>
			</a>
