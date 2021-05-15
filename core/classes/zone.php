<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("city.php");

class zone extends table {
	var $resources;
	var $view;
	var $city;
	
	//Constructor
	function __constructor($zone = "") {
		$this->zone($zone);
	}
	
	//Constructor anterior
	function zone ($zone  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_ZONE");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->city = new city();
		$this->view = "VIE_ZONE_SUMMARY";		
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
            $this->CITY_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Ciudad " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la ciudad
    function getCity() {
        //Asigna el valor del escenario
        $this->CITY_ID = $this->city->ID;
        //Busca la informacion
        $this->city->__getInformation();
    }

	//Funcion para obtener la informacion de la zona
	function __getInformation() {
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
			$this->error = $_SESSION["NOT_REGISTERED"];
			$this->nerror = 20;
		}
		else {
			//Asigna la informacion
			$this->setCity($this->CITY_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion que retorna JSON para autocomplete
	function showAutocompleteOptionList($term, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT ZONE_ID, ZONE_NAME, CONCAT(FULL_STREET,' => ',FULL_AVENUE), PARENT_ZONE, LATITUDE_FROM, LONGITUDE_FROM, PARENT_ZONE_NAME " .
					"FROM $this->view WHERE IS_BLOCKED = FALSE AND ZONE_NAME LIKE '%" . $term . "%' " .
					"ORDER BY 2";
		$this->sql = "SELECT DISTINCT Z.ZONE_ID, Z.ZONE_NAME, CONCAT(Z.FULL_STREET,' => ',Z.FULL_AVENUE), Z.PARENT_ZONE, Z.LATITUDE_FROM, Z.LONGITUDE_FROM, " .
				"(SELECT ZONE_NAME FROM $this->table WHERE ID = Z.PARENT_ZONE LIMIT 1) PARENT_ZONE_NAME " .
				"FROM $this->view Z WHERE Z.IS_BLOCKED = FALSE AND Z.ZONE_NAME LIKE '%" . $term . "%' ORDER BY 2";
					
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
            if(!mb_detect_encoding($row["6"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[6] = utf8_encode($row[6]);
            }
			$text = $row[1];
			$text .= ($row[6] != "") ? " (" . $row[6] . ")" : "";
			$data = array("id" => $row[0],
							"label" => $text,
							"value" => $row[2],
							"parent" => $row[3],
							"lat" => $row[4],
							"lng" => $row[5]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
	}
	
	//Funcion que despliega los valores en una zona
	function showOptionList($tabs = 8,$selected = "", $lang = 0, $parent = true) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		$stabs = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT ZONE_ID, ZONE_NAME, CONCAT(FULL_STREET,' => ',FULL_AVENUE), PARENT_ZONE, LATITUDE_FROM, LONGITUDE_FROM FROM $this->view WHERE IS_BLOCKED = FALSE AND " .
			(!$parent ? "NOT " : "") . "PARENT_ZONE IS NULL ORDER BY 2";
			
		$this->sql = "SELECT DISTINCT Z.ZONE_ID, Z.ZONE_NAME, CONCAT(Z.FULL_STREET,' => ',Z.FULL_AVENUE), Z.PARENT_ZONE, Z.LATITUDE_FROM, Z.LONGITUDE_FROM, " .
				"(SELECT ZONE_NAME FROM $this->table WHERE ID = Z.PARENT_ZONE LIMIT 1) PARENT_ZONE_NAME " .
				"FROM $this->view Z WHERE Z.IS_BLOCKED = FALSE AND " .
			(!$parent ? "NOT " : "") . "PARENT_ZONE IS NULL ORDER BY 2";
			
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			$text = $row[1];
			$child = "";
			$latlon = "";
			if(!$parent) {
				$text .= " (" . $row[6] . ")";
				$child = "data-parent=\"" . $row[3] . "\"";
				$latlon = "data-latitude=\"" . $row[4] . "\" data-longitude=\"" . $row[5] . "\"";
			}
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected $child $latlon>$text</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' $child $latlon>$text</option>\n";
		}
		//Retorna
		return $return == "" ? $this->sql : $return;
	}
	
	//Funcion para buscar una zona por otra informacion
    function getInformationByOtherInfo($field = "ZONE_NAME") {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE $field = " . $this->_checkDataType($field);
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "0";
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
        }
        else {
            //Asigna el ID
            $this->ID = $row[0];
            //Llama el metodo
            $this->__getInformation();
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
    }
	
	//Funcion que activa o habilita a un empleado
	function activate($activate) {
		//Ajusta la informacion
		$this->IS_BLOCKED = ($activate == "true") ? "0" : "1";
		//Realiza la actualizacion
		parent::_modify();
		//Verifica si no hubo error
		if($this->nerror > 0) {
			return false;
		}
		//Retorna 
		return true;
	}
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["EMPLOYEE_ID", "FULL_NAME", "IDNUMBER", "CODE", "EMAIL", "AREA_NAME", "ACCESS_NAME", "COSTCENTER", "IS_BLOCKED", "ACCESS_ID"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND (LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " OR LANGUAGE_ID IS NULL)";
		else
			$sWhere .= " WHERE (LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " OR LANGUAGE_ID IS NULL)";
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(EMPLOYEE_ID) FROM $this->view $sWhere";
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
			"data" => array(),
			"sql" => "");
		
		//Arma la sentencia SQL
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		$output["sql"] = $this->sql;
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == "EMPLOYEE_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[8])
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pencil-square-o\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $view . $edit . $delete . "</div></div>";
						$row[$aColumnsBD[$i]] = $aRow[$i];
						$row[$aColumnsBD[count($aColumnsBD)-1]] = $action;
					}
				}
				else if($aColumnsBD[$i] == "ID") {
					$first = "<input type=\"checkbox\" class=\"flat\" name=\"table_records\" value=\"" . $this->inter->Encriptar($aRow[0]) . "\" data-name=\"$aRow[1]\">";
					$row[$aColumnsBD[$i]] = $first;
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"];
				}
				else if($aColumnsBD[$i] != ' ') {
					$row[$aColumnsBD[$i]] = $aRow[$i];
				}
			}
			array_push($output['data'],$row);
		}
		return $output;
	}
	
	//Funcion que muestra la forma
	function showForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$valcode = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$valcode = $this->getNextCode();
			$link = "core/actions/_save/__newEmployee.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "readonly=\"readonly\"", "readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editEmployee.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled" );
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteEmployee.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmEmployee\" name=\"frmEmployee\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["AREA_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" " . $readonly[$cont++] . ">\n";
		$return .= $this->area->showOptionList(8,$showvalue ? $this->AREA_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ACCESS_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbAccess\" name=\"cbAccess\" " . $readonly[$cont++] . ">\n";
		$return .= $this->access->showOptionList(8,$showvalue ? $this->ACCESS_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= $this->showField("ID_USER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CODE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("FIRST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("LAST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("IDNUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("COSTCENTER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		if($viewData) {
			$return .= $this->showField("REGISTERED_ON", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
			$return .= $this->showField("REGISTERED_BY", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$cont++;
			$cont++;
		}

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
		
		/*
		if($disabled == "")
			$disabled = "disabled";
		else
			$disabled = "";
		*/
		
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
		$return .= $this->showField("IDNUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $disabled);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["AREA_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" $disabled>\n";
		$return .= $this->area->showOptionList(8,$showvalue ? $this->AREA_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";

		//Retorna
		return $return;
	}

	//Funcion para obtener zonas aleatorias
	function getRandomZone($limit = 3) {
		//Arma la sentencia SQL
		$this->sql = "SELECT Z.ZONE_ID, Z.ZONE_NAME, Z.PARENT_ZONE, (SELECT Z1.ZONE_NAME FROM $this->table Z1 WHERE Z1.ID = Z.PARENT_ZONE LIMIT 1) PARENT_ZONE_NAME " .
					"FROM $this->view Z WHERE Z.IS_BLOCKED = FALSE AND NOT Z.PARENT_ZONE IS NULL ORDER BY RAND() LIMIT $limit";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"zone" => $row[1],
							"parent" => $row[2],
							"parent_name" => $row[3],
							"valid" => false);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}

	//Funcion que busca el parent zone 
	function getParentZone($id = 0) {
		//Verifica si hay id
		if($id == 0)
			$id = $this->ID;
		//Arma la sentencia SQL
		$this->sql = "SELECT P.ID, P.ZONE_NAME FROM $this->table T INNER JOIN $this->table P ON (T.PARENT_ZONE = P.ID) WHERE T.ID = " . $this->_checkDataType("ID");
		//Resultado a devolver
		$return = array("id" => 0,
						"name" => "");
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            //Asigna el ID
            $return["id"] = $row[0];
			$return["name"] = $row[1];
        }
		return $return;
	}

	//Funcion que despliega los valores para el webservice
	function listData() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ZONE_ID, ZONE_NAME, PARENT_ZONE, LATITUDE_FROM, LONGITUDE_FROM, LATITUDE_TO, LONGITUDE_TO, CITY_NAME, COUNTRY, FULL_STREET, FULL_AVENUE " .
					"FROM $this->view WHERE IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"zone" => $row[1],
							"parent" => $row[2],
							"latitude_from" => $row[3],
							"longitude_from" => $row[4],
							"latitude_to" => $row[5],
							"longitude_to" => $row[6],
							"city" => $row[7],
							"country" => $row[8],
							"street" => $row[9],
							"avenue" => $row[10]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}	
		
}

?>
