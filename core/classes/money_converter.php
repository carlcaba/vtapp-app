<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class money-bill-1_converter extends table {
	var $view;
	
	//Constructor
	function __constructor() {
		$this->money-bill-1_converter();
	}
	
	//Constructor anterior
	function money-bill-1_converter() {
		//Llamado al constructor padre
		parent::table("TBL_money-bill-1_CONVERTER");
		//Inicializa los atributos
		$this->DATERATE = "CURDATE()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		$this->view = "VIE_money-bill-1_CONVERTER_SUMMARY";
	}

	//Funcion para obtener la informacion de la conversion
	function __getInformation() {
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM $this->table WHERE ";
		//Verifica criterios
		if($this->ID == 0)
			//Termina la sentencia sql
			$this->sql .= "DATERATE = " . $this->_checkDataType("DATERATE") . 
						" AND money-bill-1_FROM = " . $this->_checkDataType("money-bill-1_FROM") . 
						" AND money-bill-1_TO = " . $this->_checkDataType("money-bill-1_TO");
		else
			//Termina la sentencia sql
			$this->sql .= "ID = " . $this->_checkDataType("ID");
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = 0;
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
        }
        else {
            //Asigna los valores
            $this->ID = $row[0];
			$this->money-bill-1_FROM = $row[1];
			$this->money-bill-1_TO = $row[2];
			$this->VALUE_TO = $row[3];
			$this->DATERATE = $row[4];
			$this->REGISTERED_ON = $row[5];
			$this->REGISTERED_BY = $row[6];
			$this->IS_BLOCKED = $row[7];
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
    }	
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["money-bill-1_ID", "money-bill-1_FROM", "money-bill-1_TO", "VALUE_TO", "DATERATE", "REGISTERED_ON", "REGISTERED_BY", "IS_BLOCKED", "LANGUAGE_ID"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(money-bill-1_ID) FROM $this->view $sWhere";
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
					if($aColumnsBD[$i] == "money-bill-1_ID") {
						//Verifica el estado para activar o desactivar
					$title = $aRow[1] . "-" . $aRow[2] . " - " . $aRow[4];
						if($aRow[7])
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate(" . $aRow[$i] . ",true,'" . $title . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate(" . $aRow[$i] . ",false,'" . $title . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show(" . $aRow[$i] . ",'view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show(" . $aRow[$i] . ",'edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show(" . $aRow[$i] . ",'delete');\"><i class=\"fa fa-trash\"></i></button>";
												
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
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newTRM.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editTRM.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteTRM.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmTRM\" name=\"frmTRM\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}
		$return .= $this->showField("money-bill-1_FROM", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("money-bill-1_TO", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("VALUE_TO", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("DATERATE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
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