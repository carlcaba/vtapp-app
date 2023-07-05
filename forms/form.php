<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	require_once("../core/classes/city.php");
	$city = new city();
	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="apple-touch-icon" sizes="57x57" href="../img/logo/icons/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="../img/logo/icons/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="../img/logo/icons/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="../img/logo/icons/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="../img/logo/icons/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="../img/logo/icons/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="../img/logo/icons/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="../img/logo/icons/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="../img/logo/icons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="../img/logo/icons/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="../img/logo/icons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="../img/logo/icons/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="../img/logo/icons/favicon-16x16.png">
		<meta name="msapplication-TileImage" content="../img/logo/icons/ms-icon-144x144.png">
		<title>UBIO</title>
		<style>
		   @font-face { font-family: Arial !important; font-display: swap !important; }
		</style>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/font-awesome.css" rel="stylesheet">
		<link href="css/forms.css" rel="stylesheet">
		<link href="js/sweetalert2/sweetalert2.css" rel="stylesheet">
		<script type="text/javascript" src="js/jquery.min.js"></script>
	</head>
	<body classname="snippet-body">
		<nav class=" navbar navbar-expand-lg navbar-light bg-light py-lg-0 ">
			<a class="navbar-brand" href="#">
				<img src="../img/logo/logo_app.png" width="180" height="80" alt="UBIO">
			</a>
			<button class="navbar-toggler" type="button"
				data-toggle="collapse" 
				data-target="#navbarResponsive"
				aria-controls="navbarResponsive" 
				aria-expanded="false" 
				aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarResponsive">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item active">
						<a class="nav-link" href="#">Registro
							<span class="sr-only">(current)</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Sobre Ubio</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Sé un aliado</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Planes y precios</a>
					</li>
				</ul>
			</div>
		</nav>		
		<form id="frmRegister" autocomplete="off">
			<div class="container-fluid px-1 py-10 mx-auto">
				<div class="row d-flex justify-content-center">
					<div class="col-xl-5 col-lg-6 col-md-7">
						<div class="card b-0">
							<h3 class="heading">Regístrate <small id="smlOption"></small></h3>
							<p class="desc">Completa la forma o llama al <span class="yellow-text">+57 300 276 1234</span> para que tu empresa empiece a hacer parte de UBIO®</p>
							<ul id="progressbar" class="text-center">
								<li class="active step0" id="step1">1</li>
								<li class="step0" id="step2">2</li>
								<li class="step0" id="step3">3</li>
								<li class="step0" id="step4">4</li>
							</ul>
							<fieldset class="show">
								<div class="form-card">
									<h5 class="sub-heading">Seleccione su opción</h5>
									<div class="row px-1 radio-group">
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1" src="img/type1.png">
											</div>
											<p class="sub-desc" data-ptype="3" data-ctype="2">Envios por demanda</p>
										</div>
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1 fit-image" src="img/type2.png">
											</div>
											<p class="sub-desc" data-ptype="4" data-ctype="3">Mensajería a la medida</p>
										</div>
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1 fit-image" src="img/type3.png">
											</div>
											<p class="sub-desc" data-ptype="2" data-ctype="1">Paquete de servicios</p>
										</div>
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1 fit-image" src="img/type4.png">
											</div>
											<p class="sub-desc" data-ptype="5" data-ctype="1">Afilia tu empresa</p>
										</div>
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1 fit-image" src="img/type5.png">
											</div>
											<p class="sub-desc" data-ptype="1" data-ctype="1">Aliado</p>
										</div>
										<div class="card-block text-center radio">
											<div class="image-icon">
												<img class="icon icon1 fit-image" src="img/type6.png">
											</div>
											<p class="sub-desc" data-ptype="6" data-ctype="4">Contra entrega</p>
										</div>
									</div>
									<button id="next1" type="button" class="btn-block btn-primary mt-3 mb-1 next">SIGUIENTE PASO<span class="fa fa-long-arrow-right"></span></button>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-card">
									<h5 class="sub-heading mb-4">Información personal <small class="text-danger mb-3">* son campos requeridos</small></h5>
									<div class="form-group">
										<label class="form-control-label">Nombre * :</label>
										<input type="text" id="txtName" name="txtName" placeholder="Ingrese su nombre" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Apellido * :</label>
										<input type="text" id="txtLastName" name="txtLastName" placeholder="Ingrese su apellido" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Correo electrónico * :</label>
										<input type="email" id="txtEmail" name="txtEmail" placeholder="Ingrese su correo electrónico" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Teléfono contacto * :</label>
										<input type="text" id="txtPhone" name="txtPhone" placeholder="Ingrese su número de contacto" class="form-control" autocomplete="off">
									</div>
									<button id="next2" type="button" class="btn-block btn-primary mt-3 mb-1 next mt-4">SIGUIENTE PASO<span class="fa fa-long-arrow-right"></span></button>
									<button type="button" class="btn-block btn-secondary mt-3 mb-1 prev"><span class="fa fa-long-arrow-left"></span>ANTERIOR</button>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-card">
									<h5 class="sub-heading mb-4">Información empresarial <small class="text-danger mb-3">* son campos requeridos</small></h5>
									<div class="form-group">
										<label class="form-control-label">Nombre empresa * :</label>
										<input type="text" id="txtCompany" name="txtCompany" placeholder="Ingrese el nombre de su compañía" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Dirección * :</label>
										<input type="text" id="txtAddress" name="txtAddress" placeholder="Ingrese su dirección" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Ciudad * :</label>
										<select name="cbCity" id="cbCity" class="form-control">
											<?= $city->showAllOptionList(9,1) ?>
										</select>
									</div>
									<div class="form-group">
										<label class="form-control-label">Contraseña * :</label>
										<input type="password" id="txtPassword" name="txtPassword" placeholder="Su contraseña de acceso" class="form-control" autocomplete="off">
									</div>
									<div class="form-group">
										<label class="form-control-label">Cantidad envíos mensuales:</label>
										<div class="select mb-3">
											<select name="cbPackages" id="cbPackages" class="form-control">
												<option value="">NA</option>
												<option value="1-20">1-20 envíos/mes</option>
												<option value="20-50">20-50 envíos/mes</option>
												<option value="50-100">50-100 envíos/mes</option>
												<option value="100+">100+ envíos/mes</option>
											</select>
										</div>
									</div>
									<button id="next3" type="button" class="btn-block btn-primary mt-3 mb-1 next mt-4" >ENVIAR<span class="fa fa-long-arrow-right"></span></button>
									<button type="button" class="btn-block btn-secondary mt-3 mb-1 prev"><span class="fa fa-long-arrow-left"></span>ANTERIOR</button>
								</div>
							</fieldset>
							<fieldset>
								<div class="form-card">
									<h5 class="sub-heading mb-4">Success!</h5>
									<p class="message">Thank You for choosing our website.<br>Quotation will be sent to your Email ID and Contact Number.</p>
									<div class="check">
										<img class="fit-image check-img" src="https://i.imgur.com/QH6Zd6Y.gif">
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
		</form>
		<template id="tmplSave">
			<swal-title>
				¿Desea enviar esta información?
			</swal-title>
			<swal-icon type="question" color="red"></swal-icon>
			<swal-button type="confirm">
				Guardar
			</swal-button>
			<swal-button type="cancel">
				Cancelar
			</swal-button>
			<swal-param name="allowEscapeKey" value="false" />
			<swal-param name="customClass" value='{ "popup": "my-popup" }' />
			<swal-function-param name="didOpen" value="console.log('Checking subscription')" />
		</template>		
		<script type="text/javascript" src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		<!-- SweetAlert2 -->
		<script src="js/sweetalert2/sweetalert2.all.js"></script>
		<script src="../js/resources.js"></script>
		<script type="text/javascript">
			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true,
				didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer)
					toast.addEventListener('mouseleave', Swal.resumeTimer)
				}
			});
			const Save = Swal.mixin({
				icon: "question",
				imageUrl: 'img/logo/logo_app.png',
				imageWidth: 214,
				imageHeight: 108,
				imageAlt: 'UBIO',
				showConfirmButton: false
			});
			$(document).ready(function(){
				var current_fs, next_fs, previous_fs;
				$('input[type=text]').on("change", function () {
					$(this).val($(this).val().toUpperCase());
				});
				$(".next").click(function(){
					let id = $(this).attr("id");
					let step1 = true;
					let step2 = true;
					let step3 = true;

					if(id == "next1" && $('.radio.selected').length == 0) {
						Toast.fire({
							icon: 'error',
							title: 'Es necesario que seleccione alguna de las opciones.'
						});
						step1 = false;
					}
					else if(id == "next2") {
						if($("#txtName").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese su nombre.'
							});
							step2 = false;
							$("#txtName").css('border-color', "red");
							return false;
						}
						else {
							$("#txtName").css('border-color', "green");
						}
						if($("#txtLastName").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese su apellido.'
							});
							$("#txtLastName").css('border-color', "red");
							step2 = false;
							return false;
						}
						else {
							$("#txtLastName").css('border-color', "green");
						}
						if($("#txtEmail").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese su correo electrónico.'
							});
							$("#txtEmail").css('border-color', "red");
							step2 = false;
							return false;
						}
						else {
							$("#txtEmail").css('border-color', "green");
						}
						if($("#txtPhone").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese su número de contacto.'
							});
							$("#txtPhone").css('border-color', "red");
							step2 = false;
							return false;
						}
						else {
							$("#txtPhone").css('border-color', "green");
						}
						if(!ValidateEmail($("#txtEmail").val())) {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese un correo electrónico válido.'
							});
							$("#txtEmail").css('border-color', "red");
							step2 = false;
							return false;
						}
						else {
							$("#txtEmail").css('border-color', "green");
						}
					}
					else if(id == "next3") {
						if($("#txtCompany").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese el nombre de su compañía.'
							});
							step3 = false;
							$("#txtCompany").css('border-color', "red");
							return false;
						}
						else {
							$("#txtCompany").css('border-color', "green");
						}
						if($("#txtAddress").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese la dirección.'
							});
							$("#txtAddress").css('border-color', "red");
							step3 = false;
							return false;
						}
						else {
							$("#txtAddress").css('border-color', "green");
						}
						if($("#cbCity option:selected").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor seleccione la ciudad.'
							});
							$("#cbCity").css('border-color', "red");
							step3 = false;
							return false;
						}
						else {
							$("#cbCity").css('border-color', "green");
						}
						if($("#txtPassword").val() == "") {
							Toast.fire({
								icon: 'error',
								title: 'Hay errores en la información ingresada. Por favor ingrese su contraseña de acceso.'
							});
							$("#txtPassword").css('border-color', "red");
							step3 = false;
							return false;
						}
						else {
							$("#txtPassword").css('border-color', "green");
						}
						step3 = false;
						Save.fire({
							template: '#tmplSave'
						}).then((result) => {
							if (result.isConfirmed) {
								let $frm = $("#frmRegister");
								let datasObj = $frm.serializeObject();
								datasObj["ClientType"] = $(".radio.selected").find("p").data("ctype");
								datasObj["PaymentType"] = $(".radio.selected").find("p").data("ptype");
								let datas = JSON.stringify(datasObj);
								let sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
								$.ajax({
									url: "../core/actions/_save/__newRegister.php",
									data: { 
										strModel: datas
									},
									dataType: "json",
									method: "POST",
									beforeSend: function (xhrObj) {
										Toast.fire({
											html: "<h5>Guardando...</h5>",
											showConfirmButton: false
										});
									},
									success:function(data){
										if(data.success && data.error == null) {
											current_fs = $(this).parent().parent();
											next_fs = $(this).parent().parent().next();
											$(current_fs).removeClass("show");
											$(next_fs).addClass("show");

											$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
											current_fs.animate({}, {
												step: function() {
													current_fs.css({
														'display': 'none',
														'position': 'relative'
													});
													next_fs.css({
														'display': 'block'
													});
												}
											});
											Save.fire('Su información fue registrada correctamente.<br /><small><strong>Su usuario</strong>: ' + data.access.user + '<br/><strong>Su contraseña</strong>: ' + data.access.pass + '</small>', '', 'success').then((result) =>{
												location.href = data.link;
											});
										}
										else {
											Save.fire({
												icon: 'error',
												html: '<h5>' + data.message + '</h5>'
											});
											return false;										
										}
									},
									error: function(xhr, ajaxOptions, thrownError) {
										Save.fire({
											icon: 'error',
											html: '<h5>' + thrownError + '</h5>'
										});
										return false;										
									}
								});
								return false;
							}
							else if (result.isDismissed) {
								Save.fire('Revise la información e intente nuevamente', '', 'info')
								return false;
							}
						});
					}
					let str1 = "next1";
					let str2 = "next2";
					let str3 = "next3";

					if(step1 && step2 && step3) {
						current_fs = $(this).parent().parent();
						next_fs = $(this).parent().parent().next();
						$(current_fs).removeClass("show");
						$(next_fs).addClass("show");

						$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
						current_fs.animate({}, {
							step: function() {
								current_fs.css({
									'display': 'none',
									'position': 'relative'
								});
								next_fs.css({
									'display': 'block'
								});
							}
						});
						return true;
					}
					return false;
				});
				$(".prev").click(function(){
					current_fs = $(this).parent().parent();
					previous_fs = $(this).parent().parent().prev();
					$(current_fs).removeClass("show");
					$(previous_fs).addClass("show");
					$("#progressbar li").eq($("fieldset").index(next_fs)).removeClass("active");
					current_fs.animate({}, {
						step: function() {
							current_fs.css({
								'display': 'none',
								'position': 'relative'
							});
							previous_fs.css({
								'display': 'block'
							});
						}
					});
				});
				$('.radio-group .radio').click(function(){
					$('.radio-group .radio').removeClass("selected");
					$(this).addClass('selected');
					$("#smlOption").html($(this).find("p").html());
				});
			});
			var myLink = document.querySelectorAll('a[href="#"]');
			myLink.forEach(function(link){
				link.addEventListener('click', function(e) {
					e.preventDefault();
				});
			});
			function ValidateEmail(mail) {
				if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
					return (true);
				}
				return false;
			}			
		</script>
	</body>
</html>