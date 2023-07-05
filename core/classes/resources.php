<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("language.php");

class resources extends table {
	var $language;
	var $view;
	
	//Constructor
	function __constructor($resource = "") {
		$this->resources($resource);
	}
	
	//Constructor anterior
	function resources($resource = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_RESOURCE");
		//Inicializa los atributos
		$this->RESOURCE_NAME = $resource;
		$this->LANGUAGE_ID = $_SESSION["LANGUAGE"];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relacion con otras clases
		$this->language = new language();
		$this->view = "VIE_RESOURCES_SUMMARY";
	}
	
	//Funcion que carga todos los recursos en variable global
	function loadResources($lang = 0) {
		//Verifica el lenguaje
		if($lang != 0) {
			if(!defined('LANGUAGE'))
				define("LANGUAGE", $lang);
			$_SESSION["LANGUAGE"] = $lang;
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT RESOURCE_NAME, RESOURCE_TEXT FROM $this->table WHERE LANGUAGE_ID = " . 
					$_SESSION["LANGUAGE"] . " AND IS_SYSTEM = TRUE ORDER BY RESOURCE_NAME";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$row[1] = html_entity_decode($row[1]);
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $_SESSION[$row[0]] = utf8_encode($row[1]);
            }
            else {
                //Guarda la informacion en GLOBALS
                $_SESSION[$row[0]] = $row[1];
            }
		}
	}
	
	//Funcion que busca el recurso por nombre
	function getResourceByName($resource = "", $lang = null) {
		//Variable a retornar
		$result = "";
		//Verifica el recurso
		if($resource != "") {
			//Asigna el nombre
			$this->RESOURCE_NAME = $resource;
		}
		if($lang == null)
			$lang = $_SESSION["LANGUAGE"];
		//Arma la sentencia SQL
		$this->sql = "SELECT RESOURCE_TEXT FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME") . " AND LANGUAGE_ID = $lang";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Asigna el error
			$result = $_SESSION["RESOURCE_NOT_FOUND"];
			$this->nerror = 20;
			$this->error = $result;
		}
		else {
			//Asigna el valor
			$result = $row[0];
			$this->nerror = 0;
			$this->error = "";
		}
		//Regresa el valor
		return $result;
	}
	
	//Funcion que busca el recurso por nombre
	function getResourceObjByName($resource = "", $lang = null) {
		//Verifica el recurso
		if($resource != "") {
			//Asigna el nombre
			$this->RESOURCE_NAME = $resource;
		}
		if($lang == null)
			$lang = $_SESSION["LANGUAGE"];
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME") . " AND LANGUAGE_ID = $lang";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Asigna el error
			$this->nerror = 20;
			$this->error = $_SESSION["NO_INFORMATION"];
		}
		else {
			//Asigna el valor
			$this->ID = $row[0];
			$this->__getInformation();
		}
	}
	
	//Funcion que retorna los lenguajes por recurso
	function getLanguageArray($resource = "") {
		//Verifica el recurso
		if($resource != "") {
			//Asigna el nombre
			$this->RESOURCE_NAME = $resource;
		}
		//Arma el sql
		$this->sql = "SELECT LANGUAGE_ID FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME") . " ORDER BY LANGUAGE_ID";
		//Resultado
		$result = array();
		//Recorre los resultados
		foreach($this->__getAllData() as $row) {
			array_push($result,$row[0]);
		}
		//Retorna
		return $result;
		
	}

	//Funcion que busca el recurso por el texto del recurso
	function getResourceObjByText($text = "", $lang = null) {
		//Verifica el recurso
		if($text != "") {
			//Asigna el nombre
			$this->RESOURCE_TEXT = $text;
		}
		if($lang == null)
			if(!empty($_SESSION["LANGUAGE"]))
				$lang = $_SESSION["LANGUAGE"];
			else
				$lang = 1;
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_TEXT = " . $this->_checkDataType("RESOURCE_TEXT") . " AND LANGUAGE_ID = $lang LIMIT 1";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Asigna el error
			$this->nerror = 20;
			$this->error = $_SESSION["NO_INFORMATION"];
		}
		else {
			//Asigna el valor
			$this->ID = $row[0];
			$this->__getInformation();
		}
	}	
	
	//Funcion que genera el siguiente recurso
	function getNextResource() {
		//Selecciona el id
		$id = $this->RESOURCE_NAME;
		//Arma la sentencia sql
		$this->sql = "SELECT CAST(RIGHT(RESOURCE_NAME, LENGTH(RESOURCE_NAME) - LENGTH('$id')) AS UNSIGNED) FROM $this->table WHERE RESOURCE_NAME LIKE '$id%' ORDER BY 1 DESC LIMIT 1";
		//resultado
		$result = 0;
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if($row) {
			//Asigna el valor
			$result = $row[0];
		}
		//Asigna el valor
		$this->RESOURCE_NAME = $id . ++$result;
		
	}
	
	//Funcion para obtener todos los recursos
	function getAllResources($lang = "") {
		//Verifica el lenguaje
		if($lang == "")
			$lang = $_SESSION["LANGUAGE"];
		//Arma la sentencia SQL
		$this->sql = "SELECT RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_NAME FROM $this->view WHERE LANGUAGE_ID = $lang AND LNG_ID = $lang " .
				"AND IS_BLOCKED = FALSE ORDER BY 1";
		//Retorna
		return $this->__getAllData();
	}
	
	//Funcion que retorna el resumen por usuario
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["ID", "RESOURCE_NAME", "RESOURCE_TEXT", "LANGUAGE_NAME", "IS_SYSTEM", "IS_BLOCKED"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(ID) FROM $this->view $sWhere";
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
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD);$i++) {
				if(strpos($aColumnsBD[$i],"ID") !== false) {
					//Verifica el estado para activar o desactivar
					if($aRow[5])
						$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
					else 
						$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
					
					$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
					$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
					$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
											
					$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $view . $edit . $delete . "</div></div>";
					$row[$aColumnsBD[$i]] = $aRow[$i];
					$row[$aColumnsBD[count($aColumnsBD)]] = $action;
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"];
				}
				else if($aColumnsBD[$i] == "IS_SYSTEM") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_YES"] : $_SESSION["MSG_NO"];
				}
				else if($aColumnsBD[$i] != ' ') {
					$row[$aColumnsBD[$i]] = $aRow[$i];
				}
			}
			array_push($output['data'],$row);
		}
		array_push($output['sql'],$this->sql);
		return $output;
	}
	
	//Funcion que muestra la forma
	function showForm($action, $tabs = 5) {
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$addId = false;
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", "", "", "", "", "", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newResource.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "disabled=\"disabled\"", "disabled=\"disabled\"", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editResource.php";
			$addId = true;
		}
		else {
			$readonly = array("readonly=\"readonly\"", "readonly=\"readonly\"", "disabled=\"disabled\"", "disabled=\"true\"", "disabled=\"true\"");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteResource.php";
			$addId = true;
		}
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		$system = ($this->IS_SYSTEM == 1) ? "checked" : "";
		//variable a retornar
		$return = "$stabs<form id=\"frmResource\" name=\"frmResource\" role=\"form\">\n";
		//Muestra la GUI
		$return .= $this->showField("RESOURCE_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("RESOURCE_TEXT", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["LANGUAGE_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbLanguage\" name=\"cbLanguage\" " . $readonly[$cont++] . ">\n";
		$return .= $this->language->showOptionList(8,$showvalue ? $this->LANGUAGE_ID : 0,true,true);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["IS_SYSTEM"] . " *</label>\n";
		$return .= "$stabs\t\t\t\t\t" . $_SESSION["MSG_NO"] . " <input type=\"checkbox\" class=\"js-switch\" id=\"chkSystem\" name=\"chkSystem\" $system " . $readonly[$cont++] . "/> " . $_SESSION["MSG_YES"] . "\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["IS_BLOCKED"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbBlocked\" name=\"cbBlocked\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t\t<option value=\"FALSE\"" . ($this->IS_BLOCKED ? "" : " selected") . ">" . $_SESSION["ACTIVE"] . "</option>\n";
		$return .= "$stabs\t\t\t\t<option value=\"TRUE\"" . ($this->IS_BLOCKED ? " selected" : "") . ">" . $_SESSION["IS_BLOCKED"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		if($addId)
			$return .= "$stabs\t<input type=\"hidden\" id=\"hfId\" name=\"hfId\" value=\"$this->ID\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores para el webservice
	function listData($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT RESOURCE_TEXT FROM $this->view WHERE LANGUAGE_ID = $lang AND IS_BLOCKED = FALSE AND RESOURCE_NAME = 'DELAY_TIME' LIMIT 1";
		//Variable a retornar
		$return = array();
		//Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            return array();
        }
		//Recorre los valores
		foreach(explode(";",$row[0]) as $val) {
			$arrVal = explode(":",$val);
			$data = array("id" => $arrVal[0],
							"delay_time" => $arrVal[1],
							"language" => $lang);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}	
	
}

?>