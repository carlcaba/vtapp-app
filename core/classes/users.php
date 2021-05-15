<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("table.php");
require_once("interfaces.php");
require_once("access.php");
require_once("city.php");
require_once("configuration.php");
require_once("resources.php");
require_once("phpmailer/PHPMailerAutoload.php");

class users extends table {
	//Relacion con otras clases
	var $inter;
	var $access;
	var $conf;
	var $adminMail;
	var $view;
	var $city;
	
	//Constructor
	function __constructor($user = "") {
		$this->users($user);
	}
	
	//Constructor anterior
	function users($user = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_USER");
		//Clases relacionadas
		$this->conf = new configuration("INIT_PASSWORD");
		//Inicializa los atributos
		$this->ID = $user;
		$this->THE_PASSWORD = $this->conf->verifyValue();
		$this->REGISTERED_ON = "NOW()";
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		$this->_addUniqueColumn("EMAIL");
		//Clases relacionadas
		$this->inter = new interfaces();
		$this->access = new access();
		$this->city = new city();
		$this->adminMail = "carlos.cabrera@vtapp.logicaestudio.com";
		$this->view = "VIE_USER_SUMMARY";
		if($user != "")
			$this->__getInformation();
	}
	
	//Funcion para Set el acceso
	function setAccess($access) {
		//Asigna la informacion
		$this->access->ID = $access;
		//Verifica la informacion
		$this->access->__getInformation();
		//Si no hubo error
		if($this->access->nerror == 0) {
			//Asigna el valor
			$this->ACCESS_ID = $access;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->ACCESS_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el acceso
	function getAccess() {
		//Asigna el valor del acceso
		$this->ACCESS_ID = $this->access->ID;
		//Busca la informacion
		$this->access->__getInformation();
	}
	
	//Funcion para Set la ciudad
	function setCity($city) {
		//Asigna la informacion
		$this->city->ID = $city;
		//Verifica la informacion
		$this->city->__getInformation();
		//Si no hubo error
		if($this->city->nerror == 0) {
			//Asigna el valor
			$this->CITY_ID = $city;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->CITY_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get la ciudad
	function getCity() {
		//Asigna el valor del ciudad
		$this->CITY_ID = $this->city->ID;
		//Busca la informacion
		$this->city->__getInformation();
	}

	//Funcion para Set la referencia
	function setReference($reference, $update = false) {
		//Asigna la informacion
		$this->REFERENCE = $reference;
		//Si hay que actualizar
		if($update) {
			$this->sql = "UPDATE " . $this->table . " SET REFERENCE = " . $this->_checkDataType("REFERENCE") . " WHERE ID = " . $this->_checkDataType("ID");
			$this->executeQuery();
		}
	}
	
	//Funcion para generar un ID
	function generateUserID() {
		$id = $this->generateRandomString();
		//Verifica los valores
		if($this->txtFIRST_NAME != "" && $this->txtLAST_NAME != "") {
			//Separa los nombres
			$names = explode(" ",$this->txtFIRST_NAME);
			$lname = explode(" ",$this->txtLAST_NAME);
			$id = $names[0];
			//Si tiene mas de un nombre
			if(count($names) > 1)
				$id .= substr($names[1],0,1);
			//Agrega el apellido
			$id .= $lname[0];
			//Si tiene mas de un apellido
			if(count($lname) > 1)
				$id .= substr($lname[1],0,1);
		}
		//termina
		return $id;
	}
	
	//Genera una cadena aleatoria
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	

	//Funcion para obtener la informacion del usuario
	function __getInformation() {
		//Verifica que no haya ingresado el usuario SUPERADMIN
		if($this->ID == $this->conf->verifyValue("SUPERADMIN_USER")) {
			//Limpia el error
			$this->nerror = 0;
			$this->error = "";
			//Pasa los valores 
			$this->ID = $this->conf->verifyValue("SUPERADMIN_USER");
			$this->THE_PASSWORD = $this->conf->verifyValue("SUPERADMIN_PASSWORD");
			$this->FIRST_NAME = "ADMINISTRADOR";
			$this->LAST_NAME = "";
			$this->EMAIL = $this->adminMail;
			$this->REFERENCE = "";
			$this->PHONE = "12184366";
			$this->CELLPHONE = "3002764204";
			$this->ADDRESS = "Carrera 98a #65-10, Bogotá, Colombia";
			$this->REGISTERED_ON = "2016-10-01";
			$this->CHANGE_PASSWORD = 0;
			$this->IS_BLOCKED = 0;
			//Informacion de clases relacionadas
			$this->setAccess(100);
			//Regresa
			return;
		}
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
			$this->error = $_SESSION["USER_NOT_REGISTERED"];
			$this->nerror = 20;
		}
		else {
			//Arma la sentencia SQL
			$this->sql = "SELECT THE_PASSWORD FROM $this->table WHERE ID = " . $this->_checkDataType("ID");
			//Extrae el resultado
			$row = $this->__getData();
			//Asigna la contrase�a
			$this->THE_PASSWORD = Desencriptar($row[0]);
			//Asigna los otros valores
			$this->setAccess($this->ACCESS_ID);
			$this->setCity($this->CITY_ID);
			
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Verifica el usuario por nombre y apellido
	function getInfoByName() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE FIRST_NAME = " . $this->_checkDataType("FIRST_NAME") . " AND LAST_NAME = " . $this->_checkDataType("LAST_NAME");
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["USER_NOT_REGISTERED"];
		}
		else {
			//Asigna los atributos
			$this->ID = $row[0];
			//Obtiene la informacion
			$this->__getInformation();
		}
	}

	//Verifica el usuario por mail
	function getInfoByMail() {
		//Verifica que no haya ingresado el usuario SUPERADMIN
		if($this->EMAIL == $this->adminMail) {
			//Limpia el error
			$this->nerror = 0;
			$this->error = "";
			//Pasa los valores 
			$this->ID = $this->conf->verifyValue("SUPERADMIN_USER");
			$this->THE_PASSWORD = $this->conf->verifyValue("SUPERADMIN_PASSWORD");
			$this->FIRST_NAME = "ADMINISTRADOR";
			$this->LAST_NAME = "";
			$this->PHONE = "12184366";
			$this->CELLPHONE = "3002764204";
			$this->ADDRESS = "Carrera 98a #65-10, Bogotá, Colombia";
			$this->REFERENCE = "";
			$this->EMAIL = $this->adminMail;
			$this->REGISTERED_ON = "2016-10-01";
			$this->CHANGE_PASSWORD = 0;
			$this->IS_BLOCKED = 0;
			//Informacion de clases relacionadas
			$this->setAccess(100);
			//Regresa
			return;
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE EMAIL = " . $this->_checkDataType("EMAIL");
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["USER_NOT_REGISTERED"];
		}
		else {
			//Asigna los atributos
			$this->ID = $row[0];
			//Obtiene la informacion
			$this->__getInformation();
		}
	}

	//Funcion que muestra el nombre completo
	function getFullName() {
		//Verifica que no haya ingresado el usuario SUPERADMIN
		if($this->ID == $this->conf->verifyValue("SUPERADMIN_USER")) {
			$this->FIRST_NAME = "ADMINISTRADOR";
			$this->LAST_NAME = "";
		}
		return $this->FIRST_NAME . " " . $this->LAST_NAME;
	}
	
	//Cuenta el total de usuarios
	function getTotalUsers() {
		//Variable a retornar
		$return = 0;
		try {
			//Arma la sentencia SQL
			$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE";
			//Obtiene los resultados
			$row = $this->__getData();
			//Valida el resultado
			if($row) {
				$return = $row[0];
			}
		}
		catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->nerror = 40;
		}
		return $return;
	}
	
	//Cuenta el total de usuarios creados en la ultima semana
	function getTotalUsersLastWeek() {
		//Variable a retornar
		$return = 0;
		try {
			//Total de usuarios
			$total = $this->getTotalUsers();
			//Arma la sentencia SQL
			$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE AND DATE(REGISTERED_ON) BETWEEN DATE_SUB(CURDATE(),INTERVAL 7 DAY) AND CURDATE()";
			//Obtiene los resultados
			$row = $this->__getData();
			//Valida el resultado
			if($row) {
 				if($total > 0)
					$return = (($row[0] / $total) * 100);
			}
		}
		catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->nerror = 40;
		}
		return $return;
	}
	
	//Funcion que genera el listado de destinatarios
	function getDestinyList($option, $company = "", $tableemp = "", $tablesxem = "") {
		//Arma la sentencia SQL
		$this->sql = "SELECT U.EMAIL FROM $this->table U";
		//Determina el filtro
		$where = "WHERE U.IS_BLOCKED = FALSE";
		$inner = "";
		//Verifica la opciona
		switch($option) {
			//Administradores plataforma
			case "adm":
				$where .= " AND U.ACCESS_ID >= 80";
				break;
			//Administradores plataforma empresa
			case "cad":
				$inner = " INNER JOIN $tableemp E ON (E.EMAIL = U.EMAIL) ";
				$where .= " AND U.ACCESS_ID > 60";
				if($company != "")
					$where .= " AND E.COMPANY_ID = '$company'";
				break;
			//Todos los usuarios de la empresa
			case "all":
				$inner = " INNER JOIN $tableemp E ON (E.EMAIL = U.EMAIL) ";
				if($company != "")
					$where .= " AND E.COMPANY_ID = '$company'";
				break;
			//Los usuarios con alguna evaluacion pendiente
			case "pen":
				$inner = " INNER JOIN $tableemp E ON (E.EMAIL = U.EMAIL) INNER JOIN $tablesxem S ON (S.EMPLOYEE_ID = E.ID)";
				$where .= " AND S.COMPLETED = FALSE";
				if($company != "")
					$where .= " AND E.COMPANY_ID = '$company'";
				break;
			//Los usuarios con alguna evaluaciones finalizadas
			case "fin":
				$inner = " INNER JOIN $tableemp E ON (E.EMAIL = U.EMAIL) INNER JOIN $tablesxem S ON (S.EMPLOYEE_ID = E.ID)";
				$where .= " AND S.COMPLETED = TRUE";
				if($company != "")
					$where .= " AND E.COMPANY_ID = '$company'";
				break;
		}
		//Completa la sentencia sql
		$this->sql .= " $inner $where ORDER BY 1";
		//Variable a retornar
		$result = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			array_push($result,$row[0]);
		}
		return $result;
	}

