<?
	require_once("../classes/users.php");
	require_once("../classes/client.php");
	require_once("../classes/partner.php");
	require_once("../classes/service.php");
		
	$client = new client();
	$partner = new partner();
	$service = new service();
	$users = new users();

?>
					<!-- Small boxes (Stat box) -->
					<div class="row">
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box">
								<span class="info-box-icon bg-info elevation-1">
									<i class="fa fa-briefcase"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text"><a href="#"><?= $_SESSION["MENU_2"] ?></a></span>
									<span class="info-box-number"><?= number_format($client->getTotalCount(),0,".",",") ?></span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
					
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-primary elevation-1">
									<i class="fa fa-handshake-o"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text"><a href="#p"><?= $_SESSION["MENU_2"] ?></a></span>
									<span class="info-box-number"><?= number_format($partner->getTotalCount(),0,".",",") ?></span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->

						<!-- fix for small devices only -->
						<div class="clearfix hidden-md-up"></div>

						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-success elevation-1">
									<i class="fa fa-shopping-cart"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text"><a href="#"><?= $_SESSION["MENU_3"] ?></a></span>
									<span class="info-box-number"><?= number_format($service->getTotalCount(),0,".",",") ?></span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
						
						<div class="col-12 col-sm-6 col-md-3">
							<div class="info-box mb-3">
								<span class="info-box-icon bg-warning elevation-1">
									<i class="fa fa-users"></i>
								</span>
								<div class="info-box-content">
									<span class="info-box-text"><a href="#"><?= $_SESSION["MENU_7"] ?></a></span>
									<span class="info-box-number"><?= $users->getTotalUsers() ?></span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->