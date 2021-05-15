<?

// LOGICA ESTUDIO 2020

//Incluye las clases dependientes
require_once("table.php");
require_once("partner.php");
require_once("vehicle_type.php");

class partner_rate extends table {
	var $partner;
	var $vehicle;
	var $view;

	//Constructor de la clase
	function __constructor() {
		$this->partner_rate();
	}
	
	//Constructor anterior
	function partner_rate() {
		//Llamado al constructor padre
		parent::tabla("TBL_PARTNER_RATE");
		//Valores por defecto
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Clases relacionadas
		$this->vehicle = new vehicle_type();
		$this->partner = new partner();
		//Vista relacionada
		$this->view = "VIE_PARTNER_RATE_SUMMARY";
	}

    //Funcion para Set el aliado
    function setPartner($value) {
		//Asigna la informacion
		$this->partner->ID = $value;
		//Verifica la informacion
		$this->partner->__getInformation();
		//Si no hubo error
		if($this->partner->nerror == 0) {
			//Asigna el valor
			$this->PARTNER_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->PARTNER_ID = "NULL";
			//Genera error
			$this->nerror = 20;
			$this->error = "Partner " . $_SESSION["NOT_REGISTERED"];
		}
    }
	
    //Funcion para Get el aliado
    function getPartner() {
		//Asigna el valor del aliado
		$this->PARTNER_ID = $this->partner->ID;
		//Busca la informacion
		$this->partner->__getInformation();
    }	

    //Funcion para Set el vehiculo
    function setVehicle($value) {
		//Asigna la informacion
		$this->vehicle->ID = $value;
		//Verifica la informacion
		$this->vehicle->__getInformation();
		//Si no hubo error
		if($this->vehicle->nerror == 0) {
			//Asigna el valor
			$this->VEHICLE_TYPE_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->VEHICLE_TYPE_ID = "NULL";
			//Genera error
			$this->nerror = 20;
			$this->error = "Vehicle type" . $_SESSION["NOT_REGISTERED"];
		}
    }
	
    //Funcion para Get el vehiculo
    function getVehicle() {
		//Asigna el valor del vehiculo
		$this->VEHICLE_TYPE_ID = $this->vehicle->ID;
		//Busca la informacion
		$this->vehicle->__getInformation();
    }	

	//Funcion para seleccionar operador
	function selectPartner($distance, $round, $filter = "") {
		//Arma la sentencia SQL
		$this->sql = "SELECT PARTNER_RATE_ID, PARTNER_NAME, SKIN, DISTANCE_MIN, DISTANCE_MAX, PRICE, PARTNER_ID, ICON, ROUND_TRIP, TIME_MIN, TIME_MAX, VEHICLE_TYPE_ID, VEHICLE_TYPE_NAME " .
					"FROM $this->view " .
					"WHERE IS_BLOCKED = FALSE AND $distance BETWEEN DISTANCE_MIN AND DISTANCE_MAX ";
		//Si hay un filtro			
		if($filter != "") 
			$this->sql .= "AND PARTNER_ID IN ($filter) ";
		//Termina la sentencia SQL
		$this->sql .= "ORDER BY PRICE";
		$count = 0;
		$max = 0;
		$id = "";
		$min = 900000000;
		//Valor a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$skins = ["","","",""];
			if($row[2] != "") {
				$skins = explode(",",$row[2]);
			}
			$price = !$round ? $row[5] : $row[8];
			if($price < $min)
				$min = $price;
			if($price > $max)
				$max = $price;
			$timeText = sprintf($_SESSION["TIME_TO_DELIVER"],$row[9],$row[10]);
			$return .=  "<label class=\"partner-selection btn btn-lg btn-block\">\n";
			$return .=  "<input type=\"radio\" class=\"optPartner\" name=\"optPartner_$row[0]\" id=\"optPartner_$row[0]\" value=\"$row[0]\" data-rate=\"$price\" data-vehicle=\"$row[11]\" data-partner=\"$row[1]\" data-partnerid=\"$row[6]\">\n";
			$return .=  "<div class=\"info-box mb-3 " . $skins[3] . "\">\n";
			$return .=  "<span class=\"info-box-icon\"><img src=\"img/partners/" . $row[6] . ".png\" class=\"img-fluid\"></span>\n";
			$return .=  "<div class=\"info-box-content\">\n";
			$return .=  "<div class=\"row\">\n";
			$return .=  "<div class=\"col-md-3\">\n";
			$return .=  "<span class=\"info-box-number\">$row[1]</span>\n";
			$return .=  "<span class=\"info-box-text\">" . $timeText . "</span>\n";
			$return .=  "</div>\n";
			$return .=  "<div class=\"col-md-3\">\n";
			$return .=  "<span class=\"info-box-number\">" . $_SESSION["PUBLIC_PRICE"] . "</span>\n";
			$return .=  "<span class=\"info-box-text\">$ " . number_format($price*1.25,2,".",",") . "</span>\n";
			$return .=  "</div>\n";
			$return .=  "<div class=\"col-md-3\">\n";
			$return .=  "<span class=\"info-box-number\">" . $_SESSION["INSURANCE"] . "</span>\n";
			$return .=  "<span class=\"info-box-text\">$ " . number_format(800,2,".",",") . "</span>\n";
			$return .=  "</div>\n";
			$return .=  "<div class=\"col-md-3\">\n";
			$return .=  "<span class=\"info-box-number\">" . $_SESSION["VTAPP_PRICE"] . "</span>\n";
			$return .=  "<span class=\"info-box-text\">$ " . number_format($price,2,".",",") . "</span>\n";
			$return .=  "</div>\n";
			$return .=  "</div>\n";
			$return .=  "</div>\n";
			$return .=  "<span class=\"info-box-icon\"><i class=\"" . $row[7] . "\" title=\"$row[12]\"></i></span>\n";
			$return .=  "<span class=\"info-box-icon text-warning\" id=\"spanSelected_$row[0]\" style=\"display: none;\"><i class=\"fa fa-check\" title=\"" . $_SESSION["YOUR_SELECTION"] . "\"></i></span>\n";
			$return .=  "</div>\n";
			$return .=  "</label>\n";
			$id = $row[0];
			$count++;
		}
		$result = array("html" => $return,
						"cont" => $count,
						"max" => $max,
						"min" => $min,
						"sql" => $this->sql,
						"filter" => ($filter != "" && $count == 1) ? $id : "");
						