	//Funcion que despliega los valores en un usuario
	function showOptionList($tabs = 8, $selected = "", $reference = "", $fulloptions = false) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, EMAIL, ACCESS_ID, ADDRESS, PHONE, CELLPHONE, CITY_ID, FIRST_NAME, LAST_NAME, IDENTIFICATION FROM $this->table WHERE IS_BLOCKED = FALSE AND (REFERENCE = '$reference' OR REFERENCE = '') ORDER BY 1"; 
		//Variable a retornar
		$return = "$stabs<option value=\"\">" . $_SESSION["SELECT_OPTION"] . "</option>\n";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$options = "";
			if($fulloptions) {
				$ident = explode("-",$row[9]);
				$options = " data-firstname=\"" . $row[7] . "\" data-lastname=\"" . $row[8] . "\" data-typeid=\"" . $ident[0] . "\" data-identification=\"" . $ident[1] . "\" data-email=\"" . $row[1] . "\" data-address=\"" . $row[3] . "\" data-phone=\"" . $row[4] . "\" data-cellphone=\"" . $row[5] . "\" data-cityid=\"" . $row[6] . "\" ";
			}
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-access=\"" . $row[2] . "\" $options selected>" . $row[0] . " (" . $row[1] . ")</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-access=\"" . $row[2] . "\" $options>" . $row[0] . " (" . $row[1] . ")</option>\n";
		}
		//Retorna
		return $return;
	}


    //Funcion que devuelve el nombre de la imagen del usuario
    function getUserPicture($large = false) {
        //Define las extensiones a buscar
        $ext = [".png",".jpg",".gif",".jpeg"];
		//Nombre del archivo
		$filename = $this->ID;
		//Nombre largo
		$lrgname = "";
		//Define el nombre de la imagen
		if($large)
			$lrgname = "_160x160";
        //Define el path
        $path = "img/users/";
        //Define el valor por defecto
        $name = "user" . $lrgname . ".jpg";
        //Recorre el array
        foreach($ext as $valor) {
            //Define el nombre
            $check = $filename . $lrgname . $valor;
            //Verifica si existe el archivo
            if(file_exists($path . $check)) {
                $name = $check;
                break;
            }
        }
        //Retorna
        return $path . $name;
    }

	//Funcion que verifica los datos del usuario
	function check($phone = false) {         
		//Verifica que no haya ingresado el usuario SUPERADMIN
		if($this->ID == $this->conf->verifyValue("SUPERADMIN_USER")) {
			//Compara las contrase�as
			if($this->THE_PASSWORD == $this->conf->verifyValue("SUPERADMIN_PASSWORD")) {
				//Limpia el error
				$this->nerror = 0;
				$this->error = "";
				//Pasa los valores 
				$this->ID = $this->conf->verifyValue("SUPERADMIN_USER");
				$this->THE_PASSWORD = $this->conf->verifyValue("SUPERADMIN_PASSWORD");
				$this->FIRST_NAME = "ADMINISTRADOR";
				$this->LAST_NAME = "";
				$this->EMAIL = "carlcaba@gmail.com";
				$this->PHONE = "12184366";
				$this->CELLPHONE = "3002764204";
				$this->REFERENCE = "";
				$this->ADDRESS = "Carrera 98a #65-10, Bogotá, Colombia";
				$this->REGISTERED_ON = "2016-10-01";
				$this->FIRST_TIME = 0;
				$this->CHANGE_PASSWORD = 0;
				$this->IS_BLOCKED = 0;
				//Informacion de clases relacionadas
				$this->setAccess(100);
				//Regresa
				return;
			}
			else {
				//Genera el error
				$this->nerror = 20;
				$this->error = $_SESSION["WRONG_PASSWORD"];
				return;
			}
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, THE_PASSWORD, IS_BLOCKED FROM " . $this->table .
			" WHERE ID = " . $this->_checkDataType("ID");
		//Extrae el resultado
		$row = $this->__getData();
		//Usuario no existe
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["USER_NOT_REGISTERED"];
			//Sale
			return;
		}
		//Usuario bloqueado
		if($row[2]) {
			//Genera el error
			$this->nerror = 30;
			$this->error = $_SESSION["USER_BLOCKED"];
			//Termina
			return;
		}
		//Si es un mensajero
		if($phone)
			$result = $this->checkUser(Desencriptar($this->THE_PASSWORD), $phone);
		else
			$result = $this->checkUser(Desencriptar($row[1]));
		//Verifica el password
		if(!$result) {
			//Genera el error
			$this->nerror = 20;
			$this->error = $_SESSION["WRONG_PASSWORD"];
		}
		else {
			//Limpia el error
			$this->nerror = 0;
			$this->error = "";
			//Obtiene la informacion del usuario
			$this->__getInformation();
		}
	}
	
	//Valida la contrasena del usuario
	function checkUser($pass,$phone = false) {
		//Valida la informacion
		return $phone ? $pass == $this->CELLPHONE : $pass==$this->THE_PASSWORD ;
	}	
		
	//Valida el acceso del usuario a un menu
	function checkAccess($imenu) {
		//Obtiene los resultados
		$row = $this->inter->getAccessMenu($imenu);
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 50;
			$this->error = $_SESSION["MENU_NOT_REGISTERED"] . " ($imenu)";
			//Retorna
			return false;
		}
		//Obtiene el acceso
		$this->getAccess();
		//Verifica el acceso
		if($this->ACCESS_ID >= $row[0]) {
			//Limpia el error
			$this->nerror = 0;
			$this->error = "";
			//Si tiene acceso
			return true;
		}
		else {
			//Genera el error
			$this->nerror = 60;
			$this->error = $_SESSION["ACCESS_RESTRICTED"] . "(" . $this->ACCESS_ID . ")";
			//No tiene acceso
			return false;
		}
	}

	//Funcion para generar el password
	function generatePassword($len = 10, $flag_alpha = true, $flag_upper = true, $flag_number = true, $flag_special = true) {
		$alpha = "abcdefghijklmnopqrstuvwxyz";
		$alpha_upper = strtoupper($alpha);
		$numeric = "0123456789";
		$special = ".!@#$%^&*()_+-=";
		$chars = "";
		 
		//Configura los caracteres disponibles
		if ($flag_alpha)
			$chars .= $alpha;
		if ($flag_upper)
			$chars .= $alpha_upper;
		if ($flag_number)
			$chars .= $numeric;
		if ($flag_special)
			$chars .= $special;
		$length = $len;
		$len = strlen($chars);
		$pw = '';
		 
		for ($i=0;$i<$length;$i++)
			$pw .= substr($chars, rand(0, $len-1), 1);
		 
		//genera la contraseña
		$pw = str_shuffle($pw);
		
		return $pw;
	}
	
	//Funcion que adiciona el usuario
	function __add($origin = "", $lang = "", $client = "") {
		//Verifica el acceso
		if($this->acceso->ID == 70 || $this->ACCESS_ID == 70) {
			//Asigna usuario a cédula
			$this->ID = explode("-",$this->IDENTIFICATION)[1];
			//Asigna contraseña a celular
			$this->THE_PASSWORD = $this->CELLPHONE;
			//Asigna a NO CAMBIAR CONTRASEÑA
			$this->CHANGE_PASSWORD = "FALSE";
		}
		//Asigna la contraseña
		$this->THE_PASSWORD = Encriptar($this->THE_PASSWORD);
		//Realiza la adicion
		parent::_add(true,false);
		//Verifica si no hubo error
		if($this->nerror > 0) {
			$this->error .= "-" . $this->sql;
			return false;
		}
		//Verifica el lenguaje
		if($lang == "")
			$lang = $_SESSION["LANGUAGE"];
		//Genera los recursos
		$resources = new resources();

		//Verifica el mensaje
		if($client == "")
			//Obtiene informacion de la configuracion
			$mBody = sprintf($resources->getResourceByName("USER_WELCOME",$lang),
								$this->conf->verifyValue("APP_NAME"),
								$this->ID,
								$this->EMAIL,
								$this->conf->verifyValue("COMPANY_NAME"),
								$this->conf->verifyValue("COMPANY_NAME"));
		else
			//Obtiene informacion de la configuracion
			$mBody = sprintf($resources->getResourceByName("CLIENT_WELCOME",$lang),			
								$this->conf->verifyValue("APP_NAME"),
								$this->ID,
								$this->EMAIL,
								$client,
								$this->conf->verifyValue("COMPANY_NAME"),
								$this->conf->verifyValue("COMPANY_NAME"));
		$to = $this->EMAIL;
		//Envia el correo
		$this->sendMail($mBody, $to);
		
		//Envia correo con informacion
		if($client != "") {
			//Obtiene informacion de la configuracion
			$mBody = sprintf($resources->getResourceByName("CLIENT_WELCOME_ADMIN",$lang),			
								$this->conf->verifyValue("APP_NAME"),
								$this->ID,
								$this->EMAIL,
								$client,
								$this->conf->verifyValue("COMPANY_NAME"),
								$this->conf->verifyValue("COMPANY_NAME"));
			$to = $this->conf->verifyValue("MAIN_MAIL");
			//Envia el correo
			$this->sendMail($mBody, $to);
		}
	}
	
	//Funcion que activa o habilita a un usuario
	function activate($activate) {
		//Ajusta la informacion
		$this->IS_BLOCKED = ($activate == "true") ? "0" : "1";
        $this->THE_PASSWORD = Encriptar($this->THE_PASSWORD);
		//Realiza la actualizacion
		parent::_modify();
		//Verifica si no hubo error
		if($this->nerror > 0) {
			return false;
		}
		
		//Genera los recursos
		$resources = new resources("MAIL_USER_ACTIVATED");
		//Obtiene informacion de la configuracion
		/*
		$mBody = $resources->getResourceByName();
		$mBody = str_replace("{1}",$this->FIRST_NAME,$mBody);
		$mBody = str_replace("{2}",$this->conf->verifyValue("APP_NAME"),$mBody);
		$mBody = str_replace("{3}",$this->EMAIL,$mBody);
		$mBody = str_replace("{4}",Desencriptar($this->THE_PASSWORD),$mBody);
		$mBody = str_replace("{5}",$this->inter->cfecha(),$mBody);
		*/
		$mBody = sprintf($resources->getResourceByName(),
							$this->FIRST_NAME,
							$this->conf->verifyValue("APP_NAME"),
							$this->EMAIL,
							Desencriptar($this->THE_PASSWORD),
							$this->inter->cfecha());
		$to = $this->EMAIL;
		//Envia el correo
		$this->sendMail($mBody, $to, $resources->getResourceByName("SUBJECT_ACTIVATED_USER"));
		//Retorna 
		return true;
	}
	
	//Funcion que modifica la contrasena
	function modifyPassword() {
		//Asigna la contraseña
		$this->THE_PASSWORD = Encriptar($this->THE_PASSWORD);
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET THE_PASSWORD = AES_ENCRYPT(THE_PASSWORD,'" . $this->inter->clave . "') WHERE ID = " . $this->_checkDataType("ID");
		$this->sql = "UPDATE " . $this->table . " SET THE_PASSWORD = " . $this->_checkDataType("THE_PASSWORD") . " WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}

    //Funcion que modifica la primera conexion
    function modifyFirstTime() {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET FIRST_TIME = " . $this->_checkDataType("FIRST_TIME") . " WHERE ID = " . $this->_checkDataType("ID");
        //$this->sql = "UPDATE " . $this->table . " SET FIRST_TIME = " . $this->_checkDataType("FIRST_TIME") . ", ON_BOARDING = " . $this->_checkDataType("ON_BOARDING") . "  WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }

    //Funcion que actualiza el estado y posicion
    function updatePosition($state, $position, $gluser = "") {
		$lat = "NULL";
		$lng = "NULL";
		//Verifica si envía posicion
		if($position != "") {
			$pos = explode(",",$position);
			$lat = $pos[0];
			$lng = $pos[1];
		}
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET ON_LINE = " . ($state ? "TRUE" : "FALSE") . ", LATITUDE = $lat, LONGITUDE = $lng";
		//Si el estado es activo
		if($state && $gluser != "")
			$this->sql .= ", GOOGLE_USER = '" . $gluser . "'";
		//Completa la sentencia sql
		$this->sql .= " WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }


    //Funcion que modifica el cambio de contraseña
    function modifyChangePassword() {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET CHANGE_PASSWORD = " . $this->_checkDataType("CHANGE_PASSWORD") . " WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }

	//Funcion que elimina (bloquea) la informacion de un usuario
	function delete() {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET IS_BLOCKED = TRUE WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		if(!$this->executeQuery()) {
			//Genera el error
			$this->nerror = 10;
			$this->error = mysql_error();
		}
		else {
			//Limpia el error
			$this->nerror = 0;
			$this->error = "";
		}
	}	

	//Funcion que elimina la informacion de un usuario
	function deleteForever() {
		//Arma la sentencia SQL			
		$this->sql = "DELETE FROM $this->table WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Funcion que cambia la contrase�a de un usuario
	function changePassword($newpass, $email = true, $restore = false) {
		//Asigna la contraseña
		$this->THE_PASSWORD = Encriptar($newpass);
		//Arma la sentencia de actualización
		$this->sql = "UPDATE $this->table SET THE_PASSWORD = " . $this->_checkDataType("THE_PASSWORD");
		//Verifica si la contrase�a caduca
		if($this->conf->verifyValue("PASSWORD_EXPIRES") != 0)
			$this->sql .= ", LAST_FECHA = ADDDATE(CURDATE(),$_SESSION[EXPIRE_TIME])";
		//Verifica si debe confirmar el cambio de contaseña
		if($restore) 
			$this->sql .= ", CHANGE_PASSWORD = TRUE";
		else
			$this->sql .= ", CHANGE_PASSWORD = FALSE";
		//Termina la sentencia
		$this->sql .= " WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
		//Si hay error
		if($this->nerror > 0) {
			//Termina
			return $this->nerror;
		}
		//Si debe enviar un correo al usuario
		if($email) {
			$resource = new resources("MAIL_RESTORE_PASSWORD");
			//Arma el cuerpo del mensaje
			$mBody = $resource->getResourceByName();
			$mBody = str_replace("{1}",$this->FIRST_NAME . " " . $this->LAST_NAME,$mBody);
			$mBody = str_replace("{2}",$this->conf->verifyValue("APP_NAME"),$mBody);
			$mBody = str_replace("{3}",$this->EMAIL,$mBody);
			$mBody = str_replace("{4}",Desencriptar($this->THE_PASSWORD),$mBody);
			$mBody = str_replace("{5}",$this->inter->cfecha(),$mBody);
			//Envia el correo
			$this->sendMail($mBody, $this->EMAIL, $resource->getResourceByName("SUBJECT_PASSWORD_UPDATE"));
		}
		//Termina
		return $this->nerror;
	}
	
	//Funcio�n para recordar a un usuario su contrase�a
	function rememberPassword(){
		$resource = new resources("MAIL_REMEMBER_PASSWORD");
		//Arma el cuerpo del mensaje
		$mBody = $resource->getResourceByName();
		$mBody = str_replace("{1}",$this->FIRST_NAME . " " . $this->LAST_NAME,$mBody);
		$mBody = str_replace("{2}",$this->conf->verifyValue("APP_NAME"),$mBody);
		$mBody = str_replace("{3}",$this->EMAIL,$mBody);
		$mBody = str_replace("{4}",Desencriptar($this->THE_PASSWORD),$mBody);
		$mBody = str_replace("{5}",$this->inter->cfecha(),$mBody);
		//Envia el correo
        $this->sendMail($mBody, $this->EMAIL, $resource->getResourceByName("SUBJECT_REMEMBER_PASSWORD"));
		//Regresa
		return $this->nerror;
	}
	
	//Funcion que envia el correo
	function sendMail($body, $to, $subject = "DEFAULT_TEXT") {
		$resource = new resources("NEW_USER_REGISTERED");
	    if($subject == "DEFAULT_TEXT") {
	        $subject = $resource->getResourceByName() . " " . $this->conf->verifyValue("COMPANY_NAME") . "!";
		}
		//Obtiene el mail principal
		$mailto = explode(",",$this->conf->verifyValue("MAIN_MAIL"));
		$type = $this->conf->verifyValue("MAIL_TYPE");
		$web_site = $this->conf->verifyValue("WEB_SITE");
		$site_root = $this->conf->verifyValue("SITE_ROOT");
		//Envia confirmacion al usuario
		$mail = new PHPMailer(true);
		$mail->CharSet = 'utf-8';
		ini_set('default_charset', 'UTF-8');
		try {
			//Define datos del mensaje
			$mail->addReplyTo($mailto[0], $this->conf->verifyValue("COMPANY_NAME"));
			$mail->setFrom($mailto[0], $this->conf->verifyValue("COMPANY_NAME"));
			$mail->addAddress($to, $this->FIRST_NAME . " " . $this->LAST_NAME);
			$mail->Subject  = $subject;

			//Ajusta los parametros de la clase que envia el correo
			//Decirle a la clase que use transporte 
			switch($type) {
				case "SMTP": {
					$mail->IsSMTP();
					break;
				}
				case "Mail": {
					$mail->isMail();
					break;
				}
				case "SendMail": {
					$mail->IsSendmail();
					break;
				}
			}
			//Completa la informacion del cuerpo
			//$body = sprintf($body,$this->conf->verifyValue("COMPANY_NAME"),$this->ID,$this->EMAIL,$this->conf->verifyValue("APP_NAME"),$this->conf->verifyValue("APP_NAME"));
			//Mensaje opcional, por si no puede ver el mensaje HTML
			$mail->AltBody    = $resource->getResourceByName("REQUIRES_EMAIL_VIEWER");
			//$mail->addAttachment("images/logo-mail.gif", "Vtapp");
			//$mail->AddEmbeddedImage($web_site . $site_root . "images/logo-mail.gif", "logo-mail");
			$mail->msgHTML($this->mailHeader($body, $web_site, $site_root));
			
			/*
			FOR DEBUGGIN PURPOSES 
			if($_SERVER["SERVER_NAME"] == "localhost") {
				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = "ssl";
				$mail->Host = "smtp.gmail.com";
				$mail->Port = 465;
				$mail->Username ='carlcaba@gmail.com';
				$mail->Password = 'L0g1c4357ud10';
			}
			*/

			//Envia el mail y comprueba el estado	
			$mail->send();
		}
		catch (phpmailerException $e) {
			$this->error = $resource->getResourceByName("ERROR_SENDING_MAIL") . " (" . $e->errorMessage() . ")";
			$this->nerror = 18;
		}
		catch (Exception $e) {
			$this->error = $resource->getResourceByName("ERROR_SENDING_MAIL") . " (" . $e->errorMessage() . ")";
			$this->nerror = 30;
		}
	}
	
	function mailHeader($body, $web_site, $site_root) {
		//Arma la cabecera del mensaje
		return "<a href='" . $web_site . "' target='_blank'>\n" .
				"\t<img src=\"" . $web_site . $site_root . "img/logo/logo.png\" alt=\"Vtapp\" border=\"0\" />\n" .
				"</a>\n\n" .
				"\t<hr color='black' height='1' width='100%' noshade><br>\n" .
				"<font style='color:#000000;font-family:tahoma;font-size:12px'>\n" .
				$body .
				"</font>\t";
	}

	//Funcion para listar los usuarios registrados
	function listTable($tabs = 10) {
		//Cadena a retornar
		$return = "";
		//Arma la sentencia SQL
		$this->sql = "SELECT U.ID ID_USER, CONCAT(NULLIF(U.FIRST_NAME, ''),IF(NULLIF(U.FIRST_NAME, '') IS NULL,'',' '),NULLIF(U.LAST_NAME, '')) FULL_NAME, U.EMAIL EMAIL, " . 
					"R.RESOURCE_TEXT ACCESS_NAME, U.REGISTERED_ON REGISTERED_ON, U.IS_BLOCKED IS_BLOCKED, A.ID ACCESS_ID, U.CHANGE_PASSWORD FROM " . 
				$this->table . " U, " . $this->access->table . " A, " . $this->access->resources->table . " R WHERE U.ACCESS_ID = A.ID AND A.RESOURCE_NAME = R.RESOURCE_NAME " .
				"AND R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " ORDER BY U.REGISTERED_ON DESC";
				
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Inicia la GUI
			$return .= "<tr>\n";
			$return .= "<td title=\"$row[0]\">$row[0]</td>\n";
			$name = ($row[1] == "' '" ? "" : $row[1]);
            if(!mb_detect_encoding($name, 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $name = utf8_encode($name);
            }
			$return .= "<td title=\"$name\">$name</td>\n";
			$return .= "<td title=\"$row[2]\">$row[2]</td>\n";
			$return .= "<td title=\"$row[3]\">$row[3]</td>\n";
			$return .= "<td>" . ($row[7] == 0 ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"]) . "</td>\n";
			$return .= "<td>" . ($row[5] == 0 ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"]) . "</td>\n";
			
			//Verifica el estado para activar o desactivar
			if($row[5])
				$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ACTIVATE"] . "\"><i class=\"fa fa-unlock\"></i></button>";
			else 
				$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DEACTIVATE"] . "\"><i class=\"fa fa-lock\"></i></button>";
			
			$view = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('$row[0]','view')\"><i class=\"fa fa-eye\"></i></button>";
			$edit = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('$row[0]','edit')\"><i class=\"fa fa-pencil-square-o\"></i></button>";
			$delete = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('$row[0]','delete')\"><i class=\"fa fa-trash\"></i></button>";

			$return .= "<td align=\"center\"><div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $view . $edit . $delete . "</div></div></td>\n";
			$return .= "</tr>\n";
		}
		//Retorna
		return $return;
	}
	
	function dataForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "disabled", "", "", 
								"", "", "", 
								"", "", "", "",
								"", "", "", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newUser.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled", "", "", 
								"", "", "",  
								"", "", "", "",
								"", "", "", "",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editUser.php";
		}
		else {
			$readonly = array("disabled", "disabled", "disabled", "disabled", 
							"disabled", "disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteUser.php";
		}

		//Variable a regresar
		$return = array("tabs" => $stabs,
						"readonly" => $readonly,
						"actiontext" => $actiontext,
						"link" => $link,
						"showvalue" => true);
		//Retorna
		return $return;
	}
	
	//Funcion que muestra la forma para asociacion
	function showUserForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "disabled");
			$action = $_SESSION["MENU_NEW"];
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled");
			$action = $_SESSION["EDIT"];
		}
		else {
			$readonly = array("disabled", "disabled");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmAssociateUser\" name=\"frmAssociateUser\" role=\"form\">\n";
		//Muestra la GUI
		
		$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("THE_PASSWORD", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}
	
	
	//Funcion que muestra la forma
	function showForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "disabled", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newUser.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editUser.php";
		}
		else {
			$readonly = array("disabled", "disabled", "disabled", "disabled", "readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteUser.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmUser\" name=\"frmUser\" role=\"form\">\n";
		//Muestra la GUI
		
		$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("THE_PASSWORD", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("FIRST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("LAST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("IDENTIFICATION", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ACCESS_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbAccess\" name=\"cbAccess\" " . $readonly[$cont++] . ">\n";
		$return .= $this->access->showOptionList(9,$this->access->ID);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["CHANGE_PASSWORD"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbChangePassword\" name=\"cbChangePassword\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t\t<option value=\"FALSE\"" . ($this->CHANGE_PASSWORD ? "" : " selected") . ">" . $_SESSION["MSG_NO"] . "</option>\n";
		$return .= "$stabs\t\t\t\t<option value=\"TRUE\"" . ($this->CHANGE_PASSWORD ? " selected" : "") . ">" . $_SESSION["MSG_YES"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["IS_BLOCKED"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbBlocked\" name=\"cbBlocked\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t\t<option value=\"FALSE\"" . ($this->IS_BLOCKED ? "" : " selected") . ">" . $_SESSION["ACTIVE"] . "</option>\n";
		$return .= "$stabs\t\t\t\t<option value=\"TRUE\"" . ($this->IS_BLOCKED ? " selected" : "") . ">" . $_SESSION["IS_BLOCKED"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}
	
	//Funcion que retorna el resumen por usuario
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit,$options = "") {
		//	var fields = ["USER_ID", "FULL_NAME", "EMAIL", "ACCESS_NAME", "CHANGE_PASSWORD", "IS_BLOCKED", "ACCESS_ID", "PREFIX"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		//Verifica las opciones
		if($options != "") {
			if(strpos($options,";") !== false) {
				$opt = explode(";",$options);
				$sWhere .= " AND PREFIX LIKE '" . substr($opt[0],0,-1) . "%' AND REFERENCE = '" . $opt[1] . "'";
			}
			else {
				$sWhere .= " AND PREFIX LIKE '" . substr($options,0,-1) . "%'";
			}
		}
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(USER_ID) FROM $this->view $sWhere";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            return array();
        }
		$iTotal = $row[0];

		$output = array(
			"recordsTotal" => $iTotal,
			"recordsFiltered" => $iTotal,
			"data" => array());
		
		//Arma la sentencia SQL
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		$output['sql'] = $this->sql;
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == "USER_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[5])
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $this->inter->encrypt($aRow[$i]) . "',true,'$aRow[$i]');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $this->inter->encrypt($aRow[$i]) . "',false,'$aRow[$i]');\"><i class=\"fa fa-lock\"></i></button>";
						
						$profile = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["PROFILE"] . "\" onclick=\"location.href = 'profile.php?id=$aRow[$i]'\");\"><i class=\"fa fa-id-card\"></i></button>";
						$view = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $this->inter->encrypt($aRow[$i]) . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$reset = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["RESET_PASSWORD"] . "\" onclick=\"resetPassword('" . $this->inter->encrypt($aRow[$i]) . "');\"><i class=\"fa fa-wrench\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $this->inter->encrypt($aRow[$i]) . "','edit');\"><i class=\"fa fa-pencil-square-o\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $this->inter->encrypt($aRow[$i]) . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
						if(substr($aRow[7],0,-1) == "CL") 
							$funds = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ADD_FUNDS"] . "\" onclick=\"addFunds('" . $this->inter->encrypt($aRow[$i]) . "');\"><i class=\"fa fa-money\"></i></button>";
						else 
							$funds = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ADD_FUNDS"] . "\" onclick=\"addFunds('" . $this->inter->encrypt($aRow[$i]) . "');\" disabled=\"disabled\"><i class=\"fa fa-money\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $reset . $activate . $funds . $profile . $view . $edit . $delete . "</div></div>";
						$row[$aColumnsBD[$i]] = $aRow[$i];
						$row[$aColumnsBD[count($aColumnsBD)-1]] = $action;
					}
				}
				else if($aColumnsBD[$i] == "ID") {
					$first = "<input type=\"checkbox\" class=\"flat\" name=\"table_records\" value=\"" . $this->inter->Encriptar($aRow[0]) . "\" data-name=\"$aRow[1]\">";
					$row[$aColumnsBD[$i]] = $first;
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_YES"] : $_SESSION["MSG_NO"];
				}
				else if($aColumnsBD[$i] == "CHANGE_PASSWORD") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_YES"] : $_SESSION["MSG_NO"];
				}
				else if($aColumnsBD[$i] != ' ') {
					$row[$aColumnsBD[$i]] = $aRow[$i];
				}
			}
			array_push($output['data'],$row);
		}
		return $output;
	}

	//Funcion para listar los usuarios registrados y sin correo alterno
	function listTable2($tabs = 10) {
		//Cadena a retornar
		$return = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT U.ID ID_USER, CONCAT(NULLIF(U.FIRST_NAME, ''),IF(NULLIF(U.FIRST_NAME, '') IS NULL,'',' '),NULLIF(U.LAST_NAME, '')) FULL_NAME, U.EMAIL EMAIL, " . 
					"R.RESOURCE_TEXT ACCESS_NAME, U.REGISTERED_ON REGISTERED_ON, U.IS_BLOCKED IS_BLOCKED, A.ID ACCESS_ID, U.ALTERNATE_EMAIL, U.FIRST_TIME, U.CHANGE_PASSWORD " .
					"FROM $this->table U " .
					"INNER JOIN " . $this->access->table . " A ON (U.ACCESS_ID = A.ID) ".
					"INNER JOIN " . $this->access->resources->table . " R ON (R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " AND R.RESOURCE_NAME = A.RESOURCE_NAME) " .
					"WHERE U.ALTERNATE_EMAIL = '' OR U.FIRST_TIME OR U.CHANGE_PASSWORD ".
					"ORDER BY U.REGISTERED_ON DESC";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Inicia la GUI
			$return .= "$stabs<tr class=\"even pointer\">\n";
			$return .= "$stabs\t<td class=\"a-center \">\n";
			$return .= "$stabs\t\t<input type=\"radio\" class=\"flat\" id=\"optUser\" name=\"table_records\" value=\"" . $this->inter->Encriptar($row[0]) . "\" data-name=\"$row[1]\">\n";
			$return .= "$stabs\t</td>\n";
			//Verifica si es nuevo
			if($row[8])
				$span = "<span class=\"label label-success pull-right\">" . $_SESSION["NEW_USER"] . "</span>";
			else
				$span = "";
			$return .= "$stabs\t<td class=\" \" title=\"$row[0]\">$row[0] $span</td>\n";
			$name = ($row[1] == "' '" ? "" : $row[1]);
            if(!mb_detect_encoding($name, 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $name = utf8_encode($name);
            }
			$return .= "$stabs\t<td class=\" \" title=\"$name\">$name</td>\n";
			$return .= "$stabs\t<td class=\" \" title=\"$row[2]\">$row[2]</td>\n";
			$return .= "$stabs\t<td class=\" \" title=\"$row[3]\">$row[3]</td>\n";
			$return .= "$stabs\t<td class=\" \" title=\"$row[4]\">" . $this->inter->cfecha($row[4],"d m Y"," ",false) . "</td>\n";
			$return .= "$stabs\t<td class=\" \">" . ($row[5] == 0 ? $_SESSION["MSG_YES"] : $_SESSION["MSG_NO"]) . "</td>\n";
			$return .= "$stabs\t<td class=\" last\" title=\"$row[7]\">$row[7]\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfLblAlternateEmail\" name=\"hfLblAlternateEmail\" value=\"" . $comments2[1] . "\"/>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfLblEmail\" name=\"hfLblEmail\" value=\"" . $comments[1] . "\"/>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfAlternateEmail\" name=\"hfAlternateEmail\" value=\"$row[7]\"/>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfEmail\" name=\"hfEmail\" value=\"$row[2]\"/>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfFirstTime\" name=\"hfFirstTime\" value=\"$row[8]\"/>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfChangePassword\" name=\"hfChangePassword\" value=\"$row[9]\"/>\n</td>\n";
			$return .= "$stabs\t\t<input type=\"hidden\" id=\"hfPlaceHolder\" name=\"hfPlaceHolder\" value=\"" . $comments[2] . "\"/>\n</td>\n";
			$return .= "$stabs</tr>\n";
		}
		//Retorna
		return $return;
	}
	
    //Funcion que modifica el email
    function modifyEmail() {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET ID = " . $this->_checkDataType("EMAIL") . ", EMAIL = " . $this->_checkDataType("EMAIL") . " WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }

    //Funcion que modifica el email alterno
    function modifyAlternateEmail() {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET ALTERNATE_EMAIL = " . $this->_checkDataType("ALTERNATE_EMAIL") . " WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }
	
	//Funcion para mostrar los contactos en el panel
	function showContactPanel($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT USER_ID, FULL_NAME, ACCESS_NAME, REGISTERED_ON, AREA_NAME, LAST_LOGIN, LAST_LOGOUT " .
				"FROM $this->view WHERE IS_BLOCKED = FALSE AND LANGUAGE_ID = $lang AND USER_ID <> '" . $_SESSION["vtappcorp_userid"] . "' ORDER BY REGISTERED_ON DESC";
		//Variable a devolver
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Imagen del usuario
			$avatar = "img/users/" . $row[0] . ".jpg";
			$badgeStatus = "";
			if(!file_exists($avatar))
				$avatar = "img/users/user.jpg";
			//Define los datos de login logout
			if($row[5] && $row[6]) {
				$login = strtotime($row[5]);
				$logout = strtotime($row[6]);
				//Si esta online
				if($login > $logout) 
					$badgeStatus = "<span class=\"badge badge-success float-right\">Online</span>\n";
				else
					$badgeStatus = "<span class=\"badge badge-danger float-right\">Offline</span>\n";
			}
			else if($row[5]) {
				$login = strtotime($row[5]);
				$logout = strtotime(date("Y-m-d H:i:s") . " -15 minutes");
				//Si esta online
				if($login > $logout) 
					$badgeStatus = "<span class=\"badge badge-success float-right\">Online</span>\n";
				else
					$badgeStatus = "<span class=\"badge badge-danger float-right\">Offline</span>\n";
			}
			//Inicia la GUI
			$return .= "<li><a href=\"javascript:void(0);\" onclick=\"showDirectChat('$row[0]');\">\n";
			$return .= "<img class=\"contacts-list-img\" src=\"$avatar\">\n";
			$return .= "<div class=\"contacts-list-info\">\n";
			$return .= "<span class=\"contacts-list-name\">" . ucwords(strtolower($row[1]));
			$return .= "<small class=\"contacts-list-date float-right\">" . date("d/M/Y") . "</small>\n";
			$return .= "</span>\n";
			$return .= "<span class=\"contacts-list-msg\">$row[2] " . ($row[4] ? "($row[4])" : "") . " $badgeStatus</span>\n";
			$return .= "</div></a></li>\n";
		}
		return $return;
	}
	
	//Funcion que muestra la forma
	function showProfileForm($disabled, $action = "edit", $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$valcode = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es edicion
		$action = $_SESSION["EDIT"];
		$link = "core/actions/_save/__editProfile.php";
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "";
		//Muestra la GUI
		$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
		$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID_USER\" id=\"txtID_USER\" value=\"" . $this->ID_USER . "\" required=\"required\" />\n";

		$return .= $this->showField("FIRST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("LAST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("IDENTIFICATION", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $disabled);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ACCESS_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbAccess\" name=\"cbAccess\" $disabled>\n";
		$return .= $this->access->showOptionList(8,$showvalue ? $this->ACCESS_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfType\" name=\"hfType\" value=\"users\" >\n";

		//Retorna
		return $return;
	}

	//Funcion para ajustar los comentarios
	function getComments() {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
        //Ajusta la informacion de los recursos
        foreach($this->arrColComments as &$str) {
            //Si contiene definicion de tipo de campo
            if(strpos($str,",") !== false) {
                $temp = explode(",",$str);
                $str = $temp[1];
            }
        }
	}
	
    //Funcion que activa o inactiva usuario en linea
    function setOnline($value) {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET ON_LINE = " . ($value ? "TRUE" : "FALSE")  . " WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }

    //Funcion que verifica usuarios enlinea
    function getOnline($value) {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID, GOOGLE_USER FROM " . $this->table . " WHERE ON_LINE AND ACCESS_ID = 70";
		//Verify value
		if($value != "") {
			$this->sql .= " AND REFERENCE = '$value'";
		}
		//Variable a devolver
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Arma la respuesta
			$data = array("uid" => $row[0],
							"fbid" => $row[1]);
			array_push($return,$data);
		}
		return $return;
    }

	//Enviar notification a firebase
	function sendGCM($message) {
		$id = $this->GOOGLE_USER;
		if(!$this->conf->verifyValue("PUSHSAFER_ACTIVE")) {
			$url = $this->conf->verifyValue("FIREBASE_SERVER");
			$fields = array ('registration_ids' => array ($id),
							'data' => array ("message" => $message)
						);
			$fields = json_encode ($fields);
			$headers = array (
					'Authorization: key=' . $this->conf->verifyValue("FIREBASE_SERVER_KEY"),
					'Content-Type: application/json'
			);
			error_log("Start notification: data -> " . print_r($fields,true));
			$this->nerror = 0;
			$this->error = "";
			try {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				$result = curl_exec($ch);
				curl_close ($ch);
			}
			catch (Exception $ex) {
				$this->nerror = 110;
				$this->error = $ex->getMessage();
				error_log("Error on notification: " . $ex->getMessage());
				$result = "";
			}
		}
		else {
			$url = $this->conf->verifyValue("PUSHSAFER_GATEWAY");
			$pk = $this->conf->verifyValue("PUSHSAFER_PRIVATE_KEY");
			$picture = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAACPCAYAAAAhv5PqAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAABBF0RVh0WE1MOmNvbS5hZG9iZS54bXAAPD94cGFja2V0IGJlZ2luPSIgICAiIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNC4xLWMwMzQgNDYuMjcyOTc2LCBTYXQgSmFuIDI3IDIwMDcgMjI6Mzc6MzcgICAgICAgICI+CiAgIDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+CiAgICAgIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICAgICAgICAgIHhtbG5zOnhhcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+CiAgICAgICAgIDx4YXA6Q3JlYXRvclRvb2w+QWRvYmUgRmlyZXdvcmtzIENTMzwveGFwOkNyZWF0b3JUb29sPgogICAgICAgICA8eGFwOkNyZWF0ZURhdGU+MjAxOC0xMS0yOVQyMDo1ODoyN1o8L3hhcDpDcmVhdGVEYXRlPgogICAgICAgICA8eGFwOk1vZGlmeURhdGU+MjAxOS0wNi0xMFQyMDo1NDozNlo8L3hhcDpNb2RpZnlEYXRlPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIj4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgDD7DNgAAABh0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzT7MfTgAAElxwclZXeJzFmwl4U8edwOfJ2Oiy9B4EW9ITOJZl+cCSDJZtQrJNQjdNClkIpdndhJQkkM/djQlkN9AACSEcBRKglKMGcdqGzzaXCYcB3we+cLSATQLGUFLol5JNut1v26/Zfk2b2f/MOyUbbEtaOnjsp9E8/eZ/zn8k0fVtw3+jQvQmJi2AsR/jAvI3EMABPw4UkEvywB/AcJ1DhknzB+CpADxEAX9BwI/8ZHaOn8yD5/z+QIE/AA+Rnzwo8Ptz/H5UQF4CboMbCgoCOQVkEjxXUODPySGv5c8JFOSQ22B2QQ6dgSgA0ReC+1EBLA+jB9nTe+p/4bnWGHjQXNLTeuoOPdHX9uXfgp3aU1f1T7+8iP/hxoXRD5rt7Kmrn3OrG79465LrQTGH0hhWP6R5g7Vw+SzHRWUBYfLNHJcQlQWEspO6a7qHcJuRB/lZJrr8cd3VPcTXBr+L4TgjF2cwR4+fdLm654Vbl/Ci2331g9+l4zjeGBcNBRD2w5drumfeDODFt/s+HZr940D9nD4aCnB011yeduMC/sntvrvo/Hr90Ph6DhyAjTNErIDAd/s68Nt3bvzvmCunbEP3f3B/jjOYNREnASI3zu9tmTi8+CPyj2INcXouUv7UGxeeHX78E/sncKx+ZKQK+HF4+Uc3yggGiIoCwuIzxP85o5mN2APClJ/kH/AA/chIFRCm/BxJQCQEIAb/BnxE6TzNQZHlgDD5LLU/uKA+LjIPCJufwAkKAA+IRAFh8jUcSQBiDojEA8LkM8T+nJiFIylEwq2/OEEBYAJ9XCQWCLv+owqgv/SRxGC4fA1xPhIBXAIbiQXC5Ws5UgLQEOAMEVggXD7NAEZBA7wh/CwUNl8jZkCOj8gFwuYjMQPSDgswhlcLhs9nFQeArjGH5wLh82kNQI1ADaFhOd0D5RMFcEoDHwxHAxHwNYLoCUImhPMIa4h9kHwagryYhzluFNHAA+XrJO8X1mCHatAwXBOEwVcQrFgE0EAgl5ph78XD5zOsnOsYVtKA6Ad8DGscERFf31RluP8dcPbhzIx0LcW/vBewIZl4EIM8er0lPr+3eZbE93zaePn+d9C4l4o+Vox/ORPyQQtgzEaWVCi6e63ioYnXmromX28xEbb7k4YTE641DWYAlhIFCsMKDqhoAI6FckWqk7PDvVbxdW5v83TCHn+lfhNhD4HPiCzq64ycA2QNcCxrkCYmiLlBSlJQLwdlyTLCzrxS/2OJPQQ+MkiVD6tnEGOQbS9EA9GNsB0bxPGgIKFPx0mqiE3vrs9Xs7OHwGdEp0+g5Ser7IJSRUQFlUsEKUPK3SgVrjpGm3216esg/tXGwRMQI0lM5eLkXdAo+QHIaIQYMcjzOFkNoj3EWZfUbPfVBjylr30ICdCskoxX74JGZdwIKtDwwRpQb1jksU6l90b8SO95PPVG54eD84Xyl1cpwKgSUdIMpAnGwMurVGuJOoORZbBa/qf6Oi55rtZPHQKf2FbmSmtRSyl6vT5Ww6r9QtYS6bwBUa4HZH+stxV/53rbbHj81VD4oAFZIMW91V1ser2qUArREouov0MOIHZf573WWDWU+OuvAelCnQnluDcoz4VoSY+o3R+73hqY0Nu0aqjxLzYNr0gyYJf/KHuEWks81Es4D2R/pLdlGXB/P0w+WYCR54KlDtGAUh0NpCUTIuwysEHNcPKf3BhWjPmBegKnVoGwCvUMI9lCvvD1Nu3JVrGHxUcoTkgmUvyruhzjkiakx1LnOdglWnOuNf/PhPD5sMMGZxW58f0iIiFkhhFqpT+HsofLF1bQP8cnDBARqmqZdtgi+rGh/2F4fGhmA6dkOWUnlOwsJ6ig5zme6c//na+3+e+HJb34Vx/HqiTlVHZXM9XPJ3Ah/K8m9jZPGk79TbCwmZs1jI5h9OwACpAXIu/X6myJFP5dX2+jZ7j1PyKHYGNQSCneHXIthYQqDxhE/m3Iv45wzh9Ufl5aAacSVwkCVdeYDeoUSA/sv8y71mwL9/xDGitvwUpcB++EctfRukX2SU5Dbo/k/EUawyo7S+hOSC4MygahgcJNKJY0sXrB/SM7/0kaCN0DlN2O1bOcUgsQdxEvyN6FosMnJbi0CxiDTa4n2pHTHiscV0S8kY0WH17OFmJ7I/UvxiShqT+wQkVsEOpXPiZ6fKjjDWyQ7Q0seBs7SliINCarna5JH00+IvsAnL7h5EE+lYJjCSP4heQURppvwO10ghYSxDdroscPaiw3SpRc5Q8MyM9Ib5zx/598RhUJyo7HIJNGLNuM4vm1P7todxS+VsCoqx250RfWiXlRpITyXZfrTkbO1wuZWDSBar9HUkQmxPbn27vOFZJ6OHI+w5gNrCiosg8TlRvEQkCDgvnobGXy+E8a/pQD9XDkfHEVupEGVjKBcBQQsrVRxsv8cV3V5TnXw6m/BluEoAq7tAUb1dILfFR1fILzct03uX3NGM4h9+drw1tFrJ4lqrArmUfhGxtPL3NersXZvY0465OG+/Pd7vBWQFehi9Obg98EInxD46mF/IXqBmtn9dvora3G+/N9PkfYC+jfqP6fKWLIX6bmxPtp3fX3lV/rgxa+Bgbkkw76Pwn156D+lwh4d/TwlJ17vbl6qOcPiy+6+vddb24bzvnHEV3+RRLzkt6Hwgf38yVGj//nnOvNmHSJndPbNOc+87WEH87nPPdot71XG7FPzHsTe5uaJl5rak50u13uUCET3a5Mt4Pg3VBkGOhbnA5oFoclxWIRlmSyQjNbbWab2SwvUgdFqPATGvvQWlO76zDJe6o1YG22Ly/UyRNzSeDlkV8eIYnqkcOd6/HAj8c7Ps1KJjmd6anpqak8+cfbRBjHQyOpjxe3IHXba/+4GoP/48k3z0Pua/yLtvGUC7kAlB0U5FTvYvPQcoqFUMiFBYz3kJ8sD1lASgYsIHVcalIqn8TbeeETSZtdZAv/gquLwtFtZ/+YfKkWj2o7UyHvhZY8X36wl7kIOB86qCGdbqZM/ESQPddNxfd6nGk6pEt30gUAPsPOwwLohs/zo3jygCR/Pmjvgfb9mJoTZejU0eSgGijel5fnc6mmWYjq3S4LXYbVYDZDeWtxEeNbHRarI82b4s0yEX66DexvNdtsGYRIajwzUT8Ld5hZlpaDfJAJBq7/3MTSKu0Tu+fGIyH8TKFOhLQpXq/TgqwgvFUcMvNJdt5uJqWQnZc+lDKzvPC2z2B8MEBermIAQs2zIKKYgbOvAxRgQtb0dKf8KbSJ6hyY8Fse1HHiuX8QfnwuLEBOcw6ifY9oh4H42rQsj9OEUkB+RTks+CDo2gb2Vz4aN5PIUeePe9TfbuBLoHhijAlacSWq7KvVxuvIjynFm5aVrkVOsL/y2lQBOgTq55VBclYM8sB78CECc/PihSluIr4FCcM+8QppIR15MmnwkQDwpgjur7wyA+CHGYbEvmqQ1KJDkF+bLxuAJANJ6Yr7OWgs0uwDS4AFWJHJ60y3Kq9sIvY3Ad+u+kxUJ5yDBuUjOQUmgiZ8bmHJcvFB0xHJPj6SfryggRQrcX+niq9PIv5Hwk/Fp4efIciPHPmwgHiBJGlfcT8qvdtDko/Hk+YZnz7eG+p+JO8BmZXzIG2kBg36iFhidm43p3XuYKd0bkU3qfZA6cQANBNIqYhsAi4kFkEWLdLFI22sFvzP403XMilB7qd7CDRvglVwavcjpyFzKL9tc1xp25YY3L0JfVtY+/ZfqKo91AAWnyoQZPfPVNyQzk3xZEH6dYL9lUGSc23E3ryS8BgbSYBqPGrdpOls3Yzw5U0Iv1a/Ao/77BrlIxcBJ/qy1QkPbJGfKNhEnQVMHm9aCjKB+W0KCbJfkg4xqQCUdjLhLYGgDeg3bVsQvvQzhOc0r8X8Zze+KCrPX0efsRAHIEZQNgKt6P7UDVXyj/EQ9x+TniW5P2O22ZNo9jXzSvbV0A0w+Gs6+CKwX+jcgi2/unWpav/oosoDFsEBtRN8eXTH88iTafbVSpuxUHzQ+kNw/3Ti/1ab1WazpULqs49DdPex2+i7IiwtAviQ7yd8O71mA7Z2tVXVbNecPrjbgrcUZ4r1n0sQPzdeniy7f6ZYCIjxT8KfuD/YHzJQBuy+UH88THXB0n1YrED649HaESdqcstXomc3rhnz8ntrHT9YUfbYTZEGcQ8LGKtMTgb3T5MUIaQfdw7NgF5wP+L+gCf1Bw/yC05D0aN4qQLpv3Wq437F4cfXLT36vXbR2vl5+b58tZ+5fVJOBAvkquTP8niRNp3IT+qvDD7DJlqZESoPQQXcQN/QktlHn1xE2LNPvVIjqRvqDUu8aq7D5XCJj7WmZAs0B/llhR8TMmWB/FYTlB8mk1J7kuxrNpvjoA/8FQjCfv/IE6uA3TL71MtNsz6at3zAiYM1i+L+ShPdXy4l+x8cVx55vBTY1cBum3V87qKw3/+ygvYtoYPg9BBwmckWQtZCvR464e6So09XzD45pwvYhRG9/0bcv597Ad+uS3RYEpEj2WHRwq8QDex56eScnh9+NPdlie1oLjocDl6bnqbO/mIjbhebmGjRJlocyZZEB3LEB0/oBbl/KLGTW3YcdrTuGsL/f+nfTMHZX2gMcXwmGTTgSHbBcSUZhcr/tMIuOpXSuqs2taP0Vlj8rHRvP/cz0SyYiLTxWlCB1qRFiaFRILIbU87vOkXYac37c8PhW7xeaz/zQ+07epDDKrAvpbT4y4B9O62l2BOu/6X0p4P72QYYDGqfpzTv3OnsKPk8rbk4lbCf7Doy/C8RQhsQNPhBfW1qR8lvMlqK7YSd01PFe66cw9mB0zNCa7KKhbETGt9j/ti2dsSBpp8yuHyZ7mEy3r4t5sP6TTHn2nePnNK8I+aO+p7Vhx/79K2Kp7e8cnL25zOr/oUnY+lNW/KSm7ZjZ+POBcD+IuN86UNkPPvKmcc9PWexp+P4xFB2SeHIVGB/Dew7TWs0Xyx+xR5H66ZtMTvqNsRUtu2OfRbYN9X3rDn0aN+/VTy98dWTs3/9XNUbowX25kXJjVuxs37Hi8D+Mut8aTwZ91ypWu7pPoMnVBUnhrKLXtMnNCwHudcwuGG15rw03rZVc7B2Y8yBNv+IaS07Y373zz/Ki6HPNSBmdcWjNxeWP7Me2J9NO/PvOjKe2bixK6lu82ep9f5pRO5J1bvpfHdP1afui6d/PVAd/O5Mo6F+OfNl6xqE61bF7FWxK2s3xBR17B4xpdWvwfUlifMrD7ln7Tua/xLIfXdh2TMr55588b+er17wkydrl2/PbPwQjzu3qSKtZd8TwP4mO3Bkiedi5a6sntPYfeH48YHYnNk1ou5d5leEXbNyxBJpvHWr5kzNBs2mzj0jc9v8DK7dF//XQ6Vjv9lR5v7rmorJXy+smLp07skX8Iyq1/HfVS/FGQ0f4rHH1y1I6yx90tlegtO6ynDmfxzFWd2ncFZzxZKB2KTXvqPpOb+awefej/uBzN6iaar+QLO2a/9IR9suDa7dq8cVJTwuKhuPV5c/8vs3yqe+J7NrgF23Htv8S7yuC6UzIMawC9gZF4/h8RdP4MyTeybdiw1yt7SDj59YPtKjYn989oOY9+9UjdO279L8qXavQWGXPfLb+WXTVlL2mdfxY3Xv4Myq1bfIfc72fXMp+2NgXz6OM9sP9+ZALXovduUizdmzyxh8eKl5tMreV899EPMOvd4V01O3T48PldrxjnLCnnR3fvm0VRJ78rmleHz50hWU3brnDWdHMXYFynF612GceXbvPfVN+v5/1RTte13TFaSLjbFXj62Me4Ncn9msXXBks/4/d22z9K0vSru5eOuEhgVHpq955dg/4u/u/9H1SQfn17h+/hZH5qZ27l+W0rQbO05svZV2eufZnK2r4+/HLn1d81BJoSbos/jDa8Y8umdpgpVcb3pz7IgVhYZxbxZyY18t5O0jEIpd8NFz6+Yde/5jNDrTkPfWTIN0n7N973Zn8942hBZrxq+Yb7wfN9y+8KPnfvFa5fN1oeMpbXuLM1r85/B6RGXpOmgd03XQoo8q+/hzB+Ydff5U6HhSe0nZpPoNDXd3osJPjn9nRGexeQp0a3TZM47NOzKrPHSc7zh49Knq5a1fFaE5F0rHFHaWsNs6S0zTosqunFE17/Cs3aHj9vbSM0+de7f6D0Voesf++E0d+42LO/Ybvh9duac3vFoxc9sAOm9+/NzK0t/uQC917DO8C/0d6JnRZL9ZOf3Cq2Uz1/eX+0DX1NNvF9/ZhZZ3FJtXdRSbfnahxDw5Ut7/AZRanSo/HvMfAAAASG1rQkb63sr+AAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAppDOhAAA3WW1rVFN4nO1923fbRpI+NhPHkSzLdjIn+7AvOue3e/YpGdwJPoqiKCmmJIagbDkvPiBIxNzIskeWlcnw8H//VVU3bs0GCEAkJcWQbIHEtfFV9VeXrgaOX7Zupq/67mTanL3qH0+m2qzT30ss+r+cTKaWHjTtZsOYnXf3gqk6e8MWr4/awVQz7Nnh0QA+mMasv+8G04Y+67tnE9ihdQDnCOhn1ut2b6atHvzZ2x18mirfKkPFV94qLcVTPikTxZ8dnRzD+i1YfwnrD2H9lTJSdpQubL1UxrN++3SIJ909oXPvQqONsTNrtY8mU3PWOobWj2EBq71Zy92nndwO3kTLPaRvrS5bvKTF3gk/wX6HvvcHtG+nRd86fVqcsJVuD/b1Z60B2zhgZx+47CLH7HxscbSLrTzBVqmz9qmGzWmf6nia9qlBiw6s1GGhs4WBi1kBbJ7NYdNXPiifYd1IGSuj2yKkPXyEQu1xAZdAub6N9miBWVl/tFuio61Wf2J0bqk/IkZlNOiOMdriGO0CPleARAv+fga03nGsvuVYxRjmoYPtTsBjmwwf2r4QH8dM4aOm8DH0NEKjW/YxnSGkM4RMhpDJEDJnbu9XJlXXhQ/+EFacsttw3VNaUQbDJxzDAWjXv0DfPsP2RXpm6DJFywdSa3Io9aFfAkrfYVDS+pWBqTeLgbnNwdwDhbuA34nyG8DlKR+Vd8qfHNCNhFK+h88flA+5YGq812pGYdrXVFPebdWcbmupDEniA0Qy8Ar3XN0pjKXh6AxLQxuVxi6/QzdGDDrHZ8iNl+RQqHnmUq9IdsUgqwrQL9BPJ7hXCiDTYgBpQ0G3Ag6RyjDy8zoqqsVi3SJCTOCEbIo4kVKtAKh53Yo6atUO2oetQ+qgl7lqpjWXq2fLNaqr0bNvOEavwRZcS9FpCComOB15fhkemsJHv3N83F6Lsb/bmiP9JxFe78in90FjADlBm/bIbiLl/1mI7jlemu5LERMIv+GXVynTZpB5HDNjZBa2ndU7JVxQtzwGJJqa8kj2iPyvAc2LUkgGZgHLGYSWE/2JgkBG3MaBJJ9k+TgifG7ojSC5Mbck/IDIMr8k/NAvDfHjdHBRBl6yGQvDCVLn0viOuWeCByPATXvZAPcBV308ZpiJRiMfsyoOspwUdSvXQfb18n08co8tnUHHMCyGnTYuTI2R4ulBCrqNCDq0IH+SLa0Sm6VDMwoRKuY+inRk3eFeCgUlBdEydBlaNkPLZmjZrCszAsQPw0Doymhj+oclcHwceSue8vuCHInDUGwyFMlFS6Co3hZFk6HIVEwKo+UxHPUcHC2VpwGaPA/Q5EhyxbO55tlmFphsTQJMcDrLKeUAejqopPJ7mV5cSCuLec9ytaTOjIZ6uHT+IwNDfTgEsp+0NMz2LFbPNnXmd8R/6XRVYkvhTi/NItyDdJVWMV2VjdJzKUptSuwNwQXP79b3FSd9TTgNoB9PIDx5qDgZS8dpI8LpA3gk12tMDOfxWNM3KwZp3CSoDBqVQaMyaFQGjVoQmm2pCvHRmPLqk+Z6df2jL3lkZDGELIaQdSvKPqTo9WZB9HpfRxg4SmCxCSaLwWQxmDwGk8dg8qS+fwfDTRpuGSDXSJTpCtyvHeWYfxorV0VcsTKug2aNiww2qNIgv3SXkzsO3J11GFQOc/7ZWEN2YiQfu0NKML3jiaZ3xFZp7NBrpLQlRkAkTTXlyLK4ac6RlXZG0sWiGXFvpdgVhyrskd0IomxFE/NvvqxHYtY6k7biGL1CMmllepb06+NMEnqqlElyxiXwfJxy8y+XO+ZecCBLMI1r6KYRWIhjEqxvOFinOGTAIdqMwnP0rTDdtsi78qoWJ2A0swgmQwgtw1AoTLWRQs8FllqBdAauJQ+Lg6U7LPahSGeXAjZSPttmkY+JS0onLsTwRQJDzBhhsYdLfqrHvbJ8G+FJE2wFIMVMVs7QjO8xUHUOqq8L+bURzxExw70gwgTVZbA6HFeHAes7DFifA+s7YryOH/rhh1QHZ5uiD2GCqdfnMWm/H6Y43fl0nUwUYZJuLfAbo0LwCym6CP3ccdeMXInJ/T6TYQ8OEmFPS4OWhDRDUYo0GXEEmBS9zxS/KLybEbzXlFPBYpRPRfyfcFA7LLbQvHEBfIv4P5bAGGlmRb51qVQuG1zbLAduCJwWRDkSAlBfzBTfy3PIvByjGP82KvJvITPFU3uBJ9QIcDjNYYp+TRn9hnCKPGHxOM7igRws00UXLAvVZ4C6fU7N/DsCrQVSSg4jnB5AeQmf5tVVU36ErROAfOFYUnqcvHL2OS/2K+EDpBXVqZo4mEfmP2XIgBruwtpr+P4jfEJHHSPDhYMcy8VsuX7TEjELzfweGPkL+B923eSesnCGwpZMF9Mrn1go4WAWNzGFgKJ+ivw3b1vQ88QtLlsWgzQ01wdR4cEEDfZqyof94m56qWozwfvk9OcxID0GpDPnqHPXM1ltlgI0CeBTDuBr8mjGfGiSdFAY0CiWvRHq9qK4ulgCB4cYS2pj5POQy3SrYUkpktGYeTzQRrmK8EP/NGG2VWZNBCUtjnGxQSPdkekpSwAtEeFwzMgIx32t4gDLY0pDNmZEPTur75eDsUxQHnDvMUh7j4FXAMQi4eZtE0ByFdVlCLKoPBkFRYNtYoVkyJd97mF/UN4LfIkm6FIJsCgLzbUMQ80yGYZ2GkOvBIS2VA+lTg0b+q3i1YRamCZMnashLQ1ast5r8+5bHLRQ4dggSX64Ysg6rVAmKbcuanHvmvh2HquhuXjQPIpVhEFzJFjCi30w2AfmXztj7l/jh16IoRuGfv2QI1nSoximYV1gB0MYGaJDMxVch9pXJrrOtdeEoawYQR7/cUwNGaa+Iw9Ymjy71uRFHU2bZYooLAnHyFtxmBKOmQvZ8WwQ42j6nwCjRw5lvnpaVX2fEhaFxXyJ3FuJKiyID6Xqies7fH2HrY+gJGvd4Ma6wTQ0hJQU9JCFhuU6+xvCM9+6pC10obrBMr4k18yhdDBUiKSl1iUrMZFVIZOuwOSqSBFzxlyQM4Dqkgz1Z3LAQ7f8EQfRUNoVytsaRebRFAlpjLSLkwYQVEWSiiiWNtMdSS74kNnoMjhtxjhBHHhAbs4f+YhJDUyxqUchZNpinfOlBkYby8hQWytkW5EH846KpDHtMJ4rvRdhs6uOOWjFRwGjWMWwyxcFho7goqjvMHSdD+dd50XQvYhs7gXN+ig7zCAfIby96lE9eWKcwZN606HyFcnEipZYqnyhR9OPBgvmHZiiHfgU1l9TwmvR9NNldGCreAJWwLC52GiE5amCO72k7hvqINbmTyhXuHIdnKukNKRK2HTSg12B1GrIxw+FAl9fToEyf1BM48TDXNyLSeaxmbrCDTYsZpbtctr6NIqcP1Ao8w6A51Mk8nV2eQVxeXbakU6RwzxIAnG9zAhMltYy/8adDwIXIbidQPBflMTZoUxFaQwpMVhismuEo7k4lK48PoCxcgEjREkIvyEpSKciH1oRfgiHX3rcmcRbZAOyCe9SHDlcJIUfuBRe0YiXT9Wvn2jwARkYc8Q7McGUpxM2laqoYkvZJK3YgjdFqbZ5LinlgRo8R2Tw1DosiUu4eJL2rceFk0pmkrQSQ2VOeqQM/f1Zp9u+mXaS8/0CHjSNYXlNodNvAD1y91XUQQI+y0C2z1mBfbi4OgywDuOODsOps09a3um3aZd+n207RNF13HNaHETtfQFn96PBgRFXjevE+MuniBh9XoCBbfCV36FTh0MJnYNXgMLJHl3/4Ag+H/TwgSgd9rwTlX5miU1auIk/DAW3vcFt6u3Po1U8RbgJfyQSZenAa+pIVyFegkTl+5wV2KeaRA0mUU2rRVpGpI+5SFuUv/gUZTWCaBIVrj2Trq0mKJ8Jyq/lVEZO33I5uYTIJFGRz2RyQii9J6t3lrmlmsRUJjE11aAN3iD+DJ0Ep4cXTm45y9xSrUk6a5JeK1EZJXoS8feEBsrStngjsrMMvXlVSm65FUsbtdzKyG0zZXcxjEimFJK2NNx2lrOtmuRMJjmzllwZyT3lktvnT7z4SNVOSfk95TKS7XG2cI9qsmwyWTZrWZaR5RaXJT6bZKi06Y7fUQYqTDUH0bNL0tvPFmyvJkWN22VctrVEwzttPfXNSH0zU98GQ8Gux46GR3oWT2EO+OBhuP4sY321u7HYzVi1TlZxC3vkUfmJJ9QFPG8arj/LWF9NWg0mrUYtrSrS6hAiowiPUCrx+rOM9dWk5TBpObW0ykgrjHD6ZHPZNHDRU463iJ5yvKWazEZMZqNaZlV62GuqSh3P9bB4/VnG+mrSGjNpjWtpVYlrevFoXuRvbEaWKrntLGdbNckFTHJ4fweUwK4iwOcJAX6GvfpUqv+Gqg1YkUYsRkOKXVNVh3oSO/UnKxZAnmx8AlaQ8aousgQFqATwCwHgBLThuiyIf4obMVR1z8u6/1EAm9Mbm+KRuTAv+UJ3BXWoy/tULUPjaFT6cUD1gnBUAmhb1jZdNfx029SfdCfcqg2bDW2Y3mpG4GhjM4Bvqa12uNH2xr6qpTc2rOwTa2KLRPE9vObflVJscKXALejXYNQaq4Epa02zYdqa0AmMqBMMR44vAOVEW21fH2u29EbGwWjoj+ZFeTdNuCtxfEkOQyWAthP6OiQKu6ZJr6KlkDKAqhoOtl/OAKqqN71GFgOAhbXFY+28Qxs5h2piiwoR2L1u/l0pxDOuEMwzi2zZIuchVmmxZQmLrqr4P7PZMsGt6iJ3DS+W2HygEttr5ZRP2f9tMSnpTdWzmhlk0pDa7luc565ZO+LqmL8XARRSal6rigBU7Dx3BdAmByjxJGbYtoCtwVbbtp1Fd47qqCKhxXRn2yIcCbrDAxtBFt2xC2epGvwryNb3uvl3rQiMrec5OkO3s9vDI9+CfaTIee7aE3fpPQw3gicuVbGm0YSGZ6mYqeNvlooNraEx1DJUzLbndTdWsdEYf6UgoFo7RQOye938u1KDJ5EafORlkfh6nfeLyXLexdCTG+fIJm6w7uV4hqJzArFUkmoyIwW57XpgbV9GWnS/076Z7ncSlQNjUoUjKmrGREwL/t7QE2XCIr1xND/HU65n+z33Ztre28c/L0lF9pWAJm2jehzTo8KOlPasvfcKtv6PMlUatNVWNPhVFV35ET77sAY/4boRPa/FgXUN2KLSr0V7NuCvBlvwGzQ+cdWNuJ3KQPkT1ZNf8T8UNbXnZmLP1zS/6Fp5x/f9Clsk7B0/pXCXvMqL6Mx/g70NxRLa4VKXwe6R344n9KjIC+6SRV2N7/1Y+X+EIv8VjnRpWtmI8pCyI9X4N3XkFj1n8RME7uH71lDGn6B7y9v4NHH3bf7gJo9GcJMYiFd5lsDgmKq9rvkDjSdkN8JracJRbHpSSuekdzeClsvubpvmvv/BwyvU0tHc8RtpdEB+gXDHhySR/DMkJDN3hmeUwsT37ACu0Es80u1F7ZjHMNmLjuBMbBrBhD9Am53la07AY+Ee0vhd8seYgfQiPW8IevsUrjfCmIkeWEFTtGFJ47+ZuhH1h0xtMlJHvAjnjtKD1bCYN0ur5vusS635A3RkqPwfQ4Xv+wiujJmWTwIKTwCFzzROgwiwK15nXmEjapvICeKe/wvS/R1a0aHeO6bA6or34lO40gX0evbos/eg8x8I+StYl5TnGex/wiaG8qs8STDuToJziaIrsHOfKi1+q9m5MDtbNTvX7Fyzc83OK2Tn7zg7u3DucHIr258iaoW92qDm7KKcrdecXXN2zdk1Z6+Qszcij/ozXQ91pWboogxt1wxdM3TN0DVDr4GhE151zdCFGVqrGbpm6Jqha4ZeIUNvcob+lfrtr3CN3yB2rzm6KEebNUfXHF1zdM3Ra/CiExxdM3RhhjZqhq4ZumbomqFvzdCSnvJgK++0e8LOdeVdzc41O9fsLGfnWPuXwc4Pp/LuvrBzXXlXs3PNzjU7r5Kd/yqVd/eFs+vKu5qza86uOXuVnP0wK+/uC0PXlXc1Q9cMXTP0Ohj6YVXe3ReGrivvaoauGbpm6FUy9EOtvLsvHF1X3tUcXXN0zdHr8KIfVuXdfWHouvKuZuiaoWuGvj1Dt2Ev7F8JHhBqO7hEKrCzB7rRVEz4HcFdOkth53zGEvuCLWSEt1NHyzUGmVtfWg/aTF1xUSV3cl/2KNaY45wcHObtiQF3Ya1F20IN2knpUFlte8a1LX7M/tvUXg9R+zxBj4po338ojXuoe2JE+JB0b5vrXtKWit7ot1z7sDYC2P9B1BkbgvyyfVGxcuFLqzLWBW+j9kTX44lqAh/VnuiX5YluxXwKDJ3QoFswNL4XfUK43H+GFlm3Zuiaoe8XQ9e5gi+boZ/GfKqMcjn6War37xBy7FWFF6koziXpTWhb8oif8LcCZw8hhlNha5O4d0ycbVJfDDkbIzsPfgPg6TDuwr0d+B6Axo1g/zRn/xdcqQVoBYQl6/tvAbUr0l9kiz/g+3WENOr/vyPMHtGVd/Bv6qzfKqOC+YRHiidw+lfQRkvYx1/AAGLfXY2GLJJoFS3ZSs0NvV2Ub3FLHZCNRkvegF8T9g/1w4BPQ9KQUcR6DnkBAWkSWvbbRfmWYA/qKH/5XqRMW6ro3mbqbOG28nqnk045oGvQu0nHkG+Qgarr3WI/yazou9yVvbcFDalui/MspbkGLZTrTVoDt6CXjMBP+Ez3s5OQBNO9HwCxq8iz5bJV/iGiV9lWGqBXBti+IWkaY8Am6KSW0kncPoKzqGRPkTOb5E2PyGaK8c1qbOULaMM8Em8J8Q+A32WkQ6IePCO7mESryFGr0Yly8iynK98k52lV0IURyNUCfdC4fH8k+ziCHinGuk5kx1AX0HKO4L9KmrQOXViNbNL4lcN+A+LPkNk+Ve6NyPAmbA8IZeaZaHCflkQC+p1K4Dnsh/3nimLGt5xn34Jeh68hl/eqF8Jx73hfmD/yb6SNVu5V46OHCnsJYfax2xktvoIzDDOZI+t6yaNkV9sivmRcU+RKTxP7F79K9j2NM6PhvHuKjyp+T9lXyrqn/KtsS66ySKueSa9URKNENMIrpvUp786KHbEtbeFird2APZGj3sNfUYO03D2TchEr6JN7zmMrViwl9xbvch28LOPWcuz8BLZ/pvzKTvJct2ZoLWJos2bomqFrhq4Z+otk6Cx+LetD75Gcb0gOy4lox1FEa9y7iHYrliyd7YMyoRjw0+ygB6Ad9AY30/PuHr6s9g1bzOJ1umWxtfhhJugDaeHSz4n5/mWe80moq0s966p8kHndLKfdm4nXRO/w1l1U8kBGFKV73J/AkQyd6tbSWUTUeS81Ik0jgFTZNhb0+zY5vUeZI8Ky7OFjkMFHGmlEifwZ8er82OxGYmQTczWjTOZeVfZOJq9yMn+hHJJ+/0NxaUzvM10beQKzPcthuFHEcPq9Yzj0Jefvu0j27TmNH5XP9W3BERe0bzQWJrRUPga7CccxJrqrzGARXUlr3yO4Gj6faZzgmA6xKBuXZaPkVaKcgOpZVNAYZAyMaAIaH41HyFSKcpBJ7jbKWVXfl+G4CP3nPIsbPilrh3tEu9CKjzjOVGnMCGWgU6/1qIf7sDRpBDsZb1o0SlSs/ughSWIxpmmpfB2NUDKZxN+r9AINtgUUd5hRrB/6lPcp1l8N9jF2+RhvU0UIVjIgs+6EW2+RBUfcDRotNknndTo/6rxJY1MWeT2ILkrHgm1NsoooiYCQ99eC+3eEZHjnYex1JbUiX0EL03bk+8yj/wlLT7lI+UhfoY6tQer50lykCSjP0GbdXhMswMwhHrPhPm2yQwEfkTKpLwakCRpJ3KK6H6zWxJjKoT28OV93VZ7O+8SdpzVBrNtpCnL8e+ax2dVB69GEPGnma8KW8quCT7p5vwQtaPL5OCh3O/JtdeJhnKszJC1APbEpLhqSBgQUKenkDaM1XE/O9d/8rstywQvpkXfJA9kSzJf8RrT3DknzqlLdddr26l+c7ZWhmI/7c+UAzvCZIqUJMcYyLHFytEONRjuMeyeFv1MONHn3yX4U2tnPUV3Ud9DGnyjCyf611+LfLpJaWuqPecXaFdWsX0Zsm1p7q56nUVVsQJluVgXSJPZtzMk8rFL7a/W8bCyLSOIZnPGSKtLZlp2oPq9qH0wzoXmPmfB7snHxvb+lEYlPNC93GT0w7/wyG2sJdvKHnOOvC84lWFXFdL7WFNE8cW15TfNJfzDPg5WqLOvD5jXNZ30af8GeLyIo5ntOaIwCM4ch6+6Sj7kTb6ncy8eEokp1vkPKs/mEr0E+rs9tLv61aC5XWDmMnu2Y/GOMhdfj43p0128B5/Cui/W/76VHfuZLce5CVqZ2VayfJUux77F5h+kK8fC5F4fU4g8PYJ5huv65fuNUXgW9OP+snmn415xpaM/VkCyeaagJRyyaaYjRe/X5DY7QD+9yruE6ZlB8nTFrQs7C4fPhTuj86NtVGV+vmfi+MvH8rMiai/+6XFx+1vd6ufg+zftex5zKLC7+BmR4Qb7/CJANZyIl11XJeQQ0fsPySz7P8zcTVdZsBrcKnHu3M7hXNRMpiV9aC5Af/5TES8g8TYoCsVajKWiaH50v+8gmjaIaBeT7gnoFu4Mr4iHkkJ1bSn1EYzoNsqtNkrpNo7rNlNSHNMbXTEkd/we073pG+1ZVAbQY1bvVBbTtH6n9bHQ4rIoL56r2iAOvyba8U9hT1tBC39B9JO++in5gznNMnkVAPhZmpsd0RKgfHrECzuW3qTZOJb+McUgTtuD44fIqH78mefkJq7WecdlyOOPPsQsinPV2BzfT1l53Mg34z6zDvqn0M+v0Ikl/S7m/t/GTHCIfO5jzsc8yt/Tbp8MpnHfQmuBiv0ML93gy1eHbYDLVZp1+m3bp99m2Q7Y4x8VscN66mbILPwLzg/BMlEu4nZc309c92MdRZ4d8OXB/hfPBXQyO4C4GR+3JtBGMzEBFAAbnneWcaLZ/3ruZdo4HeAt73T4uel26k94ugdw9wab3cBOepDfg3wEJbbbb67KFize9u7tH33bbtHDhNGPYs40HHOBJ1dnPvV8mUwuXLvt6yhY9PP6gc4SLn13cx4PlPvs6wNP97LYI2G6PED3Bxh24XVzXdc9w0WaLrksS2HOP8bD9PRdv5uSNi9+6Ln07HBzjSQ4HjATaRE+okn/QkoqzZucdauL5MbV/0KfTwZG4OG/v0sk753ACZXZybN5M4c9kas9oEbCFxhaqsIBlB/cH9bFmtAA63j/Zw+Vgt0uX672mi2NDYePxCRxwfNKmq816BydkyHqKRy7kDtD50TFB2DvqsgXu+t8ULCBNWGRQWHE13qFJLohH334katS4WUJKQbNlQDcfkjNtQpjedY8B7qMug/sNYN/dfQOd7+UBrjjrkxZ0eb95DYcPqQd70PcRym6XburYpf2O9+g07SMSyV4XO+k+nnLvJa7f7+K1ZrNXR3DPr9hOs9nc9VR+PYww2dNScfYBy9mOUldUC13x6PggWnF+2qE6fbZIV+2z7uXw7hWw7uWke5c90rwZ/zw0jGb42TNVOOB4L2QsINfBLt1s4dt9HMNKcpfcqFYS2tlBv30zPTg9x9s7OH1DCxe+GTYs37AlI1abfuCINhi7gzZd86D9knEu/odvh9gx26/wQqcuEeWpu0tq32vvwWX70K2as1f9Y0aXe4lF/xcgF0sPmnazYYi4H7WDqQZNOTwawAfTmPX33WDa0Gd99wxP3zqIce3hnbeS9D8k+m9RWAZGLhryGEaJ7itKl6PZgbAqpPrWLtFdaxcabYydWat9NJmas9bxMXJb6xhWe7OWu087udSTW4zzW60uW7ykxd4JPwEzGq0+cWyrQwC1OmQtWidspQsUZ/izFjMzrQE7+8BlFzlm52OLo11s5Qm2CvT7VMPmtE91PE371KBFR0NSb3d0tjBwMSuAzbM5bPph91JwqtctEdIePkKh9oTh+C20RwvMyvqj3RIdbbX6E6NzS/0RMSqjQXeM0RbHaBfwYY+kv6KBwnfRoz0ZVjGGeehguxPw2CbDh7YvxMcxU/ioKXwMPY3Q6JZ9TGcI6QwhkyFkMoTMmdv7lUnVRRvqD2HFKbsN1z2lFWUwfMIxxPDjXwqbcLJIzwxdpmj5QGpNDqU+9EtA6TsMSlq/MjD1ZjEwtzmYexT3srlJGEXgHIQ/o9goVkqWYf2QC6bGe61mFKZ9TTXl3VbN6baWypAkPkAkA69wz9Wdwlgajs6wNLRRaezyO3RjxKBzfIbceEkOhZpnLvWKZFcMsqoA/QL9lCVdkgCZFgNIGwq6FXCIVIaRn9dRUS0W6xYRYgInZFPEiZRqBUDN61bUUat20D6ld7CDXuaqmdZcrp4t16iuRs++4RhhUHotRachqJjgdOT5ZXhoCh/9zvFxey3G/m5rjvSfRHixsjCfkpIXgjbtRdmQPwvRPcdL030pYgLhN/zyKmXaDDKPY2aMzMK2s3qnhAvqlseARFNTHskeGwTEgcFSSAZmAcsZhJYT/YmCQEbcxoEkn2T5OCJ8buiNILkxtyT8gMgyvyT80C8N8eN0cFEGXrIZC8MJUufS+I65Z4IHI8BNe9kA9wFXfTxmmIlGIx+zKg6ynBR1K9dB9vXyfTxyjy2dQccwLIadNi5MjZHi6UEKuo0IuivKr32ICpHLxWbp0IxChIq5jyIdWXe4l0JBSUG0DF2Gls3QshlaNuvKjADxwzAQujLamP5hCRwfR96Kp/y+IEfiMBSbDEVy0RIoqrdF0WQoMhWTwmh5DEc9B0dL5WmAJs8DNDmSXPFsrnm2mQUmW5MAE5zOcko5oGc7+crvZXpxIa0s5j3L1ZI6Mxrq4dL5jwwM9eEQyH7S0jDbs1g929SZ3xH/pdNViS2FO700i3AP0lVaxXRVNkrPpSi1KbHHRpgfIk76mnAa0Gj5xweLk7F0nDYinD5Q5dj6EsN5PNb0zYpBGjcJKoNGZdCoDBqVQaMWhGZbqkJ8NKa8+qS5Xl3/6EseGVkMIYshZN2Ksg/5GH5+9HpfRxg4SmCxCSaLwWQxmDwGk8dg8qS+f4fqUHG4ZYBcI1GmK3C/2Iw8/ITj8wVcsTKug2aNiww2qNIgv3SXkzsO3J11GFQOc/7ZWEN2YiQfu0Nemx0/yUvEDr1GSltiBETSVFOOLIub5hxZaWckXSyaEfdWil1xqMIeGZexZyuamH/zZT0Ss9aZtBXH6BWSSSvTs6RfH2eS0FOlTJIzLoHn45Sbf7ncMfeCA1mCaVxDN43AQhyTYH3DwTrFIYPoGXFheH7FK9sXeVde1eIEjGYWwWQIoWUYCoWpNlLoucBSK5DOwLXkYXGwdIfFPhTp7FLARspn2yzyMXFJ6cSFGL5IYPiBKlJ3qDL2gir453kuH1HfKQwpZrJyhmZ8j4Gqc1B9XcivjXiOiBnuBREmqC6D1eG4OgxY32HA+hxY3xHjdfzQDz+kOjjbFH0IE0y9Po9JsQyUpTjd+XSdTBRhkm4t8BujQvALKboI/dxx14xcicn9PpNhDw4SYU9Lg5aENENRijQZcQSYFL3PFL8ovJsRvGyOziVVoRfwf8JB7bDYQvPGBfAt4v9YAmOkmRX51qVSuWxwbbMcuCFwWhDlSAhAfTFTfC/PIfNyjGL826jIv4XMFE/tBZ5QI8DhNIcp+jVl9BvCKfKExeM4iwdysEwXXbAsVJ8B6vY5NfPvCLQWSCk5jHB6fJLEvLriFM0erP/X4rGk9Dh55exzXuxXwgdIK6pTNXEwj8x/ypChB2le0vSXH+mRmhOKDBcOciwXs+X6TUvELDTz8YOgWddN7ikLZyhsyXQxvfKJhRIOZnETUwgo6qfIf/O2BT1P3OKyZTFIQ3N9EBUe0GyY1ZQP+8Xd9FLVZoL3yenPY0B6DEhnzlHnrmey2iwFaBLApxzA12xKKB+aTD5a/NsoyC6SvRHq9qK4ulgCB4cYS2pj5POQy3SrYUkpktGYeTzQRrmK8EP/NGG2VWZNBCUtjnGxQSPdkekpSwAtEeFwzMgIx32t4gDLY0pDNmZEPTur75eDsUxQHnDvMUh7j4FXAMQi4eZtE0ByFdVlCLKoPBkFRYNtYoVkyJd97mF/UN4LfMnmyeOsPgxwJjIMNctkGNppDL0SENpSPZQ6NWzot4pXE2phmjB1roa0NGjJeq/Nu29x0EKFY4Mk+eGKIeu0Qpmk3Lqoxb1r4tt5rIbm4kHzKFYRBs2RYAkv9sFgH5h/7Yy5f40feiGGbhj69UOOZEmPYpiGdYEdeuSoBNGhmQquQ+0rE13n2mvCUFaMII//OKaGDFPfkQcsTZ5da/KijqbNMkUUloRj5K04TAnHzIXseDaIcTTNnuxLz7HIVU+rqu9TwqKwmC+ReytRhQXxoVQ9cX2Hr++w9RGUZK0b3Fg3mIaGkJKCHrLQsFxnf0N45luXtIUuVDdYxpfkmjmUDoYKkbTUumQlJrIqZNIVmFwVKWLOmAuCj5m8VOLXi4Ru+SMOoqG0K5S3NYrMoykS0hhpFycNIKiKJBVRLG2mO5Jc8CGz0WVw2oxxSrzSOxcxqYEpNvUohExbrHO+1MBoYxkZamuFbCvyYN6xpxjQkxDE0nsRNrvqmINWfBQwilUMu3xRYOgILor6DkPX+XDedV4E3YvI5l7QrI+ywwzyEcLbqx7VkyfGGTypNx0qX5FMrGiJpcoXejT9aLBg3oEp2oHZs7wuCkw/XUYHtoonYAUMm4uNRlieKrjTS+q+oQ722CMzaMR1xTo4V0lpSJWw6aQHuwKp1ZCPHwoFvr6cAmX+oJjGiYe5uBeTzGMzdYUbbFjMLNvltPVpFDl/oFDmHb0779PCjJm2vIK4PDvtSKfIYR4kgbheZgQmS2uZf+POB4GLENxOIPgvSuLsUKaiNIaUGCwx2TXC0VwcSlceH8BYuYARoiSE35AUpFORD60IP4TDLz3uTOItsgHZhHcpjhwuksIPXArs2Uo+Vb9+4m/xmijsKfsRwZSnEzaVqqhiS9kkrdiCN0WptnkuKeWBGjxHZPDUOiyJS7h4kvatx4WTSmaStBJDZU56pAz9/Vmn276ZJh7itM0f4tTnD4NiryL6TNwdv7Ip4LMMZPucFdin2kOePPaQJ1gcRO19AWf3o8GBEVeN68T4y6eIGH1egHHBH2bzOhpK6By8AhTw6UBw/YMj+HzQwweidNjzTvizrhKbtHBT+JAZ+P4Gt6m3P49W8RThJvyRSDR8bCZ7MTbHS5CofJ+zAvtUk6jBJKpptUjLiPQxF2mL8hefoqxGEE2iwrVn0rXVBOUzQfm1nMrI6X48Ea+TTImxBvFn6CQ4PbxwcstZ5pZqTdJZk/Raicoo0ZOIvyc0UJa2xRuRnY1ewCjILbnlVixt1HIrI7fNlN3FMCKZUkja0nDbWc62apIzmeTMWnJlJPeUS26fP/HiI1U7JeX3lMtItsfZwj2qybLJZNmsZVlGlltcluyBmW264+QLuLe4nOa3ny3YXk2KGrfLuGxryQfptvXUNyP1zUx9GwwFux47Guz17/EU5oAPHobrzzLWV7sbi92MVetkFbewRx6Vn3hCXcDzpuH6s4z11aTVYNJq1NKqIq0Oe3VEhEcolXj9Wcb6atJymLScWlplpBVGOH2yuWwauOgpx1tETzneUk1mIyazUS2zKj3stcKeCy72sHj9Wcb6atIaM2mNa2lViWt68Whe5G9sRpYque0sZ1s1yQVMcnh/B5TAriLA5wkBfoa9+lSq/4aqDViRRixGQ4pdU1WHehI79ScrFkCebHwCVpDxqi6yBAWoBPALAeAEtOG6LIh/ihsxVHXPy7r/UQCb0xub4pG5MC/5QncFdajL+1QtQ+NoVPpxQPWC+GbaGGhb1jZdNfx029SfdCfcqg2bDW2Y3mpG4GhjM4Bvqa12uNH2xr6qpTc2rOwTa2KLRPE9vObflVJscKXYo9e9fKSoNVYDU9aaZsO0NaETGFEnGI4cXwDKibbavj7WbOmNjIPR0B/Ni/JumnBX4viSHIZKAG0n9HVIFHZNk15FSyFlAFU1HGy/nAFUVW96jSwGAAtri8faeYc2cg7VxBYVIrB73fy7UohnXCGYZxbZskXOQ6zSYssSFp2/SyWr2TLBreoidw0vlth8oBLba+WUT9n/bTEp6U3Vs5oZZNKQ2u5bnOeuWTvi6pi/FwEUUmpeq4oAVOw8dwXQJgco8SRm2LaArdlrjrLozlEdVSS0mO5sW4QjQXd4YCPIojt24SxVg38F2fpeN/+uFYGx9TxHZ+h2dnt45FuwjxQ5z1174i69h+FG8MSlKtY0mtDwLBUzdfzNUrGhNTSGWoaKhe8Xk6vYaIy/UhBQrZ2iAdm9bv5dqcGTSA0+8rJIfL3O+8VkOe9i6MmNc2QTN1j3cjxD0TmBWCpJNZmRgtx2PbC2LyMtut9p30wTL7t9SmJ9qxxRUXPqte9Rkd44mp/jKdcVXmnboK02vb4WX1WLrzzGl/7q0Stt8XktDqxr0Ctt8deiPRvwV4Mt+G0mvBY4fr36QPkT1TPzpeXxnuJLxedfZruZeEph9Gp1vvffYG9DeA35BnSNC8pWjBe04wk9KvKCu2Tiq+gfp19oLxzp0rSyEeUhZUdmvcB+i56ziC9Qlr8cXmzj08Tdy18J/zeSoSq8XjjG4Jiqva75A40nZDfkr65/xqcnpXROencjekvp/N1t09z3P3h4hVo6mjt+I40OyC8Q7viQJJJ/hoRk5s7wjFKY+J4dwBV6iUe6vagd8xhWf0XzUwG/S/4Ys0+JlzY3BL19Ctcb0Xti8YEVNEWbXrJ8oYwydSPqD5naZKSOeBHOHaUHq2Exb5ZWzfdZl1rzB+jIUPk/hkr0uvKAMi2fBBSeAAqfaZwGEWBXvM68wkbUNpETxD1X83rrJwnG3Ulw7iz1KvLF7LzB2flX6re/wjV+qxm6MEMbNUPXDF0zdM3QK2TozXmGVvSaowtztFlzdM3RNUfXHL1Cjg5zHH2qV6496OLsbNXsXLNzzc41O68hx9GHe8froa7UDF2Uoe2aoWuGrhm6ZugVMvR3nKFdOHf4EC+2P1UOKOwVjjVnF+VsvebsmrNrzq45ew1edYKza4YuzNBazdA1Q9cMXTP0rRla0lMebOWddk/Yua68q9m5ZueaneXsHGv/bdj5YVbe3ReGrivvaoauGbpm6FUy9EOtvLsvHF1X3tUcXXN0zdGr5OiHV3l3X9i5rryr2blm55qd15HjeFiVd/eFoevKu5qha4auGXqVDP1Xqby7L5xdV97VnF1zds3Z6/CqH1bl3X1h6LryrmbomqFrhr49Q7dhL+xfCR6InjPMGDp+8Pnb1F7l2doDXWkqJvyO4K6dpbB1PoOJfcMTfNvt1NFZGtRYWn/aTF1vUV13cl/2YNaY8cRxyeS+89bFAPSsteheqE87KV0pq3vhiAhngwepa7aQ+yqia+g16PdQ25wHrG3bXNuStlT0Rr/l+oY5A2D/B1FnbAjyy/ZFxYj+S6sy1gVvo/ZE1+OJagIf1Z7ol+WJbsV8Cgyd0KBbMDS+F31CuNx/hhZZt2bomqHvF0PXuYIvm6GfxnyqjHI5+lmq9+8QcuxVhReJuG0zNesv3FaeqXV6k5gD+gLxEOg6srIOv2rE1LgO98EWhbrsELcHFPMhX89KspBZkRnuqjfZQm+qrul5emiuQQ/lelNFA7dSZ7pdvsriXkBA9h+9hAb8mrB/dS0sm6+yBFtT56uW76HKtKWK7j0jZrmgHpQ6m/IT/lbQwKHSBC3w4S96nmPyWE3yREINxMwW6l9A7Mh0BvdGvkS9HcH+aQ38L7hSC7AKCEnGb28BsyviG/SV/oDv1xHOqCn/jhB7RFfewb+ps36rjApmtB4pniDvr6CNlrCPv8D/Ebl2NfqxSKJpLdkCTR6Bn/CZcNhJ9O7wRUK7ZFE+wLZuxO6fKugFMosJ2wOKVjRiJg3u3pyLZByuFx4xGHLXCP6jB9pci148h/0+IBOQP/qWW5m3IP/wFcdyNnkhHPeORwTzR/4N7ssS9Ee8anz0UGEvOMs+djujxVdwhmFGa59lXi95lOxqWyCPMelLsSs9Texf/CrZ9zTO9LTz7ik+qvg9ZV8p657yr7ItucoirXomvVIRjRLRCK+Y1qe8Oyt2xLa0hYu1dgP2ROZ6D39FDdJy90zKRawrSO45j61YDZHcW7xLbQ1sncWv5Vj6B2DBqyj/wGME5R+iF17ZphvAwsi+Q/IVmS/ZBIlqKa8St6MmqGT3kbublPMYkW0Xs1Cr4e4X0IZ5JN4S5tgXLqNYZL6f+QJaRY5ajVaUk2c5XdlMvC5wh7f7opI9R7ma5F34FOV6lHVsCpEG6omXykxSJojGOMeCTtzG73+UmRmUxbmPQTofKeOEsvozYqn5HN1GIsOF2jDK5MFVxZkyeZWT+QasQTtwQ5q0HBYYRyxg3DsW2IqZn872QZlQv/k0O+gBaAe9wc30vLuHr2F8wxazeJ1uWWwtfpgJ9oKs1NLPiZmsZZ7zSWjLlnrW1ei3TDfLafcL5ZDu9R8Q7WDm8jNdGXUG2XI52j6KtF2/d9qOccf8fRexXs8pTixvK7fgiAvaN4p5hZbKM82bcBzTyruyrEV0pZz2fZOctVHJjupkRzWuOz9S5m6k2JK4OMyW3E1cvBqJpPEra9cSPnPlnp7OTOhRZsKqMxN1ZqLOTNSZiS8yMyHj1jQ7P4Kz47zFccTHz3mEGs5h3OGZjV04x0fMRFcaV20Cv+rkUXnEzj4sTRpFSLKzRWNYxSpgHpJ9XIzpIqlsUosu+Wg5q12oksEPqMpIJXkEhHpA4zbx2KJKksC4/m7t5KoicRmOafS/jkYRGfbx9yp4a7AtII40oxGTMPK+T37JavCOscvHeCP8Di07prNXqbRLo61/cWjLUMzH/blyAGf4TFHjhEZId6JzLMcXVyNf3Lh3Uvg7Wejk3Yd2+oo8AY9Glz9HtTrfQRt/IvbM/rXXYk8WSS1f6tsktzBivr3ELZCYQ5bahnu1yaoEPP41qQcGlFvWqKrAouoCrIhF78mhPTxFzCOvKs/yPnHnSWnPVwc0BU/075nHZtcgrCMfki/NRZrQ51qOuZ5l9H1kYIMqaRgD63R+hzQBR5ss0gTs4QHpwoh8EZPYIKDe769FE76L+jfeeVoTxLzWV9DCtCS/zzz6n7D0lIvUmMRXyHNr0YQ8aeZrwpbyq4Iz4t8vQQuafP4bMoAd5Vh1ssM4N25IfICMYdPo05C4IKDxKJ2ysuj5rycb829+12U14IX0yLuUfrYE05J/zCuKrqhm/TKa+5heW17qPllzjCawjpbFFmxOw3xs0fgLel8igkVQ30qvvZX3q1E1XkC5EJaFblIvbMz5XWHd6F8L/2wsi0jiGZzxkuYBsC07Ud12VRZMRyPmPY5Gvieui+/9LeWsPtHc+2V4wXnnl3GtJfDlDznHXxecwbGqSs18rRHzOic0kotjamH/3yWrtxNvqaxvY9IcrBtHC+tTlk0lX+tH2jLk1RwG4KuRx87mNqCtHZPFRp9sPVbXo7t+C9iFd11ME76XHvmZL8Xq3awxzFXxT5Ys01rwjbJHrfwM+36KRiCT66pwTUCRFON1n/tZzURlLqvYVkHWd1uxvaoRyCR+6ew+9sY/JdqBtUVN0nnMgTaFTL8fnS/7yCbFLkYB+b6gWRPsDq7IOqFV2Lml1EfkUzfIx2qS1G2KpZopqQ8p2m6mpI7/A9p3PXH3qioBFqN6t7qAM04+UvtZTBbWfoU1nz2yWjgawOzXR6ohndBIwU7q7qvoh0MZFo9mbljkfTcpzoo9EI9YAWcX2VQjo5I9UPnsD4/it+XV931N8vIT/L6eDEk5nEVvkc0ST8+5CZ8jd0JtQG+kSv3lumeGp2dD1e8IzJ6VOT9/qJ4d/ledHS7OZy0yO1wTjlg0OxxzQMuaNXu388PXMT/y64y5uHIuDp/adUh3/qHm4b8QD4v6VrPwX5eFxUqx+8bCzj1i4XU8HSHFwrPe7uBm2trrTqYB/5l12DeVfmadXsTT31LW8208i5kz9aPEmrO5Nf326XAK5xm0JrjY79DCPZ5Mdfg2mEy1Waffpl36fbbtkC3OcTEbnLdupuGFDugmJ8rl7Nh9eTN93YN9HHV2yJcD91c4H7R6cAStHhy1J9NGMDIDFW91cN5Zzolm++e9m2nneIC3sNft46LXpTvp7cLu8OUEm97DTXiS3oB/ByS02W6vyxYu3vTu7h59223TwoXTjGHPNh5wgCdVZz/3fplMLVy67OspW/Tw+IPOES5+dnEfD5b77OsAT/ez2yJguz1C9AQbd+B2cV3XPcNFmy26Lklgzz3Gw/b3XLyZkzcufuu69O1wcIwnORywALhNnRcV6w9aUhHc7LxDTTw/pvYP+nQ6OBIX5+1dOnnnHE6gzE6OzZsp/JlM7RktArbQ2EIVFrDs4P6gPtaMFkAT+yd7uBzsdulyvdd0cWwobDw+gQOOT9p0tVnv4ISSOD3FI/rZAZo5OiYIe0ddtsBd/5sGLXFCCabQ2PQSh4YvR5R+c6gM5kcKog1e/BJQ8nVEibkx/QUyBtyOAe7uGwC9u/sGetnLA7zMWZ/hzwsAWwp7HC6WnbI06WjW7dL9HDNJHe/Ron1E0tjrYn/cx5PuvcT1+124zNHxQbTi/LRDE4jYIj2diOm5w/U8YHrupNXcHmnejH8eGkYz/OyZKhxwvBdSBXDVYBfuEH9eHQHWr1gLZ7PM230Mdt4DhmJPyNBkN6rl3+jia2n8WhvAlmNgz534mqnraYWAnbve7KDfvpkenJ4jnAenb2jhwjfDhuUbtmQMatMPHNEGz/WgTRc7aL9k5Ir/4dsh9sj2K7zQqUsMeerukr7/fxr1pSsbGiLpAAAAvm1rQlN4nF1Oyw6CMBDszd/wEwCD4BHKw4atGqgRvIGxCVdNmpjN/rstIAfnMpOZnc3IKjVY1HxEn1rgGj3qZrqJTGMQ7ukolEY/CqjOG42Om+toD9LStvQCgg4MQtIZTKtysPG1Bkdwkm9kGwasZx/2ZC+2ZT7JZgo52BLPXZNXzshBGhSyXI32XEybZvpbeGntbM+joxP9g1RzHzH2SAn7UYlsxEgfgtinRYfR0P90H+z2qw7jkChTiUFa8AWnpl9ZIO0EWAAACrVta0JU+s7K/gB/V7oAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHic7Z2Nkds4DEZTSBpJISkkjaSQFJJGUkhukJt38+4LSMlZrx3beDOe1eqHpAgSogCQ+vlzGIZhGIZhGIZhGIZheEm+f//+2+/Hjx//HbsnVY57l+HZ+fDhw2+/r1+//qr32r5n/Vc5qgzD+4G8z+L28Jb+ubu2jtVvJ3+uR1cNez5+/NjW1Ur+7v9sf/r06dffb9++/fzy5ct/+qL2F7Wv8ikqL87lGOeRTv1crtrPsdpv+ZN2nVtpWl/VsWHPSs6d/i86+X/+/PnXNvVP/y25lAyQOTJiP+dU/sgUmdf+bBf0a84lP7cT2gLlG/bs5F8y8viv6OTPMeRCf7UMkXO1FfdZ5Mc14D6+OoY+AMpjPTHs2cn/rP5P+XfvDOh55F5/qy0g19q2LP3MWMnfegDo+5WedcPQc035I9eSVV3rPkhf95jAefhZksd2uiHbifWM5V9txGkM/1J14v5ztB9dzVicbR+nX2f7KVlZ3ikP+m3mXdd5LJeyrG3aIHqGMcnqmmEYhmEYhmF4RRjH35NHsNen//NvL+9Z8t36Hlzqa7o29a54hMvo7WoHz+ZnSJ3wlva+u5b38538z9jxj3yGeZ73db7ELr2V/P+G/vMWXP70s2HPw6aOTSb9d+nbwxfka+kjnc+Q+iQ/zl35A03nb6SMXI/9yL4s2y/t39qll/K3H+JR20DK3342H3M/KX2Jziy5IBtsvuznnPQL2GdYICPsdgXnUee0D5P2Z7cd2gz3Qp6ZFvLu7NmZXsrfdfSo44Gu/wN1aL3gvm0/jn17XYzQLn7IfdB2X/f/SjvreOdvzGdK9uv0WV2S3rPrf0C26QMu7KspmeFvcX9Dlvy/kz993z5Ax/tYn8DO35jyJy38AOTTyf8ovVeRP8/2+puysbyL9MXbF+f63ukG9InbCbrFuhh2/saUv8/r5E+cypn0Uv6c1/nD/nbsW0s/W0F9pT8t/Xf27eW11G3R1ZH9fTxHyGPlS4SVvzF9iLyndeXxeOZMet6mHh5V/sMwDMMwDMNQY1vsm/w8Pr9nXD32gBljvx+2ffGzTb6LC70Vf8P8w2dnZ9Pq/ODWCegOx4Tn3MD0LUJe6/NrX2c/zPKgr0Y/nKOzqyD/ld3XdjB8fNiO0BvYfz3Hp0i/UMbu22fnc+y34y/HaB/YkfFJDcd0/dx+F9d7kfLn+m5ep32Btu9a5vgPunlEnuuX88/st/M16Ijp/+dYyX+l/1d28PSlp08dGyntIvuxYzDOHMt2WeCT2MULDP/nWvLvfH7guV8lL88FLM70f3BcgMvJuXnOsOda8i/Qyek7L3iGF9bhznP1/F/pBrc5P/8dq1DM3K813btc7Vu943l83tkCGMPn9cSNOJ3Uz934n2cA5Pu/y8qxTHvkPwzDMAzDMAznGF/gazO+wOeGPrSS4/gCnxvb3MYX+HrkGqvJ+AJfg538xxf4/FxT/uMLfDyuKf9ifIGPxcrnN77AYRiGYRiGYXhuLrWVdOuGHGF/Ej9sxPdeQ+OV3xF2a62s2L0jruD93H5l+5DuKf+0MzwzXtcH2xu2ucJr8KxkbPljf8Emt2pLK5uc5W9/ImXy+jwu48qeYJvB6l4oM3rM8s/26HUKn8GmbNsrNrv633a07ps8mYbXEMOvhw2+azdd/y9s02MbW2D9T9r2+dBufb3X5/KahKvvC5FHyt/rjrEGmtfEenSQEbhedt/kMil/PztXbcZy9TWd/B1v5GP2H7Of/kl67D/6vpiPkU/u93p494x7uSbYxyH7hWW5ei7+qfy7/Z380xfUxSLRr9HtpH/0DbndMfwU1vPkwfFHZ9f/7Xsr0o8Dt5J/1x5s+3c8Af09fUfdvezaRsaokF76KR/1nYG27HpJHXDkR7+V/Auv40vsAKzWnM57zXvZyd9lyO8L+5pHlX+RMTLpx9utr89xr6eZaXVtZheXkz6/Lr/V/t19rK7N6/Kcrn6eYew/DMMwDMMwDLCaW3W0v5sr8Df4U3ZxrMPv7ObWrfZ5zoXnCh29P96CkX+PfRi2oeWcGlj553ftxbaR2nbMP9/lsN+p8PdE8P+Bj/la25PwLXEvlj/fs/E9v+o8EcvMfraMm4cj/d/Z5q3/2ea7PrbT2UZr/4zbInH++HqwAXKtv1Hobwk5xsRypiz4iO6tp27NWVs7HO2nb+Y6ASl/QA+4LWDXpy3YN4v8KHvOG7Hfr5tT0u2n3fq7QK/CteXf9Z9L5O85H+ju/Nagv8m4k38+DzqfbsEz6RXnCl9b/18qf+ttdLBjbezDQz7kcaT/U/60jUyT+BDHCDyyP+cSPG6ij9GvbiH/wj499+fdPPK8Nsd/O/njx6v0c/z36P7cYRiGYRiGYRiGe+B4y4yZXMV/3ord++pwHXjntj8w14u8FyP/NZ7f4Ph65sfRj5mDY79dprOyoXgOXvrqbIfyvKCVD9DHKBPXZvmx/zp+H5+my9PZo14BbKBpD8Vu5zUaOa+zqReeV8fPfrdcOxTbP3b+bo6X7bv255I2Zcxypd/R/b/zVWJTfnb5p/6jXrn3VQxPN08o6Xw7K/lTz+lH9Pw0fD/YZu0ftP/Q97YqP8dyjpf3V37PMs9vxU7+ltmfyn+l/1P+Of/XfmSOYavnmOfy7taH3MnfbRRIizb27G3AWP9b/91K/oX9kH7Ocy7jEtoDeZzR/5BtgzTZtk/c7e8VfEIe/61k/J7y9/gv5/jZB5j+wWI1/tvJv8h5/t3471XkPwzDMAzDMAzDMAzDMAzDMAzDMAzDMLwuxFAWl34PBB/+KtbOMUBHXOKfv+TcS8rw3hDfcktY/5i1czJ/4rEo36Xy57qOSuvstxa6OJSOjCc+4pJYQOKWvA7OUaz7Uf0aYqPg2nH0jp3yd3iJC+xi9ymTv+vuuF/KS3yVj5F2zhcg3twx547VTbw2EGsIZZ9lLTLHm+/6NfmfOZfzHT9LXo5FuqR+iTnyz7FR77GuWa7XRrk4lut/EQ9OP+V+Ozo9SjyX79vf/qEt7HQA8brEknlOQd4bx+lnu/5D/o4JXOH7Tv3iWMpL6pdzKSfpXkv/Z1x+4ucyfZs27X3Us7+34e8puR7cbl1Pu/ty3h1eG8z3s2qHfoYit+57H3DmueL5Mjl3gDaUHNUv0C4cn3otdu06+yv9x/+j87JNe95Xlx79j/tKWbmvWvetyuq1omAlt4wN7dKkbDmPhbwS55XtnraZHNWvzyNPz1V6K+jBVf8/O+79E/lzjufcZJp+Hnbx4E63m4dEnec3Ki5Z56sbK3Y603llO/T4OMt9pn7p/918hbeyK8OR3oVO/jl/o+DdwH2Ve0LGniN0Bq/pmNd47pDj1a1zj1jJv2uvjFOsH1btm/wv1ee7dUo9b+oMR/2/8DyL1btMJ/+jsvNMrPI6D+REXbI23GqsZp2Z8mdMmOsEep0vryvYvVt7jpnfHbpy8N1D9E2uWddxpn7h6Fu7HHuPeYu8o67yzXkaCWMFyHpBv6fe9Lv0kd470+5374SrsYDHOZesE3rJc3pXv5T7SK6c8+zzVodheDP/AKCC+iDgvyWjAAAu1G1rQlT6zsr+AH9tGgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAeJztnQdcU1f7xy/bAe49WLJHCHvUUWe31tZRZx21VmtbZ61bEQVFVLYs2XslzOwdwhQQSMJSFHF0vLXt2/atRc//nJuEVVBQMNp//Hx+n2Byk3vu/Z7nOc/znHPvxQAA2Gso8+x8NTqrmnBV0DhnhqBxobLbo9LL0/SUFIxHKZ0O2GIi4EodijkSR1eu1E3Z7VJp6KUe8i0Wkc/TA6w6G8BtcAEciT1gia1bmWKrWVypu7Lbp9LQyjgrG/uZUjoVcBqcIXsHALmD4sZ3QNWN9VlMsbWy26fS0OrTHIYmoFcZQ9t3BmyJHWBKLEFdyw5w/2447AdvrSionaHsNqo0dPLP4wwHrBpLwKuH/MV2gCUhAGnL16CtLRBIb+0tYUuJOnnV45TdTpWGQEILLKeAPwqO9zaAU++I82dLbCH/PeDWnXNQ3u2lje9vpNQYKr2tKg26dNNisZJ8/mgY+9kBjhTylxBhX7AC4ptfgtY7vpC/D5C0fMVm1tnoRFzDlN1elQZX00nZWH2BYCxu94g/B/JniC1AVfNGcOeOH7jVeha0QB8gbJi/OPv6KGW3V6XBlRkpF2stEI4DcIyH/B1w/kzIv7TxPdz+W1q9QGvbJVBzc2sqVWyk7PaqNLiySCdjd/MFoyF/ezl/e5w/T+oKbraexv3/rVYf2A/O/sGXujuTr49WdptVGkT+GQr+kk7+KP6j15kDcctX4E7bRdgPToG2u5fgmLAuhl5nquw2qzR4Ms/KVWvL5+vhdV/Z+G+Pi1Y3C5SgMaDtArR9mR9ovn3sV5gj2tHFFsput0qDI+O8wmEt+Xj+R+jCX1YDZEnsQHPrcTwPvNnqiccD5c3LA2DfUHa7VRocTc4iadTls4dD+7fF838FfyR6rQmourEOcr8A+Z/EfUDD7e/a2BI7I6rYUtltV+nFNTIvd7IojwX511oDbr1zh/9H/FmwT3ClTuDG7RN4HnADxgF32vxAWdOyAGqNgbLbrtIg6EHT8YJcpjZgVptD/i54/te1D6BcoLp5A+4DbrSeALfhGNB46+APLImtZWGdal7gX6B4Mj7/M0s+99uVvz2MAaxhLugCbt45A/vAOTwXuANjwtLGpf6UWlU94F8gHxIVA7TymYDX6NqNvSwXtIO5gDGobF6Nzwe1wDjwdus56AMO/cqWEO0Ka/WV3X6VXkw7EH9K8RS5/Tv06AMOgFFnifsBmP/hOQDyAagmWNG0IgH2DWW3X6UX02JyAQYKisbj8/9caU/+SI6AJp4Fff67eByA6gG34Vhw4/apdpgzLigUz1T2Maj0/DLOzsYe4jUgyJ5b79gLf5QLEHE/IGn5qiMWRD6gqnk9l1Y3S5MrVq0XfU01OikWK8HXgNTZ9sgBu/sANC/Ml7qDW63eUGdgTOiF4sInAukbXzLEJso+DpWeT2rRp7CrOUwtPAfkydd/9i4HQIexYEXzSpgD+OM1IVQflrR8eQfmiTNptapY4DXVATJFDdCvGcEc4Gn87fE6IUNsCaS39sFx4CI+R4hiwuKGt8Modao1Qq+p5mfmYn9RSqbJ/X9vMaBMXDgOMOoU88NnYBzog8eCKDfgiIlLqWJVH3gNpZ+WjDWhHKDrPPDTRK+dBcoal4I7dy/DWMALjgeXwPXmT2tgLDgKfl/Zx6PSwKQRdQgjoRyAVWfVRw7QPQ7giO0AXWwCam9+Du7AcQDlhK1tvqCoYUEwrU4VC76GOpDD0AGMahPI3+mZ9o/GAabEGl833HD7ID5H3Np2Ho4DR9Aa0lUMiWqNwGsmj+x87Fd6uUG/+HfUBtFasXoP0HLnLF4Xbm3zA9U3NjbTxaYGgvq5yj4mlfqvkbExWDmqA7P7xb6zD0B/D0oa3oJ5wCV8jQCKB4sblmTBWEDj2vfnlX1cKvVfPvncUYBZa9mPGKBLLCAhAmqtIbjWtApfL4hqAjdvn0Zx5EGqKid8nfQGuVDtd3ql8VPqgL3XBtGaUeQHam5sBW1wDGhrCwCSlm8ewfFhOUOiuob0NZFW6AmsiFI8FZ8H6n0uqG8/wIaxIFo3KG7ZheeDrXf8wLXmNS00samFsHmxso9Npf5pL34vAHwMGIgPQDkBmieS5QT1tw7APnAZzwuKG94SQN+gV9GyWtnHptKzZQjzgAe0CkPAQXmAdCCxoGK9gBlgSWxA4+3D4O7dUNDS6g349e7xNPEstXt3Kco+PpWerSuFwomAXUcYQC7YMycwxfOIptYj4MHdCNB0+wj0C3anqaq146+D5uQyRj5iVJkCTsPz8JfFhGiegCtxxmtC9+5dAZKbX8P4wPpLplQVD77i0ji+BMuilEzH67z9mQ/oyw+g68jQa1PrUXD/bhSobdn+B0Ni/glTTFD2Mar0dL2fwxjxF6PaHOc/sJpQzz4wC7+vABoDHtyPQuvJ/0uvM13MqndU9jGq1Lc0kv2wAmrpDJzd87FX1IdQTGiO32Og8da34MG9SFB1Y+2PMCeYy5TYKvs4Vepbb+az9B7hPqBedm+I5+0H+LoBsQVgSqyApGU3+P5eFKhsXttKrzN2Z0mslH2cKvWtKFrJNPw6gK7XBz+vL0D1IRQXim9+DseCq6Dyxrob1DoDZ04dUdnH+W+XGpQXt94tB8bkqzj9v7enESlv2G16pWxeeGA1wd7zAnRdIbqmpKp5Lbh/LwK+brhHrTNewJSq+sAQylYgnfNfdP6hH/8L2mIB/NuFXz+7P9/dRuFPBsxam0HwAYo+QAC0WiN0HRm42xYIam5s+YUuNl9GE6vuLzFEcqEVz3zIggzxe73AsRz69J9gPzjMk7qN5jcseNp3dWJ9sEJauSE+Djx/LtC9D6C4EuUGwvq54HabD5C27P6NIbHaQhG/0DVlI/kNiwwFDXOIRU2LbUWNS2bw6j10+Q3zlH3+la0RKdFYFe2acY98HsV0RCFX6rSYK3nqmGCVLxj/gHHd4gXqAb3FhfZ4vRj1q8Zbh2COePgxW+LwbWHd9IEe33CBZPWXXKkrFR7LXThO/cqVOj6EaoFiwfdC4Wd7ORLnj3gSVyK//o3JwqaFGrduhyiby8vUUVldF13X79izDzyCf8Nz5GwlrJ/X1/c/phbP/JNd2/P7L9oHnPDcgAp9Qc2NbaD59knAl75xmVKnP7KqbW1/jssC2jcTsu1xTF1fu73/A1Q1PIYCuG8/Xr3zDq7U5V2+1NWS1zB7THH9u8rmNFSanp2F3aJXQB/Q59we8S58vcCrdzUQNi3p7TeOMSpMZfcLfMo68efpA2jekIavK16G/AC6B10+XWxmThU/9boSfV79/Bq2xEnRj5+xL2Ivwj/7Ax7PHTi2VUL/kwn70hloC5/BfrGA3zBniqj5HXVx63fK5jcYOpbPGyOL5fruA+j1DszZvaHftIfjp46g8e2uvxHMum4p7wODw18mBzwmYNSZAl69GxC37ERrCRsgk0V5NZP6Op4I6Nf7wb2/6tYvnsj6heMtjsSJC2PfCI7UeT/sE+/zG9xt+FKP8cLGhcOKb75W/mJSJgkTU0un4dd7PjWeF+Pn4b9wLM2G52A7V+pGhH1hPLcRX9d5ll1L/N/g8lf4AgfcF6A6AbrvQM3Nz3/nST12w5hAmySZ2PVY5vPq3/hTth59sPj3LTYeM9vLr5Gw/xHaRyW0DzKUH/QVn8PcehGv/rV4XsISEkXrD3rlrH7O73XYw3/gua6AfSEY2sABvsT93uDZ3T/HA8W9J4oa5oPrNzeD4sa3c5liggWlczz4CvbJTiYvUd1r4orz49AO/69stv3V2Ty2HmDUWODX/feP4z/G17/kPnKIzrMsvmCIzfD9ljV+CMobP7wD/e5mhtgcHcPwoqaFvrAvdmnfy+0HvZwjZXPtr7S83sMoFMEEOI4/7zoPXEPIv6u9EfDasaDeA5Q3LX9c1LAwmSUmGNHrzNCxbIe+9xd2h10qtR8om+tAZJiZjkkKRZPwmpCsD7wKNtSX0PyBNWBJbIGoYSHUkgfQFxxkSYlaCaWYmbB5tkhxD2slHoeymQ5UxAwy1kotQff/cXoRP/DS+gB6Pgm6FwVX4gKE9W9CzePDMeAdhthWu/KHTd/BfOBPPB54CTHhv4A/0pzsHOw+pWQqng+8Dn0AveLPqhHbAj7sB4L6ue08qXsqW+Joxa33cOBJ55Dh33BsQjnqYOep/zr+SO9m52M/FRZPxs8v7wXn+1+mUGyA1qmh+xTypG6/cSXOYZx6p0Wceo9vePVuNXBM+EtWq3gpx6Nsji+ixVkU7AGlaCI8nzAmbBzY+n/liihbo4D+lqJ5auffYZ6aBP8+CmNDvrB+7hNZnjDkfUDZDF9UDhlZmBg9E4BVZw37AKqtDV6d92X2AzRHwZU4/caREm/z690f86TOT15sLdv/C/5I+ilxGC2XNRwwqk1hH3AZ1Pmel9UP8HFfLFt/CMcIGAsQX0auqmx2g6VRcSFYAJmp3Y7WgEIbkt8bTJFfKZvvKytlcxtsbcouwO6iZ4Qxa6xl9wh+jWJDFf9BESE9CyOTWcMAtWwGzh/dK1iJOfarLGWzGipp+e3GtmTnyp4XSK8261IreJ3iQxX/F9S01CTMn8zU+ZkimgJY+PyRk2wO6bWLEVX8X0AOWWQsIpc5/D61ZCpg1lrJrhfHrxmX9YOXPSf7ikjZXF62iJmZmHcea0Qj6gfoWiH8WaJoXYFUlnu9AkxU/Ide+hmZ2Gd5rJG5lOJpP6K6Abp/gHytjLKZqPgPjtR+AXGj+Y3zhvOb5z1tO4O0TGxzYdGUcEalKZtVa/2fV4CLiv+LSbukadk5Xr1bHUfqxOBKHQM4EsctXKmbB3zPRNA4Z6KwfpGeoGG2TtWPe4eVtC4fK2p634DbNGcpp9apjCdxB/9PakfK5jRUcqIKpv+PWWsrWzeK19lxlo+hWmF/qOCiNbISR9g3nOGrQxX8/1221PEPttT+b1Q/fAXYPENE2ViFr4tV8e+hmVnZWm1o3TAHz/V6nLd/rIfs+dkrzl9qj65/+ZtdC/t1nZ18rS/xee59oGxOQykf9KxIFONzG91eeaY4u35eqwa3vQ791vusUtuN9BKTCHqp6W/M69bPc62bshkNpSYmx2IV+Vw9/FlBT39WjNLtGc1ZPcGvd+1HP4XbNELWY2Fsi45T7T0MK2ZWWsH3+9l/xIr9/uuffWCdQcJu5gvG4Ndw9/bMSOVJbu9S4s/cepefeNUeT9i4Dffr+w9hzOLAl3ZcD5/LvGYJOOK+vwP7DLrG1I9dTUxmXLMWsKptH7Br/l/cz8YiLQMrKuCNBiz03OhenxupHJtnS4l0ttjuRCF/yj3GNTPAqe9vu4iPYZz6HrfeQ3GMIYwyc/l9T3rZXjbvtZUn7bjeZ2puql4F89r/m3vZjEsIwULy2CMe0a4Zyc5TvfPLXyeC35sMye57dh1hP7PKck1mjkZlgWjiE47UbgD3Mcf1BUfqqji+g7RSUzjO9X7/O7bE3ptX79r1fESxKm1RDKlsLi9bq0lU9YoC4QRArzLBbQVfL4R8wiDeJ6C7ZM+pxOcexcRfmdctQxnXLcypXONtGVnY9wX49QwO+DrAgf0u0RPGgIrj2gTjwMfsXvkT0zlSZ52izvsZH2KWy+IM6EOUzUMZGpXsj32ZQ9cuKhBMAOgeEyg+RHNBqB/gnF64LzjIfkM+v8QWE+4xr5tH0soNibTSmeNigrCI7AIMUDry04HvD/KL5NV38F9IExr+zq4hgO5jG7GCI3GaxJF2PNtkFbvM9hG7DmeP/IKyWShTY2MDsQ+z89Vi87kjv6eUTAGMa7PwuUE28sVoXS4aI/B5QsfOOksHKzljufD1m8jO5Z/DPvWIed1CSL9m/A2lZKZxIWc02ufqzFzsRg5LB1ArDPG89PmvYyLmwzZq1N3dh37XmsKZep913Qbuv6N/3IBtNBPWdzzr1oVdTPyFXWsnv6cOLmUzeBWkccQVM8rOxT6DPiEqlz3yWqFg/E/Ukml4rIDmhlCfQNduoByCJZHXW8SKV/henc1jZo3Vr8xqi2ZGpQmZVm6wjyKaapbPGaOdmYOhfSzIysVoJJoayBeOBSjPQ+POi61BIFbCMWMMvxG/78WkgpxJdayO/IH4B4wz3u0SH07h890r2DVE0KO2qexz/ypKNzkMs4d2upJMxQ7mMHUuFfDGxOQXTcwoFE0upJXOoFJL9XNoJfoJhSVTr9BKZnhRiydvLhCMn5PPGj01MwvnrZUegRlkpGHbsvMwLomqBnK5I/FnliJ/PzjXLhK/50gcDXj1c/A+HP4xxmVVKfg7bOd2Xtuvw2G/SeLUOsnnN1X8X0Aawe6YZsgKDNPGsL62WUumqtHINOxBLksb5PPH4NzxsUH+vNpBWtf/BxxrXAUNHTWADAaqAUntL3Lru90by5dz3aWvfSr7fP7bNI5WYiRFz6alVujDccMaX2/GU8SVgzu38DfUSl7nvTETmeXmTI7YWbvk7krFe5t5tW+0y+p9Kv4vQSb8eo+7eG1BHju++D1I+9QT6M93c+pdFPtexCknmnGrO/z+HH7t/F84YsfOa81U/Ida7/Ab57Z35gxd8oYh6AOQvx/MAXprh36JZGU9R9yn31eMQ8o+X/82HWFUWwJmtRmM8a3wugK+vhDlhwp/gPuErn7h+ccE6NcTYX7fsw0jquo2U9EaFrY8V3nKbyj7fP3bNKuQP2FLHlv3XD5/VHqhcHwppWTqA3QdCr1CnkvWWHXcj5CHcgEYGyjig4GOFZCtAPoYTclPWV3bcIkn9ejSt3r7rgOKExtg/1H2+fo3SyvkCDY6LRWzzCZh75IKNXbmMYcF5HJG5BQIxtYUiib9l1I67RF6dhkD9xc2AL8GVFE3xKWoLzl21pq61Peg//8LcqTDzwN59Y6beFLXk3zIXnZvod7G/I56VRur1no2o0r13GslSG0xhk1MTcAcMnOw5SSq2rc5TJ146DPKC/hjbxcWTYT9YiqgwfyBUT0LoHsY4/VpMRHPIWU+w1leq3aW15g7a5Lcf4wnDvJxRzb+sMW29YwqkzmFvAnKPg8qdZdG+OfYLBIZW0LKxz4jU7Hz0F+Qc9nDK/J5o+7DseQxRTQJ0NBYUmkMGDWWeO2RjfcJV8BvdAM8pCZX+H/0Kv8/qjHDeINVa9lOrzBMhn7HmETps3ahbGlPu1pmNzOmbDghpULZbXkVpBG6EZuYHI9ZZ2Vhb0F/sTO7EPMn09TzcphaNXmcEb8U8EeDwqIJsF9MhzGGIaBfM8b7BxK1Yiaglk25WSAcG5XLGrGYVKCm7ON5lsZMjSoTjbxSRBoeWqQ/I6ZU2e15VTU8zhebnJaE2cN+sTwrDzsMx5EwEhVLyaFrpeQwtMNJNLVjUB9n5WMGqRmYmm7f9cpXSfrTo0pbtUOEAAsStGiFFu0aHVmsZ5ZQpux2qfRyNHtMWMkjzWAhUAviAQy+aoQKc0ZGFE2eGlOs7LapNPT6dHRYMVAPEkLxgTrqByFFP2mHCaz0o4TKbptKQ6/zuldEnfzhOKAeUsQfdoWveSyJpOy2qTT0IuuEFEHf38kf2n/MsFCestul0tBL1yWtqkYb8lcP7GL/ocJ9uqFsZbdNpaGX/bSo0p+18NhPxh/a/l8aIYIPRwXQld02lYZeK0aFFrVrBHbj/x+tYJ7DhIu5ym6bSkOv3aPCREATZy/nHyq8hgUUYeD1qF28jnKa6SN4R9dTsETvlHCJnqdwsZ6XcJmul8B+gs+g51v2E0NLlo4MFC3WCxIt0Qvp0GK90OJFo8KL84YFw9gvUCDnz4N+oKRkSmjuQkmT6QLRDaeFrAb3hZwGN7lcF3Klrh8IGl2X8KRvaIiqPkX7MLZLp9hbpuQQbFOz7OxSMwiO6Sm2Lunxtu4ZMbZvZEbazM26YvtmVpDNW6RA/XdIl9Se0WaNI7xTZtsLd9hup3xm+Q11o9Ue2garA9Q11t8yVlofZn5oc4K1zOYU613bs+wltt6chQQ/7gLzAN6ciaHCNwd6fkbm1Lxvl1bhSkgrdyZkXHMhZFVCXXMlZFe625KhcipdbXOve9jmVbtBuRMoNbPNKLVvjGXUPvV5qE/TpimnuX9pH+UBjaN8oHFMJs3jvOvDPfkTZlwYtPqr06Tgkh90LgmBhkL+XRRY9FgTxXyQvYK/WjAPYBfE7cuTvR7V35rxN6/B/jFL6oCue0NqZ0sd2rmy6+XCqJVoDRTmbJhIaZ4al/vD9LjM+wbxKQ+M45PumybG3bNIjLpnnRR+zy455K5DSsA9l5SLd93TfKVz0s8yFmR4Hng7++TYDfnne2u389aCHS2bcjfd25S39s62/NVtOws+bvu6cFnbN5T32vZTl9w9SFt09wjtzXvH6bPvnWK43j/DdLp9jkWsusC2ybnMtdgdyrMwiRb2a559VVKZw4OEEsv7CcXm95NKLe6nlEGVW95PrbC+lw6VAZV5zeZeVqXNPVKl7f2cakILVHl+NSGZUmu/mVrjNJHZud6/P9KbeKiQp3WYA7AjvE7B/qB+grd4jLdgMNiPN424Jhp2UQh58gHmJ8ClBvsAdkkgkz8Svwt/+ArjQMy3BuzL2w4abs0ELHHXZ6UQ8escuFJiSX4ZUZfJmjLKjCSumBibB8ZHk8DkmAwwPS4F6MclAqP4GGCSEAEsEsOATVIwICZfBg7JF4Bz6jnwRpoXmJt2EsxPP1bxduZh14/IR3q2fd3G3M/AxtyNYFPeJ2Br3grwRf5ysKvgA/BN4TtgL2UROECZDw5R54CjdHdwku4ETjPsgDfTBpxnWQI/lgkIZBveCeYae4XxLUenl779tPPkE19sCWJFJiBBNAsklpiApFITkFxmBlLKzUFaGVS5BciosABZ1yxBdqUVIFdZ48qrsgL5122eFFy3bSqsIX7NFDtrVrd83l8+Z0ccYQA1OXcktWPw9QT3zIgz/MHg7zvSH+Z0FwQ4f7WLAlzql4sgcyEutQD4GtCDfyD8/EIpuMxdCsS3jAFT7IQ/fxcXZM9vtP89r5TgEpOFr2HznxJLA2Ojc8H4GBKYAvnPgPwNIH9jyN8U8reE/G0hf3s5f5fu/MGi9O8a3ss6YLgm91jXth/emAf550D+uWvAZ8/k7wz5E+X8rcBFlinw5xiCAK4+COYZccN4ZqaxffuCjLhiCzl/E8jfFPI3hfzN5fwt5Pwhe5w/Ym8j52+N+IOCGltQCEWtISTT6xxH85v69azH90d/m/dY4zAbsud38j/GLdY6xR/mElv1IuzfmhRU/Iemn8z21RT8L6EcvwRgcqmFlAIsCL4XoOAP2xFQBvQuMwCp+g1Q02qFr6MWNNkDYbMDEN2wBwVlhL0xZPz69WWT0kv+NzqWAcbEFoIJcTlgSnwWmJGQDgwSkoFxYhwwTYoClikRgJASChxSA6Au9uB/FCzOOATezdwXtix7f9f2x6zP3Qo25Gzo4L+jYDn4irYU7Ka9C/bTF4ODjAXgCHMuOM7yAJ4sF3CGTQTnODbAly3jHwD5B3INQBDPCITyDKvD+SbToovtep6nYXTJttL4HvyR7adVmYP0SnOQWWkBMqssQHa1Jci5DlVjA/Lkyq+G/KsV/AmAgvpArS2VIXYazRO//yxGU6Z9HS3R/o7ek/9fcAzwGHeu6HnZz5h5pbRJB7H3lbNHguxx+z+ZDdSOZwK1E1lwrMkA2DnKE/UgkSz3C4bt8K8EJoExIF9oBgpKrEC2yBaQRDaAVGQLsoU2pPgCG43NGOagF1j4p15YAdC9QgZjwzLB+PA0MCkiCUyLjAX6kTHAKCocmESHAPOYQGATcwkQYy8Ah6TzwCXFp5v9L874DryTub/1g6y9+uvzvkXtH7mBsk+4NmcrWE+W2f+2/JW4/W/Legd8kbEYfJW1AHyTPRfsI3mAb8ku4DDZERzLsQOehdbgAssC+i4z3P4V/EN4huAKz4gSKTTXTSnv9uxN64RS+3txInMQV2wK4kXI95vBVzNwlWMCYjimIJZnBmL58D2+OUgUmIMkgQVIEVqCzHIrUFBrDSi1nfypMv6AVkeIYImd+8MqeviBAjgGcPE+gPM/zoVseCdGej13HpA8Ao75aue7sMf5i37S9Mz5atgu/5VqO/zXq+0MWKX1xYUirQtUyF8e/wfDsSJQVDTtYvzq+LQpa6+kG68PzTBdF5JmujEkFclsclAKfu8St1G+6XuGnY3bMNIzdOOo00Ebx54O2Dje69KGSWd810/zPrd+ps+Z9YY+nuuNzx1fa+J7ZI31paNfuIaciHeNPPmXe7In5H9Kbv84/9+WZu9duCb3APptw035X99Zm7Olw/63Fax8tC17qc/qoDffXXPZ7ZMNge5rNgY5rdkU7LDmsxDCJ9tDbT7/JtIxeG+UTcvhBHPgnW8Cucv4B3MV/I1BON9451Vht3svvJNQQvgzVmQK+Zvg/FPKzX6LYJgePBtrsuJcvMEGn3jDdecSDdf5Jhqt9U0yWHspyeSbgDTb2KB08/sxFEtAvmYFqBJCF/snIP7tjDr7hZzO+4T0pU2j9mQCdfkYgPirQf7YCR5X25OvsZl1a6Dst47yFwENX8gRqoP95WKgfpa6Z/R30T23L9AOgbbvL/f/wWh84HrpeA9d3Wc5hn09L+n033Nw/h32/we0//dXkveibeZvyNv5aC15M9hAXq/g/8PW9KXWq889M8ae8dlJe78D4aaPfXIMQAjfEAR14R/GN6qIEJqOiStzVGy/I77YGnTyR2O/WWMY1VRz+UKfp+4LwyzMDvlYZ4RlWQFSuQ2girvxB4w6uxRWnZNacduWp/2O+dQvw+5qHqL25P+zxik+YbLvgObeLQyulN3TRuzP8Tv5X0TxHK9wxJFkzd1r5nXd3mBmZHGTViCKAeX8YV/Q8Gd/OvZM2pDxhzJblnfph3kZp8Cbnfb/3w+y9ry1OgePAbaszfkC4PxzOvhLtqZ9MG7FgX7n20eORBmBizTYB4SGXfm3hwtnLY4RERTb+cfhsb+MfwIcAxJLzWghBWb93Y/GcGxzYiyFAAqqbQEFclfwp9cR7jDF9ib8hjlP/w0My9bZT4bsuWjsV/B/onaSt0f3bL/HgJG2UWW04Reg34fs1RX8Uc53UXhb+0SW8cwvTvf8ztxxYaI/NRB7KDT3oxZS/EjjIt157PF/+InB1KoFWT6P5qZ3s/8HS7P22q0i4fzPrc3dDtbg/DeAzfj4v6JgS8rSAe0HwwDtXI4RjP8NQCi/gz+IEBifjC6yVGxXGCuP/eJEsrwPxgEXg/MGtD57gt/VRc3kMjtArYNjf42Cvx3MnYirud3v/9Kbdup9kwTUDrMgd4GCP1Kejme/6wBHR/rJ2KudhyzPy/hjF6E9exVuGrMvvLfvfKoL7R2P/WEOiOI/yL9J27dw2qR9/kPFfsJbpAD+nDRv0MP/1yzN3DNubQY+/ud+kvN5J/88GP/nfhy4KeGDge7rkwu5ZiCA3c3+QbjAOPaqELfvcekVC6/HiRD/WXj8l1gGYz6B2Y4AsuVA93U2t8wF+n/IvraTP6uOeIojdXvWd60nbw/8RRPGgTL+HBn/47xWLU+B4VS/kmd9/83J/sW/ap6DvH2g7Sv4+4kAdp6bOOJwslofdfxTuqEi3PZxoRgwWMjQ8SGNtNh+sD/HrG2bXTLNKIFpZ5VMc5sen+M2MTbdFeb/rgZxCa4w/3c1i490gfm/k01S8Gy3tODPPdL9S9zSzgOPtDPd4v93M/fnLc3Ax/5hu1ledZ+QIH/SJln8B/lvzfn4q83xA7N/KKJ/oeWPQWzjnvzJUQKcv01CCRHG/mYd/JPKTJ9E80zfvZQx4PtzfUCpcof87br4f2j/dXaXefXuz/quxluudjTt3enQ//NQ7K+wf5ir8Tfqej81Dxwz1b+8WgcyV/PhyXw/4o9s/0JRM/T7+hN3Xerte5rLqJLUYajWJ4/91IKRL+BHDfd65tivr59SunNcDD9rVDSzcWw09aeJMfm/Towl/zo5NuMXyP9XyP8XyP8XyP8XyP9n26Tgv+xT/IFjih+e/3n0zP+z9l96P3U3+m3b9Xl7HqyG/Nd22v/fW8kfL94Uu2ygTGaFcQg3gjmzIH+jTv58Y3KkzP6XxBXbPYotMpP5f8S/1OTeVY7ZrPNJhIHuy4NW6/qIUkNU5H8K+/fnSp/JH2m/7q5YoH6IBrCTAlQDVPSB+BFnBGr8xp/7+l7oSF9ot978Tv5oDLggatc4Vbh0wp6Qvr43ekpkaak2qvVdFsjqf5C/xmXWQb2TCU9r565RV4ulw64KwbAoLhgRxQC6URQwJjoPTHhG/U9R/3Xtpf7zTsa+Xe8lfI1+f/nqnF1/rSJt68L/k/tbsj8y3xj1zHpKT5mGcm1aQrhd7d8Q2r9RfJSM/5exIhsQA9kj/vGQf0KpSUkky2yUV+SAr8+bTat1bsf513T1/3Ze/fD/SK5jtgT+T2N/jpx/hw+4oeXJnz7xvKi37ywfe0H0WMMbbt+Vvy/06WdZV4Z/l/i0/RlPuVJ6TzOwS/03GP6WH2356MORvfoZp7SqON0o6CMihFB8oBPJhvzpQA/yH9sX/0Q5/+RgYJ8C+af0rP9C/pmH/n4nbe+S9yJ24nawmrQLKPivl/Gv2JK1Qnez74Dt3yOUa/lbMLen/zc6e1WIj+9BMXDsjy6aJeNfAvkXm6RHMCw1vz66ZKD7WkWrlY3/XeJ/FP9t6Uf8h6RruHJ/kfbuVDwH6NoH1D0FH4/5Zy3QUD+o7IaWD9zuLPL9cv6+kOd5wTWdo1kTDbZ7PW1/S8aGFuNzgLL5H7T2o+hHzXN5jmP2B/fcdsR8spQ8MqoIqIfDfYULgUakAOhchfYfzQS60VQwJiYfjI8lg8mxmWBGfCrQj08CRgmxwCQpElgkhQPblBBgnxoAHFP9ZPxTZfzx/C/r0Pdvp+6d8r7PVrSvCGj/oIf9p2/JWtmfc9hT20P5Fj3yf2j/fJMVkRwbtbv13PxokTm0/07+cUWmXuG0Acd+SJfotY7d+DPqCL+xxPYuzJpn1oAUOjP8y2ighmoBp4Qd/NU8BUHDznbjr3Gy+F7G8HNwm7OQewd/GPNdKP5D81T+/Al7/sGwp3bohhTjcZ+CPxYolGp7k6Yabzvec9vLelHFQD0M9hHIH9m/xlUR0AmH7ANJ/xnln/JwXEDiw4kBsQ8nB0Q9nBF05aF+UMjPRsH+D02C/B6ah/j+bBXi/R9C6Onv7YKP/e4Ucwq8kX62s/6Xeaj4Lej74T+tfSXB7FWknd35535yeutz8K/4szgvmGcC+Rt2jv8Cox/DuCbTL5OIo5MqXOqi4djfjb/QdFM4Y8D8dTnid2uotcRu9R+m2K6Mft1Bjyzqd81i/qhtwe0a+0jQ/iGPkzyZTvGrtb0E4wyDyxXb7daDdq5+Fn52BnL3lvHH4HtqZ5inR30b1599XRwBx3t8/hf3/zCG8Ocyhnmmq+8ar9V1O4+J0aWPNMPh51eEMvuPKgEaIQya3tn4pROPBblMOeLnNuP4BTf94+ehvN2MTnq6zTrp6Wp24pib5fFDbtYnD7rantznQji1x55wcHuaW8JZPP6fA+1/QcZxsCjz0NXFV79E+5q5lryvcUX2l2Al5L+GDOP/XMg/Z836zzJWDZTJgnC+9Z9B0PfL+Hf4/7RAKj72W8eXEf68CrnHKPx/scmfsQLTuVeo1gPd1xeUGhdQeJ0AKCj2g/wZYtz/n84pcRzI74ybufpItfbXSbL4D40BMv6PNU4LFo3zxWMAl0mXSh5qQrvH2XfwR+Lyhx3O0CGu2NGffVF0UOyn4A9zP41LrCsjjv8j9ksaEYHYQ7sPE0C/j/7m5QzzStS1/WTTgG0SijQ74wLwSJXlfwsyIf+0bw8uCsXb7LySvOfhx9D+VyrsP3fD/zbnrnH6IuuTgexjVELZB8VBXDNZ/V8+/xMmNH4czDJe5J2K13ZWxRUTwdUik6723wRzf8MrhQNiZkYXz75dcJ2omP/F7Z8hITzIK7ezDE3xGMhvIQWO2BEJ1L4rhNw7+MMxgHdwpLdQDYA/mcO8oW/wkrNHQn3hXNFDzRO57uO/7jPe73Z+3JIrG7UDhV35P9G4QN+tezS263b602LKmrTDIPMwgcz/Rxb/onE5nzj1QK855bM0Y1FmsNS9I/8/BRZkHQcLk/evevMyPvZ/soq0F8j4fybnv74F8p+6ndxv/prZ1VsiA7g2wJ9tBPnL6v/Q76P5v+zz2bMU2x2PKbIGUUIZe1n+Z8KPFZiPCM58Rr22y3lkSZay8iH7rvO/dGj7BddtLyTSn+sePytGfxHSrr43U27/fJw/FHX4GaHPGJTrneZ3538OxmWnmfv1DsT3dx8OU8OKf9IM6Lr+R/Q/TZ/8ZaMPRnTdzkU3CsYTiDuyfzgGqEeImrQu5o0aseab5zk2J49U/4duKefl9o/zf7ggbg9xnuc69PmRFeSvu/PP2cDckrt2xDfkdf35/SmX+fMS/Nh24BK+/sMABHJk9h9WZPSjb46R1aEQc3zby2TdpKtF5t34x4pM42L4/R77zfKvuzIQ+7xqa8jfBvf/eNwvJohTOIRp3uH9yvt6avr0zy83a36VCDBUB+zk/0QdckfCTgvk7KFvOIvHgIXDD2Vou320ur/7WK0XVPS3+mV57Qev/RZ9r3mGZDduX1DX7eYPv1KErgOV2z/cNkL0SDOEtXrMudTnObaFczODYP6P+HuBeemQf8bRujcjvprgtPM99Hn0R+RvwEekHfL4bxPYmLved2vu+mf97tQLgg8/9Wa6Vvswifj6L8X6nyCuPrhSZAh88wy2HA7rmNPRyShbWIxiv6tC4077F5l9HiN4Zt3PJK/afW/udfvbOVUEkIuv/7HG7R/5fZbU9pdUjs0c34gBjSE9laiz46psDDgtUvCHYwB8RfIS4Oxxv+9ddFvraK7Z9J1+A/n9YyPRmp/LcvtHtV9/fpWOZ8YY1yMXu243e0S4qF3jSqf/V8fj/6KftK+wDo2JZK2amchbp5/IXGecRFlnmlSwzjwpZ511SuZa25S0tcTUpDWOabFrXNKi13hkRCyfl3Ul1iPjMsz/ZOs/5mV4gvmpR6hzg3eqY8O0dTaW+vCXZyP7R/xx+3+yIWd94da89Xt2UtYe+oqy6tBu6keH99OWHTpIf+/QYfpbp4/Q5ieepM+VejLcgRfDHngzrWXrv9imIAD6/VChPmSvf+pIhHE3hkklHm1XhWYgqqgL/2LTlKQSm/2pZcTjaWWEE+kVxBMZFXbHs64Rj0Cdz7xmm559jdCcXWnbZf2fbP1XIbR7mpjwdyrXar1f9IBrhz21edQXV4DaHjgGeHbnr9bBH76ehQxP0TaP3hc90N+PGYby/csCfN4frf9Su8TJ0zme1HO7WZNjytq0whTxn5w/zP/VYf4/LJLzt+5VRvuYaEr7hJi89omx5PZpsZnt+vEp7Ubxie2zEmLbzRMj260Tw9vtkkMeOaT4A6dkv47637xMT/Bm8qHQuf7bcRtew/BsXk6C9p/9hSL/g/w3PNmU+0kv6/8Wd67/o/1z/d9lHoz9OIZPvEkzTx8KN+55XG/FFNn+FSUwhfbfdfw3xef/E0vM8DVAyWUWIKXcAqSVWYK0cst/rv+Dr8j3U8Q2qA/8kcC03u6f8MLskYyn7Qr5QXNXgiwP8BR2539anvN7cbNGHMroa26nLw13T6kUaAd04Q/zQDVf+qXhh2J62z51RATsg2Hd+eP1n0g2GN6l/jcupnP9r4F8/W9v9V+XDv6nwdy4A9/OPo+vj3D7KPe73z/ssP/O/L+/6z89IX8fji3w41sBX/qsHzxTDXZ9d2VWb8e0LbrICkQJTHrh3//1n2j9H1VqDUhlVs1XCy0/uBg7IPb/B7/wt3wYhX07AAAO121rQlT6zsr+AH+SgQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAeJztnY2RHCkMhR2IE3EgDsSJOBAH4kQcyF7p6j7Xu2dJQM/P/livampnu2kQEgjQg56Xl8FgMBgMBoPBYDAYDAaDweA//Pr16+Xnz59/fOI696rn4nOlrABl+PfB/1Hp+Yr+M3z//v3l06dPf3ziOvcyfPny5d/PLr59+/Y777A3ZQT0+0dG1Pu0npWeT/W/AjbR/q72X/VR+naVppPX7d/5nV1U8qzkBF0avV6ly65n7bx7PnBq56t66+wf5Wvfdbm0b3semg95Bar+r3ll9Y77nz9//vd76C3S/fjx4/e9eIa6qC8LRDq9HukzRP6eJvKIvLkXZateSBfX9XnqoGkjL09HHfR6/I3Pqv/H369fv/5+7go6+3NNZdHyI02UzzNZnyM99zL7uwxRntsIm8ff0Jmmie+MW1xzPUUanfM4tH1FPqRHF8ip6VTu+KAL2rLKHddUH6pnLZ/xfdf++swVrPx/VmbW/+l/nbyBzP7qb6hTVnfsHHpWfdEu4oMv0D6ofoE8VnJ2ukA+yiE/9xVVnf35kM/L3xn/7zEXuMX+6Dz6I/Xu5KX+lf19HeLAttg9/kZbIH/+936GrPRR2otC86FOmS7wty4r7ZG5XmV/ZNTnvfxMbytbXMUt9qcda7vv5A1k9ld/h+/N+ih93f2P6jbucd39JL4jsz960DaW6ULTqc1pF8jv9sc/8kz85RnNN64h4zPsT19RfdCfAXX17+pvGd8cmh6Z6Vv6PZ6lD3RrpciL+/hNwP+Rxu8hJ30vA/XGh2S60HIy+clfx0P6h//vsqj8Opep9Om6HQwGg8FgMBgMOjj3l91/zfJvwT24hCs4LfM0fcXbnsJj5cSlWM9kcYF7YlX+6tkVn9ZxmI/Cqc6u6Ljibe8hq8a2q2cqzqryH1Vcerf8W/m0R0Hl1j0TXqcrcnXx/Hu160xW5dX8/gnnVaU/Kf9WPq3Sk/OGzin6HgXneJCFfJwDWems0oHGFbtnHml/9OOcXMV5adxeY+ZV+tPyb+HTKj0RowvAs8LzIfPK/sTtVBaVs9NZpQO1P3Jm8mf+/8oemhP7V5yXc9bKvVYc2W751PUqn1bZH+5Y+SPlFD3/zEbI3P1/qgPPq5J/lytboRqr4Eb0fsV5BUirXEyXfrf8W/m0zk/Sh6OMaA/0NZ7dtb+OGZ72VAen9r8V6m/gGpR3r3xTZheu+9zB05+Ufyuf1ukps7fOOxkXtOzMRgHlFrO0Ozp4Dfvr2MnH9+IpL4hPU84LebLrVfqT8m/h0zLezmUDyilWZTMnd66U55FnR2eZjj3vSv6uXoPBYDAYDAaDwQrEvoj5nIJ1IGuYVSyqSxNz2x3+5x7YkTWAbh5Z5q4s9wbnYlh3ewx/BeIfrL931ibd+vWZ+xkzrlHXlIH4TqzwUWV21x8Jj10HqK/Gt7r2r2djSK/6y57nGe5pvZ33invul/TMQaYznun0SX/zOIbHaLPyd/LKZMzSddd3y8j0uINVHEn35FfncZSD8Dit7tXX50mjPgedK5ej8UDl7JQPcJn0HFHFn+HzyEdj/lqXqvyd8lzGqszq+o68xBtVxhOs7N+dtwRdzNL5L/g67f/oys8zZOc7yas6Z0I5yFKdjcj073xHV36Vl+7XdxmrMqvrO/JmejxBx4+R34pn7Oxf6X/nbBH5+qfLF3nQ/Y7P0v6exeKz8j2vnbOEVZnV9R15Mz2eIBv/lVv0Nl/t+7na/zNdVf1fy+7s7xz0qv9r3l3/r+Z/Xf/Xsqsyq+s78t5q/4COLT6G4Z90fOn4K5dpNf6r3G7/gJ7hq86fZ7pazVl8PPUxTnnFrHxFN/5r+qrM6vqOvPewP/Wu1v96L2ub3Nc+5Dyaz/89jc6RfU6fzeW7GIHOhfmeARn8PuV15Vd5rWSsyqyur9JkehwMBoPBYDAYDCro3Fw/VzjAR6OSy9cfHwHP4gJZu/sezNU6gv3Sz0QVZ6v2Y75nPIsLzPYyK7K4gO7Z1f3/J+tXtRWxNr2ecW7Yn3ueB3Lodecid7g80lRr9M4umR70XKBypJW+buUbT+D779U+VeyPmBN+Y4cjVD+j8Suu65559u97vFH5wiyPLF6dcUYdL1jF+3Y4ui7WqWcT4dczfe3IuOICT1D5f+yPDH5uJeNoVQfeRzQOp+f4KF/7hXNufFd9VGcmeF5j6/STLEbt/YW2x/kVsMPRrbgO8qv0tSvjigs8wcr/Iyt9L+NVdzhCzlJoX8/K7+TRfLszMyEPbZZyXDdVOYxt6t8oe8XRnXCdmb52ZdzlAnfQ6Vv7rPp4r+sOR6jvtcz6v47fXf/fsT9nO/Us527f0r0D2m93OLpdrrPS15X+r8/fYn/3/8ju4z/6x09W6bw9+bha2V/zzsb/HfujI792Zfw/4eh2uc5OX1fG/52zjhWq9b9y3llMgOvabzuOEPmwn84xs2eyOXBWXpVHtX4+mVtf4eh2uE5Pt1P3HRmfFTMYDAaDwWAwGLx/wOfo2u9RuJK3vlvjHu++19jACXZlf09cFGteOADWlI+oA3Y8AetaYnq6r7LbB1wBjuEUGk/scKWOrwViFr5uJH4W8H2svg7Hb+h6lTMY8dGYDW1L4wvoq+N2VcbO/l1eu2m0TroP3uW4Vx1B9rsjtPd4juuUq+kCkeZq38p0xPXsHAtxC42zOgejv89FPdANeiXWhd9x+SlDY/HVWQG1RcXR7aRxmbSuynlSR/0toSt1DCgPS1wP+2isUNMRJ6XcKl7YobK/Xq/sr/Fx2j1tEj15fEvz8vh2xatl/InbXP2YcsiKnTQBtZ/HHz2Om/F7V+q4+t0x0vv7BJ07Pd235fJ4HNrrE3D7O29APvqblMiY6QZUXNSO/SseQ7GTBj0q75nJq3yYv0fwSh1PuEPK5QNXXfmWFXiOMS6zme+1oA85X0Wf0LGp4g29/Vb9ccf+AfV/yuMpdtIo56jjoMqRfc/sv1tH5QTx+R13qJyf7se6Ah3b9ON7LeKDb/S9HNxTHWTXlV/Lnu/O14PK/vgy5dQdO2lUJp93Kt/Od/qHt5mTOgbUBrqnx8dn1622k1P+T6HjB3PM7N5qj93quu8lWo1bfl/Lr2Tp1q63pPGyK52c1vH0ucx3Xdn/NxgMBoPBYDD4u6DrGF3P3Gse2e1JjHWQvitlp0xdqxLvztaC7wFvQV6P57DuOz1HUqGzP5wA6Xbsr7EW1js89xb0eYK3IG8WjyRO7jEb57SIPTrfpVDuVuMVAZ51n6M8tMcgPCar/L/qM0ureRNDqbgYLxf5NJajHHLHKWk9tf4qL3zOjl6QXctRuU7QnTFxjke5CI2ldz7DuXvlleELPEaq9fPzjc7BVv6fcrIyvW7Z3mxv/9iN2KfHfLFttm+btgIn4nFi7K3totOLy+5ynWBlf+zqZWax/xWP6DYKMAeobHqSn3NB3l+yvKsYsO4P0ng3sdbst6Mq7lV9je6tUq4l8xkrvbi/Q64TrPy/21/nCbfan35JXP1R9td+sWt//AZ5qc8jX7f/am8HfkR5VeUPwK5eqvqeYDX/o55wjLoH5Rb7a7nuh2+1PzqkHNXLrv3JQ8cOtbnud9nJB3+u/J/L6z4/00t2z+U6Qbb+831FOrfIzl+rbhwre9H+df/DPeyv87/q3HKgs5v3cc2TvsyzXT4+/8tk0X0YK734/M/lGnxMvIX14uD1MPb/uzH8/mAwGAzuhWz9t4plgLf0rvmOZzqFrte68baKnZ5gV9f3LDPLT+M/q72RAV2XvgVcOftQgfjX7n7NW7Cja0//CPtX+WnsR2MVfsYp4wgdxC08ng53prwu/Y8zccx9lQ/jnn8ndqp18HckVrGSrG4ak9F24fIosnKyusL/uK41ju8yqb2IUztXuIvK/2uMX89L0c+U8604Qi8H3cGdaPnoRc/VoB+XJ4s56nc/f0s70ng68ngb8LoFPJbsfEC2D9tjs8TPva4Vh6f5VvrgeeLGFQe7Y3/3/0Dblo5THnfNOEIHHJXyca7D7v9d+6MXPY/pMgf0bI9C02U2Vn1l9ve5iJ6tq/JS/Si32OnDy+HeCVb+32XK9lpUHKHrhDTd+x/vYX9koq1lMgfekv0rbvFZ9s/mf/hC9Ze6jwKfVHGErlP8f9f/A7v+Dt+U6Tybw+/4f61bJs89/H9m/45bfIb/9w/193Oweu5Q5ykZR+jl6NnBqn17WteFzjOrs5luN8Vq/hdw+1fzv853ZuV09u+4Rb93z/nfW8e91zuD94Wx/2BsPxgMBoPBYDAYDAaDwWAwGAwGg8Fg8PfhEXvR2fv0kcF+E/+s9r2zx9LfaRFgb0z2eYQ+dW+pw99pXHGJ7EvzfH3/CO8A0g/7N57JU3Z1Oc1H9+3xqeyvv2PCviP22ek+tyzPam/wrfJ3e/XVhvoeEIfWG92yh0z7BPk9q21X6OryyDJ1X6T2jaz/ONivluXpn2pvnj+72huya3/ey0T6+N/fsaH2f228hv39dwfUPvTDDuwjrqB9qdvLFtf1t0U6rOxP26FPOzz/rP9znfx5l5vuodR9mwHam75riX1++ozusdV8tU2Shu8nOBlDVBf+rqGsbyuoW1ee+oLM9oy9+IZVmeSp7+9RmfX9cif2973uXOd/rSfnknScVFm4z3f0isx6LkTzpT2o3Fd808l+cT1fob4Aeaq+Tbvc8efZ2QHNx/eWr+THj2v+AXSn72JTPTLm+3yl0rHPebRO2l99T6/uZdf5lOaRvduP9uD98HRM4JxTNp9xYEP/7cxqHGb9tDOWI8vp3LCzP3rVMQv/6e1I7a/+Xfeak+eJ/fVcIu1Xy8zeXeXzrMr+/E87vjInQL7s40B+dEcbzvw6uqv8qud75d11gcr+6jcBbTGLFeiZUV3fUFedH1bnGzL7U66O5Xpdz6V6n9JzH539kcnb1zPQxV125xaR7qrc3Xh30p703Tralz7aeYrBYPCh8Q+IJGqi63e9FgAABHlta0JU+s7K/gB/ojYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHic7ZqJbeswEAVdSBpJISkkjaSQFJJGUog/NvhjPGxI2bFk+JoHDHSQ4rHLQyK13yullFJKKaWUUkr91/f39/7r62tKhd+Dsh6XTPsS6V9TVZ/dbjfl8/Nz//r6+nN+y3WnHlXWLVW+f3l5Odhj6/SvrfT/+/v7L0p1rHo/o/9p+8/g/5k+Pj5+2gBzAW2jriuMdsF1hdWR+BXOvVmadcw4s7T6s3VOGdI/pFdQPsoxSnOkildpVv/n/JH9X3VL8EUf/4nPuIgvcpzM+aPCiF/immdLlVdd17Gemc1FWR7yY2zK8yxbpp9UnFkbSLtUvs/g/w62m/n/7e3t8I6IfXim98dMI31BmyC80uKc9kf8nlYdyze8l5Fe930+k2nSnrqyLecc+Oj+n2nm/+w7fZ5MSviw7FjtJsdUylD3M/1U3iOv9N+oHWf/rvBKHx/W+WwOIB5l5P0n7z2K1vg/hc2Yb+nn+W6A7bFh9uvsm/S9fDcYjRX5Ppr9P8eQ9FWWJcs7q+8Sj6Kt/I8v8W32tZ5Ofy/o40mOtdn3ZvNR1oP8envI8TzTZMzpNulkmW75O+iv2sr/pbJRvgOWbft7e/c17ST9wPsEadGmeOYU/2c8xiTyIs1eviU96vyvlFJKKaWeU5fa581072Uv+daU6yCXsGF9G82+a/r31F+19nm1P6w51JrJbM16jdL/fW0jv/NH3/xLayGsm/TzayjLOepH/OMxu7+U3uh6ltcsrVG/Ju5szWlW5r+K/bLc+yNf1jzynPbCM7nOnm0k9145Zw2XezkmsHezJrzbOsuZ64l1j/Vm1pr6ulKF9zrWvUwrbVfH9BmQV16jHqfEeiX3SZe97qUyn6Pul2xvo/7PWhu2Zj++azT2V7zcxy3oI6zzrQk/Vi/sl2Ne/7ch9yEQexl1zLXKtFWm2fMa2bf/E0Gc0f2R/0dlPkd9/j/F/xl/9v6QduKcvRmO+DP/yVgTfmq9+pyXewL4elSn9EG3T17P8sqw0T4T97M/c515j8p8rrbwf99HKZ9QpjwvMdYxfjKW0Z7Xhp9SL8IYN/iPABvTvhBzbfd/H3Nyj/KY//l/IvMo9fvd/7Myn6tj/s+5HTv0fpJ1LfXxKX2Dv4jLPLZV+DG7Zxi25P0652HGcOJi57Q1e534M/coj5WDf2vxIW0nbcqe2cj/ozKf8y7IflvWKX1H3866Yo/RWEXcTK/n1/3Z+8GacMKW6pVh1IO5pPs35/LRNxjP9+dGefUw2kDfi0wbEz/znpW597VLaGm9QD2+9L9SSimllFJKKaWUUkpdTTsRERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERkTvkH4eXjmrZO46cAAAJcm1rQlT6zsr+AH+kDwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAeJzt12lQFGcaB/AGdJA7IAjoCDIwwIAaz3hfUSLoeGLEO3gQ5ZJI4oGSGPHAEwQFR5ie6TlhlJtRJN4K0zOD4lpG41l7VHbLTVWya22S3S1Ln+3BTfyQmGxSWJSb/4dfTdd09/tUv/9+3nmHISLmRb46UVjtukZJzKYmYnZdIWY7T047nmHyrcJ3lgfdcxvFfTJLXjjGC4T2Omr/fffiVmIOtpBTUQsxJVZyzjev9Nmo/LHrZb3VbX/pXmYhJ0UrOZe1CFrJRdlCrsrz5M6eJi/2FPmqzeTP1VEQV0VibSWFag0k0XEk1ZeTzHCUBhhLaHDFQRpSsZ/eMO2hMcd20oTqbY/HKrPl43YkO+osn1u3nmbVZFBi7RqaX5dCi+qX09KGpZRsXkirzPNozck5lHFyBmU1JVB28xRaf2oS5XwyjnLPjKJtZ4fTzgtDKP9Mf8qri6rPUYcPfa8w6kVzUKCyRJOyJZxUreGk4SM6aK1S0lsjyGCXkrFNShVXoqjyahQduxLdobpdRnXXZNRwPZYab8QKxzFPDRdkZYeOxYZt2ff6L83h56R7prPknGkkZrPwDuScJKecJnJyHOc2k8uWk4l+6/W/ZtxJvgrrty5FQv5FFmIOCe/TETu55NWMC1h/5EX3TPDn7Ldcy1vIRXGJnBzvgJC/iL1Ibqoz5K1uJj/uBPlr6ilIU0NinYlCdUaS6DUkNShJZjxKAytKaFBlEQ2tPEAjTHtpzPGdNP74x4/GlGa9PmrrMkeNvDmO/GszaF7d8/yXNf54/u+fEvJvnkibTo2jzc0jKffEkH9tqR3YtFEXMz2rKEY0sn/8T81BPdsaJeQv+UH+OoHBHinkH9mRv0nI3yRkf/xKJFV1fMrIxMseac9HVyjqY97cXR7b2bl/33fBOVXXu6Vyf2LeZe8zqar7TqnsA8FD5zRVvnuW5teOu9q7hP9WdPDi1y77z/3DZe/pr13yT3zeI9fYL3Cz6qfu69NP0ZzjWdR02rXQ/JlrYeMDUUHtfffC4/c8Civu+RTp7vkVa+4FFKvuBR8+eld8+Mjd0JLiO5LDB+5IS/fekZXsuhNTmnd7QOlHtweXbr497MiGz0eUvPfJqMLUwPH5KY7xK2fVvt/R//PqUml+fQotNq/42wJj0rXEcvnNhdy0W4u5qTff0Uy+tUI78bMU7dib6cax9syK0RVr9cM3ZbGDRmYeHiySvzPw557f29Q+uZ1tlT7L3yLkb4kQco94WNIk+fRAdejdokbJ3UMdwu+WmCV3S80Rt8qaoixlp6L0ikZZdvFxWewe5SCXiJDRLyv77+dclGmQMKu5UCZNH+qUzvVj0rjw7mu1zmPy6n7tmP2CFXx8QEHTpIBtxgn+m9gp/hsUwwI/1jmPrr/zv43BMEGR7+3uJ8rICxGlbw1xS83t6562McQ7bUOId/oHIT0zs8UBmeniXhlrxEGpq8XBGavFvdOSxaHpy/qErV0ilmS83Uea8XZIzNq3/Ydt71j7XZdfLL4+szb72fov5J9Un/J0UX3yliRNkmdgX1Hg+HVDgietGxw8ed2A4Mnvx/aeuiE2cOFHEz0TV0z8pc8/QMMP/ULZEkHlz/v/MdcqTS6oiu45bYmH5xcrGTeKY7xbVjCBeZsZ8bpdIUGb941wmz3rzReP+8VLfQ/+30Ummbf+XS70/+zv+r8h5ZsF1cumzGeTOrvWDGVr/3+WX5Z8v/5rrRFfll+QSg/poh3nx7pwtxd4aG8O7Vn5x56M4clPjeUbamhLCtLbvQP19q6ew1fZnFl1OU/kNdkd+Xf0f0PKwyTTkuiZe+SdXStDzcc8LRf2ft/t/7R8xA1Fc2S38mzmTU+NzeKhsmz35ayXfDjbcOG4W6jeJg3Q2l+TGNsc93eLb7gp7aW1u71R8zu5l9pq9VTzg/y0Nifb7S6fx1fVBnnNRpJXO/LPFPIX9n8Nq9oTNYu841LGdXatUtYSSc/6P5zUz37/jyka+vZgdlGbiLVWiliL1IPjL/ZQ891HVLXHCb19JFBnV/lwfLyv1rrzNc6qDNTZ3ooytS9yZi0Pe6gs+l46O+utsQb7avmunstXkWKGI/+adc/X/9pVx2eXze/sOs7X7u84V97iyD+MHGsAZwsn9rJk+yFTkC9T/HWbh8qa4am2xAv9fNRD3ZHlUh/OutZfa613V/PnhH7/1F3FL/XSWB3nCjw5636h/0N76Wztwvkk4VxXz+Wrpvvys4fOTq/eQNP/u/7Pa0yjxGPJ++TFczu7VrDpyqRb5a3P+19ri6AjZ8KTijV+wczRR3Vealu4t9q2UchyldD/q71YXuGl4uf4aaxtbio+zl1lmezG8gZ3tTVfyL3KQ8XHu7O8a3/TtUoh+xB3NfL/hfrONW+7N03o/+/yn29OfzpHv3TFtH2zO7vWcI4f8JWj7x39zwq//bqr4U+KzJKYyv3MFEb5hxtChj2E3jYIvb9XyPyEt9q6R+jx+V6c9ZY7a5kVrLePEBvsJZ4qnvfm+AvCejBQxLaO8dHYHgjX9I2ovNrV8/mqGTmz/sOvEqrX03Th/9/chixKrF1NckXShJn7O73/5ygt0VR2Obxj768Rel95OezLfGNYkDq/23gX7V/X+XEd6/pb/lpbcrDOPsBXa9vuq7EW99TaFgZobQmRlVcPBmrtWcIeQNZbb5/b19jmHmO62vs14TphDzCkn6Gtq+fzVZMkr93yOKF6I81o+IDklan/nnp4gTGhMNFvMZvc2bU2sS0xpGyVEGuV0OHTYd/s0Ifu26aOdN1dM72r5+G3KjehLpfihf6fqkk1Ty5cNC1u57yXUcf5xOWVBpaPorJWybeFjWEV29QRUz4si+zq5/8tc0n9s1mdULXFHKfJmjZx10JR3Oy4l1Wrl/7qwPNllyKNBY3SUesLZF397EDkk9xUED/PtNUvoTD9ZdfqzfHD5Yrm4T2WvTuhq58bAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAoDP8B12FPjfMJlZcAAABU21rQlT6zsr+AH+lhQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAeJzt1uFpg2AUhlEHcREHcRAXcRAHcREHsbyBC7emIf+KCeeBQ5tP++tNbM5TkiRJkiRJkiRJkiRJkiRJkiRJH9FxHOe+70/nOcu1d/e/uk/3b13XcxzHc5qmx8/sGP0s99S9dRbLsjxexzAMf76HdO+yY5V9s2F2rc37PbV/1Te//o3uX7bre1Y565/lep19+8bZv7pe0/3Lc77vX//X53l+2j/X7P99Zdt67tfv27b9+sz357/9v6/6Htf3q/dArtV3+5xF1Z8d12uSJEmSJEmSJEn69wYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPhAPwr5rLhS2ipmAAACvG1rQlT6zsr+AH+zOQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAeJzt27+LVFcYBmBRUmibjVaprNRm7syOv2KyiyGJvYjaGizTpLJMGstImpC/IIUBGy2snHtmNgGTxWKLvecOLqioKcQmWAQ0TO4IAyraBPQDz1M8TPvCO+fOued8s202m20DAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/pd9k/bIh5Pbq9sf/vtLdBbevYep6a/XeXAl5eHFlA+eS83wi/H08MdrW5/vGrUr0fl4u2Z10+tUL3oyaqo7o9y/Om6PRufjrfdfvckPk+mn0fkI6H+02furewYMUqv/99wb1n7vj25P8NGk/Sw6HwH9d+v/ct0sf7B+91R0PgL679b/pZSXo7MR0v/z94FvUj4UnY2Q/qtnddP/KrVHorMR0/+jlAf71/Rfgtf1f6vrf+n326vR2Qjpv3ct5f6O9XtnorMR0n/14ygPo3MR1//XqbX3L8Sr3f/T7f2/TM3B6FzE9H+/63//ZOrerxCv9n+zboY7/7x7OjoXMf1fTtmzvyAv3/s01Xe1c/+SvNj/067/sykfjs5ESP+9J6NcLY/bT6IzEdP/VsqDPZPp8ehMhPRfXU95efvGgwvRmXjn/T+/8/9pbO9fmm7P33Wfq1lqqm/HjXPfwnTd9xffgRPm/YuzeP4/7j4PjLOZj8Is+t+om8HSpDXzUZjF3v/XlIc7fts6GZ2HmP4v1o2ZjwIt3v3OT8x7lmh+5zOf9z7mzr9I8/X/oN7s763t/Us07/9GnQc7b26Z9y3Q/Lf/+9q5b6n+HuV+fzz1P+9C/Tw261+y3Wt5JToDAAAAAAAAAAAAAAAAAAAAAAAAALzv/gM26AEy6J9LxgAAKhdta0JU+s7K/gB/1PAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHic7X0ruOwo1vaSSCwSicQikUgkFhmJxCIjkVgkEhmJjYyMjI0smX9R+5zunp7p+dT/1Ihac+k+VXvXCbAu77suVObnfTaeANqzkS3G10Zgh6PDAnBdxQVrAN+FfsPzYh3ggQoQAbYKG9CeJMF33ZPZsYTB8c18c/zxQ28AlZvdQSvVcTO2vmxPFRTgeJ1A4SjpMPBhua8rP/cJEqDcVCykX40DrzeBuHNcndvez5heQmwxKfxDEfOV0g8PK9Rr2yjuRnlOIjj1lmRQQ8xfORbI0j5PBjAmbKs0uI9JbSv+7utukHfu20cXj3LFsPiNmeABPFGqg3EJD9EUCSuvl7KFSJN9DPqhrsFlobcdf3GPua5+foJbKS6jNWODiTYs1vq4xcDBgm0Onh0EdU+g+O+oOXBc+NP9PC8bDy8/vPy3uE7EOhKek03CmwVwKbYVIBX2xJwtHNUeMnDAJw+HdUtxYAK+tM1ft+Da5sAf1S+4mfs2/DQdPH4AhQu0Hjc3U+obgcfhTt3VQlHX4dbt8+unqJR1TeD3e4+O+zXIJS5Cpk7JigsYazoYCWubTsC8bYE52A/85wIqp3WBVcV8MqiG2SU70e8RgZurHbhdRuFh15IpzwuqUkUlSFdjME1nA8Y+u/gpL3RpaJNmmPXVCdG4WIY+ysocqBLLRcvF8uMpFZbUPA8s6Tb2czTF4cB/1jWbeuBi8D+kokof8OD2XBs8GU8cTSVPIyg35DbgOqcWPQmdqur904sHWUGj98KDSA22qwiQTKBzNpvOA02DWOrI+UJjWJ0mx5hKvRN0BGW7Lsr2EvyozwkzLhhqZSiUzz/UPD+dLTHpJHCdTwE9AP1/eBQaEowL/9r9CR9dPEp0wqG3VmebmmB8SSw85LiVfeBG8w5Ral3QbyVbUGHR/QGINv0YWBJZv8084ReqPxCoWW9oAIBGnhf8MDY34YGtHzZKRvGXR1vwhQV3dimazzc/LBzkQHeOCo0Gbk3gx6bdE23MBcprPj/16MlM2mrvD7MVPYDdD9old4NaiGl6RlR4BoEQ9IQkEYGva1D2OJtFt5Bt8vgJakFPmfHU1/regKueHD5+/pKG5dzg2IaRugbpQjn6teIJhgvWpAI4Va2rSxwOQ8N2tGpi6w9MC+jl50O8Au+Aea8FoQvnHo07pG0XagtQLtQFIJf44+9Ea/EVwup3/qFV/0XCwoAz9NyowZSRlZI4eOtVwIVKyvy5cxKPoxKJnlyEswgO6Mmfjis7Bn0HBHOtGEYQ4x1RKB5LSa3u96ZY3ZuExqgKuTELy/r+K0uP+qjoZFiMH107SsSjju9jCIh4JJ2nRNHXt94PEJ6iE1hgadceIOyo69EQQGzMj/tybrBtJIGoxl7XOc6E73pCR8+eoFE9FcZuZhDka4RE6vasZTsKPKj9+BZh0/w+LLXiop6basbva4cwQp9bcCj14iS/HQC6h8egkdv2zHD9NAxuyxnLcWCUWMaT+Qn6ds+19ugY2S549UhujPuNb3KfSr6AzzWs8cHg/0jgHHWpifHq64eXjwtm4KcWDO3X12HsGJWGiVtaFxk6PjzHTUBKoznzAv0CrOIk03FdFQGhAH09SIUWDGsE0P4zxsoYuuOv+emyunS/UZM9f4IBLAk3xscGtd+7/ezq53MNxD6Q46Iz+Lbv3tw2W6bRZ5WolwxSTI3Yjaqo+RGtPxe3KAyNJnfdLjdDI35CewiCXa/TCtfil1XUVwKyDDeZ0jF/amt+gmWUY0e7v3IWy8f5H9DjRNguGxI99MtLtNzu6wjFQN1X3cexTRID+zDlgJAD4/vt6OS8MM5cBtryeH+Q8652z3HfTlqiCz4jBMYNg4SM4EJFlwmZpSmVgromedhBfXTlP0L76gtZ7G0owldJcOGBybHygPELuHy9Mpcr6P3gXDK39iDt3imQbNw4t9Z0bBgFHMFAWi5CvYCj7xgElWXxhYuNg1JT3/SBxoNtPmSYSYHp/mz+9PInTg1hhmTEokczuSWNhrwjqyk/6LzPJAUBcx8c3wkDXzU9E7LtWRzHQlIjLWsicUdQLdBlEv4i52atwQjC4SXWqS3PkzMeN+rQ5MzIONRNOZkZgc+KGYosG6zo5F8qbjtIgsH6xkUWQsaxhh3WY2y/fvjO7rHnDcudW4OOL3Nhn2e4SRUXRQgy5Sx6A9Ix2hd0gRs6kmtMxtPnzsEGoc3tHMiZCA/lo4tHKeYc1HsSN8pv8MvFbmSo+KTot/DhlXtAcvVQmD4QxmvCd4xr172+oQsjuA9rWBdmeZES1kXH95rIQanNQsI5wnVNELDb3jRQPblfBNNskpDGZ1ePrtiH3U6VFNUjll9umYdH76RwA3ALLFqFHhL/VXWbNsiT98NWppvTsLjlMEVLkTcqfLf9GF2ve538NzVGXOnUtrv6elHYFaB6IeGCxwcJdRVIgD7u//OmdXCastr29VTZo7tvM1ApiPi0W+Be1Tbj1trz42AgLZpkJhLhKj22JcTAymZZkjy/XpKD2LdgXzadqN/IfGgduMzrBTPYoT6AhDIgGVC6EPpx/9c3BxXPjrML/dUO/CxOc75qu0aZPUK1ivxgC6jtgbOVQ6fy9gRpjlWSKQFS6ZCPQEzF3wbSroSL/4kdArfHp21iPDITRkiTUnGwshzDuUa9HuXj+PdYHLppjeSOsvVPbaxHQf3dELf00n06tioavssTdQzEZgXYOh1AyqtSSJkuA/LZ74qwNsLxvLHDNo5qkOUBp2PmR09wTy0NEPqtNh1IF9L9+tzKf0udyUrm21XAzuwWOrpKx4O+nYr9yXY8Z3qO44zoBPEg8f8IMUYqcW2ZLTuTDUnyjRQANw0/A94e4k/sKFlyDdlkZccKz8lGBsoXDeWZCdL60aX/lnLF2EiWEB/LwWHsx8fboeilPhjGEAAsoZW4rzP/ixtE7FoIi7lF8crGrgHScXHw7Ng3cBuBP7iDyIzeS6wGkPfFJQ7IpySBOw/ivD8e/VGschiNNrNwUAM3YLxhmYa46V49hAeE/clS57ZfF4b1mbMpbaOExz7ARDMjHsKjDLxfJw3nSf7CHcmtdQ/Ni0PByi1SjW4QZeOvhLOyz/Mfc3OVwO5Mz8w8yK0vE7XgG1IpfEx0XzG76fLBPHX1fUUKRMh6bMLxJBRI0xEOK+9OCB1fFTLsv3MHYwHbry3yckiRVi6gGbOliPQa/87U1o8ngJHvjJmFKH0L4G8Jsu06Xeisp9s2p0ZobHexhrxAjNJ6xns2ulBfmT8MAbYNResb0t0Y0GizovbfuaODw3ai5kurDC/7QukiTdL+smg7wNfx8foX5wTQsaFvv+spZ1ICbSDDJKw1vywglEWDePwoP6o6E7ZnwFXrtYUXRrw0npnqwCAJ6OAWCPO137nDRTSMgQYhlrNxPxBs5JgHkPVBrvUOiJ8WWXa07nM6bVIeqihHB/+wWt952kdxhCt3MBEpTnr79ufhdYhZ9C3FJpWnj+jAIqJZEAk9J0mG/c4dgzjwt+gYe7uZbYgbTC9+hLmPGYPCIf6Px/v/LuNC767g2NHMQT2onvjnvLFZmcsMfHoE9PA6ZokbI8Ksf29ouTJYaoH4x7xJfDHW2GkzE0EofPmndhBmMcUDE6XWDU5LgIiaTMDNqxraLp/r0+s/0nLZXcNxQlOgXiNvFvL+LmyAJQR6AuLigYsNr8T3WdLjfmmI5JSDUK4AiHEQHut1JjcohAUc+VU7QgKhkmwgekbreNeOBrOBootNm/fL8gssfFBmDFb11qD2a4KRJ5tOuvRizJQvoSRFTpW5qgpIA0HXad77UQs9gnUtHy9U5lFBRDmTo6jSZ9XsV+3w4CVZWu+uXICf2mHUpaTjNZBPrWpyqA/L0fGp+HUiOePWQth6cIPMrNZ2bKWtbD0LgxCPHhXJuFns6Md5nxXcvjV0A/2FptIRC9dtRYOBep4r/Kod700bsb6LPqhMv2vHPYtycgw0jQP57Oqn/BQvZ/0PmkXAchL+wH5QhhimbkLfW6CuXGdbFXuhq4eSZxqj41nbA3ZSn1cnG4aHCntGZbBtMe/eAYx7CwLdd74HA0z/1TuQHTeoJiSR5/54+mPa+MPQMJ8LgY6ebt32ifPtJhH62nXFQDVzQ+gUQ9WxbZzxHzhIGIPjZWbx77nGdAySzjxQSlr/9I6wQIOP75D5yNz/6B2huxY0nUt8ro8jYA4XfRdhn2sRUk7i/6Anl35JVSHCa/JXAYCBTIybWtf1RJgETkuVwaUF98yhVeMGDKOcz8T3/d07tJpnzBLvTH5hKF3lr94hQmp26CjRZvLH9R+jv7n0XLfzQuUFfZJBdUj3UqGkoBEGzgIA1Wfr95juGk0f7guoPDeHDE+LtzrI7cpb9202de129o7dxzszjua1Pcj87ncd6ad3jG4e6Puv//j6j5cEpKQzcEv+zk2ipLalg6ire/MuAHQLriKhA/NudJoaPxPg641kafGwYsxDNrPzPbDKRQmzGaAerR7VDoUsgKUb0a5PyAqynPUwuWj+dofLRxePkjsePbrv9U1WJaUT9vebyqqIcvynAMDkwjSdSBgNHThy5NnUBkvsjYDJeLrtQRz0OsoyDdoRZcAuqawB192fME48Z53r5IP4mSeIpsruzTaj6YclwcNHzDHW1rdtfe6hXmqubu3SvdNT/TAMQ3oBi8ftTFiGM/2cyFWD9oRNO14F4v5eFX5YY7C9joABYQEa6HYDR0gFdSLh5w0xivNrTtdL/VSCPyyI2edygz3u3I6GWH02Q0IQVzbbuwCQRt8XqFzuM5ZtezQhXTn/4but19xKNG7pFNgTNUrTc4R3gtxeDKpEn/doqA+CjfSMevaCu7aj3/04/5XgHFDrlF2Xep0X8PO6MbYbeKXifhcA/LVKOCNjviWBz74TrrdjRntk85cb3d8DHbq9bx33iEB3xTCJUXNQr+O5EppfFcyBziA/CDN5QjLEkHt8vv8FNbOnuId9yz54e3EoYb+y29GCYaE/BYCO0P5RkyXyp8xswaz2NPSCpM+CeG1XSdeGgEftr6ZD6BrS9OwxEuoSkgjbEmvXUdb9jDNpSmgb3CzH/4D64/qJGku6mlKI98XE8KIVxMLI9shPAWD6yOeFyrK7ho88IfONWxCeuE532fS2YcTc+LaiWoCOwHiJXFJ0dpoB0l5aSu3dYVwoAcoeyFqZUEWWj+v/7iAxipreowWhaI7g953seQYw91MAkEwhyHkOzVEDUA/MnhDtI1JA07EmNK9hnzkQAicyyQGexIvgtkkVrEXHOFjJ+Ely1cQKNKgTlip5nv1iH89/i8u80xovI4kNeLDd0dw7xjJSfhcAqosB9eIZ1uFPN8/tomjvk9WYVY7zXginawT0DbuapeOnKOS+oCyliJ8yGIf81ynPQwf3OijZkDuXHFEzPr3+NOEp+iWI+dRiNu4XQjgB/VygFB+zAHC19ZrJ7KtlPOq67VPpuRCQgtjs2ivTanPwxHCMhLgI3yU8Jhl0ezM/jKMIrHxOBilwNxFimdQCf+7j6T/UYaRp5EQTtVdsCH+SFgGhvfCIWJefAsBa2j47dfidKaRrbwMpI1fhyM1Tmm6uY1K9ePSUe1vAc1h2MaSsOTWJEV+sGqwwS+kY9cEYihG21Zk32j6eAFRwoTWHi7jZtKRsGjOlU/wi2J3qTO69iFiQ6oXnnatb4TVt9qH4Dgy6v1EAPSJ1ffaRxnDPmCp4jWL21Ym67uOX4yNpTSuz+UC7WiGQCf63z65+auDSWZTdrBUYkaG00iQePzWKlaBtBnTqdYhdIIcljkCO992FOg40aDjbg7iYobt0dewXM8A7+grOkU+kMUEvcou/BL6ZBQobxhHPUio1wMf7/8vsadwmaiMEWR4yOrokWggoYa1k5kDfPid6Cp4UBoTXTBCsr7Os2wIX64e2qb02WpDRwDh8YBvGNt0iAuWMWAEx31+AD3oFJxAN7kYtqfe70Y/7P7D6WF4C8gtBOj8xCKIHO9jMaC9LGJ5WQif1Bwz8dk9uEh8ZzwRGU/KCvMkM9QbGpOqw78zeUXs9a2g3mcAXTeWvwHdYUflw/Fx2782Tzk8v/7Yuxfba8bkK9I1OM7fNSEtS8MlsikuWIptxHQ/ylB6JXlfcBLNogbwxd3T5HuOgC2hABwKnrNEz8GUSHzb+TnyWkhe2wamLSTt57o/zPx8DOHRbBoNb6SGRC/qltSQsH86uTK23ZZYijwV6puUlSd6GQepr3MwXEVLkbCEzdfo44NqBeRPf6z8TX55Xxem9KYNBYkPS9en1T/khcnq/hGGipDVTsc1u1pejs4gRI8IUPP00M3mP3DYiqhWg0lL96tH034NDgYJRBOW/Jj64W4+8IwpCAEjNx73fe3ahZeAF12tPw9dUyWxxKI9VSAPwzbVojw8Mu92UOBC6LEB0sLX2yMPVgkzbe3AItBmV/B+JL9gqy0wijRRkX3kMH+9/n2ssNO4LR8yW/dFiRD4swc8ub2sSIv1EO4Z8N5ZbLhUctUTWQ+0XQZyfEeQjiWnH5uls//yvic+foUnWrNAW8gji894fRL9xvV0r3hhlRQmV8pZfqy0toJmDpgvasGOpHJuz6OeAXvi/pUz0EphxsTF+EesQQ5DfQ5P/lPieQ5M5oY4IZ06NEeTz/f/7GpP1SMgEOEIWa2jq56tKwY4jWqQtYPpWgW+nmU3LYSA5chgRFyQAE+7VuhQDWi28aPNraPIfCh8/Q5Mktwn7XpbxdMSP9785ZCiROBZQ3YVd2raao9d3WxKiAXdsGOnPO7WMZJXUbpfXhvRvzkur6I1k+QxIGqbehChE+q+Fr5+hSW78ScwgTe/j/F8oAPmBvA4Z8Bqckhju8DUpNhJIL/b1zFnNMYe4ILFRUuaMax8sbsvW+1hIva0GyonwDpGDyss/FD7/GJpkZpMEAecmNrN//Py9XkV/FUqWbYsSFKrpdN7Ie6VDl7WbvcxDrAJjYL3u2TDKhXYeNR3Dwng85IPzXDlZArfd/2Ph+9fQ5H0x2jA2Ite0IdaP85/rOepkbDonlgz7MUgiwTxITrYCJl0LxDXP9o82tjnHIRZJ7TE7IpDJHvjuWXhBz9dLLZd59X9tfGh/H5oMZBwNoiJd8M/X/9vruQhVuS5ha6tnYmJ3MjSsjab9mIPAai25IFEOqszCAE9kli3WBNbBOk6KFAlkR6eXy6VN2f6l8eX496FJCVb4Rz2zV/h/IQFyNumbd9FIM/OxGLsW+9JwIvEd19uLFwwBuaGCoyNnNip4pTkf8K6E72t7SJCuPFeQqPYI7dxCFlHfjU/nvw9NVgQR+YV7S2j1n148zEZ/FYlXDR085LVMwIbH/Tp3JHywb1mAnC1RXTwTyqvN2iHhIeWeufvwRs8ecUAQfTNmoVL4JR27mI1vFcS/D02Oo9AGcq9E9fLx/g8ry0587FnNWfyZjjb9ahuXcgMx0TEVazT4+mknWMkZ/GaDXDrcZa7evPcg3H65UDma5dIx7d+Nj7MK9h+GJjeOOFGhYXBl9cfx74bo9og1IDlvc6ZN2nmXCfVLBC3R23WKpHUWOebcB0JkeDdIh1aZvtbYJqZfD6ivnSFD8qNsARhnTA4g/zA0ibF/t3lT9wKlfXz+cdmz3mvQ8OwB2frMYq5zOgFmuicv0PyCwA4d47yzQCH+XSW5g9x6I9c9xEqkc8dgM5d/VyBlejyNUElH8g9Dk4Ku+zCoQOg07cf7vwsD1d4e+zW4AjVntZV4/2OO7VS/R/Tc+1UZ9COvUtQbQ0PGP3RkeMcc9Ib4TGCMxoE4p/Xr6WRnc1TiPw9NNn0sDAJfnZqTIB+WXIJr2awE3viebHTOhGyvc6CLOm0iMtfjNbdiAWVcXQhc8gzLm9zke3hh30xvuYtR039sUHdLN43s6T8PTe6liQBeYSzVH1/+bGIo1MAxhz/xv+uDBu3zDs8zkx2E3YxeN6Lb9jrwEIXL3oPDw166dXOsz5pxQrk4KsGN6GiAR3iMH7BZ/g9Dk201AoNNfu17Ux9nwDlu6JFSWJYdQ31b+auLF59oB0/OdEOblzEjVzPoByqa+zo7vSZfGIdHFNvbgrQmnEh8id3Q4MHoNYJMkYn/PDTJg+/yXGIFpvvH+7+GEZdEP11mTXtWNiqCU+Q8h5vZ22WZjTAsoCGr2A1BtMvYvrzn9oXkofaMS7gIn22knG2dwcbfjcNyi529T/dvQ5OtpJr8vDKJCggf93/W4SODw3AnJLRGkMu/QCHSezCeF1aEEaZZV6nYwm9lrSypiieqi0gnur/3YOdy/THO4troFYMjms2/D01SU5Ya3RATWbqP33+SWkId0GjEfJZ4srdI80ANNttZemlXH2yEd1ETwQwRHOF9gnlxDxdz4K3ssyFgq7Mffnkjoi1PGN0L1ZGq9rehSaJYlfeQbdbLERR/vP4H8ajMec/xgdH1n3zv/Cowb0CigRtd25OJXihgUA8RynHtq8KDdratZWa3AenPdu4nmk9BPUKA+x6Mg92CcOTvQ5NKIwq8qBAM1p6ej6f/cZXmNbENUtHD7he6gOuBd1Ym7YUpDNSpg9luQHBv743nsl3dzHszrHa2Ogv6DhjH+rWG3sNZkejNZiphV+/SX4cmJwpKazBupYmir0S4eOiP+38LlFwvSJPczMlEDOF1A85xD1qWXNqMRyvllbVYC3/sWqVUPnonETf5UYeBcRGbhLmOvrnJjO0CI0viUi7yL0OTuwdW1txnx1HXyKyo5enj8x9cC+IQ7GC4tz9k3NsXMXmzlOV1Tds2xrU4WlhdOMP4XnCFqndR6xZFvucNJgjvjIetMRZmchNSmgPBS2n78efQJBBHpBbOE9Pw1N2cnY/bxwHQlRgejK/waDMngcCuwviUt5MGx3u8HBQBsZoeHjs71n5GoPZL7jM30GuaFJbMdTwIcPa1ZMqO5eiIK0OofxmapAiZDI1S4Q+R9016ucaP5783GyluANKACKnmBPbUIGxFAw5HHRt5zWy9hzoSzJH/SY3e7ZJvH7FC7DxBXI6Mmlw2j2Tw6P1GpuBxH+DPocmFUYlb4rUxPGuo7t1Owz7e/5dTJXzrgs7Qle9zAVR1xmxlwfWSYppBfUG46+btFp7NtP4x4/0bMMBBex/JS/mTypgbFNO6vHRq0Qfyx9BkFkxJPXKeCREPolBSZ/P7x/NfTGK4UrOj6Q3FnusQbD+r4pCUnikhsNZbq4lGwuYIb9bnC3dpJgJrXpRDVih0QHD8VzLT97IO83to0niBSJdHUm6yBM2JjGURBENi+ngF1ImwgarpNkfBs6n3HZGsjVGF1mQyN1zM2KtknFORG8k9XLtGAqdmKrww6ZEdA9ujANwOT1ADkPrHNShyhFrfmRN4UZEQWhY+CKV+R6BBZR5OLfXj+f9qWfTcN5fSvm47+m4/07kiULeveNJ9Foe3lRoWEB0v4E7k9hgA3lc63YomtJfXvobZOngiDOqtpdGDEDuGxFLnFO2OlLkXDIGuY+SbhdGZ9bHx3BX9/P0XRWxtR8KnYT2PCxdoCPIWwqhCR1/mdYWz11luWuyrrUZZcyD0Vem1IhV6TRsmyzrL3UduuAHPde0u9URYiRqDyTVYbhQcmsGh9gKbO959ttSrJVhPP71+Mib53dgc7rgHRnJqaqIRGKIdhTiImwt5QcrG5BcqsVcQCRGhsxOJgKnSEEmQ0hGY9wSTOS+5p3WCYin1gVqzbBg66wxz4bwOuSA4sgg1wMBK9Zo+fv9ptIGcgZDQ85hJPJBrne0OwrYNiNmk416iU9d4mluL6Aey1nMOgK1HRBe44RbA4yiGACuJlyJFo7mzSG7WhkFfm+FcRrALWvm92Rkl0swbi5LE0j/e/zRgtQSsrHed1x5fe9k3oRwcErkQIvTdMKtZ7QbxrkCTZn2YpbbJ/+fFUEVqr23I2nY671HIHh2IvwTv0t5yTr6vW3fM9J164Cr2sYo1HAiLYz+iah+f/+UYlKyUZp03tbWXP0tf0RpQndEnLCBzWihvVA18kerDk1wtJerolJL7aISS7HmDwfjF88pcCWNLLxcJy6dZR9S72pD+ho0S0XomYyIMKscoLN/Rf9z/t3ntRZ9xKJp5B5hb9byyHHFg5WGgN1jEvN3gfhD/wf6kvlKupdAv5sl7aJJohfHMIqZn+MMaET13CJiO992g+9WXiIqEP/rT6f/MtpF1Ek4daHvcZxcP8/o/dHGqnoht7SzlonWiW/dZwvPab3T/BqEr9IAUIatoZtrnLjJd7N25P4cmlZx3QeFSiLS+RsPEvuu2vhFVZa2Cqwcl/Z1kz8tsAhuzafiBi9r+cf6XTXMm5zaZWJt3Fi0mzh4WWe2+hTMopa2ZRzmRrHtj14HM1qzHvw9N5t07o6Kt6Rx23vD6gG6BIpfOCAHtYrUduSkEvTyD177N3PGHZV/wMbYVHfyccOjo9+d996sxMfTdRiOR31lYg4FwFaRxFBpdl9xzjn8fmixbwiUqJhyhBrFAgx1EvGbzw9K5QYfZmWZzlAy9yyyog94+v/4zWc8c1JUXCDvnOiNoRUys151bAVJPZIvKEV5H6ZpBjcupZt9+WSH9y9DkReXqGPEIbhe3DvT8MK9+xeAvq0EO3fKBCpZL5W33ggGxED5e/91XWaJxhiK1ARITpeI8GAjRhkaKss7rKmMHub06Gnjbd4R8pM2ed62XJf1laFJnsOXY+gHm3OZkvznntPzMlarLw3aeM8B2DURnmY1o5z4+P//yM+mJaJ9ZRGuQZ0PjKAPKuRDCg6rUlY3011PJAbeGrNScfOgNETJRwfw5NKko8b0/T0cUlVEzNIUNZutjY7O2UG9wA1SAWWGDllcooz4fx/9ArXTjWDSIYPBMR6bZnnCVCIvJhONh7+OaxbBsHlykWzmCY/syNvPiVQ5/DE02Ziy6ivK8ywAnmxekEYUGnkPQ1vE0+Gk8RPduBLLvoSP4ePyX0LMNSHo1574PW6oKsl+pz8G36Bu0UXScwW2Jdk7LQ1/M8WCgh3jo0fzifg1NYggNcwAW1xRQRXi7hsfYhzviwPdjV8EXjCpuXAKY1j+Z/4/Xv3aDOk8I9bEzQGa+H4PC0lLPJsZl2/L18x0V78dtBZZbbdmcQweEh+o1Zhco/AxN1uTW2U5pA7+OWVjQeNCoE6Xm1T2nNAp5xEgYT5E85J4wfJqP538cEzP0pcwQCMxb//ZCCTp/ZDGRIlrZTyQrS3j3acySPe9zmOVKuP6A1GemiMgMBX7faVtSeieGGLyaB8ZHFZ4jr3aRl33aPqU/V35wH69zz6A/nv9rs95B99dLw3LFtcTFzmtAlknwfD5eePBzuD/9XNXwYCxEG+jk9cySAamMsI77Na8H6Z1XAxeP2/zJXqMT6PjndwuARNMZtU0HiOEW+FhmXzg8JXweABM4X+yZiXASUPMxhoXj7oRX/sBsbd+DmJOKZj80nv28uzq98syBD5Nfo9SUdiD7jx37TeA7a546cM3Wf7IfDuIcjV/W+eFzatiOcXddJEaHo30c/6IVu3mrDdfX+yxiGCfV6LBOh87+PdRvufbW9NQwLAr1qMf/urvifpbGTYseg8T7ClmVUrSJpTTiNishj5R9QH51h2qwY3SdQ9T64PVQLsVZKP14/9eOj6C913q1PzcSMMZXWEbco75vGwOMG723r4szeg6LgYqAMAh/sBauEMFjOKhSo+pHsaJnH5sw4PYTDAKmVJdV6xr48oS9uwSLnXetIi80s97Wj4/3v77uQ75RYFsFe0+zkwS6Y8hur12VA7YrlXvbe63nvN7VzgtOESGBM5WBPK7ex1btgux5eOksIUMK5plisi6g6ghsZtbX5cH4Jw6E0sFcINefzs/t4+tndSwQzry3uJp3LS8W9N8z26X5uvHtTrDt4lgom2MNg47T4m/1TRFE8JFzyhmiYbcj/CMwe2MNwcjA8CW1dURXQ0IBE6VagEHpzVo2uyzYj+f7eP0LKFolh7G12Od3gNHA4YpIYgZoVGIy+f48JPfGKmPAvOYIbmv3s5Rf99eQlfCr0Pe/I3tEK0IQPJkh4sf8Uy+8Z/8Dw49g+DmUrS5eB12fj8OfmcZD7cwrPpnsM++DK5UF/TXG612kBnGdh4TEcKZqJwpyrzm1vEZEyKwpfjoM4+gTup+XOUdt3OyTeDKSpfktP3MGlnJhRyJ5dlWzgXBhO1IPDwKr5+P498SDnBcgzEGfXCYX+rmTCv8/jSPEB+xuCdvtMNplZY29tJNkfm+SceW2ra8hACHHslBeSCk+vm+168iRLq7EvAiR1LY9SHm7GTe0U7QtTQK9CuE/3v/0OHmjY7bOEZnfp3EThHzcIwjeNSL5MtCRC4dstW0jl/1VidHKDrvs/WX8zqTOVobOyGIXTZAUg6TNmAX3akHMYzcGvlofCuRdPgs0vWdi9grEFf3x9XMJMldScxVLZwPtNt4I5ucNJ3M4cR8bevFUVFuUUptbd8QAzSlJi5c5+DV4pY7cV2r92g0jlCFuTit6UJLE2pQT4gnBSxBn4rLB3lRFjCwHwgHB+cfrP7Ole+leUn+oRN2lPbQEUqV1XnrDrmOvkqezzAelJkQOvASJJ2k3NPhTFctKvRzflI/tJkil5lWpG0fguxxbEfuC4WNyCMPNpoGKPPqSi6Ee179+Hv6JNH3ahRie7WiisM47r/zybHBBWvC0JZJY1FoWO3SuUT+EE7H39x0OnvN5me9rMSvGs3U2wh1bq6nM1uiGDOFE9ZljNL/GnNrz0N0qZISVQiMhfd7/ZT7Hc2FtaKG5/+pHM2Ne5x7mlzh1OfO8tZUb4riI34LPVel5h4dCO2YLIlmQaT3WRKcLPcriHILBNJHtiiahjpLe13y+Q/2T0jO7xPeaZ13Yfvz+m1dnagZoU0lYVQ6TkSIxQTVGHn9yNAbXEnv84dzrQeSX6Wxqn3e4VPDO4ZbddDY8He8vTsGgII1c+6T186tSpXTH+w6YYXwMxmmozM0+iVQumldvPj7/eIyVz6+8WbzmyHvnt7cAbSwHSrJ7Z2d9yXZ+KepdDxfR5nMhP3f46PdYm4mB5uiYHkeXRrClbCE3joZVnNZ8Q27hFmbvs4U6LkBtcSWuweiHlLF/3P/TUgYXdT8HLpaPOq/oYULrvNa6zMwPRSNHHINnJ3lYq0Tl/3WHU1e65JnHikQpjJgyMdfRtRmJVrWIYWdXrOBQjrOycY2956vPyJLPCwPNFnOUHz9/wraVQOVnIimq7arnqXNc1lTy4vR73gHqq2YzZ/eJbwLR/s8dXhB3Ol7rvCIAld17uRiqZCOzFRghz4Z04H2pLG7GeVdGS3YIj8KEWJQSNJaDfDz7jUIrBKDorsI4iGk9jy07tAizWAk1HGw9L3hs6vOOd5WW5fcdbrNd7CAKGeArU9vTvCx71Z4Ary/QlOJWAKH7uys8PA3YzAikrsBvIB6f4t7n6NSHZU5w+V5P//4WvNn5jk92C3FStiCjE3dIAUYz+92B3z1v/Y87/GB+a5JSzwN3Q9/P7bKUdcKm4xlroWpFmBN8+4lxz6mO1BQEgktWLM8L4M8qP97//nhr4dx9UZB4wVW56RMGnC9N2/zeA8TC4YE9nQuk1bBw/b7K5j3nipAIHs5eePpCFsuP9xfe2kt4q6fTQPBbkPLOSZm+1FlCXRZUqqbinpAHmY/n//rRS3EFyS4C4b2AUNbbdxv/vMPTQUdc9JpXws+LgdjiOfnjDs8yUx6zl+VBXOiTWVyc33k9x6jwR2r3vszpx/XVosJN7kAa4ox01IK2hHYDRH++/IMOes4rstnMQg7Euly3n6z8vMPVrIX32es2y9trmTZM/rjKptpS319y/W6dbHxVQc+vEDwRCqK5y3ymsiGCuDu6EsE4mV8x3Gfpc96N+cZDn4f/v+QgCz7qVkKJfuYstrmuGaDLmF//JmaZ5NVqcPEvV9nUjcp3YQD5TyC8mrBIDBIzydv7/r4BSWCYyPJ12PkVu/W4MerNpMn7twjIz/f/f+UrX/nKV77yla985Stf+cpXvvKVr3zlK1/5yle+8pWvfOUrX/nKV77yla985Stf+cpXvvKVr3zlK1/5yle+8pWvfOUrX/nKV77yla985Stf+cpXvvKVr3zlK1/5yle+8pWvfOUrX/nKV77yla985Stf+cpXvvKVr3zlK1/5yle+8pWvfOUrX/nKV77yla985Stf+cpXvvKVr3zlK1/5yle+8pWvfOUrX/nKV77yFYD/B92aGZl3Kab3AAAyE2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNC4xLWMwMzQgNDYuMjcyOTc2LCBTYXQgSmFuIDI3IDIwMDcgMjI6Mzc6MzcgICAgICAgICI+CiAgIDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+CiAgICAgIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICAgICAgICAgIHhtbG5zOnhhcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyI+CiAgICAgICAgIDx4YXA6Q3JlYXRvclRvb2w+QWRvYmUgRmlyZXdvcmtzIENTMzwveGFwOkNyZWF0b3JUb29sPgogICAgICAgICA8eGFwOkNyZWF0ZURhdGU+MjAxOC0xMS0yOVQyMDo1ODoyN1o8L3hhcDpDcmVhdGVEYXRlPgogICAgICAgICA8eGFwOk1vZGlmeURhdGU+MjAxOS0wNi0xMlQxMjo1NDo1MVo8L3hhcDpNb2RpZnlEYXRlPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIj4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz6FyrvNAAAgAElEQVR4nO1dd5wdVdl+3nNmbtuaHtI2PYQWCBB6hFBU/AQpn4AFlCKKfgqKhU9siGDDT0AQFRBEERSQXkKvISF0SIPUTd2+d2+bmXPe9/tj7r25m+wmu2Fhc5M8+d1fdubOnPPOzHPPec/bhkQE5Yh7H1ZTakbt/YdIoqYeiPz4sIlPrO5vmXZEUDkS5M47aWTt+JH/jFYOOUIpBRGaS+R+a+aUOXP7W7YdDaq/Begt/vh9UlUjqn4aqRhwhHIiEBGw5A4ynLzrucWHTuhv+XY0lB1Bhh+CsTpWeapy4xBrYMVDwh2DQbHpowwnf9Pf8u1oKDuCOFXOEToSryUAAguQoModi+GxmahwR530yLujT+1vGXcklB1BlONO1zoKEgACEFxouLDioTayJ+LO4O8+9NagaH/LuaOgrAjy0lSC0u44aA0G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";
			$data = array(
				't' => urldecode("VTAPP Notificación"),
				'm' => urldecode($message),
				's' => 61,
				'v' => 1,
				'i' => 174,
				'c' => "",
				'd' => $id,
				'u' => urldecode($url),
				'ut' => urldecode($urltitle),
				'p' => $picture,
				'k' => $pk
			);
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);			
		}
		return $result;
	}	
	
}
?>