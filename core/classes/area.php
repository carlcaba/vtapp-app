<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("client.php");
require_once("area_type.php");

class area extends table {
	var $resources;
	var $client;
	var $type;
	var $view;
	
	//Constructor
	function __constructor($area = "") {
		$this->area($area);
	}
	
	//Constructor anterior
	function area($area = '') {
		//Llamado al constructor padre
		parent::table("TBL_AREA");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->AREA_NAME = $area;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->client = new client();
		$this->type = new area_type();
		$this->view = "VIE_AREA_SUMMARY";		
	}
	
	//Funcion para Set el cliente
	function setClient($client) {
		//Asigna la informacion
		$this->client->ID = $client;
		//Verifica la informacion
		$this->client->__getInformation();
		//Si no hubo error
		if($this->client->nerror == 0) {
			//Asigna el valor
			$this->CLIENT_ID = $client;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->CLIENT_ID = "";
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el cliente
	function getClient() {
		//Asigna el valor del acceso
		$this->CLIENT_ID = $this->client->ID;
		//Busca la informacion
		$this->client->__getInformation();
	}

	//Funcion para Set el tipo de area
	function setAreaType($area) {
		//Asigna la informacion
		$this->type->ID = $area;
		//Verifica la informacion
		$this->type->__getInformation();
		//Si no hubo error
		if($this->type->nerror == 0) {
			//Asigna el valor
			$this->AREA_TYPE_ID = $area;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->AREA_TYPE_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el tipo de area
	function getAreaType() {
		//Asigna el valor del acceso
		$this->AREA_TYPE_ID = $this->type->ID;
		//Busca la informacion
		$this->type->__getInformation();
	}

	
	//Funcion para obtener la informacion del area
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
			$this->setAreaType($this->AREA_TYPE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	//Funcion para buscar un area por nombre
    function getInformationByName($name) {
        //Arma la sentencia SQL
		$this->sql = "SELECT AREA_ID FROM $this->view WHERE AREA_NAME = '$name' LIMIT 1";
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

	
	//Funcion que activa o habilita a una area
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
	
	//Funcion que despliega los valores en un area
	function showOptionList($tabs = 8,$selected = "", $isPartner = false) {
		$stabs = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		if(!$isPartner) 
			//Arma la sentencia SQL
			$this->sql = "SELECT A.AREA_ID, A.AREA_NAME, A.CLIENT_ID FROM $this->view A WHERE A.IS_BLOCKED = FALSE ORDER BY A.AREA_NAME";
		else 
			//Arma la sentencia SQL
			$this->sql = "SELECT A.ID, A.AREA_NAME, A.CLIENT_ID, P.PARTNER_NAME FROM $this->table A INNER JOIN TBL_PARTNER P ON (P.ID = A.CLIENT_ID) WHERE A.IS_BLOCKED = FALSE AND PARTNER = TRUE ORDER BY A.AREA_NAME";
			
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			if($isPartner) 
				$row[1] = $row[1] . " (" . $row[3] . ")";
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected data-client-id=\"$row[2]\">" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-client-id=\"$row[2]\">" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//var fields = ["AREA_ID", "AREA_NAME", "PARENT_AREA_NAME", "TITLE", "COSTCENTER", "CLIENT_NAME", "REGISTERED_ON", "REGISTERED_BY", "IS_BLOCKED", "CLIENT_ID"];
		//Verifica el acceso del usuario
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
			if($sWhere == "") {
				$sWhere = " WHERE CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
			else {
				$sWhere .= " AND CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL") {
			if($sWhere == "") {
				$sWhere = " WHERE PARTNER_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
			else {
				$sWhere .= " AND PARTNER_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "DO") {
			if($sWhere == "") {
				$sWhere = " WHERE CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
			else {
				$sWhere .= " AND CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
			}
		}
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(DISTINCT AREA_ID) FROM $this->view $sWhere";
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
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == "AREA_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
						$funds = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ADD_FUNDS"] . "\" onclick=\"addFunds('" . $aRow[9] . "','" . $aRow[0] . "');\"><i class=\"fa fa-money-bill-1\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $funds . $view . $edit . $delete . "</div></div>";
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
		$output['sql'] = $this->sql;
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
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newArea.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editArea.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteArea.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmArea\" name=\"frmArea\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= $this->showField("AREA_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("TITLE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("COSTCENTER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["AREA_TYPE_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbAreaType\" name=\"cbAreaType\" " . $readonly[$cont++] . ">\n";
		$return .= $this->type->showOptionList(9,$this->AREA_TYPE_ID);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["CLIENT_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbClient\" name=\"cbClient\" " . $readonly[$cont++] . ">\n";
		$return .= $this->client->showOptionList(9,$this->client->ID);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		if($viewData) {
			$return .= $this->showField("REGISTERED_ON", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("REGISTERED_BY", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("MODIFIED_ON", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("MODIFIED_BY", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
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
