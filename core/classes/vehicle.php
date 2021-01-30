<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("vehicle_type.php");
require_once("employee.php");
require_once("journey.php");

class vehicle extends table {
	var $resources;
	var $view;
	var $type;
	var $employee;
	var $journey;
	
	//Constructor
	function __constructor($vehicle = "") {
		$this->vehicle($vehicle);
	}
	
	//Constructor anterior
	function vehicle ($vehicle  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_VEHICLE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->type = new vehicle_type();
		$this->employee = new employee();
		$this->journey = new journey();
		$this->view = "VIE_VEHICLE_SUMMARY";		
	}

    //Funcion para Set el empleado
    function setEmployee($employee) {
        //Asigna la informacion
        $this->employee->ID = $employee;
        //Verifica la informacion
        $this->employee->__getInformation();
        //Si no hubo error
        if($this->employee->nerror == 0) {
            //Asigna el valor
            $this->EMPLOYEE_ID = $employee;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->EMPLOYEE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Employee " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el empleado
    function getEmployee() {
        //Asigna el valor del escenario
        $this->EMPLOYEE_ID = $this->employee->ID;
        //Busca la informacion
        $this->employee->__getInformation();
    }

    //Funcion para Set el tipo de vehiculo
    function setType($type) {
        //Asigna la informacion
        $this->type->ID = $type;
        //Verifica la informacion
        $this->type->__getInformation();
        //Si no hubo error
        if($this->type->nerror == 0) {
            //Asigna el valor
            $this->VEHICLE_TYPE_ID = $type;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->VEHICLE_TYPE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Vehicle type " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el tipo de vehiculo
    function getType() {
        //Asigna el valor del escenario
        $this->VEHICLE_TYPE_ID = $this->type->ID;
        //Busca la informacion
        $this->type->__getInformation();
    }
	
    //Funcion para Set la jornada
    function setJourney($journey) {
        //Asigna la informacion
        $this->journey->ID = $journey;
        //Verifica la informacion
        $this->journey->__getInformation();
        //Si no hubo error
        if($this->journey->nerror == 0) {
            //Asigna el valor
            $this->JOURNEY_ID = $journey;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->JOURNEY_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Journey " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la jornada
    function getJourney() {
        //Asigna el valor del escenario
        $this->JOURNEY_ID = $this->journey->ID;
        //Busca la informacion
        $this->journey->__getInformation();
    }

	//Funcion para obtener la informacion del vehiculo
	function __getInformation() {
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
            $this->error = "Vehicle " . $_SESSION["NOT_REGISTERED"];
			$this->nerror = 20;
		}
		else {
			//Asigna la informacion
			$this->setType($this->VEHICLE_TYPE_ID);
			$this->setEmployee($this->EMPLOYEE_ID);
			$this->setJourney($this->JOURNEY_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion que despliega los valores en una categoria
	function showOptionList($tabs = 8,$selected = "", $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT VEHICLE_ID, VEHICLE_FULL_NAME, PLATE FROM $this->view WHERE IS_BLOCKED = FALSE AND LANGUAGE_ID = $lang ORDER BY 2"; 
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
				$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . " (" . $row[2] . ")</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . " (" . $row[2] . ")</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores en una categoria
	function showJSONListByUser($user) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT VEHICLE_ID, VEHICLE_FULL_NAME, PLATE, VEHICLE_TYPE_ID, VEHICLE_TYPE_NAME, PARTNER_ID, PARTNER_NAME " .
					"FROM $this->view WHERE IS_BLOCKED = FALSE AND LANGUAGE_ID = $lang AND IDENTIFICATION = '$user' ORDER BY 2"; 
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("vehicle_id" => $row[0],
							"plate" => $row[2],
							"vehicle_full_name" => $row[1],
							"vehicle_type_id" => $row[3],
							"vehicle_type_name" => $row[4],
							"partner_id" => $row[5],
							"partner_name" => $row[6]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
	}



	//Funcion que despliega los valores en una categoria
	function showOptionEmployeeList($tabs = 8,$selected = "", $lang = 0) {
		//Verifica el lenguajebyhu u	QF B
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT VEHICLE_ID, VEHICLE_FULL_NAME, PLATE FROM $this->view WHERE IS_BLOCKED = FALSE AND LANGUAGE_ID = $lang ORDER BY 2"; 
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
				$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . " (" . $row[2] . ")</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . " (" . $row[2] . ")</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion para buscar un empleado por otra informacion
    function getInformationByOtherInfo($field = "PLATE") {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE $field = " . $this->_checkDataType($field);
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "UUID()";
            //Genera el error
            $this->nerror = 10;
            $this->error = "Vehicle " . $_SESSION["NOT_REGISTERED"];
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

	//Funcion para buscar un empleado por otra 
    function getInformationByUserId($userid) {
        //Arma la sentencia SQL
        $this->sql = "SELECT VEHICLE_ID FROM $this->view WHERE USER_ID = '$user' AND IS_BLOCKED = FALSE LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "UUID()";
            //Genera el error
            $this->nerror = 10;
            $this->error = "Vehicle " . $_SESSION["NOT_REGISTERED"];
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
	
	function dataForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", "",
								"", "", "",
								"", "", "",
								"",
								"", "", "",
								"", "", "", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newVehicle.php";
		}
		else if($action == "edit") {
			$readonly = array("disabled", "", "",
								"disabled", "", "", 
								"", "", "",
								"", "", "",
								"",  
								"", "", "",
								"", "", "", "",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editVehicle.php";
		}
		else {
			$readonly = array("disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteVehicle.php";
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
		
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["VEHICLE_ID", "PLATE", "VEHICLE_FULL_NAME", "BRAND", "MODEL", "INSURANCE_COMPANY", "EXPIRATION_DATE", "FULL_NAME", "LANGUAGE_ID"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(VEHICLE_ID) FROM $this->view $sWhere";
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
					if($aColumnsBD[$i] == "VEHICLE_ID") {
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
	
}

?>
