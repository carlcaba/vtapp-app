<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("notification_type.php");

class notification extends table {
	var $resources;
	var $view;
	var $type;
	
	//Constructor
	function __constructor($type = "") {
		$this->notification($type);
	}
	
	//Constructor anterior
	function notification ($type = "") {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_NOTIFICATION");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->USER_ID = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->type = new notification_type();
		//Verifica el tipo
		if($type != "" && $type != "ALL")
			$this->setType($type);
		$this->view = "VIE_NOTIFICATION_SUMMARY";		
	}

    //Funcion para Set el tipo
    function setType($type) {
		if(is_numeric($type)) {
			//Asigna la informacion
			$this->type->ID = $type;
			//Verifica la informacion
			$this->type->__getInformation();
		}
		else {
			//Asigna la informacion
			$this->type->TEXT_TYPE = $type;
			//Verifica la informacion
			$this->type->getInformationByOtherInfo();
		}
        //Si no hubo error
        if($this->type->nerror == 0) {
            //Asigna el valor
            $this->TYPE_ID = $this->type->ID;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->TYPE_ID = "0";
            //Genera error
            $this->nerror = 20;
            $this->error = "Notification type " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el tipo
    function getType() {
        //Asigna el valor del escenario
        $this->TYPE_ID = $this->type->ID;
        //Busca la informacion
        $this->type->__getInformation();
    }	

	//Funcion para contar las notificaciones
	function getTotalCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND VIEWED_ON IS NULL";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
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
	
	//Funcion que modifica las notificaciones
	function updateAll() {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET VIEWED_ON = NOW() WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND VIEWED_ON IS NULL";
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Funcion para mostrar la barra de notificaciones
	function showPanel($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT ICON, RESOURCE_TEXT, MIN(REGISTERED_ON), COUNT(NOTIFICATION_ID), TYPE_ID " .
					"FROM $this->view " .
					"WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND LANGUAGE_ID = $lang AND VIEWED_ON IS NULL ".  
					"GROUP BY ICON, RESOURCE_TEXT, TYPE_ID";
		$notis = "";
		$total = 0;
		$linkTo = "notifications.php";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$total += $row[3];
			$title = $row[4] == 1 ? $_SESSION["WARNING"] : $_SESSION["ERROR"];
			$notis .= "<div class=\"dropdown-divider\"></div>";
			$notis .= "<a href=\"" . $linkTo . "?type=" . $row[4] . "\" class=\"dropdown-item\"><i class=\"$row[0] mr-2\"></i> " . $row[3] . " " . $_SESSION["MENU_NEW"] . " " . $title;
			$time = strtotime($row[2]);
			$elap = explode(" ", $this->elapsedTime($time));
			$notis .= "<span class=\"float-right text-muted text-sm\">$elap[0] $elap[1]</span>";
			$notis .= "</a>\n";
		}
		$return = "<div class=\"dropdown-menu dropdown-menu-lg dropdown-menu-right\">\n";
		if($total > 0) {
			$return .= "<span class=\"dropdown-item dropdown-header\">$total " . $_SESSION["NOTIFICATIONS"] . "</span>\n";
			$return .= $notis;
			$return .= "<div class=\"dropdown-divider\"></div>\n" .
						"<a href=\"$linkTo\" class=\"dropdown-item dropdown-footer\">" . $_SESSION["SEE_ALL"] . " " . $_SESSION["NOTIFICATIONS"] . "</a>\n";
		}
		else {
			$return .= "<div class=\"dropdown-divider\"></div>\n" .
						"<a href=\"#\" class=\"dropdown-item dropdown-footer\">" . $_SESSION["DONT_HAVE"] . " " . $_SESSION["NOTIFICATIONS"] . "</a>\n";
		}
		$return .= "</div>\n";
		return $return;
	}
	
	//Funcion para mostrar las notificaciones
	function listTable($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT NOTIFICATION_ID, USER_ID, TYPE_ID, RESOURCE_TEXT, ICON, MESSAGE, SOURCE, REGISTERED_ON, REGISTERED_BY, VIEWED_ON " .
					"FROM $this->view " .
					"WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND LANGUAGE_ID = $lang AND VIEWED_ON IS NULL ";
		//Si hay un tipo definido
		if($this->TYPE_ID != 0)
			$this->sql .= " AND TYPE_ID = " . $this->_checkDataType("USER_ID") . " ";
		//Completa
		$this->sql .= "ORDER BY REGISTERED_ON DESC";
		
		$notis = "";
		$total = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$total++;
			$title = $row[2] == 1 ? $_SESSION["WARNING"] : $_SESSION["ERROR"];
			$class = $row[2] == 1 ? "text-warning" : "text-danger";
			$time = strtotime($row[7]);
			$elap = explode(" ", $this->elapsedTime($time,3));
			$notis .= "<tr>\n";
			//$notis .= "<td><input type=\"checkbox\"></td>\n";
			$notis .= "<td class=\"mailbox-star\" width=\"5%\"><a href=\"#\"><i class=\"$row[4] $class\"></i></a></td>\n";
			$notis .= "<td class=\"mailbox-name\" width=\"20%\"><a href=\"$row[6]\">$row[6]</a></td>\n";
			$notis .= "<td class=\"mailbox-subject\" width=\"40%\">" . $row[3] . ": " . $row[5] . "</td>\n";
			$notis .= "<td class=\"mailbox-attachment\" width=\"15%\">$title</td>\n";
			$notis .= "<td class=\"mailbox-date\" width=\"20%\">$elap[0] $elap[1]</td>\n";
			$notis .= "</tr>\n";
		}
		if($total > 0) {
			$return = $notis;
		}
		else {
			$return .= "<tr><td align=\"center\">" . $_SESSION["NO_NOTIFICATIONS"] . "</td></tr>\n";
		}
		//Actualiza las notificaciones a vistas
		$this->updateAll();
		//Retrona
		return $return;
	}
}

?>
