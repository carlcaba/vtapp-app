<?

// LOGICA ESTUDIO 2020

//Incluye las clases dependientes
require_once("table.php");
require_once("partner.php");
require_once("zone.php");
require_once("city.php");

class partner_rate extends table {
	var $partner;
	var $zoneO;
	var $zoneD;
	var $city;
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
		$this->zoneO = new zone();
		$this->zoneD = new zone();
		$this->city = new city();
		$this->partner = new partner();
		//Vista relacionada
		$this->view = "VIE_PARTNER_RATE_SUMMARY";
	}

    //Funcion para Set la ciudad
    function setCity($city) {
		//Si esta establecida
		if($city != "" && intval($city) > 0) {
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
				$this->error = "City " . $_SESSION["NOT_REGISTERED"];
			}
		}
    }
	
    //Funcion para Get la ciudad
    function getCity() {
		if($this->CITY_ID != "" && intval($this->CITY_ID) > 0) {
			//Asigna el valor del escenario
			$this->CITY_ID = $this->city->ID;
			//Busca la informacion
			$this->city->__getInformation();
		}
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
    function getUser() {
		//Asigna el valor del aliado
		$this->PARTNER_ID = $this->partner->ID;
		//Busca la informacion
		$this->partner->__getInformation();
    }	

    //Funcion para Set la zona origen
    function setZoneOrigin($value) {
		//Asigna la informacion
		$this->zoneO->ID = $value;
		//Verifica la informacion
		$this->zoneO->__getInformation();
		//Si no hubo error
		if($this->zoneO->nerror == 0) {
			//Asigna el valor
			$this->ZONE_ORIGIN_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->ZONE_ORIGIN_ID = "NULL";
			//Genera error
			$this->nerror = 20;
			$this->error = "Zone Origin " . $_SESSION["NOT_REGISTERED"];
		}
    }
	
    //Funcion para Get la zona origen
    function getZoneOrigin() {
		//Asigna el valor de la zona
		$this->ZONE_ORIGIN_ID = $this->zoneO->ID;
		//Busca la informacion
		$this->zoneO->__getInformation();
    }	

    //Funcion para Set la zona destino
    function setZoneDestination($value) {
		//Asigna la informacion
		$this->zoneD->ID = $value;
		//Verifica la informacion
		$this->zoneD->__getInformation();
		//Si no hubo error
		if($this->zoneD->nerror == 0) {
			//Asigna el valor
			$this->ZONE_DESTINATION_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->ZONE_DESTINATION_ID = "NULL";
			//Genera error
			$this->nerror = 20;
			$this->error = "Zone destination " . $_SESSION["NOT_REGISTERED"];
		}
    }
	
    //Funcion para Get la zona destino
    function getZoneDestination() {
		//Asigna el valor de la zona
		$this->ZONE_DESTINATION_ID = $this->zoneD->ID;
		//Busca la informacion
		$this->zoneD->__getInformation();
    }	


	//Funcion para seleccionar operador
	function selectPartner() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ";
		
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