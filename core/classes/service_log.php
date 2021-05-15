<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("service.php");
require_once("vehicle.php");
require_once("service_state.php");

class service_log extends table {
	var $resources;
	var $view;
	var $service;
	var $initial_state;
	var $initial_employee;
	var $initial_vehicle;
	var $final_employee;
	var $final_state;
	var $final_vehicle;
	
	//Constructor
	function __constructor($service_log = "") {
		$this->service_log($service_log);
	}
	
	//Constructor anterior
	function service_log ($service_log  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SERVICE_LOG");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->service = new service();
		$this->initial_state = new service_state();
		$this->final_state = new service_state();
		$this->initial_employee = new employee();
		$this->final_employee = new employee();
		$this->initial_vehicle = new vehicle();
		$this->final_vehicle = new vehicle();
		$this->view = "VIE_SERVICE_LOG_SUMMARY";		
	}

    //Funcion para Set el servicio
    function setService($servicio) {
        //Asigna la informacion
        $this->service->ID = $servicio;
        //Verifica la informacion
        $this->service->__getInformation();
        //Si no hubo error
        if($this->service->nerror == 0) {
            //Asigna el valor
            $this->SERVICE_ID = $servicio;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->SERVICE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Servicio " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el servicio
    function getService() {
        //Asigna el valor del escenario
        $this->SERVICE_ID = $this->service->ID;
        //Busca la informacion
        $this->service->__getInformation();
    }

    //Funcion para Set el estado inicial
    function setInitialState($state) {
        //Asigna la informacion
        $this->initial_state->ID = $state;
        //Verifica la informacion
        $this->initial_state->__getInformation();
        //Si no hubo error
        if($this->initial_state->nerror == 0) {
            //Asigna el valor
            $this->STATE_INITIAL_ID = $state;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->STATE_INITIAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Estado inicial " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el estado inicial
    function getInitialState() {
        //Asigna el valor del escenario
        $this->STATE_INITIAL_ID = $this->initial_state->ID;
        //Busca la informacion
        $this->initial_state->__getInformation();
    }

    //Funcion para Set el estado final
    function setFinalState($state) {
        //Asigna la informacion
        $this->final_state->ID = $state;
        //Verifica la informacion
        $this->final_state->__getInformation();
        //Si no hubo error
        if($this->final_state->nerror == 0) {
            //Asigna el valor
            $this->STATE_FINAL_ID = $state;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->STATE_FINAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Estado final " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el estado final
    function getFinalState() {
        //Asigna el valor del escenario
        $this->STATE_FINAL_ID = $this->final_state->ID;
        //Busca la informacion
        $this->final_state->__getInformation();
    }

    //Funcion para Set el empleado inicial
    function setInitialEmployee($employee) {
        //Asigna la informacion
        $this->initial_employee->ID = $employee;
        //Verifica la informacion
        $this->initial_employee->__getInformation();
        //Si no hubo error
        if($this->initial_employee->nerror == 0) {
            //Asigna el valor
            $this->EMPLOYEE_INITIAL_ID = $employee;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->EMPLOYEE_INITIAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Empleado inicial " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el empleado inicial
    function getInitialEmployee() {
        //Asigna el valor
        $this->EMPLOYEE_INITIAL_ID = $this->initial_employee->ID;
        //Busca la informacion
        $this->initial_employee->__getInformation();
    }


    //Funcion para Set el empleado final
    function setFinalEmployee($employee) {
        //Asigna la informacion
        $this->final_employee->ID = $employee;
        //Verifica la informacion
        $this->final_employee->__getInformation();
        //Si no hubo error
        if($this->final_employee->nerror == 0) {
            //Asigna el valor
            $this->EMPLOYEE_FINAL_ID = $employee;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->EMPLOYEE_FINAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Empleado final " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el empleado final
    function getFinalEmployee() {
        //Asigna el valor
        $this->EMPLOYEE_FINAL_ID = $this->final_employee->ID;
        //Busca la informacion
        $this->final_employee->__getInformation();
    }

    //Funcion para Set el vehiculo inicial
    function setInitialVehicle($vehicle) {
        //Asigna la informacion
        $this->initial_vehicle->ID = $vehicle;
        //Verifica la informacion
        $this->initial_vehicle->__getInformation();
        //Si no hubo error
        if($this->initial_vehicle->nerror == 0) {
            //Asigna el valor
            $this->VEHICLE_INITIAL_ID = $vehicle;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->VEHICLE_INITIAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Vehículo inicial " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el vehiculo inicial
    function getInitialVehicle() {
        //Asigna el valor
        $this->VEHICLE_INITIAL_ID = $this->initial_vehicle->ID;
        //Busca la informacion
        $this->initial_vehicle->__getInformation();
    }


    //Funcion para Set el vehiculo final
    function setFinalVehicle($vehicle) {
        //Asigna la informacion
        $this->final_vehicle->ID = $vehicle;
        //Verifica la informacion
        $this->final_vehicle->__getInformation();
        //Si no hubo error
        if($this->final_vehicle->nerror == 0) {
            //Asigna el valor
            $this->VEHICLE_FINAL_ID = $vehicle;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->VEHICLE_FINAL_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Empleado final " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el vehiculo final
    function getFinalVehicle() {
        //Asigna el valor
        $this->VEHICLE_FINAL_ID = $this->final_vehicle->ID;
        //Busca la informacion
        $this->final_vehicle->__getInformation();
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
			$this->setService($this->SERVICE_ID);
			$this->setInitialState($this->STATE_INITIAL_ID);
			$this->setFinalState($this->STATE_FINAL_ID);
			$this->setInitialEmployee($this->EMPLOYEE_INITIAL_ID);
			$this->setFinalEmployee($this->EMPLOYEE_FINAL_ID);
			$this->setInitialVehicle($this->VEHICLE_INITIAL_ID);
			$this->setFinalVehicle($this->VEHICLE_FINAL_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion para adicionar un registro al log
	function __add() {
		//Verifica los valores
		if($this->EMPLOYEE_INITIAL_ID == "''") {
			$this->EMPLOYEE_INITIAL_ID = "NULL";
		}
		if($this->EMPLOYEE_FINAL_ID == "''") {
			$this->EMPLOYEE_FINAL_ID = "NULL";
		}
		if($this->VEHICLE_INITIAL_ID == "''") {
			$this->VEHICLE_INITIAL_ID = "NULL";
		}
		if($this->VEHICLE_FINAL_ID == "''") {
			$this->VEHICLE_FINAL_ID = "NULL";
		}
		//Llama la agregada 
		parent::_add();
	}
	
	//Funcion para localizar el ultimo log
	function getLastLog() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE SERVICE_ID = " . $this->_checkDataType("SERVICE_ID") . " ORDER BY REGISTERED_ON DESC LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			//Asigna el ID
			$this->ID = $row[0];
			//Busca la informacion
			$this->__getInformation();
		}
	}

	//Funcion para obtener la última posicion
	function getLastPosition() {
		//Arma la sentencia SQL
		$this->sql = "SELECT LAST_POSITION FROM $this->table WHERE SERVICE_ID = " . $this->_checkDataType("SERVICE_ID") . " AND LAST_POSITION <> '' ORDER BY REGISTERED_ON DESC LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			//Asigna el ID
			return $row[0];
		}
		else {
			return "";
		}
	}

	
	//Funcion para listar los servicios de acuerdo al estado
	function listServices($state, $id, $usr = "", $history = false, $limit = 0) {
		//Define las columnas
		$columns = ["SERVICE_ID", "CLIENT_ID", "CLIENT_NAME", "CLIENT_IDENTIFICATION", "CLIENT_ADDRESS", "CLIENT_PHONE", "CLIENT_CELLPHONE", 
					"REQUESTED_BY", "REQUESTED_EMAIL", "REQUESTED_ADDRESS", "REQUESTED_PHONE", "REQUESTED_CELLPHONE", "REQUEST_CITY_ID", "REQUEST_CITY_NAME", "REQUEST_COUNTRY", 
					"DELIVER_DESCRIPTION", "OBSERVATION", "DELIVER_TO", "DELIVER_EMAIL", "DELIVER_ADDRESS", "DELIVER_PHONE", "DELIVER_CELLPHONE", "DELIVERY_TYPE_NAME", "DELIVERY_CITY_ID", "DELIVERY_CITY_NAME", "DELIVERY_COUNTRY", 
					"LAT_REQUEST_INI", "LON_REQUEST_INI", "LAT_REQUEST_END", "LON_REQUEST_END", "REQUESTED_ZONE", "ZONE_NAME_REQUEST",
					"LAT_DELIVERY_INI", "LON_DELIVERY_INI", "LAT_DELIVERY_END", "LON_DELIVERY_END", "DELIVER_ZONE", "ZONE_NAME_DELIVERY",
					"STATE_ID", "SERVICE_STATE_NAME", "ROUND_TRIP", "FRAGILE", "VEHICLE_TYPE_ID", "VEHICLE_TYPE_NAME", "TIME_START_TO_DELIVER", "TIME_FINISH_TO_DELIVER", "ID_STATE", 
					"EMPLOYEE_INITIAL_ID", "EMPLOYEE_INITIAL_NAME", "PARTNER_INITIAL_ID", " EMPLOYEE_FINAL_ID", "EMPLOYEE_FINAL_NAME", "PARTNER_FINAL_ID"];
		if($history) {
			$columns = ["SERVICE_ID", "REGISTERED_ON", "PRICE", "REQUESTED_ADDRESS", "DELIVER_ADDRESS", "REQUESTED_COORDINATES", "DELIVER_COORDINATES"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT " . implode(",", $columns) .
					" FROM $this->view WHERE STATE_ID = '$state' ";
					
		if($history)			
			$this->sql .= "AND EMPLOYEE_FINAL_ID = '$id' ";
		else 
			$this->sql .= "AND NOTIFIED_EMPLOYEE = '$id' ";
		
		if($usr != "")
			$this->sql .= "AND USER_NOTIFICATION = '$usr' AND NOTIFICATION_BLOCKED = FALSE ";
		
		$this->sql .= "ORDER BY SERVICE_ID";
		
		if($history && $limit > 0)
			$this->sql .= " LIMIT $limit";
		
		//Variable a retornar
		$return = array();
		$isThereData = false;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			for($i = 0; $i < count($row); $i++) {
				$data[$columns[$i]] = $row[$i];
			}
			if($history) {
				$reqcor = explode(",", $row[5]);
				$delcor = explode(",", $row[6]);
				$data["DISTANCE"] = number_format(($this->haversineGreatCircleDistance(floatval($reqcor[0]), floatval($reqcor[1]), floatval($delcor[0]), floatval($delcor[1])) / 1000), 2) . " Kms";
			}
			array_push($return,$data);
			$isThereData = true;
		}
		if(!$isThereData) {
			$return["success"] = false;
			$return["message"] = $_SESSION["NO_DATA"];
		}
		return $return;
	}
	
	function updatePosition($object) {
		//Asigna el servicio
		$this->setService($object->id);
		//Si hay error
		if($this->nerror > 0) {
			return false;
		}
		//Obtiene el ultimo registro
		$this->getLastLog();
		
		//Verifica la posicion final
		if(!preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $object->position)) {
			$this->nerror = 104;
			$this->error = $_SESSION["BAD_POSITION"];
			return false;
		}
		
		if($this->service->STATE_ID != $this->service->state->getIdByStep(9) && $this->service->state->STEP_ID < 9) {
			//Actualiza el estado del servicio
			$this->service->updateState($this->service->state->getIdByStep(9));
			
			//Busca ultimo log
			$this->getLastLog();
			
			//Modifica la locacion
			$this->LAST_POSITION = $object->position;

			//Llama la agregada 
			parent::_modify();
		}
		
		//Retorna
		return $this->nerror == 0;
	}

	function onRoad($object) {
		//Asigna el servicio
		$this->setService($object->id);
		//Si hay error
		if($this->nerror > 0) {
			return false;
		}
		//Obtiene el ultimo registro
		$this->getLastLog();
		
		//Verifica la posicion final
		if(!preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $object->position)) {
			$this->nerror = 104;
			$this->error = $_SESSION["BAD_POSITION"];
			return false;
		}
		
		if($this->service->STATE_ID != $this->service->state->getIdByStep($object->state) && $this->service->state->STEP_ID < $object->state) {
			//Actualiza el estado del servicio
			$this->service->updateState($this->service->state->getIdByStep($object->state));
			
			//Busca ultimo log
			$this->getLastLog();
			//Modifica la locacion
			$this->LAST_POSITION = $object->position;

			//Llama la agregada 
			parent::_modify();
		}
		
		//Retorna
		return $this->nerror == 0;
	}

	function delivered($object) {
		//Asigna el servicio
		$this->setService($object->id);
		//Si hay error
		if($this->nerror > 0) {
			return false;
		}
		//Obtiene el ultimo registro
		$this->getLastLog();
		
		if($this->service->STATE_ID != $this->service->state->getIdByStep($object->state) && $this->service->state->STEP_ID < $object->state) {
			//Actualiza el estado del servicio
			$this->service->updateState($this->service->state->getIdByStep($object->state));
		}
		
		//Retorna
		return $this->nerror == 0;
	}
	
	function finished($object) {
		//Asigna el servicio
		$this->setService($object->id);
		//Si hay error
		if($this->nerror > 0) {
			return false;
		}
		//Obtiene el ultimo registro
		$this->getLastLog();
		
		if($this->service->STATE_ID != $this->service->state->getIdByStep($object->state) && $this->service->state->STEP_ID < $object->state) {
			//Actualiza el estado del servicio
			$this->service->updateState($this->service->state->getIdByStep($object->state));
		}
		
		//Retorna
		return $this->nerror == 0;
	}	
	
	function updateProcess($object) {
		//Asigna el servicio
		$this->setService($object->id);
		//Si hay error
		if($this->nerror > 0) {
			return false;
		}
		//Obtiene el ultimo registro
		$this->getLastLog();
		//Verifica el estado
		if($object->state == null) {
			$this->nerror = 104;
			$this->error = $_SESSION["STATE_NOT_FOUND"];
			return false;
		}
		//Busca el estado
		$state = $this->service->state->getInformationByOtherInfo($object->state,"ID_STATE");
		//Verifica
		if($this->service->state->nerror > 0) {
			$this->nerror = 105;
			$this->error = $_SESSION["STATE_NOT_FOUND"];
			return false;
		}
		
		//Genera el nuevo log
		$this->ID = "UUID()";
		$this->setInitialState($this->service->STATE_ID);
		$this->setFinalState($state->ID);
		$this->LAST_POSITION = $this->getLastPosition();
		$this->REGISTERED_BY = "WS.Vtapp";
		$this->MODIFIED_BY = "WS.Vtapp";
		$this->MODIFIED_ON = "NOW()";

		//Llama la agregada 
		parent::_add();
		
		//Si se logra insertar correctamente
		if($this->nerror == 0) {
			//Actualiza el servicio
			$this->service->setState($state->ID);
			$this->service->_modify();
			$this->nerror = $this->service->nerror;
			$this->error = $this->service->error;
			$this->sql = $this->service->sql;
		}
		
		//Retorna
		return $this->nerror == 0;
    }
    
	function getComments() {
		$this->resources = new resources();
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
    
    function addLogLine($icon, $time, $msg) {
        $return = "<li><i class=\"" . $icon . "\"></i>\n";
        $return .= "<div class=\"timeline-item\">\n";
        $return .= "<span class=\"time\"><i class=\"fa fa-clock-o\"></i> $time</span>\n";
        $return .= "<h3 class=\"timeline-header no-border\">$msg</h3>\n";
        $return .= "</div>\n";
        $return .= "</li>\n";
        return $return;
    }
	
	function showTimelineActivity() {
		//Arma la sentencia sql
		$this->sql = "SELECT * FROM $this->view WHERE SERVICE_ID = " . $this->_checkDataType("SERVICE_ID") . " ORDER BY REGISTERED_ON DESC" ;
		//Define el resultado
		$return = "";
        $fecha = "";
        $position = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica la fecha
			if($fecha != $row[76]) {
				//Genera el nuevo timeline
				$return .= "<li class=\"time-label\"><span class=\"bg-success\">" . date("d M Y",strtotime($row[76])) . "</span></li>\n";
				//Asigna la fecha
				$fecha = $row[76];
			}
			$time = strtotime($row[76] . " " . $row[77]);
            $elap = explode(" ", $this->elapsedTime($time));			
            $icon = $row[78] . " bg-info";
            $msg = $_SESSION["SERVICE"] . " Estado: " . $row[73];

            $return .= $this->addLogLine($icon,$row[77],$msg);

            //Si es la asignacion de un aliado
            if($row[70] != $row[71]) {
                //Si es la primera asignacion
                if($row[70] == "") {
                    $icon = "fa fa-briefcase bg-success";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . " (" . $row[80] . ")";
                }
                else {
                    $icon = "fa fa-coffee bg-warning";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . " (" . $row[79] . " -> " . $row[80] . ")";
                }
                $return .= $this->addLogLine($icon,$row[77],$msg);
            }
            //Si es la asignacion de un empleado
            if($row[62] != $row[63]) {
                //Si es la primera asignacion
                if($row[62] == "") {
                    $icon = "fa fa-user-plus bg-success";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . " (" . $row[69] . ")";
                }
                else {
                    $icon = "fa fa-user-times bg-warning";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . " (" . $row[68] . " -> " . $row[69] . ")";
                }
                $return .= $this->addLogLine($icon,$row[77],$msg);
            }
            //Si asigna un vehiculo
            if($row[64] != $row[65]) {
                //Si es la primera asignacion
                if($row[64] == "") {
                    $icon = "fa fa-motorcycle bg-success";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . " (" . $row[75] . ")";
                }
                else {
                    $icon = "fa fa-paper-plane bg-warning";
                    $msg = $_SESSION["SERVICE"] . " " . $row[73] . "(" . $row[74] . " -> " . $row[75] . ")";
                }
                $return .= $this->addLogLine($icon,$row[77],$msg);
            }
            //Si hay movimiento
            if($position != $row[67]) {
                $icon = "fa fa-map-marker bg-info\" title=\"" . $_SESSION["VIEW_MAP"] . "\" onclick=\"showMap(" . $row[67] . ");\"";
                $msg = $_SESSION["UPDATE_POSITION"] . " (" . $row[25] . ") ";
                $return .= $this->addLogLine($icon,$row[77],$msg);
                $position = $row[67];
            }
            //Si hay comentarios
            if($row[66] != "") {
                $icon = "fa fa-comment bg-success";
                $msg = $_SESSION["COMMENTS"] . "<br />";
                $msg .= "<blockquote class=\"quote-secondary\"><p>$row[66]</p><small>$row[24] <cite title=\"$row[77]\">$row[77]</cite></small></blockquote>";
                $return .= $this->addLogLine($icon,$row[77],$msg);
            }
		}
		//Si no hay actividad
		if($return == "") {
			$time = strtotime($this->service->REGISTERED_ON);
			$return .= "<div class=\"time-label\"><span class=\"bg-warning\">" . date("d M Y", strtotime($this->service->REGISTERED_ON)) . "</span></div>\n";
			$return .= "<div><i class=\"fa fa-plus bg-success\"></i><div class=\"timeline-item\"><span class=\"time\"><i class=\"fa fa-clock-o\"></i> " . $this->elapsedTime($time) . "</span>\n";
			$return .= "<h3 class=\"timeline-header border-0\">" . sprintf($_SESSION["SERVICE_REGISTERED_BY"],$this->service->REGISTERED_BY) . "</h3></div></div>\n";
		}

		//Retorna
		return $return;
	}

	/**
	 * Calculates the great-circle distance between two points, with
	 * the Haversine formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */
	private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius;
	}
}

?>
