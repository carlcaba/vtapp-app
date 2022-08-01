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
	
	$result = checkSession("terms.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

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
		<aside class="main-sidebar elevation-4 <?= $skin[1] ?>">
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
							<h1 class="m-0 text-dark"><i class="fa fa-book"></i> <?= $_SESSION["TERMS_AND_CONDITIONS"] ?></h1>
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
				<div class="accordion" id="accordionTerms">
					<div class="card">
						<div class="card-header" id="definitionHeader">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#definition" aria-expanded="true" aria-controls="definition">
									DEFINICIÓN Y ALCANCE.
								</button>
							</h2>
						</div>
						<div id="definition" class="collapse show" aria-labelledby="definitionHeader" data-parent="#accordionTerms">
							<div class="card-body">
								El sitio web denominado <i>vtapp.com.co</i> y/o <i>vtapp.com</i>, el portal mensajeros Aliados, Pago contra entrega y Pre pagada VTAPP la aplicación móvil versión mensajero, que en adelante denominaremos como <strong>el sistema</strong> y/o <strong>la herramienta</strong> son propiedad exclusiva de <strong>SOLUCIONAPPS SAS</strong> sociedad comercial legal existente de acuerdo con la legislación colombiana identificada con el <strong>NIT 901.447.292-1</strong>, domiciliada en la ciudad de CHIA, con dirección de notificaciones en la Calle 22 No 1 A 09 TR C of 305 y correo electrónico info@vtapp.com.co en adelante VTAPP.<br /><br /> 
								VTAPP es una plataforma tecnológica <i>de contacto</i>, provee a sus usuarios única y exclusivamente una solución de software para comunicar a <strong>usuarios clientes</strong> con empresas <strong>Aliadas</strong> de mensajería urbana, brindando la oportuna y eficiente prestación de servicios de mensajería urbana motorizada individual a través de mensajeros que se movilizan a pie o en vehículos motos, automotores, vehículos eléctricos, bicicletas, camionetas, camiones de su propiedad, que laboran para empresas proveedoras de este tipo de servicios debidamente registrados y autorizados por entidad competente.<br /><br /> 
								VTAPP no es una empresa prestadora de servicios de mensajería urbana motorizada, ni una empresa de servicios postales, ni de giros postales, ni de envíos nacionales, no presta servicios de transporte, ni logístico, no actúa como representante, intermediario, agente comercial, comisionista de una o varias empresas de transporte o logísticos.<br /><br /> 
								Cualquier persona natural o jurídica que ingrese, se registre o utilice <strong>el sistema</strong> se considera <strong>Usuario</strong>, igualmente, cada vez que el usuario vaya a hacer uso de la <strong>herramienta</strong> declara haber leído y aceptado los presentes términos y condiciones junto con las modificaciones que se apliquen al presente documento cada vez que el usuario ingrese de nuevo a <strong>el sistema</strong>.<br /><br /> 
								Adicionalmente y de acuerdo a los presentes Términos y Condiciones, la Empresa de mensajería <strong>Aliada</strong> contrata con VTAPP el servicio de: Suministro de software de comunicación y derechos de acceso al sistema de información <strong>PORTAL</strong>, para comunicar la solicitud del servicio de transporte de mensajería urbana motorizada con usuarios personas naturales y/o jurídicas, la relación entre Aliado Vtapp, empleador mensajero, mensajero motorizado y VTAPP se encuentra condicionada <i>única</i> y exclusivamente por los siguientes clausulados.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section1Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section1" aria-expanded="false" aria-controls="section1">
									1. Definiciones.
								</button>
							</h2>
						</div>
						<div id="section1" class="collapse" aria-labelledby="section1Header" data-parent="#accordionTerms">
							<div class="card-body">
								<ul>
									<li><strong>VTAPP</strong> agrupa entre otras una aplicación móvil denominada <strong>mensajero</strong>, herramienta sistema y/o plataforma tecnológica.</li> 
									<li><strong>Sistema</strong> y/o <strong>Herramienta</strong> son los elementos de soporte lógico software, logos, imágenes, licencia de uso, manual de usuario, incluye la aplicación para dispositivos móviles VTAPP usuario y mensajero, el portal web <i>www.vtapp.com.co</i> además del portal Aliados y portal cliente Pago contra entrega y Prepagada.</li>
									<li><strong>Servicio de mensajería urbana motorizada</strong>, es el servicio de transporte de sobres/paquetes mensajería suministrada por mensajeros y <strong>Aliados</strong> empleadores de mensajería, que se desplazan a pie o en vehículos motocicletas, automotores, vehículos eléctricos, bicicletas, para la entrega de sobres y paquetes.</li> 
									<li><strong>Servicios logísticos y/o postales</strong> son los servicios logísticos de transporte, envío o cualquiera similar que presten los mensajeros motorizados usuarios que se contacten a través del <strong>sistema</strong> y/o la <strong>herramienta</strong>, y los servicios de envío de objetos postales en los que interviene la persona natural o jurídica de acuerdo al artículo 3 de la ley 1369 de 2009.</li>
									<li><strong>Aliado Vtapp</strong> es la empresa persona natural o jurídica vinculante y responsable de sus empleados mensajeros motorizados con contrato laboral a término fijo, indefinido o cualquiera otro establecido por las leyes nacionales.</li>
									<li><strong>Mensajeros motorizados</strong> es el personal autorizada para conducir el vehículo destinado a prestar el servicio de transporte de mensajería urbana motorizada bajo la responsabilidad del Aliado empleador mensajero motorizado debidamente registrada según las normas legales, el mensajero motorizado es un usuario de la <strong>herramienta</strong> y se beneficia de la licencia de uso no exclusiva limitada únicamente bajo los presentes términos y condiciones.</li>
									<li><strong>Sobres, paquetes y envíos</strong> son los elementos que transporta un mensajero motorizado debidamente registrado en el <strong>sistema</strong>, se limita también a los términos y condiciones generales de la <strong>herramienta</strong> bajo responsabilidad del usuario y del mensajero motorizado exclusivamente, mientras lo transporta y entrega.</li>
									<li><strong>Usuarios</strong> integra a los mensajeros motorizados, a los empleadores mensajeros motorizados y a toda persona natural o jurídica que se relacionen como proveedores o clientes a través del <strong>sistema</strong>.</li>
									<li>Clientes</strong> son los usuarios que contratan y pagan los servicios de envío de paquetes y sobres a mensajeros motorizados a través de la <strong>herramienta</strong>.</li>
									<li><strong>Portal</strong> es el ambiente administración suministrado por VTAPP a las empresas de mensajería Aliadas con el cual permite a este último monitorear y controlar la actividad logística de sus empleados del uso de la <strong>aplicación</strong>.</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section2Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section2" aria-expanded="false" aria-controls="section2">
									2. Acceso a la plataforma.
								</button>
							</h2>
						</div>
						<div id="section2" class="collapse" aria-labelledby="section2Header" data-parent="#accordionTerms">
							<div class="card-body">
								La instalación, descarga y actualización de la <strong>herramienta</strong> puede realizarse desde las principales tiendas de aplicaciones para móviles.<br /><br />
								El uso de versiones no actualizadas de la aplicación puede afectar el correcto funcionamiento del <strong>sistema</strong>. En caso de requerir algún tipo de soporte escribanos al correo <a href="mailto:info@vtapp.com.co">info@vtapp.com.co</a>.<br /><br /> 
								El mensajero motorizado y el Aliado Vtapp empleador mensajero deben validar antes de la descarga del <strong>sistema</strong> que son compatibles a los dispositivos y tiene la obligación de tener descargado el software necesario para brindar toda la protección necesaria de ataques de virus informáticos y malware. En cualquier evento, el Aliado y mensajero motorizado son los únicos responsables de cualquier daño causado a sus móviles, por el uso de versiones incompletas, versiones de sistemas operativos no legales o no autorizadas o por la entrada de virus o malware a sus dispositivos móviles, exonerando de cualquier reclamación al <strong>sistema</strong>. 
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section3Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section3" aria-expanded="false" aria-controls="section3">
									3. Registro de usuarios en el <strong>sistema</strong>.
								</button>
							</h2>
						</div>
						<div id="section3" class="collapse" aria-labelledby="section3Header" data-parent="#accordionTerms">
							<div class="card-body">
								Para efectuar el registro de los mensajeros motorizados y los Aliados empleadores de mensajeros motorizados es responsabilidad y obligación del <strong>usuario</strong> ser mayor de edad y gozar de plena capacidad para contraer las obligaciones del presente contrato de adhesión, que se declaran en los presentes términos y condiciones, el mensajero motorizado y el empleador motorizado declaran tener capacidad legal para contraer obligaciones, además declara carecer de impedimentos legales que le impidan vincularse con los aquí citados términos y condiciones.<br /><br /> 
								<strong>VTAPP</strong> no valida la integridad física y salud de los mensajeros motorizados y empleadores motorizados, es responsabilidad del <strong>Aliado</strong> Vtapp empleador mensajeros, avalar y asumir su exclusiva responsabilidad y obligación en cumplimiento al Plan Estratégico de Seguridad Vial, para lo cual deberá contar con lo establecido en la Resolución 0001565 de 2015 respecto al nivel mínimo de competencias, incluidos los correspondientes exámenes médicos, psico censo metricos (psicológico, biométrico, audio métrico, coordinación visual) entre otros, todos los <strong>usuarios</strong> deben validar el estado de salud de los mensajeros motorizados y reportar al <strong>sistema</strong> cualquier deficiencia con respecto a la calidad del servicio prestado y el estado de salud y cualquier inconformidad para que la <strong>herramienta</strong> califique, retire y reporte a los <strong>usuarios</strong> que no cumplan con los requisitos establecidos por la legislación y la normatividad laboral del área donde se presta el servicio, este reporte debe estar actualizado al Ministerio de Trabajo por períodos no mayores a ciento ochenta (180) días, y en caso de cualquier irregularidad, deben ser reportados debidamente al correo info@vtapp.com.co con copia al Ministerio de trabajo y al Ministerio de Transporte.<br /><br />
								Es obligación del <strong>Aliado</strong> Vtapp empleador mensajero garantizar la implementación y documentación del Plan de mantenimiento preventivo de vehículos por personal idóneo y capacitado bajo un cronograma de revisión periódico, en caso de requerirlo deberá suministrar a VTAPP la documentación exigida por la ley para su respectiva verificación y validación, establecidos por la entidad competente en el caso Colombia, la Secretaria de Movilidad.<br /><br />
								La empresa <strong>Aliado</strong> Vtapp empleador mensajero motorizado debe implementar mecanismos de capacitación en seguridad vial, velar por que la documentación legal requerida este actualizada y al día en cumplimiento de la resolución 0001565 de 2014 Y el Decreto 1079 reglamentario de sector transporte de 2015.<br /><br />
								El empleador motorizado declara conocer y aceptar el contenido del art.12 Ley 1503 de 2011, el Dec 2851 de 2013 y la Res. 0001565 de junio 2014, así como aplicar su normatividad con su consecuente constante implementación, documentación, auditoria y actualización. VTAPP es una herramienta tecnológica que conecta y comunica a <strong>usuarios</strong> que requieren los servicios de mensajería urbana motorizada en tiempo real y no constituye nada más que un canal de comunicación facilitando el contacto entre <strong>cliente</strong> y <strong>proveedor</strong>.<br /><br />
								Mediante la aceptación por parte del <strong>Aliado</strong> Vtapp empleador mensajero y mensajero motorizado, se exime a la <strong>herramienta</strong> denominada <strong>VTAPP</strong> de toda responsabilidad respecto a cualquier incidente y/o accidente ocasionado por el mensajero motorizado en el que se vea involucrado. NO existe ninguna responsabilidad de la sociedad <strong>SOLUCIONAPPS SAS</strong> ni de la aplicación <strong>VTAPP</strong> por los eventos que vulneren la integridad del <strong>usuario</strong> mensajero motorizado por la prestación de los servicios de mensajería urbana suministrados a través del aplicativo <strong>VTAPP</strong>.<br /><br />
								El registro del empleador y sus mensajeros motorizados como <strong>usuarios</strong>, se efectuará por medio de la página web, a través de la dirección www.vtapp.com.co/Registro/ Aliado Vtapp.<br /><br />
								Es responsabilidad del <strong>Aliado</strong> Vtapp empleador mensajero y del mensajero motorizado salva guardar los usuarios y contraseñas de acceso a la <strong>herramienta</strong> y en caso de olvidarla o perderla, debe notificarlo al correo info@vtapp.com.co para su nueva asignación.<br /><br />
								Los derechos adquiridos con el registro al <strong>sistema</strong> son intransferibles y personales, no deberán ser suministrados a terceros externos no autorizados, deben ser custodiados por el <strong>sistema</strong>, cualquier daño o perjuicio ocasionado por mal uso o robo será responsabilidad exclusiva del mensajero motorizado y el Aliado Vtapp empleador mensajero motorizado.<br /><br />
								El mensajero motorizado y el Aliado Vtapp empleador mensajero motorizado, declara que los documentos suministrados y la información es además de veraz, actualizada. El registro del mensajero motorizado y Aliado Vtapp empleador mensajero deberán ser evaluados y complementados con algunos datos adicionales como:
								<ul>
									<li>Información de vehículo.</li>
									<li>Seguro Obligatorio para Accidentes de Tránsito - SOAT.</li>
									<li>Placa.</li>
									<li>Pase vigente.</li>
									<li>Fotografía 3x4 en fondo blanco.</li>
									<li>Antecedentes judiciales.</li>
									<li>Tipo de contrato laboral o por prestación de servicios.</li>
									<li>Referencias laborales y cualquiera otro requerido por el <strong>sistema</strong> para avalar la confiabilidad del servicio.</li>
								</ul>
								En cualquier caso, VTAPP se reserva el derecho de validar la veracidad de la información y documentación suministrada por los <strong>usuarios</strong> y de aceptar ó rechazarla por inconsistencias ó documentos faltantes dentro del trámite de aprobación. 
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section4Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section4" aria-expanded="false" aria-controls="section4">
									4. Protección y tratamiento de datos personales.
								</button>
							</h2>
						</div>
						<div id="section4" class="collapse" aria-labelledby="section4Header" data-parent="#accordionTerms">
							<div class="card-body">
								En cumplimiento con la ley 1581 de 2012 y su decreto reglamentario VTAPP ha adoptado un manual de procedimientos y políticas que pueden ser consultados en las <a href="#" target="_blank">políticas de privacidad</a>. VTAPP en condición de responsable, utiliza la información suministrada por los <strong>usuarios</strong> dentro de las contempladas en el cumplimiento de su política. En concordancia con la validez de la manifestación de voluntad a través de medios electrónicos establecidas en la ley 527 de 1999, los <strong>usuarios</strong> al momento de la creación de la cuenta de usuario, manifiestan expresamente tener capacidad para celebrar todo tipo de transacciones que se deben realizar utilizando la <strong>herramienta</strong>. Al aceptar los presentes Términos y Condiciones, el <strong>usuario</strong> acepta el contenido total de la Política de Privacidad, el manual interno de políticas y Procedimientos para Protección de datos personales publicados por VTAPP. De acuerdo con los presentes Términos y Condiciones, el <strong>usuario</strong> expresa autorización para el tratamiento de la información correspondiente a sus datos personales incluida la autorización expresa de transferencia y transmisión internacional de sus datos personales incluyendo sus nombres, imagen, edad, número de teléfono, correo, localización y dirección dentro de las finalidades comunicadas en el manual interno de políticas y procedimientos para Protección de datos personales publicados por VTAPP.<br /><br /> 
								Los empleadores mensajeros motorizados y los mensajeros motorizados, que deseen vincularse al <strong>sistema</strong>, autorizan expresamente a VTAPP para transmitir, compartir, transferir a terceros que lleven a cabo estudios de seguridad y antecedentes disciplinarios, laborales y/o personales.<br /><br />
								Si hubiese alguna venta, fusión, consolidación, integración empresarial, cambio de control societario, transferencia de activos sustancial, escisión o transferencia global de activos, reorganización o liquidación de los propietarios accionistas del <strong>sistema</strong> VTAPP entonces, podrá discrecionalmente y bajo cualquier título, transferir, transmitir, vender o asignar los datos personales recabados a cualquier tercero vinculada con cualquiera de las operaciones descritas a una cualquiera de las partes relevantes.<br /><br />
								Para el uso efectivo del <strong>sistema</strong> los <strong>usuarios</strong> autorizan a VTAPP a compartir con terceros y entre ellos los datos personales necesarios pertinentes para el uso adecuado de la <strong>herramienta</strong>, como la dirección de solicitud de servicio, nombres, número de teléfono, correo electrónico, imagen fotográfica etc.<br /><br />
								En virtud de los presentes términos y condiciones el <strong>usuario</strong> se obliga a no compartir, suministrar, facilitar la información a los demás <strong>usuarios</strong> de la plataforma, Aliado Vtapp empleador mensajeros motorizados o mensajeros motorizados, no podrán enviar mensajes de texto, conversaciones no autorizadas por fuera de la <strong>herramienta</strong> a compañeros y mucho menos terceros externos al <strong>sistema</strong> suministrando información parcial o total de los <strong>usuarios</strong> denominados <strong>clientes</strong>.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section5Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section5" aria-expanded="false" aria-controls="section5">
									5. Evaluación de los usuarios.
								</button>
							</h2>
						</div>
						<div id="section5" class="collapse" aria-labelledby="section5Header" data-parent="#accordionTerms">
							<div class="card-body">
								Al analizar cada servicio prestado el <strong>cliente</strong> podrá calificar el nivel de servicio suministrado por el <strong>usuario</strong>, la calificación podrá ser consultada anónimamente por otros <strong>clientes</strong> para mejorar la calidad del servicio suministrado. VTAPP se reserva el derecho de retirar, suspender, bloquear o retirar a cualquier <strong>usuario</strong> del <strong>sistema</strong> con o sin justa causa, se reserva el derecho de iniciar acciones legales o penales que utilicen la <strong>herramienta</strong> para cometer o facilitar la comisión de algún tipo de delito, el <strong>usuario</strong> renuncia a reclamar cualquier indemnización, compensación por la cancelación, retiro o suspensión de su cuenta dentro de la <strong>herramienta</strong>.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section6Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section6" aria-expanded="false" aria-controls="section6">
									6. Licencia de uso.
								</button>
							</h2>
						</div>
						<div id="section6" class="collapse" aria-labelledby="section6Header" data-parent="#accordionTerms">
							<div class="card-body">
								VTAPP suministrará al <strong>usuario</strong> una licencia limitada parcial, personal, no exclusiva, no comercial, no transferible y totalmente revocable para utilizar la plataforma de conformidad con los términos contenidos en este documento.<br /><br />
								La licencia estará vigente solo mientras se acceda al <strong>sistema</strong> en tanto sea autorizada previamente por VTAPP, incluye los derechos de uso y acceso a la plataforma "sistema de información portal Aliado el cual obliga al Aliado Vtapp empleador mensajeros motorizados.<br /><br />
								VTAPP se reserva los derechos sobre el <strong>sistema</strong> contemplados no expresamente concedidos, se prohíbe expresamente al <strong>usuario</strong> la producción, reproducción, copia, transformación, distribución, modificación, creación de obras derivadas, scraping, ingeniería inversa, extracción de código fuente parcial o total, modelo de comunicación de la plataforma, interfaces gráficas que hacen parte integral debidamente registradas bajo derechos y propiedad protegidos por las normas de la entidad responsable de proteger los derechos de propiedad intelectual.<br /><br />
								El mensajero motorizado y el Aliado Vtapp Aliado empleador mensajeros motorizados aceptan que las imágenes, logotipos, textos, descripciones, plantillas, código fuente, bases de datos, arquitectura, símbolos, señales distintivas, manuales y cualquier otro material y contenido que hace parte de la <strong>herramienta</strong> hacen parte de los derechos de la propiedad intelectual y o derechos de propiedad exclusivos de la sociedad desarrolladora de la <strong>herramienta</strong> VTAPP.<br /><br />
								<strong>SOLUCIONAPPS SAS</strong> y su aplicación denominada VTAPP se reserva el derecho de no autorizar el uso, suspender, cancelar, retirar a cualquier mensajero motorizado o Aliado Vtapp empleador mensajero motorizado sin lugar a indemnización por ningún concepto.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section7Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section7" aria-expanded="false" aria-controls="section7">
									7. Cobros asociados al uso de la plataforma.
								</button>
							</h2>
						</div>
						<div id="section7" class="collapse" aria-labelledby="section7Header" data-parent="#accordionTerms">
							<div class="card-body">
								El Aliado Vtapp empleador mensajero motorizado y el mensajero motorizado deberán pagar una tarifa que será enviada al correo electrónico del contacto registrado por la empresa en el sitio www.vtapp.com/contacto una vez el empleador ingrese los datos básicos de la empresa con la que va a vincular a sus colaboradores.<br /><br /> 
								Adicionalmente, cada <strong>usuario</strong> Aliado Vtapp empleador mensajero motorizado y mensajeros motorizados, deberá pagar a VTAPP como contraprestación por los derechos de uso portal y servicios de la <strong>herramienta</strong> los servicios de comunicaciones y la licencia de uso software una remuneración que se calculará como una tarifa fija o variable por cada servicio suministrado y/o como una suma por período diario, semanal, mensual, anual etc. Las tarifas aplicables por cada servicio serán comunicadas a través del <strong>sistema</strong> de información portal Aliado Vtapp empleador mensajero de la aplicación de cada <strong>usuario</strong>, del correo electrónico, y/o a través de mensajes de texto SMS. La remuneración podrá ser pagada o debitada del saldo a favor que cada Aliado empleador de mensajería y mensajero motorizado reporte en la cuenta individual suministrada por el <strong>sistema</strong>. Los gastos en que se hayan incurrido aplicables a la transacción como comisiones interbancarias, comisión al intermediario del sistema de pagos y las retenciones legalmente aplicables que le hayan practicado a VTAPP incluyendo las comisiones por tarjetas de crédito ó débito, o por las retenciones que VTAPP o los intermediarios de los sistemas de pagos deba practicar de conformidad con la ley, serán descontados al <strong>empleador</strong> y al <strong>mensajero</strong> motorizado aplicables entre otras la retención en la fuente sobre el valor de los servicios cuando los pagos lleguen a superar los límites legales fijados para el efecto. En todo y cualquier caso, será posible hacer cruce de cuentas cuando este posea saldo a favor derivada por cargos por servicios individuales o corporativos, generados por el servicio del <strong>sistema</strong>, si además, el mensajero motorizado tuviese saldo a favor y su cuenta fuera retirada por la <strong>herramienta</strong> o por solicitud expresa y voluntad del <strong>usuario</strong>, podrá solicitar por escrito que el saldo a favor sea abonado a nombre del Aliado Vtapp empleador mensajero motorizado al que se encuentra contractualmente vinculado, en caso de que su contrato ya hubiese terminado, VTAPP podrá hacer el pago de la cifra adeudada a una cuenta cuyo titular sea el mismo prestador del servicio adeudado, sin que este hecho implique ningún tipo de vínculo comercial, laboral ni contractual.<br /><br />
								El Aliado Vtapp empleador mensajero motorizado y el mensajero motorizado, autoriza expresamente a VTAPP en condición de medio de contacto para que reciba a través del <strong>sistema</strong> o por medio de terceros (pasarela) proveedores de sistema de pago los pagos correspondientes a servicios de mensajería urbana motorizada, los ingresos por concepto de servicios corporativos o empresariales o cualquier servicio pagado con bonos, vales, o tarjetas de crédito.<br /><br />
								VTAPP, podrá hacer el pago efectivo previa autorización ó no de la empresa empleador de mensajería persona natural o jurídica de las obligaciones contraídas como proveedor de software de comunicación en especie con productos o servicios al valor pactado con los proveedores de los mismos, cuando considere que los saldos por pagar puedan ser compensados justamente, así mismo podrá recaudar y/o efectuar el pago en monedas de diferentes denominaciones en otros países donde sea un medio legal de pago, dinero  virtual <strong>Bitcoins</strong> o pesos colombianos.<br /><br />
								Los pagos en efectivo que sean recibidos por el mensajero motorizado deben ser reportados por este como pagados y serán descontados de los saldos pendientes por pagar que la <strong>herramienta</strong> adeude al prestador del servicio. 
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section8Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section8" aria-expanded="false" aria-controls="section8">
									8. Obligaciones.
								</button>
							</h2>
						</div>
						<div id="section8" class="collapse" aria-labelledby="section8Header" data-parent="#accordionTerms">
							<div class="card-body">
								Para todos los efectos legales del presente contrato el representante legal del empleador declara que conoce el contenido de la resolución 0001565 de 6 junio 2014, el artículo 12 ley 1503 de 2011 y el decreto 2851 de 2013 y que da cabal cumplimiento a las obligaciones, planes, cronogramas, indicadores, auditorias, políticas, procedimientos y documentación exigida por el Ministerio de Trabajo, Protección Social y Ministerio de Transporte en todo el territorio nacional de la Guía metodológica para la elaboración del Plan Estratégico de Seguridad Vial (PESV). El <strong>usuario</strong> de la <strong>herramienta</strong> declara estar habilitado, ser mayor de edad, responsable contractualmente, y estar autorizado bajo la ley para prestar el servicio de mensajería urbana motorizada, VTAPP es simplemente un medio de conexión, que no presta los servicios de transporte, no es empleador, comisionista, representante, intermediario, solo es un medio de contacto entre los <strong>clientes</strong> y los mensajeros motorizados laboralmente vinculados "única" y "exclusivamente" a la empresa Aliado Vtapp empleador mensajeros motorizados, para beneficio mutuo y en el desempeño de actividades ordinarias, inherentes y conexas a su objeto, la función de VTAPP es facilitar su comunicación. El <strong>usuario</strong> acepta cumplir con los presentes Términos y Condiciones.<br /><br />
								<ul>
									<li>Entender los requerimientos de los servicios solicitados por los <strong>clientes</strong> a través del <strong>sistema</strong>.</li> 
									<li>Cumplir con las obligaciones derivadas del documento <strong>Código de conducta</strong> y <strong>alcance obligaciones motorizado y usuario</strong>, el cual declara aceptar y conocer en su totalidad.</li>
									<li>El <strong>usuario</strong> deberá informar de cualquier cambio respecto a su información de registro o bancaria, eximiendo a VTAPP por la demora o no suministro de esa información al <strong>sistema</strong>.</li>
									<li>Pagar a VTAPP una tarifa por cada servicio solicitada a través del <strong>sistema</strong> la cual será calculada por la <strong>herramienta</strong> de acuerdo a las variables suministradas por VTAPP en términos de tiempos, distancias y calificaciones de clientes respecto al nivel de servicio esperado.</li>
									<li>El <strong>usuario</strong> debe mantener el vehículo en perfecto estado, al igual que los documentos exigidos por la ley del área donde preste el servicio, actualizados sin vencimiento, debe ser el responsable de su actualización en el <strong>sistema</strong> y asume la responsabilidad total de los eventos posteriores que pueda ocasionar el no actualizar o informar a la <strong>herramienta</strong>.</li>
									<li>El Aliado Vtapp empleador mensajero motorizado y el mensajero motorizado se obligan a mantener actualizado los pagos en seguridad social, pensión, EPS, ARL SOAT, seguros de accidentes personales, Impuestos, partes, sanciones de sus colaboradores.</li>
									<li>El <strong>usuario</strong> deberá suministrar veraz información a través del portal y de la aplicación por medio de información personal, documentación y referencias, igualmente, números de cuentas, nombres de bancos y tipos de cuenta para el pago efectivo de los pagos que VTAPP hará una vez sean descontados los costos, impuestos y comisiones pactados.</li>
									<li>El <strong>usuario</strong> se hace responsable de la validez y buen reporte de sus antecedentes disciplinarios, policivos, tributarios y asume toda responsabilidad de las consecuencias que genere su incumplimiento, a su vez exime de cualquier y toda responsabilidad respecto a los hechos que pueda ocasionar su incumplimiento.</li>
									<li>En caso de presentarse algún tipo de inconveniente durante el transporte del servicio que le impida entregar el sobre/paquete transportado estará obligado a entregarlo a otro mensajero motorizado sea o no empleado de la empresa a la que se encuentre vinculado, el sistema no pagará el servicio excepto en caso de accidente vehicular.</li>
								</ul>
								El <strong>Usuario</strong> no podrá suministrar claves accesos, información de <strong>clientes</strong> con otro <strong>usuario</strong> que este registrado o no en el <strong>sistema</strong> ni permitir que otros <strong>usuarios</strong> ingresen a la <strong>herramienta</strong>, asumiendo todas las responsabilidades y consecuencias legales o penales que este evento genere.
								<ul>
									<li>Utilizar los canales de comunicación suministrados por VTAPP.</li>
									<li>El mensajero motorizado debe cumplir con el documento CÓDIGO DE CONDUCTA, adjunto al presente acuerdo el cual declara conocer y aceptar en su totalidad.</li>
								</ul>
								Es obligación única y exclusiva del Aliado Vtapp empleador mensajero cumplir con el artículo 26 y 27   de la ley 527 de 1999.<br /><br />
								El Aliado Vtapp empleador mensajero motorizado y los mensajeros motorizados, serán responsables exclusivos del pago de los impuestos que correspondan a cada uno de acuerdo con las obligaciones contempladas en la ley de la zona donde se suministre el servicio.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section9Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section9" aria-expanded="false" aria-controls="section9">
									9. Pagos.
								</button>
							</h2>
						</div>
						<div id="section9" class="collapse" aria-labelledby="section9Header" data-parent="#accordionTerms">
							<div class="card-body">
								El <strong>sistema</strong> consignará al Aliado Vtapp empleador mensajero </strong> los valores efectivamente recibidos por VTAPP ya sea que los haya recibido a través de tarjetas de crédito, bonos, vales, efectivo o cualquier otro medio. A las sumas a consignar o transferir le será descontada la comisión de la <strong>herramienta</strong>, de acuerdo a la tabla correspondiente relacionada en el anexo No 2 <strong>Tarifas Vtapp</strong> del presente documento.<br /><br />
								Los pagos correspondientes a la totalidad de los porcentajes totalidad de los porcentajes mensajero y empleador  se efectuarán al depósito del pago del total del valor de los servicios recibidos por VTAPP, a mas tardar, la semana siguiente dentro de los ocho (8) siguientes días de cada pago, de acuerdo a la tabla correspondiente relacionada en el anexo No 2 <strong>Comisiones del sistema</strong> del presente documento. En caso que de acuerdo con la información registrada y con base en los sistemas de GPS y monitoreo reportados el <strong>usuario</strong> declare fue prestado y que el servicio fuera reportado como no pagado, igualmente en caso que el <strong>cliente</strong> presente alguna reclamación o queja por la deficiente calidad del servicio VTAPP se reserva el derecho de transferir o no el pago de la cifra adeudada al  Aliado Vtapp empleador mensajero y/o mensajero motorizado , hasta que el <strong>sistema</strong> valide la exactitud de los cobros y del servicio suministrado y las causales de la reclamación o queja por parte del <strong>usuario cliente</strong>.<br /><br />
								El mensajero motorizado deberá abstenerse de entregar la mercancía transportada en caso de NO pago del servicio de mensajería contratado y deberá devolverlo al punto de contacto de su empleador, por ningún motivo y bajo ninguna circunstancia podrá guardarlo, retenerlo, ocultarlo, destruirlo ó botarlo para presionar el pago de un servicio de transporte suministrado por el motorizado.<br /><br />
								En caso de no pago por parte del <strong>usuario</strong> cliente contratante, es responsabilidad y obligación del empleador del mensajero motorizado y el mensajero motorizado, el cobro efectivo de la gestión y de la comisión generada para la empresa SOLUCIONAPPS SAS quien suministró el servicio de contacto con el cliente a través del aplicativo VTAPP.<br /><br />
								 En caso que el <strong>usuario</strong> adeude por algún concepto cualquier cifra a VTAPP, el <strong>sistema</strong> podrá retener y/o compensar un valor equivalente de las sumas en discusión que llegara a tener el <strong>mensajero</strong> en la cuenta a su favor, este cobro será aplicable a todos los pagos que el <strong>cliente</strong> haga a través de bonos, tarjetas de crédito y cualquier medio de pago.<br /><br />
								En caso que la <strong>herramienta</strong> haya pagado por error las sumas por conceptos que entren en investigación y no sean autorizadas, el <strong>mensajero</strong> deberá devolver inmediatamente dichas sumas y acreditar ante VTAPP la veracidad con pruebas fehacientes de los servicios suministrados.<br /><br />
								El Aliado Vtapp empleador mensajero motorizado y el mensajero motorizado, deberá suministrar a VTAPP el nombre tipo y número de cuenta donde desea sea consignado el valor del servicio pagado a VTAPP o al proveedor de la pasarela medio de pago, si esto generará un saldo a favor del empleador o mensajero, para cobrarlo deberá completar el proceso de verificación, completando y aceptando los formatos suministrados por la <strong>herramienta</strong>. El <strong>usuario</strong> no podrá solicitar un pago o transferencia a un tercero diferente al titular de quien suministro el servicio. La solicitud de supresión o modificación de un número de cuenta será suministrada exclusivamente por el Aliado Vtapp empleador mensajero motorizado, bajo ningún caso ni circunstancia podrá hacerlo independientemente el mensajero motorizado, este procedimiento se hará bajo los formatos y superando los filtros de verificación de identidad que estime necesario el <strong>sistema</strong>.<br /><br />
								VTAPP podrá informar dentro de un plazo no mayor a treinta (30) días, cuando se efectúe la aceptación de la anulación o supresión de la cuenta retirada ó modificada, hasta tanto el <strong>Usuario</strong> deberá recibir los pagos correspondientes en la cuenta anteriormente suministrada.<br /><br />
								El <strong>usuario</strong> autoriza expresamente que las entidades financieras en las que posee cuenta puedan suministrar información necesaria como el número de cuenta y demás datos con VTAPP para facilitar el pago de las obligaciones adquiridas por el <strong>sistema</strong> con el Aliado Vtapp empleador mensajero motorizado y el mensajero motorizado.<br /><br />
								En cuanto se haga la aprobación y validación a través de los medios que facilita el <strong>sistema</strong> para la actualización del nombre tipo y número de cuenta de pago la <strong>herramienta</strong> notificará al mensajero motorizado y al Aliado empleador motorizado del cambio efectuado y del retiro de la anterior cuenta registrada.<br /><br />
								Sin perjuicio de lo anterior, el <strong>usuario</strong> acepta recibir el pago de los servicios adeudados a cuentas donde se compruebe que es el titular aun cuando no las haya vinculado o presentado a la <strong>herramienta</strong> y descontar los costos financieros que las entidades causen por los servicios de transferencia y consignación.<br /><br />
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section10Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section10" aria-expanded="false" aria-controls="section10">
									9.1. Manejo de pagos en efectivo.
								</button>
							</h2>
						</div>
						<div id="section10" class="collapse" aria-labelledby="section10Header" data-parent="#accordionTerms">
							<div class="card-body">
								En caso que el mensajero reciba pagos en <strong>efectivo</strong> por la prestación de los servicios de mensajería y el suministro de software de comunicación y derechos de acceso al sistema de información <strong>PORTAL</strong>, para comunicar la solicitud del servicio de transporte de mensajería urbana motorizada con usuarios personas naturales y jurídicas; tanto el mensajero como el empleador autorizan descontar de su saldo pendiente por pagar, los valores por concepto de comisiones del <strong>sistema</strong> el mensajero por su parte, se compromete a entregar a la empresa empleador motorizado las sumas que le correspondan.<br /><br />
								En caso de que el mensajero retenga sumas que no le corresponden al pago de sus servicios por un plazo mayor a cinco (5) días; el <strong>sistema</strong> retirará definitivamente al mensajero de la aplicación y su Aliado Vtapp empleador mensajero se hará responsable solidario del pago efectivo de los valores que correspondan a las comisiones de la <strong>herramienta</strong>, el Aliado Vtapp empleador mensajero autoriza le sean descontados los valores del pago a su favor al giro siguiente de causada la retención de los pagos al mensajero. 
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section11Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section11" aria-expanded="false" aria-controls="section11">
									10. Restricciones.
								</button>
							</h2>
						</div>
						<div id="section11" class="collapse" aria-labelledby="section11Header" data-parent="#accordionTerms">
							<div class="card-body">
								El <strong>sistema</strong> no efectuará el pago del servicio a mensajero ni Vtapp Aliado Vtapp empleador mensajeros en los siguientes eventos específicos: 
								<ul>
									<li>Cuando el <strong>usuario cliente</strong> cancele su petición antes de que el mensajero motorizado recoja el <strong>paquete/sobre</strong>.</li>
									<li>Cuando el <strong>mensajero motorizado</strong> demore por cualquier motivo la recogida del <strong>sobre/paquete</strong> en punto origen y el <strong>usuario/cliente</strong> en consecuencia decida no pagar el servicio solicitado.</li>
									<li>Cuando el <strong>mensajero motorizado</strong> deteriore, pierda, bote y/o le sea robado <strong>paquete/sobre</strong>.</li>
									<li>Cuando <strong>usuario cliente</strong> reporte daño, deterioro <strong>paquete/sobre</strong> y el <strong>sistema</strong> valide el evento.</li>
									<li>Cuando otro mensajero de otra o la misma empresa empleadora deba recoger el sobre/paquete en un punto de contacto debido a que el prestador inicial se vea impedido por cualquier causa a entregar el servicio asignado.</li>
									<li>No se efectuará el pago del servicio cuando mensajero motorizado no reporte la terminación exitosa con la entrega del sobre/paquete transportado a través de la aplicación.</li>
									<li>En caso de reporte encuesta satisfacción servicio deficiente por debajo de los standares mínimos de servicio exigido por la <strong>herramienta</strong>, VTAPP podrá afectar el valor del pago Aliado Vtapp empleador mensajero y mensajero motorizado, por concepto <strong>deterioro niveles de percepción cliente servicio aplicación</strong>.</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section12Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section12" aria-expanded="false" aria-controls="section12">
									11. Cancelación.
								</button>
							</h2>
						</div>
						<div id="section12" class="collapse" aria-labelledby="section12Header" data-parent="#accordionTerms">
							<div class="card-body">
								El registro a la <strong>herramienta</strong> se cancelará: 
								<ul>
									<li>Por solicitud expresa del empleador presentada al correo electrónico info@vtapp.com.co.</li>
									<li>Por disolución, liquidación en persona jurídica o muerte en el caso de que sea una persona natural.</li>
									<li>Por justas causas determinadas por <strong>SOLUCIONAPPS SAS</strong> que vulneren procesos o la imagen de la <strong>aplicación</strong> VTAPP.</li>
									<li>Por el incumplimiento de las obligaciones contenidas en el numeral siete (7) del presente documento.</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section13Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section13" aria-expanded="false" aria-controls="section13">
									12. Cláusula compromisoria.
								</button>
							</h2>
						</div>
						<div id="section13" class="collapse" aria-labelledby="section13Header" data-parent="#accordionTerms">
							<div class="card-body">
								Todas las diferencias o controversia surgidas de este documento serán puestas a conocimiento del Centro de Arbitraje y Conciliación de la Cámara de Comercio de Bogotá.<br /><br /> 
								El árbitro será designado por las partes en común acuerdo, o en su defecto designado por el Tribunal de la Cámara de Comercio de Bogotá, la ley será aplicable a la normatividad Colombiana, el Tribunal de arbitramento será en la ciudad de Bogotá, la Secretaría del Tribunal estará integrada por un miembro de la lista oficial de secretarios del Centro de Arbitraje y Conciliación de la Cámara de Comercio de Bogotá.<br /><br />
								<ul>
									<li>
										<strong>COMUNICACIONES Y NOTIFICACIONES</strong><br />
										Cualquier información que deba suministrarse a los <strong>usuarios</strong> utilizará como medios validos correos electrónicos, SMS mensajes de texto celular, o publicados en la página oficial del <strong>sistema</strong> y será calificada como entregada cuando existan rastros del hecho.
									</li>
									<li>
										<strong>TARIFAS APLICABLES SERVICIO</strong><br />
										El <strong>usuario</strong> autoriza a VTAPP a descontar la comisión por servicios del <strong>sistema</strong> que hacen parte del anexo No 2 Comisiones del <strong>sistema</strong> el cual declara conocer en su totalidad.
									</li>
									<li>
										<strong>MODIFICACIONES A LOS TERMINOS Y CONDICIONES</strong><br />
										Cualquier modificación parcial o total de los presentes Términos y Condiciones serán comunicadas a los <strong>usuarios</strong> por medio de cualquier medio privado ó público, quien podrá aceptarla o rechazarla, si el <strong>usuario</strong> no manifiesta su rechazo o negación, dentro de los tres (3) días hábiles siguientes a la emisión de la comunicación, en caso contrario se entenderá que el <strong>usuario</strong> acepta la totalidad de las modificaciones propuestas,  las modificaciones surtirán efecto inmediato cumplido el plazo para su rechazo o negación.
									</li>
									<li>
										<strong>ALCANCE</strong><br />
										Los presentes Términos y Condiciones surten efecto y son aplicables a todo acto mientras el <strong>usuario</strong> se encuentre activo en el <strong>sistema</strong>, así mismo se actualizarán y se entenderá aceptada cada vez que el <strong>usuario</strong> ingrese y/o utilice la <strong>herramienta</strong>. El acuerdo que resulte de la aceptación de los presentes Términos y Condiciones sustituye todos los acuerdos, representaciones, declaraciones de garantía pactadas entre las partes y sustituyen expresamente los términos de cualquier oferta mercantil que se haya comunicado anteriormente.
									</li>
									<li>
										<strong>LEY APLICABLE</strong><br />
										Los presentes Términos y Condiciones se regirán, aplicarán e interpretarán conforme a la Ley de la República de Colombia.
									</li>
								</ul>
							</div>
						</div>
					</div>
					<br />
					<h5>TÉRMINOS Y CONDICIONES USUARIOS EMPLEADORES MENSAJEROS MOTORIZADOS Y MENSAJEROS MOTORIZADOS.</h5>
					<br />
					<div class="card">
						<div class="card-header" id="section14Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section14" aria-expanded="false" aria-controls="section14">
									POLÍTICAS TARIFARIAS.
								</button>
							</h2>
						</div>
						<div id="section14" class="collapse" aria-labelledby="section14Header" data-parent="#accordionTerms">
							<div class="card-body">
								NO Usura. Vtapp se acoge a las políticas tarifarias establecidas por entidad competente según art 11,12 y 19Principio general de libertad de Tarifas de la ley 1369 de 2009 expedida por la Comisión de Regulación de Comunicaciones (Ley 1480 de 2011 art 55 Estatuto Protección al Consumidor).<br /><br /> 
								Los servicios de mensajería urbana motorizada suministrados por las empresas legalmente constituidas serán contratados por diversas personas naturales y jurídicas, más no constituyen servicios de mensajería expresa del sector postal, no hacen parte de <i>un número plural de objetos postales que se entregan a un operador postal para ser repartidos entre un plural de destinatarios</i> numeral 3.6 art 3 CRC. <br /><br />
								En concordancia con Decreto 2622 de 1994, 229 de 1995 Artículo 20º <i><strong>Contratación con terceros</strong>. Los concesionarios o licenciatarios de los servicios postales podrán contratar con terceros y bajo su responsabilidad e identificación, algunas de las actividades operativas necesarias para la prestación del servicio, de lo cual deberán informar al Ministerio de Comunicaciones</i>, la empresa Aliado Vtapp empleador mensajero deberá notificar al Ministerio del alcance del presente acuerdo.<br /><br />
								Ni la <strong>herramienta</strong> Vtapp ni el <strong>sistema</strong>, usurpan, suplantan, desplazan o remplazan a las entidades gubernamentales en el establecimiento de políticas tarifarias de acuerdo al art 425 del Código Penal, en tanto no promueve, alienta ni participa en el aumento de la capacidad transportadora de las empresas legalmente constituidas como prestadoras de servicio de mensajería urbana motorizada, adicionalmente, las tarifas son establecidas y pagadas de igual forma de acuerdo al cumplimiento del art 11 y 19 de la ley 1369 de 2009 a todos y cada uno de los distintos empleadores de mensajería urbana motorizada legalmente constituidos que ingresen al <strong>sistema</strong>.<br /><br />
								VTAPP no posee flota propia de transporte ni hace parte de ninguna sociedad con ningún <strong>Aliado Vtapp empleador mensajero motorizado</strong> y/o operador Postal.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section15Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section15" aria-expanded="false" aria-controls="section15">
									OBLIGACIONES LABORALES.
								</button>
							</h2>
						</div>
						<div id="section15" class="collapse" aria-labelledby="section15Header" data-parent="#accordionTerms">
							<div class="card-body">
								El empleador de los mensajeros motorizados se obliga a dar cumplimiento al ART. 12 capítulo 3 de la ley 1503 de 2011: 
								<blockquote class="blockquote">
									<i>Toda entidad, organización o empresa del sector público o  privado que para cumplir sus fines misionales ó en el desarrollo de sus actividades posea, fabrique, ensamble, comercialice, contrate o administre flotas de vehículos automotores o no automotores superiores a diez (10) unidades, o contrate o administre personal de conductores, contribuirán al objeto de la presente ley. Para tal efecto, deberá diseñar el Plan Estratégico de Seguridad Vial que será revisado cada dos (2) años para ser ajustado en lo que se requiere.</i>
								</blockquote>
								Es obligación del empleador y/o del mensajero motorizado estar al día en los pagos  del período prestado en Seguridad social ARL, Pensiones, Salud, al igual que Cajas de Compensación, ICBF, SENA,   seguros del vehículo SOAT, impuestos, multas, embargos, partes e infracciones, ocasionados antes, durante y/o después de la prestación del servicio en el que VTAPP obra exclusivamente como una herramienta que facilita y conecta a un proveedor y un cliente quienes obran en nombre propio y bajo su propia responsabilidad y riesgo. El Aliado Vtapp empleador mensajero declara que bajo ninguna circunstancia VTAPP incurrirá en <strong>coexistencia</strong> de contratos según el art 16 del Código Sustantivo del Trabajo (CST), por cuanto el <strong>usuario cliente</strong> de la app VTAPP, efectúa mediante la aplicación un acuerdo eventual, no periódico y temporal, sin subordinación ni dependencia con el <strong>mensajero motorizado</strong> un contacto para la prestación de un servicio esporádico,  el único y exclusivo contratante de los <strong>mensajeros</strong> es el Aliado Vtapp empleador mensajero quien recibe  los pagos y en consecuencia es el responsable de los pagos correspondientes a todas las prestaciones sociales del trabajador. Para  facilitar la administración al <strong>Aliado Vtapp empleador mensajero</strong> el <strong>sistema</strong> facilitará el acceso a un Portal administrativo de monitoreo y control de las actividades de su trabajador con la aplicación VTAPP. La jornada laboral de los mensajeros estará supeditada a lo establecido entre el empleador y su empleado, los servicios suministrados por VTAPP deben prestarse dentro de los horarios laborales pactados entre ambas partes, por ningún motivo y bajo ninguna circunstancia la "herramienta" establece los horarios de trabajo ni asume la obligación de pagos por horas extras, dominicales, festivos etc. En caso de que el empleador y mensajero determinen suministrar el servicio solicitado por el "usuario", este estará regulado bajo lo establecido en el dec 1072 2015  único reglamentario del sector trabajo art 2.2.1.2.1.1 y sub siguientes. La aplicación permitirá al mensajero habilitarse e in habilitarse dentro del <strong>sistema</strong> para que este tenga el control y la autonomía de sus horarios laborales, adicionalmente, dentro del PORTAL el <strong>Aliado Vtapp empleador mensajero</strong> tendrá la posibilidad de conectar o des conectar a sus colaboradores en los horarios laborales pactados entre ambas partes.<br /><br />
								Si <strong>mensajero motorizado</strong> y/o <strong>Aliado Vtapp empleador mensajero</strong> deciden prestar un servicio en horario extra laboral, dominical, festivo, nocturno, a un <strong>usuario/cliente</strong> a través de la herramienta este último asume como contratante todo pago de horas extras, prestaciones sociales, parafiscales y exime de dicha obligación tanto a la <strong>herramienta</strong> como al <strong>usuario cliente</strong>.<br /><br />
								De otra parte, no podrán descargar la <strong>herramienta</strong>, mensajeros motorizados independientes no  vinculados a alguna empresa legalmente constituida, será potestad libre y voluntaria de la <strong>herramienta</strong> aceptar ó no a <strong>mensajeros motorizados</strong> que se desvinculen de un <strong>Aliado Vtapp empleador mensajero</strong> y sean contratados por otra empresa empleadora vinculada, excepto cuando el Aliado Vtapp empleador mensajero pueda evidenciar una falta grave que afecte tanto la imagen como la reputación del anterior Aliado Vtapp empleador mensajero.<br /><br />
								Sin embargo, el <strong>mensajero motorizado</strong> desvinculado de la <strong>herramienta</strong> por parte de un <strong>Aliado Vtapp empleador mensajero</strong> no podrá descargar la <strong>aplicación</strong> con otro <strong>empleador</strong> mensajero hasta dentro de un plazo mínimo de seis (6) meses.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section16Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section16" aria-expanded="false" aria-controls="section16">
									SISTEMA DE GESTIÓN SEGURIDAD EN TRABAJO.
								</button>
							</h2>
						</div>
						<div id="section16" class="collapse" aria-labelledby="section16Header" data-parent="#accordionTerms">
							<div class="card-body">
								Elementos de protección: El <strong>usuario</strong> empleador mensajería debe definir los Elementos de Protección Personal (EPP) requeridos para sus usuarios mensajeros motorizados y sus acompañantes, de tal manera que se garantice seguridad en la conducción, el mensajero motorizado deberá garantizar como mínimo el porte de la siguiente indumentaria:  
								<ul>
									<li>Chaquetas anti fricción con franjas anti reflectivas.</li>
									<li>Coderas.</li>
									<li>Rodilleras.</li>
									<li>Casco abatible.</li>
									<li>Guantes.</li>
									<li>Botas.</li>
									<li>Gafas motocicleta.</li>
									<li>Antifricción.</li>
								</ul>
								Adicionalmente junto a la ARL el empleador deberá: 
								<ul>
									<li>Capacitar al personal, para el uso adecuado de los (EPP)* Establecer periodos y políticas para la verificación de su estado* Que los EPP cumplan con la exigencia mínimas de calidad, según lo establecido por la ley y normatividad.</li>
								</ul>
								Las pruebas para garantizar el nivel mínimo de competencias que debe realizar el <strong>usuario</strong> mensajero y empleador para validar el estado de salud de los "usuarios" mensajeros motorizados antes de ser ingresados  a VTAPP si este lo requiere son: 
								<ul>
									<li>Exámenes médicos de acuerdo con lo establecido en la ley.</li>
									<li>Exámenes psico censo métricos de acuerdo con lo establecido en la ley.</li>
									<li>Visiometria- Audiometría.</li>
									<li>Exámenes de coordinación motríz.</li>
									<li>Examen de psicología.</li>
									<li>Prueba teórica: Esta prueba debe medir el nivel de conocimiento del conductor, sobre los factores propios de la conducción, normatividad, vía y del vehículo que va a conducir.</li>
									<li>Prueba práctica: Realizar una prueba que permita conocer los hábitos y habilidades en la conducción, estas pruebas deben estar basadas en el tipo de vehículo que se va a conducir. Las pruebas deben ser realizadas, por personal que garantice idoneidad en cada campo.</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section17Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section17" aria-expanded="false" aria-controls="section17">
									CONTENIDO DE SOBRES Y PAQUETES.
								</button>
							</h2>
						</div>
						<div id="section17" class="collapse" aria-labelledby="section17Header" data-parent="#accordionTerms">
							<div class="card-body">
								Es responsabilidad del mensajero motorizado revisar  el contenido del elemento a transportar  para verificar que no contiene sustancias alucinógenas, drogas psicoactivas, estupefacientes, opio, marihuana, cocaína, alcohol, ningún tipo de narcóticos, explosivos, armas blancas y/o contundentes, armas de fuego, elementos radioactivos, elementos y/o sustancias químicas, mascotas, dinero en efectivo, títulos valores, cheques en blanco, cheques viajero, joyas ó metales preciosos en polvo ó en barra, objetos constitutivos del patrimonio cultural, histórico, antigüedades, objetos artísticos y/o obras de arte, máquinas para acuñar monedas, mercancía peligrosa, material  radioactivo, explosivos, contaminantes, inflamables, combustibles, líquidos y sólidos corrosivos, inflamables, pinturas, venenos, materias grasas y polvos colorantes, todo tipo de extintores, desechos orgánicos y hospitalarios, animales vivos o muertos sin disecar,  contratos cuyo valor no superara el de su costo material y bajo ninguna circunstancia respecto a un valor subjetivo del  contenido, ninguna especie de títulos valores, efectivo  y otros elementos que infrinjan las leyes y normas legales Colombianas  art 19 ley 19 de 1978 ó del país donde se habilite el uso de la <strong>herramienta</strong> y que el cliente le  entregue al motorizado utilizando el aplicativo VTAPP propiedad de la firma <strong>SOLUCIONAPPS SAS</strong>, la cual  no se hace responsable de las consecuencias que el transporte de este tipo de elementos ocasione  al usuario ó al motorizado.<br /><br />
								Sin embargo y en concordancia con lo estipulado en el art. 15 de la Constitución Política de Colombia: 
								<blockquote class="blockquote">
									<i>La correspondencia y demás formas de comunicación privada son inviolables, solo pueden ser interceptadas o revisadas mediante orden judicial, en los casos y con las formalidades que establezca la ley.</i>
								</blockquote>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" id="section18Header">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#section18" aria-expanded="false" aria-controls="section18">
									POLÍTICA DE USO MARCAS ALIADO VTAPP EMPLEADOR MENSAJERO.
								</button>
							</h2>
						</div>
						<div id="section18" class="collapse" aria-labelledby="section18Header" data-parent="#accordionTerms">
							<div class="card-body">
								Sin perjuicio de ninguna índole, VTAPP podrá publicar los logotipos, marcas, nombres de las empresas Aliado Vtapp empleador mensajero que hayan prestado el servicio de mensajería urbana dentro de la aplicación para facilitarle al usuario identificar  la empresa que suministró los servicios contratados, sin que exista por parte de la <strong>herramienta</strong> ninguna obligación en el pago por conceptos de derechos de autor, derechos de publicidad, usufructo de marca ó imagen, siempre y cuando la <strong>herramienta</strong> respete las políticas de uso de marca del Aliado Vtapp empleador mensajeros. La <strong>aplicación</strong> se reserva el derecho de retirar ó publicar la marca ó únicamente el nombre del empleador sin logotipos sin necesidad de notificación y/o autorización preliminar. Por el contrario, VTAPP podrá cobrar por concepto <strong>Pauta publicidad</strong> durante los períodos que determine pueda ser un elemento diferenciador de calidad en el mercado a la tarifa que establezca previa notificación y aprobación por medio electrónico y/o físico con mínimo un plazo de treinta (30) días anticipación, el pago de la tarifa por parte del empleador se establecerá por períodos trimestrales y su cancelación se regirá por las tarifas que la <strong>herramienta</strong> determine si al publicar los logos de los Aliados se genera una recordación de marca en los usuarios.
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	include("core/templates/__footer.tpl");
?>
  <script>
		$(document).ready(function() {
		});
  </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