		return $result;
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
			$readonly = array("disabled", "", 
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
			$link = "core/actions/_save/__newUserAddress.php";
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
								);
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editUserAddress.php";
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
							);
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteUserAddress.php";
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

    //Funcion para generar el JSON 
    function showListJSON() {
		//Arma la sentencia SQL
        $this->sql = "SELECT ID, ADDRESS_NAME, ADDRESS, ZONE_NAME, PARENT_ZONE, CITY_NAME, COUNTRY " .
					"FROM " . $this->view . " WHERE USER_ID = " . $this->_checkData("USER_ID") . " ORDER BY ADDRESS_NAME";
		//Variable a retornar
		$return = array(array("text" => $_SESSION["SELECT_OPTION"],
						"value" => ""));
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array(	"id" => $row[0],
							"name" => $row[1],
							"address" => $row[2],
							"zone" => $row[3],
							"parent" => $row[4],
							"city" => $row[5],
							"country" => $row[6]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
    }
	
	//Funcion para mostrar los registros en una tabla
	function showTableData() {
		//Arma la sentencia SQL
        $this->sql = "SELECT partner_rate_ID, ADDRESS_NAME, ADDRESS, ZONE_NAME, PARENT_ZONE_NAME, CITY_NAME, COUNTRY, LATITUDE, LONGITUDE " .
					"FROM " . $this->view . " WHERE USER_ID = '" . $_SESSION['vtappcorp_userid'] . "' ORDER BY ADDRESS_NAME";
		$count = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			echo "<tr>\n";
			echo "<td>$row[1]</td>\n";
			echo "<td>$row[2],$row[5],$row[6]</td>\n";
			if($row[4] != "")
				echo "<td>$row[4]</td>\n";
			else
				echo "<td>$row[3]</td>\n";
			echo "<td><button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"btnSelectAddress\" name=\"btnSelectAddress\" data-id=\"$row[0]\" data-latitude=\"$row[7]\" data-longitude=\"$row[8]\">" . $_SESSION["SELECT"] . "</button></td>\n";
			echo "</tr>\n";
			$count++;
		}
		if($count == 0)
			echo $this->sql;
	}

	//Funcion para mostrar los registros en una tabla
	function showTableDataJSON($type) {
		//Arma la sentencia SQL
        $this->sql = "SELECT partner_rate_ID, ADDRESS_NAME, ADDRESS, ZONE_NAME, PARENT_ZONE_NAME, CITY_NAME, COUNTRY, LATITUDE, LONGITUDE, ZONE_ID, PARENT_ZONE " .
					"FROM " . $this->view . " WHERE USER_ID = '" . $_SESSION['vtappcorp_userid'] . "' AND IS_ORIGIN = " . strtoupper($type) . " ORDER BY ADDRESS_NAME";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			if($row[4] != "")
				$value = $row[4];
			else
				$value = $row[3];
			$address = $row[2] . ", " . $row[5] . ", " . $row[6];
			$data = array("id" => $row[0],
						"name" => $row[1],
						"address" => $address,
						"zone" => $value,
						"zone_id" => $row[9],
						"parent_zone" => $row[10],
						"button" => "<button type=\"button\" class=\"btn btn-warning btn-sm btnSelectAddress\" id=\"btnSelectAddress\" name=\"btnSelectAddress\" data-address=\"$address\" data-latitude=\"$row[7]\" data-longitude=\"$row[8]\" data-zone=\"$row[9]\" data-parentzone=\"$row[10]\">" . $_SESSION["SELECT"] . "</button>"
					);
			array_push($return,$data);
		}
		//Retorna
		return $return;
	}
}	

?>