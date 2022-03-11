<?
// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("country.php");
require_once("resources.php");

class city extends table {
	//Relacion otras clases
	var $country;
	var $view;
	
	//Constructor de la clase
	function __constructor($ciudad = "") {
		$this->city($ciudad);
	}
	
	//Constructor anterior
	function city($ciudad = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_CITY");
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION["vtappcorp_userid"];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Clases relacionadas
		$this->country = new country();
		$this->view = "VIE_CITIES_SUMMARY";		
	}
	
	//Funcion para Set el pais
	function setCountry($value) {
		//Asigna la informacion
		$this->country->ID = $value;
		//Verifica la informacion
		$this->country->__getInformation();
		//Si no hubo error
		if($this->country->nerror == 0) {
			//Asigna el valor
			$this->COUNTRY_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->COUNTRY_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el country
	function getCountry() {
		//Asigna el valor del country
		$this->COUNTRY_ID = $this->country->ID;
		//Busca la informacion
		$this->country->__getInformation();
	}

	//Funcion para obtener la informacion del municipio
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
			//Asigna los otros valores
			$this->setCountry($this->COUNTRY_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion para buscar una ciudad por otra informacion
    function getInformationByOtherInfo($field = "CITY_NAME") {
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

	//Funcion para listar los countrys disponibles
	function showOptionList($tabs = 8,$selected = 0) {		
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			@$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, CITY_NAME FROM " . $this->table . " WHERE COUNTRY_ID = " . $this->_checkDataType("COUNTRY_ID") . " AND IS_BLOCKED = FALSE ORDER BY CITY_NAME";
        //Variable a retornar
        $return = "";
        //Recorre los valores
        foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
            //Si la opcion se encuentra seleccionada
            if($row[0] == $selected)
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
            else
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
        }
        //Retorna
        return $return;
	}

	//Funcion para listar los countrys disponibles
	function showAllOptionList($tabs = 8,$selected = 0) {		
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			@$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT CITY_ID, CONCAT(CITY_NAME,' (',COUNTRY,')') NAME FROM " . $this->view . " WHERE NOT CITY_ID IS NULL ORDER BY 2";
        //Variable a retornar
        $return = "";
        //Recorre los valores
        foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
            //Si la opcion se encuentra seleccionada
            if($row[0] == $selected)
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
            else
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
        }
        //Retorna
        return $return;
	}

	
    //Funcion para generar el JSON 
    function showListJSON() {
		//Arma la sentencia SQL
        $this->sql = "SELECT ID, CITY_NAME FROM " . $this->table . " WHERE COUNTRY_ID = " . $this->_checkDataType("COUNTRY_ID") . " AND IS_BLOCKED = FALSE ORDER BY CITY_NAME";
		//Variable a retornar
		$return = array(array("text" => $_SESSION["SELECT_OPTION"],
						"value" => ""));
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("text" => $row[1],
							"value" => $row[0]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
    }	
	
	//Funcion para devolver las ciudades para autocomplete
	function showAutocomplete($term = '') {
		//Define el resultado
		$result = array();
		//Arma la sentencia SQL
		$this->sql = "SELECT M.ID, D.ID, CONCAT(M.CITY_NAME,' (',D.COUNTRY_NAME,')') NOMBRE FROM " . 
				$this->table . " M INNER JOIN " . $this->country->table . " D ON (M.COUNTRY_ID = D.ID) " .
				"WHERE M.IS_BLOCKED = FALSE AND M.CITY_NAME LIKE '%$term%'";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Los agrega al array
			array_push($result, $row);
		}
		//Retorna
		return($result);
	}
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["CITY_ID", "CITY_NAME", "COUNTRY", "CAPITAL", "CURRENCY", "CURRENCYCODE", "ID"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND NOT CITY_NAME IS NULL";
		else
			$sWhere .= " WHERE NOT CITY_NAME IS NULL";
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(" . $fields[0] . ") FROM $this->view $sWhere";
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
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == $fields[0]) {
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
												
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
		array_push($output['sql'],$this->sql);
		return $output;
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
			$readonly = array("readonly=\"readonly\"", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newCity.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editCity.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteCity.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmCity\" name=\"frmCity\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= $this->showField("CITY_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["COUNTRY_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbCountry\" name=\"cbCountry\" " . $readonly[$cont++] . ">\n";
		$return .= $this->country->showOptionList(8,$showvalue ? $this->COUNTRY_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

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
	
}	

?>