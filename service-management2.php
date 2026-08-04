<?

/*	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();

	$source = "";
	if(!empty($_GET['src'])) {
		$source = $_GET['src'];
	}
	
	$filename = "services.php" . ($source == "" ? "" : "?src=" . $source);

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	require_once("core/classes/users.php");
	$uscli = new users($_SESSION["vtappcorp_userid"]);

	$action = "";
	$id = "";
	$userId = "";
	if(!empty($_GET['id'])) {
		$id = $_GET['id'];
	}
	if(!empty($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	if($id == "" && $action != "new") {
		//Verifica si es un aliado 
		if(substr($uscli->access->PREFIX,0,2) == "AL")
			$id = $uscli->REFERENCE;
		//Si sigue siendo vacio
		if($id == "")
			$inter->redirect("services.php");		
	}
	
	require_once("core/classes/service.php");
	$service = new service();

	if($id != "") {
		//Asigna la informacion
		$service->ID = $id;
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["NOT_REGISTERED"];
			$id = "";
		}
	}

	//Verifica si tiene servicio asignado
	require_once("core/classes/assign_service.php");
	$assi = new assign_service();
	
	switch($action) {
		case "new": {
			$titlepage = substr($_SESSION["MENU_NEW"],-1) == "o" ? substr($_SESSION["MENU_NEW"],0,-1) . "a" : $_SESSION["MENU_NEW"];
			$text_title =  "Ingrese la información solicitada para crear un nuevo registro. <small>Los campos marcados con * son requeridos.</small>";
			break;
		}
		case "edit": {
			$titlepage = "Editar";
			$text_title =  "Modifique la información disponible. No todos los campos son editables. <small>Los campos marcados con * son requeridos</small>";
			break;
		}
		case "delete": {
			$titlepage = "Confirme que desea eliminar este registro.";
			$text_title =  $_SESSION["DELETE_TEXT"];
			break;
		}
		case "view": {
			$titlepage = $_SESSION["VIEW"];
			$text_title =  "Información";
			
			$assi->setService($id);
			$assi->getInformationByService();
			if($assi->nerror > 0)
				$assi = new assign_service();
				
			break;
		}
	}
	
	$dataForm = $service->dataForm($action);
	
	//Inicia el contador
	$cont = 0;
	$payment = true;
	$userId = $uscli->ID;
	
	//Verifica si es un aliado 
	if(substr($uscli->access->PREFIX,0,2) == "AL") {
		$dataForm["readonly"][14] = "disabled";
		$payment = false;
	}
	else if(substr($uscli->access->PREFIX,0,2) == "CL") {
		$dataForm["readonly"][12] = "disabled";
		$dataForm["readonly"][14] = "disabled";
		$service->setClient($uscli->REFERENCE);
	}

	$gate = $conf->verifyValue("PAYMENT_GATEWAY");
	$accTok = 0;
	$err = 0;

	//Verifica la pasarela
	if($gate == "WOMPI") {
		//Libreria requerida
		require_once("core/classes/ws_query.php");
		require_once("core/actions/_save/__wompiGatewayFunctions.php");

		$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
		$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
		$urlReturn = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $conf->verifyValue("PAYMENT_WOMPI_REDIRECT");
		
		$accTok = 1;
		
		//Obtiene el acceptance token
		$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

		//Si no es null
		if($accTokRet["token"] != null) {
			$accTokData = $accTokRet["token"];
		}
		else {
			$err = 1;
		}
	}

	if($uscli->PHONE == "" && $uscli->CELLPHONE != "")
		$uscli->PHONE = $uscli->CELLPHONE;
	
	$max_size = $conf->verifyValue("MAXIMUM_SIZE");
	$max_weight = $conf->verifyValue("MAXIMUM_WEIGHT");
	*/
	
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
		<title>UBIO - Nuevo servicio</title>
		<meta name="description" content="Nuevo servicio" />
		<!-- Favicon -->
		<link rel="icon" type="image/x-icon" href="img/logo/icons/favicon.ico" />

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link
		  href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
		  rel="stylesheet" />

		<!-- Icons -->
		<link rel="stylesheet" href="css/new-service/fonts/fontawesome.css" />
		<link rel="stylesheet" href="css/new-service/fonts/tabler-icons.css" />
		<link rel="stylesheet" href="css/new-service/fonts/flag-icons.css" />

		<!-- Core CSS -->
		<link rel="stylesheet" href="css/new-service/rtl/core.css" class="template-customizer-core-css" />
		<link rel="stylesheet" href="css/new-service/rtl/theme-default.css" class="template-customizer-theme-css" />
		<link rel="stylesheet" href="css/new-service/demo.css" />

		<!-- Vendors CSS -->
		<link rel="stylesheet" href="css/new-service/libs/perfect-scrollbar/perfect-scrollbar.css" />
		<link rel="stylesheet" href="css/new-service/libs/node-waves/node-waves.css" />
		<link rel="stylesheet" href="css/new-service/libs/typeahead-js/typeahead.css" />
		<link rel="stylesheet" href="css/new-service/libs/bs-stepper/bs-stepper.css" />
		<link rel="stylesheet" href="css/new-service/libs/bootstrap-select/bootstrap-select.css" />
		<link rel="stylesheet" href="css/new-service/libs/select2/select2.css" />

		<!-- Page CSS -->

		<!-- Helpers -->
		<script src="js/new-service/helpers.js"></script>

		<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
		<!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
		<script src="js/new-service/template-customizer.js"></script>
		<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
		<script src="js/new-service/config.js"></script>
	</head>

	<body>
		<!-- Layout wrapper -->
		<div class="layout-wrapper layout-content-navbar">
			<div class="layout-container">
				<!-- Menu -->
				<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
					<div class="app-brand demo">
						<a href="dashboard.php" class="brand-link ">
							<img src="img/logo/only_logo.png" alt="UBIO" class="brand-image elevation-3" style="opacity: .8">
							<span class="ubio-brand">
								ubio
							</span>
							<span class="brand-text font-weight-light"><small class="brand-text small-brand-text"> &nbsp;Admin</small></span>
						</a>
					</div>
					<div class="user-panel mt-3 pb-3 mb-3 d-flex">
						<div class="image">
							<img src="img/users/carlcaba_160x160.jpg" class="img-circle elevation-2" alt="carlcaba" id="imgUserInfo">
						</div>
						<div class="info">
							<a href="profile.php?id=carlcaba" class="d-block">ADMINISTRADOR </a>
						</div>
					</div>					
					<div class="menu-inner-shadow"></div>
					<ul class="menu-inner py-1">
						<li class="menu-item">
							<!-- Aca depende si el usuario es externo o ya se habia loggeado en UBIO -->
							<a href="#" class="menu-link">
								<i class="menu-icon tf-icons ti ti-home"></i>
								<div data-i18n="Inicio">Inicio</div>
							</a>
						</li>						
						<!-- Dashboards -->
						<li class="menu-item">
							<a href="javascript:void(0);" class="menu-link menu-toggle">
								<i class="menu-icon tf-icons ti ti-motorbike"></i>
								<div data-i18n="Servicios">Servicios</div>
								<div class="badge bg-label-primary rounded-pill ms-auto">3</div>
							</a>
							<ul class="menu-sub">
								<li class="menu-item">
									<a href="#" class="menu-link">
										<div data-i18n="Nuevo">Nuevo</div>
									</a>
								</li>
								<li class="menu-item">
									<a href="#" class="menu-link">
										<div data-i18n="Gesti&oacute;n">Gesti&oacute;n</div>
									</a>
								</li>
								<li class="menu-item">
									<a href="#" class="menu-link">
										<div data-i18n="Mis servicios">Mis servicios</div>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</aside>
				<!-- / Menu -->
				<!-- Layout container -->
				<div class="layout-page">
					<!-- Content wrapper -->
					<div class="content-wrapper">
						<!-- Content -->
						<div class="container-xxl flex-grow-1 container-p-y">
							<h4 class="fw-bold py-3 mb-4">
								<span class="text-muted fw-light">
									Inicio /
								</span> 
								Solicitar servicio
							</h4>
							<p class="mb-2">
								Completando la siguiente informaci&oacute;n puede solicitar un nuevo servicio. <br /><small>Los campos marcados con * son requeridos.</small>
							</p>
							<!-- Default -->
							<div class="row">
								<div class="col-12">
									<h5>Nuevo servicio</h5>
								</div>	
								<!-- Vertical Icons Wizard -->
								<div class="col-12">
									<div class="bs-stepper vertical wizard-vertical-icons-example">
										<div class="bs-stepper-header">
											<div class="step active" data-target="#account">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle">
														<i class="ti ti-user-search"></i>
													</span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Cuenta</span>
														<span class="bs-stepper-subtitle">Detalles de tu cuenta</span>
													</span>
												</button>
											</div>
											<div class="line"></div>
											<div class="step" data-target="#origin">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle">
														<i class="ti ti-current-location"></i>
													</span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Origen</span>
														<span class="bs-stepper-subtitle">¿Dónde debemos recoger tu envío?</span>
													</span>
												</button>
											</div>
											<div class="line"></div>
											<div class="step" data-target="#destiny">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle"><i class="ti ti-map-2"></i> </span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Destino</span>
														<span class="bs-stepper-subtitle">¿Adónde lo debemos llevar?</span>
													</span>
												</button>
											</div>
											<div class="line"></div>
											<div class="step" data-target="#paquete">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle"><i class="ti ti-package"></i> </span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Paquete</span>
														<span class="bs-stepper-subtitle">¿Qué es lo que debemos llevar?</span>
													</span>
												</button>
											</div>
											<div class="line"></div>
											<div class="step" data-target="#operador">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle"><i class="ti ti-truck"></i> </span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Operador</span>
														<span class="bs-stepper-subtitle">¿Quién lo va a llevar?</span>
													</span>
												</button>
											</div>
											<div class="line"></div>
											<div class="step" data-target="#summary">
												<button type="button" class="step-trigger">
													<span class="bs-stepper-circle"><i class="ti ti-file-dots"></i> </span>
													<span class="bs-stepper-label">
														<span class="bs-stepper-title">Resumen</span>
														<span class="bs-stepper-subtitle">Resumen de tu solicitud.</span>
													</span>
												</button>
											</div>
										</div>
										<div class="bs-stepper-content">
											<form onSubmit="return false">
												<!-- Account Details -->
												<div id="account" class="content active">
													<div class="content-header mb-3">
														<h6 class="mb-0">Detalles de la cuenta</h6>
														<small>Ingresa los detalles de tu cuenta o tus datos.</small>
													</div>
													<div class="row g-3">
														<div class="col-sm-6">
															<label class="form-label" for="txtUser">Usuario (Si estás registrado)</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-user"></i>
																</span>															
																<input type="text" id="txtUser" name="txtUser" class="form-control" placeholder="john.doe" />
															</div>
														</div>
														<div class="col-sm-6 form-password-toggle">
															<label class="form-label" for="txtPassword">Contraseña (Si tienes usuario)</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-key"></i>
																</span>															
																<input
																	type="password"
																	id="txtPassword"
																	name="txtPassword"
																	class="form-control"
																	placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
																	aria-describedby="confirm-password7" />
																<span class="input-group-text cursor-pointer" id="confirm-password7">
																	<i class="ti ti-eye-off"></i>
																</span>
															</div>
														</div>
														<div class="col-sm-6">
															<label class="form-label" for="txtEmail">Email <small>*</small></label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-mail"></i>
																</span>															
																<input
																	type="text"
																	id="txtEmail"
																	name="txtEmail"
																	class="form-control"
																	placeholder="Ej: john.doe@domain.com"
																	aria-label="Ej: john.doe@domain.com" />
															</div>
														</div>
														<div class="col-sm-6">
															<label class="form-label" for="txtCellphone">Celular <small>*</small></label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-phone"></i>
																</span>															
																<input
																	type="text"
																	id="txtCellphone"
																	name="txtCellphone"
																	class="form-control"
																	placeholder="Ej: 310123456"
																	aria-describedby="Ej: 310123456" />
															</div>
														</div>
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev" disabled>
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-primary btn-next">
																<span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
																<i class="ti ti-arrow-right"></i>
															</button>
														</div>
													</div>
												</div>
												
												<div id="origin" class="content">
													<div class="content-header mb-3">
														<h6 class="mb-0">Origen</h6>
														<small>Ingresa la información de donde recogemos tu envío.</small>
													</div>
													<div class="row g-3">
														<div class="col-sm-4">
															<label class="form-label" for="txtNameRequest">Nombre *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-user"></i>
																</span>															
																<input type="text" id="txtNameRequest" name="txtNameRequest" class="form-control" placeholder="Ej: John Doe" />
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="txtEmailRequest">Email *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-mail"></i>
																</span>															
																<input
																	type="text"
																	id="txtEmailRequest"
																	name="txtEmailRequest"
																	class="form-control"
																	placeholder="Ej: john.doe@domain.com"
																	aria-label="Ej: john.doe@domain.com" />
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="txtCellphoneRequest">Celular *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-phone"></i>
																</span>															
																<input
																	type="text"
																	id="txtCellphoneRequest"
																	name="txtCellphoneRequest"
																	class="form-control"
																	placeholder="Ej: 310123456"
																	aria-describedby="Ej: 310123456" />
															</div>
														</div>
														<div class="col-sm-12">
															<label class="form-label" for="txtAddressRequest">Dirección *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-home-2"></i>
																</span>															
																<input
																	type="text"
																	id="txtAddressRequest"
																	name="txtAddressRequest"
																	class="form-control"
																	placeholder="Ej: Avenida Siempre Viva # 36-58 Apartamento 508"
																	aria-describedby="Ej: Avenida Siempre Viva # 36-58 Apartamento 508" />
																<span class="input-group-text cursor-pointer" id="check-map" title="Buscar en mapa">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0" /></svg>
																</span>
																<span class="input-group-text cursor-pointer" id="current-location" title="Usar ubicación actual">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-current-location"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1a1 1 0 0 1 1 1v1.055a9.004 9.004 0 0 1 7.946 7.945h1.054a1 1 0 0 1 0 2h-1.055a9.004 9.004 0 0 1 -7.944 7.945l-.001 1.055a1 1 0 0 1 -2 0v-1.055a9.004 9.004 0 0 1 -7.945 -7.944l-1.055 -.001a1 1 0 0 1 0 -2h1.055a9.004 9.004 0 0 1 7.945 -7.945v-1.055a1 1 0 0 1 1 -1m0 4a7 7 0 1 0 0 14a7 7 0 0 0 0 -14m0 3a4 4 0 1 1 -4 4l.005 -.2a4 4 0 0 1 3.995 -3.8" /></svg>
																</span>
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbCityR">Ciudad</label>
															<select class="select2 fnCityChange" id="cbCityR">
																<option label="" value=""></option>
																<option value="1">Bogotá D.C.</option>
																<option value="9">Chía</option>
																<option value="2">Medellín</option>
																<option value="4">Cali</option>
																<option value="6">Ibagué</option>
															</select>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbLocaleR">Localidad</label>
															<select class="select2" id="cbLocaleR">
																<option label=""></option>
																<option value="16">ANTONIO NARIÑO</option><option value="13">BARRIOS UNIDOS</option><option value="8">BOSA</option><option value="3">CHAPINERO</option><option value="20">CIUDAD BOLIVAR</option><option value="11">ENGATIVA</option><option value="10">FONTIBON</option><option value="9">KENNEDY</option><option value="18">LA CANDELARIA</option><option value="15">LOS MARTIRES</option><option value="1">NO DEFINIDA</option><option value="17">PUENTE ARANDA</option><option value="19">RAFAEL URIBE URIBE</option><option value="5">SAN CRISTOBAL</option><option value="4">SANTA FE</option><option value="12">SUBA</option><option value="21">SUMAPAZ</option><option value="14">TEUSAQUILLO</option><option value="7">TUNJUELITO</option><option value="2">USAQUEN</option><option value="6">USME</option>
															</select>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbTownR">Barrio</label>
															<select class="select2" id="cbTownR">
																<option label=""></option>
																<option value="268" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">ALAMOS NORTE</option><option value="269" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">AUTOPISTA MEDELLIN</option><option value="270" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BELLAVISTA OCCIDENTAL</option><option value="271" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOCHICA</option><option value="272" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOCHICA II</option><option value="273" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA</option><option value="274" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA</option><option value="275" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA ORIENTAL</option><option value="276" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BONANZA</option><option value="277" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOSQUE POPULAR</option><option value="278" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOYACA</option><option value="279" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CENTRO ENGATIVA</option><option value="280" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDAD BACHUE</option><option value="281" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDAD BACHUE I</option><option value="282" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDADELA COLSUBSIDIO</option><option value="283" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CEDRO</option><option value="284" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CEDRO A.S.D.</option><option value="285" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CORTIJO</option><option value="286" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL DORADO</option><option value="287" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL ENCANTO</option><option value="288" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL LAUREL</option><option value="289" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL MADRIGAL</option><option value="290" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL MUELLE</option><option value="291" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL REAL</option><option value="292" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">ENGATIVA</option><option value="293" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORENCIA</option><option value="294" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORIDA BLANCA</option><option value="295" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORIDA BLANCA NORTE</option><option value="296" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">GARCES NAVAS</option><option value="297" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">GARCES NAVAS ORIENTAL</option><option value="298" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">JARDIN BOTANICO</option><option value="299" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA CABANA</option><option value="300" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA ESTRADA</option><option value="301" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA ESTRADITA</option><option value="302" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA GRANJA</option><option value="303" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA PRIMAVERA</option><option value="304" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA SERENA</option><option value="305" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA SOLEDAD NORTE</option><option value="306" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LAS FERIAS</option><option value="307" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LAS FERIAS OCCIDENTAL</option><option value="308" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS ALAMOS</option><option value="309" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS ANGELES</option><option value="310" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS CEREZOS</option><option value="311" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">MINUTO DE DIOS</option><option value="312" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">NORMANDIA</option><option value="313" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">NORMANDIA OCCIDENTAL</option><option value="314" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PALOBLANCO</option><option value="315" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PARIS</option><option value="316" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PARIS GAITAN</option><option value="317" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">QUINTAS DE STA. BARBARA</option><option value="318" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">QUIRIGUA</option><option value="319" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SAN IGNACIO</option><option value="320" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SAN JOAQUIN</option><option value="321" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA  HELENITA</option><option value="322" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA CECILIA</option><option value="323" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA MARIA</option><option value="324" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA MONICA</option><option value="325" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SN. ANTONIO ENGATIVA</option><option value="326" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">TABORA</option><option value="327" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA AMALIA</option><option value="328" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA DEL MAR</option><option value="329" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA GLADYS</option><option value="330" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA LUZ</option><option value="331" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLAS DE GRANADA</option><option value="332" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLAS DE GRANADA I</option>
															</select>
														</div>
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev">
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-primary btn-next">
																<span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
																<i class="ti ti-arrow-right"></i>
															</button>
														</div>
													</div>
												</div>
												
												<div id="destiny" class="content">
													<div class="content-header mb-3">
														<h6 class="mb-0">Destino</h6>
														<small>Ingresa la información de a quien y donde entregamos tu envío.</small>
													</div>
													<div class="row g-3">
														<div class="col-sm-4">
															<label class="form-label" for="txtNameDeliver">Nombre *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-user"></i>
																</span>															
																<input type="text" id="txtNameDeliver" name="txtNameDeliver" class="form-control" placeholder="Ej: John Doe" />
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="txtEmailDeliver">Email *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-mail"></i>
																</span>															
																<input
																	type="text"
																	id="txtEmailDeliver"
																	name="txtEmailDeliver"
																	class="form-control"
																	placeholder="Ej: john.doe@domain.com"
																	aria-label="Ej: john.doe@domain.com" />
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="txtCellphoneDeliver">Celular *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-phone"></i>
																</span>															
																<input
																	type="text"
																	id="txtCellphoneDeliver"
																	name="txtCellphoneDeliver"
																	class="form-control"
																	placeholder="Ej: 310123456"
																	aria-describedby="Ej: 310123456" />
															</div>
														</div>
														<div class="col-sm-12">
															<label class="form-label" for="txtAddressDeliver">Dirección *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-home-2"></i>
																</span>															
																<input
																	type="text"
																	id="txtAddressDeliver"
																	name="txtAddressDeliver"
																	class="form-control"
																	placeholder="Ej: Avenida Siempre Viva # 36-58 Apartamento 508"
																	aria-describedby="Ej: Avenida Siempre Viva # 36-58 Apartamento 508" />
																<span class="input-group-text cursor-pointer" id="check-map" title="Buscar en mapa">
																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0" /></svg>
																</span>
															</div>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbCityD">Ciudad</label>
															
															<select class="select2 fnCityChange" id="cbCityD">
																<option label="" value=""></option>
																<option value="1">Bogotá D.C.</option>
																<option value="9">Chía</option>
																<option value="2">Medellín</option>
																<option value="4">Cali</option>
																<option value="6">Ibagué</option>
															</select>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbLocaleD">Localidad</label>
															<select class="select2" id="cbLocaleD">
																<option label=""></option>
																<option value="16">ANTONIO NARIÑO</option><option value="13">BARRIOS UNIDOS</option><option value="8">BOSA</option><option value="3">CHAPINERO</option><option value="20">CIUDAD BOLIVAR</option><option value="11">ENGATIVA</option><option value="10">FONTIBON</option><option value="9">KENNEDY</option><option value="18">LA CANDELARIA</option><option value="15">LOS MARTIRES</option><option value="1">NO DEFINIDA</option><option value="17">PUENTE ARANDA</option><option value="19">RAFAEL URIBE URIBE</option><option value="5">SAN CRISTOBAL</option><option value="4">SANTA FE</option><option value="12">SUBA</option><option value="21">SUMAPAZ</option><option value="14">TEUSAQUILLO</option><option value="7">TUNJUELITO</option><option value="2">USAQUEN</option><option value="6">USME</option>
															</select>
														</div>
														<div class="col-sm-4">
															<label class="form-label" for="cbTownD">Barrio</label>
															<select class="select2" id="cbTownD">
																<option label=""></option>
																<option value="268" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">ALAMOS NORTE</option><option value="269" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">AUTOPISTA MEDELLIN</option><option value="270" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BELLAVISTA OCCIDENTAL</option><option value="271" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOCHICA</option><option value="272" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOCHICA II</option><option value="273" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA</option><option value="274" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA</option><option value="275" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOLIVIA ORIENTAL</option><option value="276" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BONANZA</option><option value="277" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOSQUE POPULAR</option><option value="278" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">BOYACA</option><option value="279" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CENTRO ENGATIVA</option><option value="280" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDAD BACHUE</option><option value="281" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDAD BACHUE I</option><option value="282" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">CIUDADELA COLSUBSIDIO</option><option value="283" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CEDRO</option><option value="284" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CEDRO A.S.D.</option><option value="285" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL CORTIJO</option><option value="286" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL DORADO</option><option value="287" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL ENCANTO</option><option value="288" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL LAUREL</option><option value="289" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL MADRIGAL</option><option value="290" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL MUELLE</option><option value="291" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">EL REAL</option><option value="292" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">ENGATIVA</option><option value="293" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORENCIA</option><option value="294" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORIDA BLANCA</option><option value="295" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">FLORIDA BLANCA NORTE</option><option value="296" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">GARCES NAVAS</option><option value="297" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">GARCES NAVAS ORIENTAL</option><option value="298" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">JARDIN BOTANICO</option><option value="299" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA CABANA</option><option value="300" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA ESTRADA</option><option value="301" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA ESTRADITA</option><option value="302" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA GRANJA</option><option value="303" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA PRIMAVERA</option><option value="304" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA SERENA</option><option value="305" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LA SOLEDAD NORTE</option><option value="306" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LAS FERIAS</option><option value="307" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LAS FERIAS OCCIDENTAL</option><option value="308" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS ALAMOS</option><option value="309" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS ANGELES</option><option value="310" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">LOS CEREZOS</option><option value="311" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">MINUTO DE DIOS</option><option value="312" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">NORMANDIA</option><option value="313" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">NORMANDIA OCCIDENTAL</option><option value="314" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PALOBLANCO</option><option value="315" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PARIS</option><option value="316" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">PARIS GAITAN</option><option value="317" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">QUINTAS DE STA. BARBARA</option><option value="318" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">QUIRIGUA</option><option value="319" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SAN IGNACIO</option><option value="320" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SAN JOAQUIN</option><option value="321" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA  HELENITA</option><option value="322" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA CECILIA</option><option value="323" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA MARIA</option><option value="324" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SANTA MONICA</option><option value="325" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">SN. ANTONIO ENGATIVA</option><option value="326" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">TABORA</option><option value="327" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA AMALIA</option><option value="328" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA DEL MAR</option><option value="329" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA GLADYS</option><option value="330" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLA LUZ</option><option value="331" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLAS DE GRANADA</option><option value="332" data-parent="11" data-latitude="4.7071" data-longitude="-74.1072">VILLAS DE GRANADA I</option>
															</select>
														</div>
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev">
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-primary btn-next">
																<span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
																<i class="ti ti-arrow-right"></i>
															</button>
														</div>
													</div>
												</div>
												
												<div id="paquete" class="content">
													<div class="content-header mb-3">
														<h6 class="mb-0">Paquete</h6>
														<small>Ingresa la información de lo que debemos llevar.</small>
													</div>
													<div class="row g-3">
														<div class="col-sm-2">
															<label class="form-label" for="cbTipo">Tipo de paquete *</label>
															<select class="select2" id="cbTipo">
																<option label=""></option>
																<option>Paquete</option>
																<option>Sobre</option>
																<option>Caja</option>
															</select>
														</div>
														<div class="col-sm-2">
															<label class="form-label" for="txtQuantity">Cantidad *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-box"></i>
																</span>															
																<input
																	type="text"
																	id="txtQuantity"
																	name="txtQuantity"
																	class="form-control"
																	placeholder="Ej: 1"
																	value="1" 
																	aria-label="Ej: 1" />
															</div>
														</div>
														<div class="col-sm-2">
															<label class="form-label" for="txtWidth">Ancho (cm) *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-arrow-autofit-width"></i>
																</span>															
																<input
																	type="text"
																	id="txtWidth"
																	name="txtWidth"
																	class="form-control"
																	placeholder="Ej: 100"
																	aria-describedby="Ej: 100" />
															</div>
														</div>
														<div class="col-sm-2">
															<label class="form-label" for="txtHeight">Alto (cm) *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-arrow-autofit-height"></i>
																</span>															
																<input
																	type="text"
																	id="txtHeight"
																	name="txtHeight"
																	class="form-control"
																	placeholder="Ej: 100"
																	aria-describedby="Ej: 100" />
															</div>
														</div>
														<div class="col-sm-2">
															<label class="form-label" for="txtLength">Largo (cm) *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-ruler"></i>
																</span>															
																<input
																	type="text"
																	id="txtLength"
																	name="txtLength"
																	class="form-control"
																	placeholder="Ej: 100"
																	aria-describedby="Ej: 100" />
															</div>
														</div>
														<div class="col-sm-2">
															<label class="form-label" for="txtWeigth">Peso (gr) *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-barbell"></i>
																</span>															
																<input
																	type="text"
																	id="txtWeigth"
																	name="txtWeigth"
																	class="form-control"
																	placeholder="Ej: 100"
																	aria-describedby="Ej: 100" />
															</div>
														</div>
														<div class="col-sm-12">
															<label class="form-label" for="txtDescription">Descripción *</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-file-text"></i>
																</span>															
																<input
																	type="text"
																	id="txtDescription"
																	name="txtDescription"
																	class="form-control"
																	placeholder="Ej: Documentos para firmar"
																	aria-describedby="Ej: Documentos para firmar" />
															</div>
														</div>
														<div class="col-sm-3 mt-5">
															<label class="switch switch-square switch-lg">
																<input type="checkbox" class="switch-input" />
																<span class="switch-toggle-slider">
																	<span class="switch-on">
																		<i class="ti ti-check"></i>
																	</span>
																	<span class="switch-off">
																		<i class="ti ti-x"></i>
																	</span>
																</span>
																<span class="switch-label">¿Es frágil?</span>
															</label>														
														</div>
														<div class="col-sm-3 mt-5">
															<label class="switch switch-square switch-lg">
																<input type="checkbox" class="switch-input" />
																<span class="switch-toggle-slider">
																	<span class="switch-on">
																		<i class="ti ti-check"></i>
																	</span>
																	<span class="switch-off">
																		<i class="ti ti-x"></i>
																	</span>
																</span>
																<span class="switch-label">¿Requiere ida y vuelta?</span>
															</label>														
														</div>
														<div class="col-sm-3">
															<label class="form-label" for="cbHorario">Hora de entrega</label>
															<select class="select2" id="cbHorario">
																<option label=""></option>
																<option value="17" data-end="18">5 PM - 6 PM</option>
																<option value="18" data-end="19">6 PM - 7 PM</option>
																<option value="19" data-end="20">7 PM - 8 PM</option>
																<option value="20" data-end="21">8 PM - 9 PM</option>
																<option value="21" data-end="22">9 PM - 10 PM</option>
																<option value="22" data-end="23">10 PM - 11 PM</option>
																<option value="23" data-end="24">11 PM - 12 PM</option>
															</select>
														</div>		
														<div class="col-sm-3">
															<label class="form-label" for="txtPrecio">Precio</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-file-dollar"></i>
																</span>															
																<input
																	disabled
																	type="text"
																	id="txtPrecio"
																	name="txtPrecio"
																	class="form-control"
																	placeholder="Ej: 5.000"
																	aria-describedby="Ej: 5.000" />
															</div>
														
														</div>
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev">
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-primary btn-next">
																<span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
																<i class="ti ti-arrow-right"></i>
															</button>															
														</div>
													</div>
												</div>

												<div id="operador" class="content">
													<div class="content-header mb-3">
														<h6 class="mb-0">Operador</h6>
														<small>Selecciona la información de la empresa de mensajería.</small>
													</div>
													<div class="row g-3">
														<div class="col-12">
															<!-- Hoverable Table rows -->
															<div class="card">
																	<div class="table-responsive text-nowrap">
																		<table class="table table-hover">
																		<thead>
																			<tr>
																				<th>Empresa</th>
																				<th>Tiempo</th>
																				<th>Vehículos</th>
																				<th>Estado</th>
																				<th>Costo</th>
																				<th>Acciones</th>
																			</tr>
																		</thead>
																		<tbody class="table-border-bottom-0">
																		<tr>
																			<td>
																				<img src="img/partners/b3dc8655-0750-11eb-a413-52540003f9f1.png" class="img-fluid">
																				<strong>Empresa 1</strong>
																			</td>
																			<td>30 - 45 minutos</td>
																			<td>
																				<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Bicicleta">
																						<img src="img/vehicles/1.png" alt="Avatar" class="rounded-circle" />
																					</li>
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Motocicleta">
																						<img src="img/vehicles/2.png" alt="Avatar" class="rounded-circle" />
																					</li>
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Camión">
																						<img src="img/vehicles/3.png" alt="Avatar" class="rounded-circle" />
																					</li>
																				</ul>
																			</td>
																			<td><span class="badge bg-label-primary me-1">Activos</span></td>
																			<td>$35,000.00</td>
																			<td>
																				<div class="dropdown">
																					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
																						<i class="ti ti-dots-vertical"></i>
																					</button>
																					<div class="dropdown-menu">
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-pencil me-1"></i> Editar
																						</a>
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-trash me-1"></i> Eliminar
																						</a>
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<img src="img/partners/63fa66a7-9aff-11ea-8350-54e6f54a840f.png" class="img-fluid">																			
																				<strong>Empresa 2</strong>
																			</td>
																			<td>45 - 60 minutos</td>
																			<td>
																				<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Motocicleta">
																						<img src="img/vehicles/2.png" alt="Avatar" class="rounded-circle" />
																					</li>
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Camión">
																						<img src="img/vehicles/3.png" alt="Avatar" class="rounded-circle" />
																					</li>
																				</ul>
																			</td>
																			<td><span class="badge bg-label-success me-1">Disponibles</span></td>
																			<td>$45,000.00</td>
																			<td>
																				<div class="dropdown">
																					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
																						<i class="ti ti-dots-vertical"></i>
																					</button>
																					<div class="dropdown-menu">
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-pencil me-1"></i> Editar
																						</a>
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-trash me-1"></i> Eliminar
																						</a>
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<img src="img/partners/cd47da34-e16a-11e9-a869-52540003f9f1.png" class="img-fluid">																			
																				<strong>Empresa 3</strong>
																			</td>
																			<td>60 - 90 minutos</td>
																			<td>
																				<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Motocicleta">
																						<img src="img/vehicles/2.png" alt="Avatar" class="rounded-circle" />
																					</li>
																				</ul>
																			</td>
																			<td><span class="badge bg-label-warning me-1">Ocupados</span></td>
																			<td>$30,000.00</td>
																			<td>
																				<div class="dropdown">
																					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
																						<i class="ti ti-dots-vertical"></i>
																					</button>
																					<div class="dropdown-menu">
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-pencil me-1"></i> Editar
																						</a>
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-trash me-1"></i> Eliminar
																						</a>
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td>
																				<img src="img/partners/17f7ca4a-e16a-11e9-a869-52540003f9f1.png" class="img-fluid">																			
																				<strong>Empresa 4</strong>
																			</td>
																			<td>Más de 90 minutos</td>
																			<td>
																				<ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
																					<li
																						data-bs-toggle="tooltip"
																						data-popup="tooltip-custom"
																						data-bs-placement="top"
																						class="avatar avatar-xs pull-up"
																						title="Motocicleta">
																						<img src="img/vehicles/2.png" alt="Avatar" class="rounded-circle" />
																					</li>
																				</ul>
																			</td>
																			<td><span class="badge bg-label-danger me-1">Distantes</span></td>
																			<td>$55,000.00</td>
																			<td>
																				<div class="dropdown">
																					<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
																						<i class="ti ti-dots-vertical"></i>
																					</button>
																					<div class="dropdown-menu">
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-pencil me-1"></i> Editar
																						</a>
																						<a class="dropdown-item" href="javascript:void(0);">
																							<i class="ti ti-trash me-1"></i> Eliminar
																						</a>
																					</div>
																				</div>
																			</td>
																		</tr>
																		</tbody>
																	</table>
																</div>
															</div>
														</div>														
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev">
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-primary btn-next">
																<span class="align-middle d-sm-inline-block d-none me-sm-1">Siguiente</span>
																<i class="ti ti-arrow-right"></i>
															</button>
														</div>
													</div>
												</div>
											
												<div id="summary" class="content">
													<div class="content-header mb-3">
														<h6 class="mb-0">Detalles de la cuenta</h6>
														<small>Ingresa los detalles de tu cuenta o tus datos.</small>
													</div>
													<div class="row g-3">
														<div class="col-sm-6">
															<label class="form-label" for="txtUser">Usuario (Si estás registrado)</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-user"></i>
																</span>															
																<input type="text" id="txtUser" name="txtUser" class="form-control" placeholder="john.doe" />
															</div>
														</div>
														<div class="col-sm-6 form-password-toggle">
															<label class="form-label" for="txtPassword">Contraseña (Si tienes usuario)</label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-key"></i>
																</span>															
																<input
																	type="password"
																	id="txtPassword"
																	name="txtPassword"
																	class="form-control"
																	placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
																	aria-describedby="confirm-password7" />
																<span class="input-group-text cursor-pointer" id="confirm-password7">
																	<i class="ti ti-eye-off"></i>
																</span>
															</div>
														</div>
														<div class="col-sm-6">
															<label class="form-label" for="txtEmail">Email <small>*</small></label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-mail"></i>
																</span>															
																<input
																	type="text"
																	id="txtEmail"
																	name="txtEmail"
																	class="form-control"
																	placeholder="Ej: john.doe@domain.com"
																	aria-label="Ej: john.doe@domain.com" />
															</div>
														</div>
														<div class="col-sm-6">
															<label class="form-label" for="txtCellphone">Celular <small>*</small></label>
															<div class="input-group input-group-merge">
																<span class="input-group-text">
																	<i class="ti ti-phone"></i>
																</span>															
																<input
																	type="text"
																	id="txtCellphone"
																	name="txtCellphone"
																	class="form-control"
																	placeholder="Ej: 310123456"
																	aria-describedby="Ej: 310123456" />
															</div>
														</div>
														<div class="col-12 d-flex justify-content-between">
															<button class="btn btn-label-secondary btn-prev" disabled>
																<i class="ti ti-arrow-left me-sm-1"></i>
																<span class="align-middle d-sm-inline-block d-none">Anterior</span>
															</button>
															<button class="btn btn-success btn-submit">Enviar</button>															
														</div>
													</div>
												</div>
											
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Footer -->
					<footer class="content-footer footer bg-footer-theme">
						<div class="container-xxl">
							<div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
								<div>
									©
									<script>
										document.write(new Date().getFullYear());
									</script>
									, made with ❤️ by 
									<a href="https://pixinvent.com" target="_blank" class="fw-semibold">Pixinvent</a>
								</div>
								<div>
									<a href="https://themeforest.net/licenses/standard" class="footer-link me-4" target="_blank">
										License
									</a>
									<a href="https://1.envato.market/pixinvent_portfolio" target="_blank" class="footer-link me-4">
										More Themes</a>
									<a
										href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
										target="_blank"
										class="footer-link me-4">
										Documentation
									</a>
									<a href="https://pixinvent.ticksy.com/" target="_blank" class="footer-link d-none d-sm-inline-block">
										Support
									</a>
								</div>
							</div>
						</div>
					</footer>
					<div class="content-backdrop fade"></div>
				</div>
				<!-- Content wrapper -->
			</div>
			<!-- / Layout page -->
		</div>
		<!-- Overlay -->
		<div class="layout-overlay layout-menu-toggle"></div>
		<!-- Drag Target Area To SlideIn Menu On Small Screens -->
		<div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="js/new-service/libs/jquery/jquery.js"></script>
    <script src="js/new-service/libs/popper/popper.js"></script>
    <script src="js/new-service/bootstrap.js"></script>
    <script src="js/new-service/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="js/new-service/libs/node-waves/node-waves.js"></script>

    <script src="js/new-service/libs/hammer/hammer.js"></script>
    <script src="js/new-service/libs/i18n/i18n.js"></script>
    <script src="js/new-service/libs/typeahead-js/typeahead.js"></script>

    <script src="js/new-service/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="js/new-service/libs/bs-stepper/bs-stepper.js"></script>
    <script src="js/new-service/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="js/new-service/libs/select2/select2.js"></script>

    <!-- Main JS -->
    <script src="js/new-service/main.js"></script>

    <!-- Main JS -->
    <script src="js/new-service/service-management2.js"></script>

    <!-- Page JS -->
    <script src="js/new-service/form-wizard-icons.js"></script>
  </body>
</html>
