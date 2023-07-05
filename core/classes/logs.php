<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");

class logs extends table {
	var $view;
	var $table2;
	var $vie2;
	
	//Constructor
	function __constructor($trx = "") {
		$this->logs($trx);
	}
	
	//Constructor anterior
	function logs($trx = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_LOG");
		//Inicializa los atributos
		$this->USER_IP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$this->USER_ID = ($_SESSION['vtappcorp_userid'] === NULL) ? "visitante" : $_SESSION['vtappcorp_userid'];
		$this->LOGDATE = "NOW()";
		$this->TEXT_TRANSACTION = $trx;
		$this->view = "VIE_LOG_SUMMARY";
		$this->table2 = "TBL_SYSTEM_TRACE";
		$this->vie2 = "VIE_LOGIN_SUMMARY";
	}
	
	function _add($verify = true, $getid = true) {
		//Llama al add parent
		parent::_add($verify, $getid);
		if($this->TEXT_TRANSACTION == "AUTO-LOGOFF") {
			//Arma la sentencia de insercion
			$this->sql = "UPDATE TBL_SYSTEM_USER SET LOGGED = FALSE WHERE ID = " . $this->_checkDataType("USER_ID");
			//La ejecuta
			$this->executeQuery();
		}
	}
	
	function Login($data = "") {
		//Llama al add parent
		parent::_add(false,true);
		//Verifica los datos
		if($data != "") {
			//Decodifica el valores
			$decode = json_decode($data);
			//Arma la sentencia de insercion
			$this->sql = "INSERT INTO $this->table2 (ID,LOG_ID,TABLE_ORIGIN,RECORD_ID,OLD_RECORD,NEW_RECORD) " .
					"VALUES (0," . $this->_checkDataType("ID") . ",'LOGIN','LOCALIZATION','$data','" . ($decode->lat ."," . $decode->lon) . "')";
			//La ejecuta
			$this->executeQuery();
		}
		//Arma la sentencia de insercion
		$this->sql = "UPDATE TBL_SYSTEM_USER SET LOGGED = TRUE WHERE ID = " . $this->_checkDataType("USER_ID");
		//La ejecuta
		$this->executeQuery();
	}

	function Logout($data = "") {
		//Llama al add parent
		parent::_add(false,true);
		//Verifica los datos
		if($data != "") {
			//Decodifica el valores
			$decode = json_decode($data);
			//Arma la sentencia de insercion
			$this->sql = "INSERT INTO $this->table2 (ID,LOG_ID,TABLE_ORIGIN,RECORD_ID,OLD_RECORD,NEW_RECORD) " .
					"VALUES (0," . $this->_checkDataType("ID") . ",'LOGOUT','LOCALIZATION','$data','" . ($decode->lat ."," . $decode->lon) . "')";
			//La ejecuta
			$this->executeQuery();
		}
		//Arma la sentencia de insercion
		$this->sql = "UPDATE TBL_SYSTEM_USER SET LOGGED = FALSE WHERE ID = " . $this->_checkDataType("USER_ID");
		//La ejecuta
		$this->executeQuery();
	}
	
