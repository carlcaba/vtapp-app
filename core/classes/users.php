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
		//Define el nombre de la imagen
		if($large)
			$filename .= "_160x160";
        //Define el path
        $path = "img/users/";
        //Define el valor por defecto
        $name = "user.jpg";
        //Recorre el array
        foreach($ext as $valor) {
            //Define el nombre
            $check = $filename . $valor;
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
	function check() {         
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
		//Verifica el password
		if(!$this->checkUser(Desencriptar($row[1]))) {
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
	function checkUser($pass) {
		//Valida la informacion
		return $pass==$this->THE_PASSWORD;
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
		if($this->acceso->ID == 70) {
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
				"\t<img src=\"" . $web_site . $site_root . "images/logo-mail.gif\" alt=\"Vtapp\" border=\"0\" />\n" .
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
	
	function sendGCM($message, $id) {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array (
				'registration_ids' => array (
						$id
				),
				'data' => array (
						"message" => $message
				)
		);
		$fields = json_encode ( $fields );
		$headers = array (
				'Authorization: key=' . "YOUR_KEY_HERE",
				'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$result = curl_exec($ch);
		echo $result;
		curl_close ($ch);
	}	
	
}
?>