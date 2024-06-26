<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("users.php");
require_once("client.php");
require_once("zone.php");
require_once("service_state.php");
require_once("delivery_type.php");
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
	var $vie2;
	var $vie3;
	
	//Constructor
	function __constructor($service = "") {
		$this->service($service);
	}
	
	//Constructor anterior
	function service ($service  = '') {
		//Llamado al constructor padre
		parent::table("TBL_SERVICE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->REQUESTED_IP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
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
		$this->vie2 = "VIE_NOT_BIDDED_SUMMARY";		
		$this->vie3 = "VIE_SERVICE_LIST_SUMMARY";		
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
    function setRequestZone($zone, $default = false) {
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
			if($default)
				//Asigna valor por defecto
				$this->REQUESTED_ZONE = $this->request_zone->getDefaultZone();
			else {
				//Asigna valor por defecto
				$this->REQUESTED_ZONE = "";
				//Genera error
				$this->nerror = 20;
				$this->error = "Zona de recogida " . $_SESSION["NOT_REGISTERED"];
			}
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
    function setDeliverZone($zone, $default = false) {
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
			if($default)
				//Asigna valor por defecto
				$this->DELIVER_ZONE = $this->deliver_zone->getDefaultZone();
			else {
				//Asigna valor por defecto
				$this->DELIVER_ZONE = "";
				//Genera error
				$this->nerror = 20;
				$this->error = "Zona de entrega " . $_SESSION["NOT_REGISTERED"];
			}
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
		$minute = intval(date("i"));
		$sum = 1;
		if($minute > 30)
			$sum++;
		$hour = intval(date("H")) + $sum;
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
			$return .= "<option value=\"" . $i . "\" data-end=\"" . $j . "\">" . $text . "</option>\n";
		}
		//Retorna
		return $return;
		
	}
	
	//Funcion para contar los asociados
	function getTotalCount() {
		_error_log("Service getTotalCount start at " . date("Y-m-d h:i:s"));		
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(DISTINCT S.ID) FROM $this->table S ";
		$where = "WHERE S.IS_BLOCKED = FALSE ";
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
			$where .= "AND S.CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL") {
			$this->sql .= "INNER JOIN TBL_PARTNER_CLIENT PC ON (PC.CLIENT_ID = S.CLIENT_ID) ";
			$where .= "AND PC.PARTNER_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		else if($_SESSION["vtappcorp_useraccess"] == "VIS") {
			$where .= "AND S.USER_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		$this->sql .= $where;
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
		_error_log("Service getTotalCount finish at " . date("Y-m-d h:i:s"),$this->sql);		
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

	function CheckToCollect() {
		//Arma la sentencia SQL
		$this->sql = "SELECT TO_COLLECT FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("ID");
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = false;
        //Registro existe
        if($row)
			$return = ($row[0] == "1");
		return $return;	
	}

	function getTotal($type = 0, $curmon = true) {
		_error_log("Service getTotal start at " . date("Y-m-d h:i:s"));		
		//Arma la sentencia SQL
		$this->sql = "SELECT SUM(PRICE) FROM $this->table ";
		if($curmon) 
			$this->sql .= "WHERE MONTH(REGISTERED_ON) = MONTH(CURRENT_DATE()) AND YEAR(REGISTERED_ON) = YEAR(CURRENT_DATE())";
		else 
			$this->sql .= "WHERE YEAR(REGISTERED_ON) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(REGISTERED_ON) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row) {
			switch($type) {
			case -1:
				$return = array("0" => floatval($row[0]),
								"1" => floatval($row[0] * 0.7),
								"2" => floatval($row[0] * 0.3));
				break;
			case 0:
				$return = floatval($row[0]);
				break;
			case 1:
				$return = floatval($row[0] * 0.7);
				break;
			case 2:
				$return = floatval($row[0] * 0.3);
				break;
			}
		}
		_error_log("Service getTotal finish at " . date("Y-m-d h:i:s"),$this->sql);		
		return $return;	
	}

	function raiseTotal($type = 0) {
		//Arma la sentencia SQL
		$current = $this->getTotal($type);
		$last = $this->getTotal($type,false);
		//Valor a retornar
		$return = "";
		if($last > 0)
			$percent = $current * 100 / $last;
		else 
			$percent = 0;
		//Verifica valores
		if($percent > 100)
			$return = "<span class=\"description-percentage text-success\"><i class=\"fa fa-caret-up\"></i> " . number_format($percent-100,2,".",",") . "%</span>";
		else if($percent < 100)
			$return = "<span class=\"description-percentage text-danger\"><i class=\"fa fa-caret-down\"></i> " . number_format($percent-100,2,".",",") . "%</span>";
		else 
			$return = "<span class=\"description-percentage text-warning\"><i class=\"fa fa-caret-left\"></i> " . number_format(0,2,".",",") . "%</span>";
		return $return;	
	}
	
	function updateState($state = "", $lp = "") {
		//Verifica el estado
		if($state == "") 
			$state = $this->state->getNextState();
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET STATE_ID = '" . $state . "' WHERE ID = " . $this->_checkDataType("ID");
		_error_log("Update Service: ", $this->sql);
		//Ejecuta la sentencia
		$this->executeQuery();
	}

	function ConditionLoad() {
		return " AND STATE_ID IN ('" . $this->state->getIdByStep(2) . "','" . $this->state->getIdByStep(3) . "') ";
	}
	
	function loadCount() {
		//Verifica la informacion
		$this->request_zone->ZONE_NAME = "NO DEFINIDA";
		$this->request_zone->getInformationByOtherInfo();
		//Arma la sentencia de consulta
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE AND " .
					"REGISTERED_BY = " . $this->_checkDataType("REGISTERED_BY") . " AND " .
					"REQUESTED_ZONE = " . $this->request_zone->ID . " AND " .
					"DELIVER_ZONE = " . $this->request_zone->ID .
					$this->ConditionLoad();
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
		//Arma la sentencia de consulta
		$this->sql = "SELECT * FROM $this->view WHERE IS_BLOCKED = FALSE AND " .
					"REGISTERED_BY = " . $this->_checkDataType("REGISTERED_BY") . " AND " .
					"PAYED = FALSE AND NOTIFIED = FALSE " .
					$this->ConditionLoad();
		_error_log("Testing error log", $this->sql);
		//Variable a retornar
		$return = "";
		$counter = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$blZR = false;
			$blZD = false;
			$return .= "<tr>\n";
			//Requested address
			$return .= "<td id=\"tdREQUESTED_ADDRESS_$counter\">$row[13]</td>\n";
			//Requested zone
			if($row[40] == $this->request_zone->ZONE_NAME) {
				$blZR = true;
				$return .= "<td><input id=\"txtZONE_REQUEST_$counter\" name=\"txtZONE_REQUEST_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $_SESSION["START_TYPING_ZONE"] . "\" /></td>\n";
			}
			else 
				$return .= "<td><input id=\"txtZONE_REQUEST_$counter\" name=\"txtZONE_REQUEST_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $_SESSION["START_TYPING_ZONE"] . "\" value=\"$row[40]\" disabled/></td>\n";
			//Deliver to
			$return .= "<td>$row[16]</td>\n";
			//Deliver address
			$return .= "<td id=\"tdDELIVER_ADDRESS_$counter\">$row[20]</td>\n";
			//Deliver zone
			if($row[48] == $this->request_zone->ZONE_NAME) {
				$return .= "<td><input id=\"txtZONE_DELIVER_$counter\" name=\"txtZONE_DELIVER_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $_SESSION["START_TYPING_ZONE"] . "\"/></td>\n";
				$blZD = true;
			}
			else 
				$return .= "<td><input id=\"txtZONE_DELIVER_$counter\" name=\"txtZONE_DELIVER_$counter\" type=\"text\" class=\"form-control\" placeholder=\"" . $_SESSION["START_TYPING_ZONE"] . "\" value=\"$row[48]\" disabled/></td>\n";
			//Type
			$return .= "<td>$row[28]</td>\n";
			//PRICE
			$badge = "<a class=\"badge badge-primary\" id=\"btnCalculate_$counter\" name=\"btnCalculate_$counter\" href=\"#\" onclick=\"calculate($counter);\">" . $_SESSION["CALCULATE"] . "</a>";
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
			if($row[69] == "" || $row[70] == "")
				$maps = "<button type=\"button\" class=\"btn btn-default\" id=\"btnLocate_$counter\" name=\"btnLocate_$counter\" title=\"" . $_SESSION["COMPLETE_LOCATION"] . "\" onclick=\"completeLocation($counter);\" " . ($blZR && $blZD ? "" : "disabled") . "><i class=\"fa fa-map\"></i></button>";
			else 
				$maps = "";
			$save = "<button type=\"button\" class=\"btn btn-success\" title=\"" . $_SESSION["SAVE"] . "\" onclick=\"save($counter);\" id=\"btnSave_$counter\" name=\"btnSave_$counter\" " . ($blZR && $blZD ? "" : "disabled") . "><i class=\"fa fa-floppy-o\"></i></button>";
			$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $row[0] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
			$delete = "<button type=\"button\" class=\"btn btn-danger\" id=\"btnDelete_$counter\" name=\"btnDelete_$counter\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $row[0] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
			$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $maps . $view . $save . $delete . "</div></div>";
			//acciones
			$return .= "<td>$action</td>";
			//Hiddens
			$return .= "<input type=\"hidden\" id=\"hfId_$counter\" name=\"hfId_$counter\" value=\"$row[0]\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfZonReq_$counter\" name=\"hfZonReq_$counter\" value=\"" . ($blZR ? "" : $row[52]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLatReq_$counter\" name=\"hfLatReq_$counter\" value=\"" . ($blZR ? "" : $row[36]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLngReq_$counter\" name=\"hfLngReq_$counter\" value=\"" . ($blZR ? "" : $row[38]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfZonDel_$counter\" name=\"hfZonDel_$counter\" value=\"" . ($blZD ? "" : $row[53]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLatDel_$counter\" name=\"hfLatDel_$counter\" value=\"" . ($blZD ? "" : $row[44]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfLngDel_$counter\" name=\"hfLngDel_$counter\" value=\"" . ($blZD ? "" : $row[46]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfDistance_$counter\" name=\"hfDistance_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfAskClient_$counter\" name=\"hfAskClient_$counter\" value=\"$ask\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfPrice_$counter\" name=\"hfPrice_$counter\" value=\"" . ($blZR && $blZD ? "" : $row[21]) . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfClientId_$counter\" name=\"hfClientId_$counter\" value=\"$row[2]\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfSaved_$counter\" name=\"hfSaved_$counter\" value=\"" . ($blZR && $blZD ? "false" : "true") . "\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfPayed_$counter\" name=\"hfPayed_$counter\" value=\"false\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfObjPay_$counter\" name=\"hfObjPay_$counter\" value=\"\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfToPay_$counter\" name=\"hfToPay_$counter\" value=\"false\" />\n";
			$return .= "<input type=\"hidden\" id=\"hfUserId_$counter\" name=\"hfUserId$counter\" value=\"$row[1]\" />\n";
			$return .= "</tr>\n";
			//Incrementa contador
			$counter++;
		}
		echo $return;
		return $counter;
	}
	
	function dataForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		$stabs = "";
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
	
	//Funcion para verificar datos adicionales
	function getAditionalData() {
		$fields = ["SERVICE_ID", "CLIENT_NAME", "REQUESTED_BY", "REQUESTED_ADDRESS", "ZONE_NAME_REQUEST", "DELIVER_TO", "DELIVER_ADDRESS", "ZONE_NAME_DELIVERY", 
				"DELIVERY_TYPE_NAME", "PRICE", "SERVICE_STATE_NAME", "NOTIFIED", "PAYED", "ICON_STATE", "ID_STATE", "DATE_FORMAT(SUBTIME(STR_TO_DATE(TIME_START_TO_DELIVER,'%H'),'00:30:00'),'%l:%i %p')", "DATE_FORMAT(STR_TO_DATE(TIME_FINISH_TO_DELIVER,'%H'),'%l:%i %p')", "CLIENT_PAYMENT_TYPE", "PARTNER_NAME", "NUMERIC_ID"];
		$this->sql = "SELECT DISTINCT " . str_replace(" , "," ",implode(", ",$fields)) . " FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("ID");
		$row = $this->__getData();
		return $row;
	}

	//Funcion que muestra los servicios de un perfil
	function showCards($reference) {
		$result = "<div class=\"card-body\">\n" .
					"<div>\n" .
					"<div class=\"btn-group w-100 mb-2\">\n" .
					"<a class=\"btn btn-default active\" href=\"javascript:void(0)\" title=\"" . $_SESSION["ALL_ITEMS"] . "\" data-filter=\"all\"> " . $_SESSION["ALL_ITEMS"] . " </a>\n";
		$sWhere = "";
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
			$sWhere = "WHERE CLIENT_ID = '$reference'";
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL") {
			$sWhere = "WHERE PARTNER_ID = '$reference'";
		}
		else if($_SESSION["vtappcorp_useraccess"] == "VIS") {
			$sWhere = "WHERE USER_ID = '$reference'";
		}
		$this->sql = "SELECT DISTINCT SERVICE_STATE_NAME, ID_STATE, ICON_STATE, COUNT(SERVICE_ID) " .
				"FROM $this->view " . $sWhere .
				" GROUP BY SERVICE_STATE_NAME, ID_STATE, ICON_STATE " .
				"ORDER BY 4 DESC LIMIT 4";
		_error_log($this->sql);
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$result .= "<a class=\"btn btn-default bg-light\" href=\"javascript:void(0)\" title=\"" . $_SESSION["STATUS"] . " " . $row[0] . "\" data-filter=\"$row[1]\"> <i class=\"$row[2]\"></i>&nbsp; " . $_SESSION["STATUS"] . " $row[0] ($row[3])</a>\n";
		}
		$result .= "</div>\n";
		$this->sql = "SELECT DISTINCT SERVICE_ID, USER_ID, CLIENT_NAME, DELIVER_TO, DELIVER_ADDRESS, REQUESTED_BY, SERVICE_STATE_NAME, ID_STATE, ICON, ICON_STATE, DATE_FORMAT(REGISTERED_ON,'%Y%m%d') " .
			"FROM $this->view $sWhere";
		_error_log($this->sql);
		$result .= "<div class=\"mb-2\">\n" .
			"<a class=\"btn btn-secondary\" href=\"javascript:void(0)\" data-shuffle> Reordenar </a>\n" .
			"<div class=\"float-right\">\n" .
			"<select class=\"custom-select\" style=\"width: auto;\" data-sortOrder>\n" .
			"<option value=\"state\"> Ordenar por estado </option>\n" .
			"<option value=\"destiny\"> Ordenar por destinatario </option>\n" .
			"<option value=\"date\"> Ordenar por fecha </option>\n" .
			"</select>\n" .
			"<div class=\"btn-group\">\n" .
			"<a class=\"btn btn-default\" href=\"javascript:void(0)\" data-sortAsc> Ascendente </a>\n" .
			"<a class=\"btn btn-default\" href=\"javascript:void(0)\" data-sortDesc> Descendente </a>\n" .
			"</div>\n" .
			"</div>\n" .
			"</div>\n";
		$result .= "<div>\n<div class=\"filter-container p-0 row\">\n";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$state = str_replace(" ","-",$row[6]);
			$result .= "<div class=\"filtr-item col-sm-3\" data-category=\"$row[7]\" data-sort=\"$state sample\" data-state=\"$row[7]\" data-destiny=\"$row[3]\" data-date=\"$row[10]\">\n";
				$result .= "<div class=\"card card-primary card-outline direct-chat direct-chat-primary\">\n";
					$result .= "<div class=\"card-header\">\n";
						$result .= "<h3 class=\"card-title text-truncate\" style=\"max-width: 280px\" title=\"$row[3]\"><i class=\"$row[9]\" title=\"$row[6]\"></i>&nbsp;$row[3]</h3>\n";
						$result .= "<div class=\"card-tools\">\n";
							$result .= "<button type=\"button\" title=\"" . $_SESSION["MINIMIZE"] . "\" class=\"btn btn-tool\" data-card-widget=\"collapse\">\n";
								$result .= "<i class=\"fa fa-minus\"></i>\n";
							$result .= "</button>\n";
							/*
							$result .= "<button type=\"button\" title=\"" . $_SESSION["TIMELINE"] . "\" class=\"btn btn-tool\" onclick=\"location.href='service-log.php?id=$row[0]'\">\n";
								$result .= "<i class=\"fa fa-history\"></i>\n";
							$result .= "</button>\n";
							$result .= "<button type=\"button\" class=\"btn btn-tool\" title=\"". "Información" . "\">\n";
								$result .= "<i class=\"fa fa-info\"></i>\n";
							$result .= "</button>\n";
							$result .= "<button type=\"button\" title=\"" . $_SESSION["DELETE"] . "\" class=\"btn btn-tool\" data-card-widget=\"remove\">\n";
								$result .= "<i class=\"fa fa-trash\"></i>\n";
							$result .= "</button>\n";
							*/
						$result .= "</div>\n";
					$result .= "</div>\n";
					$result .= "<div class=\"card-body\">\n";
						$result .= "<a href=\"javascript:void(0);\" data-toggle=\"lightbox\" data-title=\"" . $_SESSION["DESTINY"] . ": $row[3]\" data-height=\"490\" data-remote=\"core/actions/_load/__loadInfoService.php?id=$row[0]\">\n";
							$result .= "<div class=\"direct-chat-messages\">\n";
								$result .= "<div class=\"direct-chat-msg\">\n";
									$result .= "<div class=\"direct-chat-infos clearfix\">\n";
										$result .= "<span class=\"float-left\">" . $_SESSION["DESTINY"] . "</span>\n";
									$result .= "</div>\n";
									$result .= "<div class=\"direct-chat-timestamp\">$row[4]</div>\n";
								$result .= "</div>\n";
								$result .= "<div class=\"direct-chat-msg\">\n";
									$result .= "<div class=\"direct-chat-infos clearfix\">\n";
										$result .= "<span class=\"float-left\">" . $_SESSION["STATUS"] . "</span>\n";
									$result .= "</div>\n";
									$result .= "<div class=\"direct-chat-timestamp\">$row[6]</div>\n";
								$result .= "</div>\n";
								$result .= "<div class=\"direct-chat-msg\">\n";
									$result .= "<div class=\"direct-chat-infos clearfix\">\n";
										$result .= "<span class=\"float-left\">" . $_SESSION["SERVICE_TABLE_TITLE_3"] . "</span>\n";
									$result .= "</div>\n";
									$result .= "<div class=\"direct-chat-timestamp\">$row[5]</div>\n";
								$result .= "</div>\n";
							$result .= "</div>\n";
						$result .= "</div>\n";
					$result .= "</a>\n";
				$result .= "</div>\n";
			$result .= "</div>\n";
		}
		$result .= "</div>\n</div>\n";
		return $result;
	}
		
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["SERVICE_ID", "CLIENT_NAME", "REQUESTED_BY", "REQUESTED_ADDRESS", "ZONE_NAME_REQUEST", "DELIVER_TO", "DELIVER_ADDRESS", "ZONE_NAME_DELIVERY", 
				"DELIVERY_TYPE_NAME", "PRICE", "SERVICE_STATE_NAME", "NOTIFIED", "PAYED", "ICON_STATE", "ID_STATE", "TO_COLLECT"];
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
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "VI") {
			$sWhere .= " AND USER_ID = '" . $_SESSION["vtappcorp_userid"] . "'";
		}
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(DISTINCT " . $fields[0] . ") FROM $this->vie3 $sWhere";
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
		$this->sql = "SELECT DISTINCT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->vie3 $sWhere $sOrder $sLimit";
		$output["sql"] = $this->sql;
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-2;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == $fields[0]) {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
							$activate = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["ACTIVATE"] . "\" type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["DEACTIVATE"] . "\" type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["VIEW"] . "\" type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["EDIT"] . "\" type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["DELETE"] . "\" type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
						$actBid = ($aRow[11] == "0" && ($aRow[12] == "1" || $aRow[15] == "1")) ? "" : "disabled";
						$actPay = ($aRow[12] == "1" || $aRow[15] == "1") ? "disabled" : "";
						$payed = "";
						if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL")
							$bid = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["NOTIFY"] . "\" type=\"button\" class=\"btn btn-success\" name=\"bidBtn" . $aRow[0] . "\" id=\"bidBtn" . $aRow[0] . "\" title=\"" . $_SESSION["NOTIFY"] . "\" onclick=\"startBid('" . $aRow[$i] . "');\" $actBid><i class=\"fa fa-gavel\"></i></button>";
						else if($_SESSION["vtappcorp_useraccess"] == "GOD") {
							$bid = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["NOTIFY"] . "\" type=\"button\" class=\"btn btn-success\" name=\"bidBtn" . $aRow[0] . "\" id=\"bidBtn" . $aRow[0] . "\" title=\"" . $_SESSION["NOTIFY"] . "\" onclick=\"startBid('" . $aRow[$i] . "');\" $actBid><i class=\"fa fa-gavel\"></i></button>";
							$payed = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["MARK_AS_PAYED"] . "\" type=\"button\" class=\"btn btn-default\" name=\"payBtn" . $aRow[0] . "\" id=\"payBtn" . $aRow[0] . "\" title=\"" . $_SESSION["MARK_AS_PAYED"] . "\" onclick=\"markAsPayed('" . $aRow[$i] . "');\" $actPay><i class=\"fa fa-credit-card\"></i></button>";
						}
						else	
							$bid = "";
						if($aRow[11] == 1) {
							$assign = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["ASSIGN"] . "\" type=\"button\" class=\"btn btn-default\" name=\"assBtn" . $aRow[0] . "\" id=\"assBtn" . $aRow[0] . "\" title=\"" . $_SESSION["ASSIGN"] . "\" onclick=\"assign('" . $aRow[$i] . "');\" $actBid><i class=\"fa fa-motorcycle\"></i></button>";
						}
						else {
							$assign = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . "Información" . "\" type=\"button\" class=\"btn btn-default\" name=\"assBtn" . $aRow[0] . "\" id=\"assBtn" . $aRow[0] . "\" title=\"" . "Información" . "\" onclick=\"information('" . $aRow[$i] . "');\" $actBid><i class=\"fa fa-street-view\"></i></button>";
						}
						$history = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["TIMELINE"] . "\" type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["TIMELINE"] . "\" onclick=\"location.href = 'service-log.php?id=" . $aRow[$i] . "';\"><i class=\"fa fa-history\"></i></button>";
						
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $history . $activate . $payed . $bid . $assign . $view . $edit . $delete . "</div></div>";
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
	
	//Funcion para mostrar el proceso de asignar servicio
	// SAA = Service Already Assigned
	function processAssign($step = 1, $SAA = 7) {
		$datZone = $this->request_zone->getRandomZone();
		$parZone = $this->request_zone->getParentZone();
		array_push($datZone, array("id" => $this->request_zone->ID,
							"zone" => $this->request_zone->ZONE_NAME,
							"parent" => $parZone["id"],
							"parent_name" => $parZone["name"],
							"valid" => true));
		array_push($datZone, array("id" => -1,
							"zone" => $_SESSION["NO_ZONE_DEFINED"],
							"parent" => -1,
							"parent_name" => $_SESSION["NO_PARENT_ZONE_DEFINED"],
							"valid" => true));
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT CLIENT_ID,CLIENT_IDENTIFICATION,CLIENT_NAME,DELIVER_ADDRESS,DELIVER_CELLPHONE,DELIVER_DESCRIPTION,DELIVER_EMAIL,DELIVER_PHONE,DELIVER_TO,DELIVER_ZONE,DELIVERY_CITY_ID," .
							"DELIVERY_CITY_NAME,DELIVERY_COUNTRY,DELIVERY_TYPE_ID,DELIVERY_TYPE_NAME,FRAGILE,ICON,ID_STATE,LANGUAGE_ID,LAT_DELIVERY_END,LAT_DELIVERY_INI," .
							"LAT_REQUEST_END,LAT_REQUEST_INI,LON_DELIVERY_END,LON_DELIVERY_INI,LON_REQUEST_END,LON_REQUEST_INI,OBSERVATION,PRICE,QUANTITY,REGISTERED_BY," .
							"REGISTERED_ON,REQUEST_CITY_ID,REQUEST_CITY_NAME,REQUEST_COUNTRY,REQUESTED_ADDRESS,REQUESTED_BY,REQUESTED_CELLPHONE,REQUESTED_EMAIL,REQUESTED_PHONE,REQUESTED_ZONE," .
							"ROUND_TRIP,SERVICE_ID,SERVICE_STATE_NAME,STATE_ID,TIME_FINISH_TO_DELIVER,TIME_START_TO_DELIVER,TOTAL_HEIGHT,TOTAL_LENGTH,TOTAL_WEIGHT,TOTAL_WIDTH," . 
							"USER_ID,VEHICLE_TYPE_ID,VEHICLE_TYPE_NAME,ZONE_NAME_DELIVERY,ZONE_NAME_REQUEST,CLIENT_PAYMENT_TYPE_ID,CLIENT_PAYMENT_TYPE,IS_MARCO,QUOTA_AVAILABLE, " .
							"TIMESTAMP_START_TO_PICK, TIMESTAMP_START_TO_DELIVER, TIMESTAMP_FINISH_TO_DELIVER " .
					"FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("ID");
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$validate = array();
			//Si el servicio ya fue asignado
			if($row[17] == $SAA) {
				$data = array("id" => $row[42],
								"status" => $_SESSION["SERVICE_ALREADY_ASSIGNED"]);
				array_push($return,$data);
				//Genera el error
				$this->nerror = 187;
				$this->error = $_SESSION["SERVICE_ALREADY_ASSIGNED"];
				break;
			}
			$data = array("id" => $row[42],	//ServiceId
					"client_id" => $row[0], //ClientId
					"client_identification" => $row[1], //ClientIdentification
					"client_name" => $row[2], //ClientName
					"deliver_to" => $row[8], //DeliverName
					"requested_by" => $row[36],
					"requested_address" => $row[35],
					"requested_cellphone" => $row[37],
					"requested_email" => $row[38],
					"requested_lat" => $row[22],
					"requested_lon" => $row[26],
					"requested_zone_id" => $row[40],
					"requested_zone_name" => $row[55],
					"requested_parent_zone_id" => $parZone["id"],
					"requested_parent_zone_name" => $parZone["name"],
					"requested_city_id" => $row[32],
					"requested_city_name" => $row[33],
					"delivery_type_id" => $row[13],
					"delivery_type_name" => $row[14],
					"quantity" => $row[29],
					"height" => $row[47],
					"width" => $row[50],
					"length" => $row[48],
					"weight" => $row[49],
					"fragile" => $row[15],
					"roundtrip" => $row[41],
					"notes" => $row[27],
					"time_start" => $row[46],
					"time_finish" => $row[45],
					"vehicle_type_id" => $row[52],
					"vehicle_type_name" => $row[53],
					"vehicle_icon" => $row[16],
					"today" => date("y-m-d h:i:sa"));
			$parZoneD = $this->deliver_zone->getParentZone();
			switch($step) {
				//Paso 1
				case 1: {
					foreach($datZone as $value) {
						array_push($validate, array("id" => $value["id"],
													"text" => $value["zone"] . " (" . $value["parent_name"] . ")",
													"valid" => $value["valid"]));
					}
					break;
				}
				case 2: {
					$times = array();
					$now = time();
					for($int = 15;$int < 50;$int+=10)
						array_push($times,date("h:i A", $now + ($int * 60)));
					$sel = $times[0];
					/*
					$timePick = strtotime(date("Y-m-d") . " " . $row[60]);
					$timeRequest = strtotime(date("Y-m-d") . " " . $row[61]);
					for($i=0;$i<5;$i++) {
						$tm = mt_rand($timePick,$timeRequest);
						array_push($times,date("h:i A",$tm));
					}
					$times[mt_rand(0,4)] = date("h:i A",$timePick);
					*/
					shuffle($times);
					foreach($times as $key => $value) {
						array_push($validate, array("id" => ($key + 1) ,
													"text" => $value,
													"valid" => $value == $sel));
						/*
						if($key+1 < count($times)) {
							array_push($validate, array("id" => ($key + 1) ,
														"text" => sprintf($_SESSION["TIME_PICK_UP"],$value,$times[$key+1],$_SESSION["MINUTES"]),
														"valid" => $value == 30));
						}
						else {
							array_push($validate, array("id" => ($key + 1) ,
														"text" => sprintf($_SESSION["TIME_PICK_UP_MORE"],1,$_SESSION["HOUR"]),
														"valid" => false));
						}
						*/
					}

					//Agrega los campos del paso
					$data["deliver_address"] = $row[3];
					$data["deliver_cellphone"] = $row[4];
					$data["deliver_email"] = $row[6];
					$data["deliver_lat"] = $row[20];
					$data["deliver_lon"] = $row[24];
					$data["deliver_zone_id"] = $row[9];
					$data["deliver_zone_name"] = $row[54];
					$data["deliver_parent_zone_id"] = $parZoneD["id"];
					$data["deliver_parent_zone_name"] = $parZoneD["name"];
					$data["deliver_city_id"] = $row[10];
					$data["deliver_city_name"] = $row[11];
					$data["deliver_description"] = $row[5];
					break;
				}
				case 3: {
					/*
					$maxtime = mktime($this->TIME_FINISH_TO_DELIVER,0,0,intval(date("m")),intval(date("d")),intval(date("Y")));
					$now = mktime(intval(date("H")),0,0,intval(date("m")),intval(date("d")),intval(date("Y")));
					$hourdiff = round(($maxtime - $now)/3600, 1);
					$times = array(1,2,3);
					$istrue = false;
					*/
					$times = array();
					$timeRequest = strtotime(date("Y-m-d") . " " . $row[62]);
					for($int = 15;$int < 50;$int+=10)
						array_push($times,date("h:i A", $timeRequest + ($int * 60)));					
					$sel = $times[3];
					/*
					$timePick = strtotime(date("Y-m-d") . " " . $row[61]);
					$timeRequest = strtotime(date("Y-m-d") . " " . $row[62]);					
					for($i=0;$i<3;$i++) {
						$tm = mt_rand($timePick,$timeRequest);
						array_push($times,date("h:i A",$tm));
					}
					$times[mt_rand(0,2)] = date("h:i A",$timeRequest);
					*/
					shuffle($times);
					foreach($times as $key => $value) {
						array_push($validate, array("id" => ($key + 1) ,
													"text" => $value,
													"valid" => $value == $sel));
						/*
						if($key == 0) {
							array_push($validate, array("id" => ($key + 1),				
														"text" => $value . " " . $_SESSION["HOUR"],
														"valid" => $hourdiff == $value));					
						}
						else {
							array_push($validate, array("id" => ($key + 1) ,
														"text" => sprintf($_SESSION["TIME_PICK_UP"],$times[$key - 1],$value,$_SESSION["HOURS"]),
														"valid" => (($key == 2 && $hourdiff > $value) ? true : $hourdiff == $value)));
						}
						*/
					}
					$parZoneD = $this->deliver_zone->getParentZone();

					//Agrega los campos del paso
					$data["deliver_address"] = $row[3];
					$data["deliver_cellphone"] = $row[4];
					$data["deliver_email"] = $row[6];
					$data["deliver_lat"] = $row[20];
					$data["deliver_lon"] = $row[24];
					$data["deliver_zone_id"] = $row[9];
					$data["deliver_zone_name"] = $row[54];
					$data["deliver_parent_zone_id"] = $parZoneD["id"];
					$data["deliver_parent_zone_name"] = $parZoneD["name"];
					$data["deliver_city_id"] = $row[10];
					$data["deliver_city_name"] = $row[11];
					$data["deliver_description"] = $row[5];
					$data["pay_on_deliver"] = $row[58] == "0";	//Si es contrato marco
					$data["price"] = ($row[58] == "0" ? $row[28] : 0); //Si NO es contrato marco, enviar precio
					break;
				}
				//Asignar el servicio a este usuario
				case 4: {
					array_push($validate, array("id" => -1 ,
												"text" => "TO_ASSIGN",
												"valid" => true));
					//Agrega los campos del paso
					$data["deliver_address"] = $row[3];
					$data["deliver_cellphone"] = $row[4];
					$data["deliver_email"] = $row[6];
					$data["deliver_lat"] = $row[20];
					$data["deliver_lon"] = $row[24];
					$data["deliver_zone_id"] = $row[9];
					$data["deliver_zone_name"] = $row[54];
					$data["deliver_parent_zone_id"] = $parZoneD["id"];
					$data["deliver_parent_zone_name"] = $parZoneD["name"];
					$data["deliver_city_id"] = $row[10];
					$data["deliver_city_name"] = $row[11];
					$data["deliver_description"] = $row[5];
					$data["pay_on_deliver"] = $row[58] == "0";	//Si es contrato marco
					$data["price"] = ($row[58] == "0" ? $row[28] : 0); //Si NO es contrato marco, enviar precio
				}
			}
			$data["validation"] = $validate;
			array_push($return,$data);
		}
		//Verifica si el array esta vacio
		if(empty($return)) {
			$this->nerror = 185;
			$this->error = "Service " . $_SESSION["NOT_REGISTERED"];
		}
		return $return;
	}
	
	function isPayed() {
		//Arma la sentencia SQL
		$this->sql = "SELECT PAYED FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("ID") . " LIMIT 1";
		//Valor a retornar
		$return = false;
		//Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row)
			$return = $row[0] == "1";
		return $return;	
		
	}
	
	function GetCoordinates($url, $key, $field = "REQUESTED") {
		$result = "";
		$data = null;
		$this->nerror = 0;
		$this->error = "";
		try {
			$url = sprintf($url,rawurlencode($this->arrColDatas[$field . "_ADDRESS"]),$key);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
			$result = curl_exec($ch);
			curl_close ($ch);
			$data = json_decode($result);
			switch(json_last_error()) {
				case JSON_ERROR_NONE:
					break;
				case JSON_ERROR_DEPTH:
					throw new Exception('JSON DECODE ERROR - Excedido tamaño máximo de la pila\n' . $result);
					break;
				case JSON_ERROR_STATE_MISMATCH:
					throw new Exception('JSON DECODE ERROR - Desbordamiento de buffer o los modos no coinciden\n' . $result);
					break;
				case JSON_ERROR_CTRL_CHAR:
					throw new Exception('JSON DECODE ERROR - Encontrado carácter de control no esperado\n' . $result);
					break;
				case JSON_ERROR_SYNTAX:
					throw new Exception('JSON DECODE ERROR - Error de sintaxis, JSON mal formado\n' . $result);
					break;
				case JSON_ERROR_UTF8:
					throw new Exception('JSON DECODE ERROR - Caracteres UTF-8 malformados, posiblemente codificados de forma incorrecta\n' . $result);
					break;
				default:
					throw new Exception('JSON DECODE ERROR - Error desconocido\n' . $result);
				break;
			}			
			if($data->status != "OK") {
				throw new Exception($data->error_message);
			}
			if(!property_exists($data[0],"geometry")) {
				throw new Exception("No geometry found in address from GoogleMaps");
			}
			if(!property_exists($data[0]->geometry,"location")) {
				throw new Exception("No geometry.location found in address from GoogleMaps");
			}
		}
		catch (Exception $ex) {
			$this->nerror = 110;
			$this->error = $ex->getMessage();
			_error_log("Error getting coordinates: " . $ex->getMessage());
			$data = null;
		}
		return $data;
	}

    function url_encode($string){
        return urlencode(utf8_encode($string));
    }
   
    function url_decode($string){
        return utf8_decode(urldecode($string));
    }
	
	function OtherUrlEncode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '%20', '%22', '%3C', '%3E', '%25', '%7C');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", " ", '"', "<", ">", "%", "|");
		return str_replace($replacements, $entities, $string);
	}	
	
	function DashboardGraphData($month, $year) {
		$this->sql = "SELECT CONCAT(MONTHNAME(REGISTERED_ON),'/',YEAR(REGISTERED_ON)) MONTH_, COUNT(SERVICE_ID) TOTAL, " .
					"SUM(CASE WHEN ID_STATE < 6 THEN 1 ELSE 0 END) PROCESO, " .
					"SUM(CASE WHEN ID_STATE > 5 AND ID_STATE < 11 THEN 1 ELSE 0 END) EN_CAMINO, " .
					"SUM(CASE WHEN ID_STATE > 10 THEN 1 ELSE 0 END) TERMINADO " .
					"FROM $this->view " .
					"WHERE MONTH(REGISTERED_ON) = $month AND YEAR(REGISTERED_ON) = $year " .
					"GROUP BY CONCAT(MONTHNAME(REGISTERED_ON),'/',YEAR(REGISTERED_ON))";
		$this->sql = "SELECT CONCAT(MONTHNAME(S.REGISTERED_ON),'/',YEAR(S.REGISTERED_ON)) MONTH_, COUNT(S.ID) TOTAL, " .
					"SUM(CASE WHEN E.STEP_ID < 6 THEN 1 ELSE 0 END) PROCESO, " .
					"SUM(CASE WHEN E.STEP_ID > 5 AND E.STEP_ID < 11 THEN 1 ELSE 0 END) EN_CAMINO, " .
					"SUM(CASE WHEN E.STEP_ID > 10 THEN 1 ELSE 0 END) TERMINADO " .
					"FROM $this->table S " .
					"INNER JOIN " . $this->state->table . " E ON (E.ID = S.STATE_ID) " .
					"WHERE MONTH(S.REGISTERED_ON) = $month AND YEAR(S.REGISTERED_ON) = $year " .
					"GROUP BY CONCAT(MONTHNAME(S.REGISTERED_ON),'/',YEAR(S.REGISTERED_ON))";
		//Valor a retornar
		$return = array("month" => date("F", mktime(0, 0, 0, $month, 1, $year)),
						"year" => $year,
						"month_num" => $month,
						"month_name" => date("F/Y", mktime(0, 0, 0, $month, 1, $year)),
						"total" => 0,
						"process" => 0,
						"on_road" => 0,
						"finish" => 0);
		//Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			$return["total"] = $row[1];
			$return["process"] = $row[2];
			$return["on_road"] = $row[3];
			$return["finish"] = $row[4];
		}
		return $return;	
	}
	
	function DashboardSummaryGraph() {
		_error_log("Service DashboardSummaryGraph start at " . date("Y-m-d h:i:s"));		
		//Valor a retornar
		$return = "";
		$this->sql = "SELECT SS.SERVICE_STATE_NAME, (SELECT COUNT(*) FROM $this->table) TOTAL, SS.BACKGROUND_COLOR, COUNT(S.ID) " .
					"FROM $this->table S " .
					"INNER JOIN " . $this->state->view . " SS ON (SS.SERVICE_STATE_ID = S.STATE_ID) " .
					"GROUP BY SS.SERVICE_STATE_NAME LIMIT 4";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$return .= "<div class=\"progress-group\">\n";
			$return .= $row[0] . "\n";
			$return .= "<span class=\"float-right\"><b>$row[3]</b>/$row[1]</span>\n";
			$return .= "<div class=\"progress progress-sm\">\n";
			$perc = intval((intval($row[3]) / intval($row[1])) * 100);
			$return .= "<div class=\"progress-bar $row[2]\" style=\"width: " . $perc . "%\"></div>\n";
			$return .= "</div>\n";
			$return .= "</div>\n";
		}
		_error_log("Service DashboardSummaryGraph finish at " . date("Y-m-d h:i:s"),$this->sql);		
		return $return;
	}

    //Funcion que verifica servicios que no estan en subasta
    function getNotBidded() {
        //Arma la sentencia SQL
        $this->sql = "SELECT * FROM " . $this->vie2 . " WHERE EMPLOYEE_ID = '' AND STEP_ID <= 7";
		//Variable a devolver
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Arma la respuesta
			$data = array("sid" => $row[0],
							"stateid" => $row[1],
							"service_state" => $row[2],
							"registered_on" => $row[3],
							"notified_on" => $row[4],
							"minutes" => intval($row[5]),
							"new_stateid" => $row[7],
							"notification_id" => $row[8],
							"service_due" => intval($row[9]) == 1);
			array_push($return,$data);
		}
		return $return;
    }

    //Funcion que verifica servicios que no fueron atendidos
    function getNotAttended() {
        //Arma la sentencia SQL
        $this->sql = "SELECT * FROM " . $this->vie2 . " WHERE STEP_ID BETWEEN 7 AND 10 AND SERVICE_DUE = TRUE";
		//Variable a devolver
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Arma la respuesta
			$data = array("sid" => $row[0],
							"stateid" => $row[1],
							"service_state" => $row[2],
							"registered_on" => $row[3],
							"notified_on" => $row[4],
							"minutes" => intval($row[5]),
							"new_stateid" => $row[7],
							"notification_id" => $row[8],
							"service_due" => intval($row[9]) == 1,
							"employee_id" => $row[11]);
			array_push($return,$data);
		}
		return $return;
    }

	//Funcion que obtiene el aliado asociado a un servicio
	function getPartner($id = "") {
		$result = "";
		if($id != "") {
			$this->ID = $id;
		}
		$this->sql = "SELECT IFNULL(PARTNER_ID,'') FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("ID");
		//Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			$result = $row[0];
		}
		return $result;	
	}

}

?>