	function LoginStats($arr) {
		//Arma el SQL
		$this->sql = "SELECT SUBSTRING(PREFIX_ID,1,2), PROFILE_NAME, SUM(LOGINS) " .
					"FROM $this->vie2 " .
					"WHERE PREFIX_ID IN ('" . implode("','", $arr) . "') " .
					"GROUP BY SUBSTRING(PREFIX_ID,1,2)";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Retorna
			return 0;
		}
		else {
			//Retorna
			return $row[2];
		}
	}
	
	//Funcion que busca el ultimo acceso de un usuario
	function getLastAccess() {
		//Arma el SQL
		$this->sql = "SELECT ID FROM $this->table WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " ORDER BY LOGDATE DESC LIMIT 1";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["NO_ACCESS_USER"];
		}
		else {
			//Asigna los atributos
			$this->ID = $row[0];
			//Obtiene la informacion
			$this->__getInformation();
		}
	}

	//Funcion que cuenta los accesos del usuario
	function countAccess() {
		//Arma el SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND TEXT_TRANSACTION = 'LOGIN'";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Retorna
			return 0;
		}
		else {
			//Retorna
			return $row[0];
		}
	}
	
	//Funcion para devolver las conexiones del usuario
	function showAccess() {
		//Define el resultado
		$result = array();
		//Arma la sentencia SQL
		$this->sql = "SELECT LOGDATE, COUNT(ID) FROM $this->table WHERE TEXT_TRANSACTION = 'LOGIN' AND USER_ID = " . $this->_checkDataType("USER_ID") . " GROUP BY LOGDATE ORDER BY LOG_DATE";
		$this->sql = "SELECT DATE(LOGDATE) LOGDATE, SUM(IF(TEXT_TRANSACTION='LOGIN',1,0)) INGRESOS, SUM(IF(TEXT_TRANSACTION='Termina sesi&oacute;n' OR TEXT_TRANSACTION='AUTO-LOGOFF',1,0)) SALIDAS " .
			"FROM $this->table WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " GROUP BY DATE(LOGDATE) ORDER BY LOGDATE";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Los agrega al array
			array_push($result, $row);
		}
		//Retorna
		return($result);
	}
	
	//Funcion que verifica el ultimo acceso segun la IP
	function getLastUser() {
		//Arma el SQL
		$this->sql = "SELECT USER_ID FROM $this->table WHERE DATE(LOGDATE) = CURDATE() AND USER_IP = " . $this->_checkDataType("USER_IP") . " AND TEXT_TRANSACTION = 'AUTO-LOGOFF' ORDER BY ID DESC LIMIT 1";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Retorna
			return "";
		}
		else {
			//Retorna
			return $row[0];
		}
	}
	
	function elapsedTime($timestamp, $precision = 2) {
		$time = time() - $timestamp;
		$a = array($_SESSION["DECADE"] => 315576000, 
					$_SESSION["YEAR"] => 31557600, 
					$_SESSION["MONTH"] => 2629800, 
					$_SESSION["WEEK"] => 604800, 
					$_SESSION["DAY"] => 86400, 
					$_SESSION["HOUR"] => 3600, 
					$_SESSION["MINUTE"] => 60, 
					$_SESSION["SECOND"] => 1);
		$i = 0;
		foreach($a as $k => $v) {
			$$k = floor($time/$v);
			if ($$k) $i++;
			$time = $i >= $precision ? 0 : $time - $$k * $v;
			$s = $$k > 1 ? 's' : '';
			$$k = $$k ? $$k.' '.$k.$s.' ' : '';
			@$result .= $$k;
		}
		return $result ? $result : $_SESSION["NOW"];
	}	
	
	//Funcion para mostrar la informacion del mapa
	function showGraphData() {
		//Arma la sentencia SQL
		$this->sql = "SELECT REC_ORIGINAL, COUNT(DISTINCT USER_ID) FROM $this->view where TEXT_TRANSACTION = 'LOGIN' AND NOT OLD_RECORD IS NULL GROUP BY OLD_RECORD";
		//Define el array de retorno
		$result = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica la informacion 
			$logData = json_decode($row[0]);
			//Arma el array interno
			$data = array("latLng" => array($logData->lat, $logData->lon),
							"name" => $logData->country . " ($row[1])");
			//Lo adiciona
			array_push($result,$data);
		}
		//Retorna
		return json_encode($result);
	}
	
	//Funcion para mostrar la actividad
	function showTimelineActivity($user = "") {
		if($user != "")
			$this->USER_ID = $user;
		//Arma la sentencia sql
		$this->sql = "SELECT * FROM $this->view WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " ORDER BY LOGDATE DESC LIMIT 20" ;
		//Define el resultado
		$return = "";
		$fecha = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica la fecha
			if($fecha != $row[2]) {
				//Genera el nuevo timeline
				$return .= "<li class=\"time-label\"><span class=\"bg-success\">" . date("d M Y",strtotime($row[2])) . "</span></li>\n";
				//Asigna la fecha
				$fecha = $row[2];
			}
			$time = strtotime($row[2] . " " . $row[3]);
			$elap = explode(" ", $this->elapsedTime($time));			
			$return .= "<li><i class=\"" . $row[5] . "\"></i>\n";
			
			//Verifica la accion
			$data = explode(" ",$row[4]);			
			if(!$row[6]) {
				$return .= "<div class=\"timeline-item\">\n";
				$return .= "<span class=\"time\"><i class=\"fa fa-clock-o\"></i> $row[3]</span>\n";
				if($data[0] == "Add") {
					$tbl = explode("/",$row[4]);
					$return .= "<h3 class=\"timeline-header no-border\">" . sprintf($_SESSION["RECORD_ADDED"],$data[1]) . " " . $tbl[1] . "</h3>\n";
				}
				else if($data[0] == "Change") {
					$file = explode("/",end($data));
					$userimg = explode(".",end($file));
					$return .= "<h3 class=\"timeline-header no-border\"><a href=\"profile.php?id=" . $userimg[0] . "\">" . $userimg[0] . "</a> " . $_SESSION["CHANGE_PROFILE_IMAGE"] . "</h3>\n";
					$return .= "<div class=\"timeline-body\"><img class=\"profile-user-img img-fluid img-circle\" src=\"" . end($data) . "\" alt=\"...\" />\n</div>\n";
				}
				else if($data[0] == "LOGIN") {
					$msg = $_SESSION[str_replace(" ","_",$row[4])];
					if($row[8]) {
						$location = json_decode($row[8]);
						$msg .= " Origin: <a href=\"#\">" . $location->query . "</a> - " . $location->country . " - " . $location->countryCode;
					}
					$return .= "<h3 class=\"timeline-header no-border\">$msg</h3>\n";
				}
				else {
					$return .= "<h3 class=\"timeline-header no-border\">" . $_SESSION[str_replace(" ","_",$row[4])] . "</h3>\n";
				}
				$return .= "</div>\n";
			}
			else {
				$return .= "<div class=\"timeline-item\">\n";
				$return .= "<span class=\"time\"><i class=\"fa fa-clock-o\"></i> $row[3]</span>\n";
				$return .= "<h3 class=\"timeline-header\">" . sprintf($_SESSION["RECORD_UPDATED"], $row[6]) . "</h3>\n";
				$return .= "<div class=\"timeline-body collapse\" id=\"collapse_" . $row[0] . "\">\n";
				$return .= "<strong>Id</strong> <code>$row[7]</code><br />\n";
				$return .= "<strong>" . $_SESSION["ORIGINAL_DATA"] . "</strong> <pre>$row[8]</pre>\n";
				$return .= "<strong>" . $_SESSION["NEW_DATA"] . "</strong> <pre>$row[9]</pre>\n";
				$return .= "</div>\n";
				$return .= "<div class=\"timeline-footer\">\n";
				$return .= "<a class=\"btn btn-primary btn-sm\" data-toggle=\"collapse\" href=\"#collapse_" . $row[0] . "\" role=\"button\" aria-expanded=\"false\" aria-controls=\"collapse_" . $row[0] . "\">" . $_SESSION["READ_MORE"] . "</a></div>\n";
				$return .= "</div>\n";
			}
			$return .= "</li>\n";
		}
		//Si no hay actividad
		if($return == "") {
			$return = "<li class=\"time-label\"><span class=\"bg-warning\">" . date("d M Y") . "</span></li>\n";
			$return .= "<li><i class=\"fa fa-calendar-times-o bg-danger\"></i><div class=\"timeline-item\"><h3 class=\"timeline-header no-border\">" . $_SESSION["NO_ACTIVITY"] . "</h3></div></li>\n";
		}
		//Retorna
		return $return;
	}
	
}

?>