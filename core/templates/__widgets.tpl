<?
	_error_log("Widgets loading start at " . date("Y-m-d h:i:s"));

	require_once("core/classes/users.php");
	require_once("core/classes/client.php");
	require_once("core/classes/partner.php");
	require_once("core/classes/service.php");
		
	$client = new client();
	$partner = new partner();
	$service = new service();
	$usua = new users();

?>

			<div class="row">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-info">
						<div class="inner">
							<h3><?= number_format($client->getTotalCount(),0,".",",") ?></h3>
							<p><?= $_SESSION["MENU_2"] ?></p>
						</div>
						<div class="icon">
							<i class="ion ion-briefcase"></i>
						</div>
						<a href="clients.php" class="small-box-footer"><?= $_SESSION["MORE_INFO"] ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-success">
						<div class="inner">
							<h3><?= number_format($partner->getTotalCount(),0,".",",") ?></h3>
							<!-- <sup style="font-size: 20px">%</sup> -->
							<p><?= $_SESSION["MENU_3"] ?></p>
						</div>
						<div class="icon">
							<i class="ion ion-ios-body"></i>
						</div>
						<a href="partners.php" class="small-box-footer"><?= $_SESSION["MORE_INFO"] ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-warning">
						<div class="inner">
							<h3><?= number_format($service->getTotalCount(),0,".",",") ?></h3>

							<p><?= $_SESSION["MENU_4"] ?></p>
						</div>
						<div class="icon">
							<i class="ion ion-ios-paperplane"></i>
						</div>
						<a href="my-services.php" class="small-box-footer"><?= $_SESSION["MORE_INFO"] ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-danger">
						<div class="inner">
							<h3><?= $usua->getTotalUsers() ?></h3>

							<p><?= $_SESSION["MENU_7"] ?></p>
						</div>
						<div class="icon">
							<i class="ion ion-person-add"></i>
						</div>
						<a href="users-manager.php" class="small-box-footer"><?= $_SESSION["MORE_INFO"] ?> <i class="fa fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
			</div>
<?
	_error_log("Widgets finishes at " . date("Y-m-d h:i:s"));
?>