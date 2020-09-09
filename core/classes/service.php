<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("users.php");
require_once("client.php");
require_once("zone.php");
require_once("service_state.php");
require_once("delivery_type.php");
require_once("vehicle_type.php");

class service extends table {
	var $resources;
	var $view;
	var $user;
	var $client;
	var $request_zone;
	var $deliver_zone;
	var $state;
	var $type;
	var $vehicle;
	
	//Constructor
	function __constructor($service = "") {
		$this->service($service);
	}
	
	//Constructor anterior
	function service ($service  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SERVICE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->REQUESTED_IP = $_SERVER['REMOTE_ADDR'];
		$this->QUANTITY = 1;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->user = new users();
		$this->client = new client();
		$this->request_zone = new zone();
		$this->deliver_zone = new zone();
		$this->state = new service_state();
		$this->type = new delivery_type();
		$this->vehicle = new vehicle_type();
		$this->view = "VIE_SERVICE_SUMMARY";		
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
	
    //Funcion para Set la zona de recogida
    function setRequestZone($zone) {
        //Asigna la informacion
        $this->request_zone->ID = $zone;
        //Verifica la informacion
        $this->request_zone->__getInformation();
        //Si no hubo error
        if($this->request_zone->nerror == 0) {
            //Asigna el valor
            $this->REQUESTED_ZONE = $zone;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->REQUESTED_ZONE = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Zona de recogida " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la zona de recogida
    function getRequestZone() {
        //Asigna el valor del escenario
        $this->REQUESTED_ZONE = $this->request_zone->ID;
        //Busca la informacion
        $this->request_zone->__getInformation();
    }

    //Funcion para Set la zona de entrega
    function setDeliverZone($zone) {
        //Asigna la informacion
        $this->deliver_zone->ID = $zone;
        //Verifica la informacion
        $this->deliver_zone->__getInformation();
        //Si no hubo error
        if($this->deliver_zone->nerror == 0) {
            //Asigna el valor
            $this->DELIVER_ZONE = $zone;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->DELIVER_ZONE = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Zona de entrega " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la zona de entrega
    function getDeliverZone() {
        //Asigna el valor del escenario
        $this->DELIVER_ZONE = $this->deliver_zone->ID;
        //Busca la informacion
        $this->deliver_zone->__getInformation();
    }

    //Funcion para Set el estado
    function setState($state) {
        //Asigna la informacion
        $this->state->ID = $state;
        //Verifica la informacion
        $this->state->__getInformation();
        //Si no hubo error
        if($this->state->nerror == 0) {
            //Asigna el valor
            $this->STATE_ID = $state;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->STATE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Estado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el estado
    function getState() {
        //Asigna el valor del escenario
        $this->STATE_ID = $this->state->ID;
        //Busca la informacion
        $this->state->__getInformation();
    }

    //Funcion para Set el tipo de entrega
    function setDeliveryType($type) {
        //Asigna la informacion
        $this->type->ID = $type;
        //Verifica la informacion
        $this->type->__getInformation();
        //Si no hubo error
        if($this->type->nerror == 0) {
            //Asigna el valor
            $this->DELIVERY_TYPE = $type;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->DELIVERY_TYPE = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Estado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el tipo de entrega
    function getDeliveryType() {
        //Asigna el valor del escenario
        $this->DELIVERY_TYPE = $this->type->ID;
        //Busca la informacion
        $this->type->__getInformation();
    }

    //Funcion para Set el vehiculo
    function setVehicle($vehicle) {
        //Asigna la informacion
        $this->vehicle->ID = $vehicle;
        //Verifica la informacion
        $this->vehicle->__getInformation();
        //Si no hubo error
        if($this->vehicle->nerror == 0) {
            //Asigna el valor
            $this->VEHICLE_TYPE_ID = $vehicle;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->VEHICLE_TYPE_ID = 0;
			//Verifica si el campo es requeridos
			if(explode(",",$this->arrColFlags["VEHICLE_TYPE_ID"])[0] == "NO") {
				//Genera error
				$this->nerror = 20;
				$this->error = "Tipo vehículo " . $_SESSION["NOT_REGISTERED"];
			}
        }
    }
	
    //Funcion para Get el vehiculo
    function getVehicle() {
        //Asigna el valor del escenario
        $this->VEHICLE_TYPE_ID = $this->vehicle->ID;
        //Busca la informacion
        $this->vehicle->__getInformation();
    }

	//Funcion para mostrar las horas disponibles
	function showTimeOptionList() {
		$hour = intval(date("H")) + 1;
		//Calcula las tabs
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		for($i = $hour; $i < 24; $i++) {
			$j = $i + 1;
			$text = ($i > 12 ? $i - 12 : $i);
			$text .= " " . ($i >= 12 ? "PM" : "AM") . " - ";
			$text .= ($j > 12 ? $j - 12 : $j);
			$text .= " " . ($j >= 12 ? "PM" : "AM");
			//Ajusta al diseño segun GUI
			$return .= "$stabs<option value=\"" . $i . "\" data-end=\"" . $j . "\">" . $text . "</option>\n";
		}
		//Retorna
		return $return;
		
	}
	
	//Funcion para contar los asociados
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
	
	//Funcion para obtener la informacion de la categoria
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
			$this->setRequestZone($this->REQUESTED_ZONE);
			$this->setDeliverZone($this->DELIVER_ZONE);
			$this->setState($this->STATE_ID);
			$this->setDeliveryType($this->DELIVERY_TYPE);
			$this->setVehicle($this->VEHICLE_TYPE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	function updateState($state) {
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET STATE_ID = '" . $state . "' WHERE ID = " . $this->_checkDataType("ID");
		//Ejecuta la sentencia
		$this->executeQuery();
	}
	
	function loadCount() {
		//Verifica la informacion
		$this->request_zone->ZONE_NAME = "NO DEFINIDA";
		$this->request_zone->getInformationByOtherInfo();
		$this->state->ID = $this->state->getIdByResource("SERVICE_STATE_1");
		//Arma la sentencia de consulta
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE AND " .
					"REGISTERED_BY = " . $this->_checkDataType("REGISTERED_BY") . " AND " .
					"REQUESTED_ZONE = " . $this->request_zone->ID . " AND " .
					"DELIVER_ZONE = " . $this->request_zone->ID . " AND " .
					"STATE_ID = '" . $this->state->ID . "'";
		//Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
		return $return;	
	}

	function showLoaded() {
		//Verifica la informacion
		$this->request_zone->ZONE_NAME = "NO DEFINIDA";
		$this->request_zone->getInformationByOtherInfo();
		$this->state->ID = $this->state->getIdByResource("SERVICE_STATE_1");
		//Arma la sentencia de consulta
		$this->sql = "SELECT * FROM $this->view WHERE IS_BLOCKED = FALSE AND " .
					"REGISTERED_BY = " . $this->_checkDataType("REGISTERED_BY") . " AND " .
					"REQUESTED_ZONE = " . $this->request_zone->ID . " AND " .
					"DELIVER_ZONE = " . $this->request_zone->ID . " AND " .
					"STATE_ID = '" . $this->state->ID . "'";
		//Variable a retornar
		$return = "";
		$counter = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$return .= "<tr>\n";
			//Requested address
			$return .= "<td>$row[13]</td>\n";
			//Requested zone
			$return .= "<td><input id=\"txtZONE_REQUEST_$counter\" name=\"txtZONE_REQUEST_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $SESSION["START_TYPING_ZONE"] . "\"/></td>\n";
			//Deliver to
			$return .= "<td>$row[16]</td>\n";
			//Deliver address
			$return .= "<td>$row[20]</td>\n";
			//Deliver zone
			$return .= "<td><input id=\"txtZONE_DELIVER_$counter\" name=\"txtZONE_DELIVER_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $SESSION["START_TYPING_ZONE"] . "\"/></td>\n";
			//Type
			$return .= "<td>$row[28]</td>\n";
			//PRICE
			$badge = "<a class=\"badge badge-primary\" href=\"#\" onclick=\"calculate($counter);\">" . $_SESSION["CALCULATE"] . "</a>";
			$return .= "<td><span id=\"spPrice_$counter\">$row[21]</span> $badge</td>\n";
			//Ida y vuelta
			$return .= "<td><input id=\"cbRoundTrip_$counter\" name=\"cbRoundTrip_$counter\" type=\"checkbox\" class=\"form-control\"" . ($row[50] ? "checked" : " ") . " data-toggle=\"toggle\" data-on=\"" . $_SESSION["MSG_YES"] . "\" data-off=\"" . $_SESSION["MSG_NO"] . "\" data-onstyle=\"success\" data-offstyle=\"primary\" /></td>\n";
			//Cliente
			$return .= "<td>";
			//Si el cliente no esta definido
			if($row[3] == "NO DEFINIDO") {
				$return .= "<select id=\"cbClient_$counter\" name=\"cbClient_$counter\" class=\"form-control\">";
				$return .= $this->client->showOptionList();
				$return .= "</select>";
				$ask = "true";
			}
			else {
				$return .= $row[3];
				$ask = "false";
			}
			$return .= "</td>";
			//Actions
			$save = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["SAVE"] . "\" onclick=\"save($counter);\" id=\"btnSave_$counter\" name=\"btnSave_$counter\"><i class=\"fa fa-floppy-o\"></i></button>";
			$view = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $row[0] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
			$delete = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $row[0] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
			$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $view . $save . $delete . "</div></div>";
			//acciones
			$return .= "<td>$action</td>";
			//Hiddens
			$return .= "<input type=\"hidden\" id=\"hfId_$counter\" name=\"hfId_$counter\" value=\"$row[0]\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfZonReq_$counter\" name=\"hfZonReq_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLatReq_$counter\" name=\"hfLatReq_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLngReq_$counter\" name=\"hfLngReq_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfZonDel_$counter\" name=\"hfZonDel_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLatDel_$counter\" name=\"hfLatDel_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLngDel_$counter\" name=\"hfLngDel_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfDistance_$counter\" name=\"hfDistance_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfAskClient_$counter\" name=\"hfAskClient_$counter\" value=\"$ask\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfPrice_$counter\" name=\"hfPrice_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfClientId_$counter\" name=\"hfClientId_$counter\" value=\"$row[2]\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfSaved_$counter\" name=\"hfSaved_$counter\" value=\"false\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfPayed_$counter\" name=\"hfPayed_$counter\" value=\"false\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfObjPay_$counter\" name=\"hfObjPay_$counter\" value=\"\" />\n";
			
			$return .= "</tr>\n";
			//Incrementa contador
			$counter++;
		}
		echo $return;	
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
			$readonly = array(//Step 1
								"disabled", "disabled", 
								"", "",
								"", 
								"", "", 
								//Step 2
								"", "", 
								"", "",
								"",
								"", "",
								//Step 3
								"", "", "", "",
								"", "", "",
								"", "disabled", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newService.php";
		}
		else if($action == "edit") {
			$readonly = array(//Step 1
								"disabled", "disabled", 
								"", "",
								"", 
								"", "", 
								//Step 2
								"", "", 
								"", "",
								"",
								"", "",
								//Step 3
								"", "", "", "",
								"", "", "",
								"", "disabled", ""
								/*
								"readonly=\"readonly\"", "disabled", 
								"", "disabled", "", "", 
								"", "", "", "",
								"", "", 
								"", "", "",
								"disabled", "disabled", "disabled", "disabled" */);
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editService.php";
		}
		else {
			$readonly = array(//Step 1
								"disabled", "disabled", 
								"disabled", "disabled",
								"disabled", 
								"disabled", "disabled", 
								//Step 2
								"disabled", "disabled", 
								"disabled", "disabled",
								"disabled",
								"disabled", "disabled",
								//Step 3
								"disabled", "disabled", "disabled", "disabled",
								"disabled", "disabled", "disabled",
								"disabled", "disabled", "disabled"			
							/*
							"disabled", "disabled", 
							"disabled", "disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled"*/);
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteService.php";
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
		
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["SERVICE_ID", "CLIENT_NAME", "REQUESTED_BY", "REQUESTED_ADDRESS", "ZONE_NAME_REQUEST", "DELIVER_TO", "DELIVER_ADDRESS", "ZONE_NAME_DELIVERY", 
				"DELIVERY_TYPE_NAME", "PRICE", "SERVICE_STATE_NAME", "ID_STATE"];
		//Agrega la clausula WHERE personalizada
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];		
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
			$sWhere .= " AND CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL") {
			$sWhere .= " AND ID_STATE = 2";
		}
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(DISTINCT " . $fields[0] . ") FROM $this->view $sWhere";
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
		$this->sql = "SELECT DISTINCT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
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
						if($aRow[11] == 1) {
							$assign = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ASSIGN"] . "\" onclick=\"assign('" . $aRow[$i] . "');\"><i class=\"fa fa-motorcycle\"></i></button>";
						}
						else {
							$assign = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["INFORMATION"] . "\" onclick=\"information('" . $aRow[$i] . "');\"><i class=\"fa fa-street-view\"></i></button>";
						}
						$history = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["TIMELINE"] . "\" onclick=\"location.href = 'service-log.php?id=" . $aRow[$i] . "';\"><i class=\"fa fa-history\"></i></button>";
						
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $history . $activate . $assign . $view . $edit . $delete . "</div></div>";
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

	//Funcion para mostrar los servicios para asignar
	function showToAssign() {
		//Arma la sentencia SQL
		$this->sql = "SELECT SERVICE_ID, REQUESTED_ADDRESS, DELIVER_ADDRESS, DELIVER_TO, DELIVERY_TYPE_NAME, FRAGILE, ROUND_TRIP, " .
				"LAT_REQUEST_INI, LON_REQUEST_INI, LAT_DELIVERY_INI, LON_DELIVERY_INI, ZONE_NAME_REQUEST, ZONE_NAME_DELIVERY, REQUESTED_ZONE, DELIVER_ZONE ".
				"FROM $this->view WHERE ID_STATE = 1 AND IS_BLOCKED = FALSE AND CLIENT_ID = " . $this->_checkDataType("CLIENT_ID") . " ORDER BY REGISTERED_ON";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"from" => $row[1],
							"to" => $row[2],
							"deliver_to" => $row[3],
							"type" => $row[4],
							"fragile" => $row[5],
							"roundtrip" => $row[6],
							"lat_ini" => $row[7],
							"lng_ini" => $row[8],
							"lat_end" => $row[9],
							"lng_end" => $row[10],
							"zone_ini" => $row[11],
							"zone_end" => $row[12],
							"id_zone_ini" => $row[13],
							"id_zone_end" => $row[14],
							"title" => $row[4] . " " . $_SESSION["TO"] . " " . $row[3]);
			array_push($return,$data);
		}
		return $return;
	}
}

?>
