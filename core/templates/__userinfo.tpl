<?
	require_once("core/classes/users.php");
	$usrx = new users($_SESSION["vtappcorp_userid"]);
?>
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="<?= $usrx->getUserPicture(true) ?>" class="img-circle elevation-2" alt="<?= $usrx->ID ?>" id="imgUserInfo" />
					</div>
					<div class="info">
						<a href="profile.php?id=<?= $usrx->ID ?>" class="d-block"><?= $usrx->FIRST_NAME . " " . $usrx->LAST_NAME ?></a>
					</div>
				</div>
