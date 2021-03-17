<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("quota_type.php");
require_once("client.php");
require_once("payment.php");

class quota extends table {
	var $type;
	var $client;
	var $payment;
	var $view;
	
	//Constructor
	function __constructor($quota = "") {
		$this->quota($quota);
	}
	
	//Constructor anterior
	function quota ($quota  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_QUOTA");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->CLIENT_ID = $quota;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->type = new quota_type();
		$this->client = new client();
		$this->payment = new payment();
		$this->view = "VIE_QUOTA_SUMMARY";		
	}
	
	//Funcion para Set el tipo
	function setType($value) {
		//Asigna la informacion
		$this->type->ID = $value;
		//Verifica la informacion
		$this->type->__getInformation();
		//Si no hubo error
		if($this->type->nerror == 0) {
			//Asigna el valor
			$this->QUOTA_TYPE_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->QUOTA_TYPE_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el tipo
	function getType() {
		//Asigna el valor del tipo
		$this->QUOTA_TYPE_ID = $this->type->ID;
		//Busca la informacion
		$this->type->__getInformation();
	}

	//Funcion para Set el cliente
	function setClient($value) {
		//Asigna la informacion
		$this->client->ID = $value;
		//Verifica la informacion
		$this->client->__getInformation();
		//Si no hubo error
		if($this->client->nerror == 0) {
			//Asigna el valor
			$this->CLIENT_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->CLIENT_ID = "UUID()";
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el cliente
	function getClient() {
		//Asigna el valor del cliente
		$this->CLIENT_ID = $this->client->ID;
		//Busca la informacion
		$this->client->__getInformation();
	}

	//Funcion para Set el payment
	function setPayment($value) {
		if($value != "") {
			//Asigna la informacion
			$this->payment->ID = $value;
			//Verifica la informacion
			$this->payment->__getInformation();
			//Si no hubo error
			if($this->payment->nerror == 0) {
				//Asigna el valor
				$this->PAYMENT_ID = $value;
				//Genera error
				$this->nerror = 0;
				$this->error = "";
			}
			else {
				//Asigna valor por defecto
				$this->PAYMENT_ID = "UUID()";
				//Genera error
				$this->nerror = 20;
				$this->error = $_SESSION["NOT_REGISTERED"];
			}
		}
	}
	
	//Funcion para Get el payment
	function getPayment() {
		//Asigna el valor del cliente
		$this->PAYMENT_ID = $this->payment->ID;
		//Busca la informacion
		$this->payment->__getInformation();
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
			$this->setType($this->QUOTA_TYPE_ID);
			$this->setClient($this->CLIENT_ID);
			$this->setPayment($this->PAYMENT_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}		
	
	//Funcion que despliega los valores en una categoria
	function showOptionList($tabs = 8,$selected = "") {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT QUOTA_ID, QUOTA_NAME, REGISTERED_ON, (AMOUNT-USED) FROM $this->view WHERE IS_BLOCKED = FALSE AND CLIENT_ID = " . $this->_checkDataType("CLIENT_ID") . " ORDER BY 2"; 
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
			}
			$row[2] = date("d-m-Y h:i",strtotime($row[2]));
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected data-max=\"$row[3]\">" . $row[1] . $_SESSION["BALANCE"] . number_format($row[3],2,".",",") . " (" . $row[2] . ")</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-max=\"$row[3]\">" . $row[1] . $_SESSION["BALANCE"] . number_format($row[3],2,".",",") . "  (" . $row[2] . ")</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion para buscar un empleado por otra informacion
    function getInformationByOtherInfo($field = "PARTNER_NAME") {
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

	//Funcion que aumenta el uso del cupo
	function useQuota($value) {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET USED = USED + $value WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
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
			$readonly = array("", "", "disabled", 
								"", "",
								"", "", "", "disabled");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newQuota.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", 
								"disabled", "disabled",
								"disabled", "disabled", "", "disabled",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editQuota.php";
		}
		else {
			$readonly = array("disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteQuota.php";
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
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit,$options = "") {
		$fields = ["QUOTA_ID", "CLIENT_NAME", "AMOUNT", "USED", "IS_PAYED", "IS_VERIFIED", "CREDIT_CARD_NUMBER", "CREDIT_CARD_NAME", "QUOTA_TYPE_ID"];
		//Verifica las opciones
		if($options != "") {
			$sWhere .= " WHERE CLIENT_ID = '$options'";
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
						if(!$aRow[4])
							$pay = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["PAY"] . "\" onclick=\"payment('" . $aRow[$i] . "','" . $aRow[2] . "');\"><i class=\"fa fa-credit-card-alt\"></i></button>";
						else
							$pay = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["PAYED"] . "\" ><i class=\"fa fa-check-circle\"></i></button>";
						$list = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["LIST_SERVICES"] . "\" onclick=\"list('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-list-alt\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $pay . $list . $view . $edit . $delete . "</div></div>";
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
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "", "", "", "", "", "disabled","disabled","");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newClient.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "", "", "", "", "", "disabled", "disabled", "");			
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editClient.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled" );
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteClient.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmClient\" name=\"frmClient\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= $this->showField("PARTNER_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("ADDRESS", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		if($this->CITY_ID == "") {
			$readonly[$cont] = "disabled";
		}
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["CITY_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbCity\" name=\"cbCity\" " . $readonly[$cont++] . ">\n";
		$return .= $this->city->showOptionList(8,$showvalue ? $this->CITY_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= $this->showField("PHONE_1", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("PHONE_2", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL_1", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL_2", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CONTACT_NAME_1", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CONTACT_NAME_2", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
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