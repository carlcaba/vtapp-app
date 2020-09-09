<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("users.php");
require_once("client.php");
require_once("area.php");
require_once("quota.php");

class quota_employee extends table {
	var $view;
	var $user;
	var $client;
	var $quota;
	var $area;
	
	//Constructor
	function __constructor($user = "") {
		$this->quota_employee($user);
	}
	
	//Constructor anterior
	function quota_employee ($user  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_QUOTA_EMPLOYEE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->USER_ID = $user;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->user = new users($user);
		$this->client = new client();
		$this->quota = new quota();
		$this->area = new area();
		$this->view = "VIE_QUOTA_EMPLOYEE_SUMMARY";		
	}

    //Funcion para Set el usuario
    function setUser($usuario) {
        //Asigna la informacion
        $this->user->ID = $usuario;
        //Verifica la informacion
        $this->user->__getInformation();
        //Si no hubo error
        if($this->user->nerror == 0) {
            //Asigna el valor
            $this->USER_ID = $usuario;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->USER_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Usuario " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el usuario
    function getUser() {
        //Asigna el valor del escenario
        $this->USER_ID = $this->user->ID;
        //Busca la informacion
        $this->user->__getInformation();
    }

    //Funcion para Set el cliente
    function setClient($cliente) {
        //Asigna la informacion
        $this->client->ID = $cliente;
        //Verifica la informacion
        $this->client->__getInformation();
        //Si no hubo error
        if($this->client->nerror == 0) {
            //Asigna el valor
            $this->CLIENT_ID = $cliente;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->CLIENT_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Cliente " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el cliente
    function getClient() {
        //Asigna el valor del escenario
        $this->CLIENT_ID = $this->client->ID;
        //Busca la informacion
        $this->client->__getInformation();
    }
	
    //Funcion para Set el cupo
    function setQuota($quota) {
        //Asigna la informacion
        $this->quota->ID = $quota;
        //Verifica la informacion
        $this->quota->__getInformation();
        //Si no hubo error
        if($this->quota->nerror == 0) {
            //Asigna el valor
            $this->QUOTA_ID = $quota;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->QUOTA_ID = "UUID()";
            //Genera error
            $this->nerror = 20;
            $this->error = "Cupo " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el cupo
    function getQuota() {
        //Asigna el valor del cupo
        $this->QUOTA_ID = $this->quota->ID;
        //Busca la informacion
        $this->quota->__getInformation();
    }

    //Funcion para Set el area
    function setArea($area) {
        //Asigna la informacion
        $this->area->ID = $area;
        //Verifica la informacion
        $this->area->__getInformation();
        //Si no hubo error
        if($this->area->nerror == 0) {
            //Asigna el valor
            $this->AREA_ID = $area;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->AREA_ID = "UUID()";
            //Genera error
            $this->nerror = 20;
            $this->error = "Área " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el area
    function getArea() {
        //Asigna el valor del cupo
        $this->AREA_ID = $this->area->ID;
        //Busca la informacion
        $this->area->__getInformation();
    }

	//Funcion para contar los cupos
	function getTotalCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}
	
	//Funcion para obtener la informacion del cupo
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
			$this->setClient($this->CLIENT_ID);
			$this->setUser($this->USER_ID);
			$this->setQuota($this->QUOTA_ID);
			$this->setArea($this->AREA_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	//Funcion para obtener la informacion del cupo de la vista
	function __getInformationFromView() {
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM $this->view WHERE QUOTA_EMPLOYEE_ID = " . $this->_checkDataType("ID");
		//Verifica la informacion
		return $this->__getAllData();
	}
	
	//Funcion para buscar un cupo por otra informacion
    function getInformationByOtherInfo($field = "USER_ID", $value = "", $field2 = "", $value2 = "") {
        //Arma la sentencia SQL
		if($value == "") {
			$this->sql = "SELECT ID FROM $this->table WHERE $field = " . $this->_checkDataType($field) . " AND IS_BLOCKED = FALSE";
		}
		else {
			$this->sql = "SELECT QUOTA_EMPLOYEE_ID FROM $this->view WHERE $field = '$value' AND IS_BLOCKED = FALSE";
		}
		if($field2 != "" && $value2 != "") {
			$this->sql .= " AND $field2 = '$value2'";
		}			
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "0";
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
			//Valor a retornar
			$return = null;
        }
        else {
            //Asigna el ID
            $this->ID = $row[0];
            //Llama el metodo
            $return = $this->__getInformationFromView();
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
		return $return;
    }
		
	function dataForm($action, $source, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array(	//ID
								"disabled",
								//Client
								"", 
								//Area
								"", 
								//Usuario
								"", 
								//Cupo
								"",
								//Valor
								"",
								//Usado
								"disabled",
								//Bloqueado
								"disabled");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newAddFunds.php";
		}
		else if($action == "edit") {
			$readonly = array(	//ID
								"readonly=\"readonly\"",
								//Client
								"disabled", 
								//Area
								"disabled", 
								//Usuario
								"disabled", 
								//Cupo
								"disabled",
								//Valor
								"",
								//Usado
								"disabled",
								//Bloqueado
								"disabled",
								//Auditoria
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editAddFunds.php";
		}
		else {
			$readonly = array(	//ID
								"disabled",
								//Client
								"disabled", 
								//Area
								"disabled", 
								//Usuario
								"disabled", 
								//Cupo
								"disabled",
								//Valor
								"disabled",
								//Usado
								"disabled",
								//Bloqueado
								"disabled",
								//Auditoria
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteAddFunds.php";
		}

		//Ajusta los campos de acuerdo a la fuente
		switch($source) {
			case "CLIENT":{
				$readonly[0] = "disabled";
				break;
			}
			case "POSITION":
			case "AREA": {
				$readonly[0] = "disabled";
				$readonly[1] = "disabled";
				$readonly[2] = "disabled";
				$readonly[3] = "disabled";
				break;
			}
			case "USER": {
				$readonly[0] = "disabled";
				$readonly[1] = "disabled";
				$readonly[2] = "disabled";
				$readonly[3] = "disabled";
				break;
			}
		}
		//Variable a regresar
		$return = array("tabs" => $stabs,
						"readonly" => $readonly,
						"actiontext" => $actiontext,
						"link" => $link,
						"showvalue" => true,
						"icon" => "<i class=\"fa fa-money\"></i> ",
						"title" => "<span id=\"actionId\"> " . $actiontext . "</span> " . $_SESSION["QUOTA_ASSIGNATION"] . " <small>" . $_SESSION[$source] . "</small>");
		//Retorna
		return $return;
	}
		
	//Funcion que modifica un cupo
	function __modify($usedvalue = 0) {
		//Ajusta la informacion
		$this->IS_BLOCKED = ($this->USED == $this->AMOUNT);
		//Realiza la actualizacion
		parent::_modify();
		//Verifica si no hubo error
		if($this->nerror > 0) {
			return false;
		}
		//Modifica el cupo original
		$this->quota->USED = $this->quota->USED + $usedvalue;
		$this->quota->IS_BLOCKED = ($this->quota->USED == $this->quota->AMOUNT);
		$this->quota->_modify();
		//Verifica si no hubo error
		if($this->quota->nerror > 0) {
			return false;
		}
		//Retorna 
		return true;
	}

	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["SERVICE_ID", "CLIENT_NAME", "REQUESTED_BY", "REQUESTED_ADDRESS", "ZONE_NAME_REQUEST", "DELIVER_TO", "DELIVER_ADDRESS", "ZONE_NAME_DELIVERY", 
				"DELIVERY_TYPE_NAME", "PRICE", "SERVICE_STATE_NAME", "LANGUAGE_ID"];
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
					if($aColumnsBD[$i] == $fields[0]) {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
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
