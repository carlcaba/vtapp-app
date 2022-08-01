<?
	if(!isset($loadScripts))
		$loadScripts = true;
?>
		<footer class="main-footer">
			<div class="row">
				<div class="col-sm-3" align="center">
					<a href="#" class="nav-link ">
						<p><i class="nav-icon fa fa-question-circle"></i> <?= $_SESSION["HELP"] ?></p>
					</a>
				</div>
				<div class="col-sm-3" align="center">
					<a href="faqs.php" class="nav-link ">
						<p><i class="nav-icon fa fa-info-circle"></i> <?= $_SESSION["FAQS"] ?></p>
					</a>
				</div>
				<div class="col-sm-3" align="center">
					<a href="terms.php" class="nav-link ">
						<p><i class="nav-icon fa fa-book"></i> <?= $_SESSION["TERMS_AND_CONDITIONS"] ?></p>
					</a>
				</div>
				<div class="col-sm-3" align="center">
					<a href="#" class="nav-link ">
						<p><i class="nav-icon fa fa-graduation-cap"></i> <?= $_SESSION["TUTORIAL"] ?></p>
					</a>
				</div>
			</div>
			<strong><a href="http://www.vtapp.com" target="_blank">Vtapp</a> - v.2.8 - Copyright &copy; 2019-<a href="javascript: showVariables();"><?= date("Y") ?></a></strong>
			<!--
			<a href="http://www.logicaestudio.com" target="_blank">Lógica Estudio</a>.</strong> All rights reserved.
			-->
			<div class="float-right d-none d-sm-inline-block"><b>AdmLTE Version</b> 3.1.0</div>
		</footer>
	</div>
	<!-- ./wrapper -->
	
<?
	if($loadScripts) {
?>
	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="plugins/jQueryUI/jquery-ui.1.12.1.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
	  $.widget.bridge('uibutton', $.ui.button)
	</script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- Morris.js charts -->
	<script src="plugins/raphael/raphael-min.js"></script>
	<script src="plugins/morris/morris.min.js"></script>
	<!-- Sparkline -->
	<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
	<!-- jvectormap -->
	<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<!-- jQuery Knob Chart -->
	<script src="plugins/knob/jquery.knob.js"></script>
	<!-- daterangepicker -->
	<script src="plugins/moment/moment.min.js"></script>
	<script src="plugins/daterangepicker/daterangepicker.js"></script>
	<!-- datepicker -->
	<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
	<!-- Bootstrap WYSIHTML5 -->
	<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
	<!-- Slimscroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>

	<!-- AdminLTE App -->
	<script src="js/adminlte.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="js/demo.js"></script>
<?
	}
?>