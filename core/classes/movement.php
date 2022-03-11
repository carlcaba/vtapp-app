<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("employee.php");

class movement extends table {
	var $resources;
	var $view;
	var $view2;
	var $employee;
	
	//Constructor
	function __constructor($movement = "") {
		$this->movement($movement);
	}
	
	//Constructor anterior
	function movement($movement = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_MOVEMENT");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->INTERNAL_NUMBER = $movement;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->employee = new employee();
		$this->view = "VIE_MOVEMENT_SUMMARY";		
		$this->view2 = "VIE_MOVEMENT_DETAIL_SUMMARY";		
	}

    //Funcion para Set el empleado
    function setEmployee($empleado) {
        //Asigna la informacion
        $this->employee->ID = $empleado;
        //Verifica la informacion
        $this->employee->__getInformation();
        //Si no hubo error
        if($this->employee->nerror == 0) {
            //Asigna el valor
            $this->ID_EMPLOYEE = $empleado;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_EMPLOYEE = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Employee " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el empleado
    function getEmployee() {
        //Asigna el valor del empleado
        $this->ID_EMPLOYEE = $this->employee->ID;
        //Busca la informacion
        $this->employee->__getInformation();
    }	
	
	//Funcion para obtener la informacion del movimiento
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
			$this->setEmployee($this->ID_EMPLOYEE);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}	
	
	//Funcion para buscar un movimiento por otra informacion
    function getInformationByOtherInfo($field = "INTERNAL_NUMBER") {
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
	
	//Funcion para contar todas las ordenes
    function getTotalOrders() {
        //Arma la sentencia SQL
        $this->sql = "SELECT COUNT(ID) FROM $this->table";
        //Obtiene los resultados
        $row = $this->__getData();
		//Variable a retornar
		$return = 0;
        //Registro no existe
        if($row)
            $return = $row[0];
		//Regresa
		return $return;
    }		
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["MOVEMENT_ID", "INTERNAL_NUMBER", "MOVE_DATE", "MOVEMENT_TYPE", "ITEMS", "REGISTERED_ON", "REGISTERED_BY", "IS_BLOCKED", "ID_EMPLOYEE", "LANGUAGE_ID"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(MOVEMENT_ID) FROM $this->view $sWhere";
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
					if($aColumnsBD[$i] == "MOVEMENT_ID") {
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','output.php');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','editoutput.php');\"><i class=\"fa fa-pen-to-square\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $view . $edit . "</div></div>";
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
	function showForm($action, $tabs = 5, $datatable = false) {
		//Resources
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";		
		//Verifica si es nuevo registro o es edicion
		$readonly = array("", "", "", "");
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		$onlyfield = true;
		//Definicion de la forma
		$form = "";

		//Cadena a retornar
		$form .= $this->showField("INTERNAL_NUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++], null);
		
		$date = $showvalue ? (($this->MOVE_DATE != "CURDATE()") ? $this->MOVE_DATE : date("Y-m-d")) : date("Y-m-d");
		$form .= $this->showField("MOVE_DATE", "$stabs\t", "", "", $showvalue, $date, false, "9,9,12", $readonly[$cont++], null);
		
		$form .= "$stabs\t<div class=\"form-group\">\n";
		$form .= "$stabs\t\t<label>" . $_SESSION["EMPLOYEE"] . " *</label>\n";
		$form .= "$stabs\t\t\t<select class=\"form-control\" style=\"width: 100%;\" id=\"cbEmployee\" name=\"cbEmployee\" " . $readonly[$cont++] . ">\n";
		$form .= $this->employee->showOptionList(8,$showvalue ? $this->ID_EMPLOYEE : "");
		$form .= "$stabs\t\t\t</select>\n";
		$form .= "$stabs\t</div>\n";
		
		if($action != "new") {
			//Arma la sentencia sql para mostrar los items
			$this->sql = "SELECT MOVEMENT_ID, INTERNAL_NUMBER, MOVE_DATE, DETAIL_ID, MOVEMENT_TYPE_ID, RESOURCE_TEXT, APPLIED, EMPLOYEE_NAME, PRODUCT_ID, PRODUCT_NAME, QUANTITY, PRICE, FACTOR, ID_ORDER, CODE, UNIT, " .
						"money-bill-1_FACTOR, money-bill-1TYPE, ACTUAL_EXISTENCE, ENTRIES, OUTPUTS FROM $this->view2 WHERE MOVEMENT_ID = '" . $this->ID . "' AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " ORDER BY ID_ORDER";

			//Si NO es para datatable
			if(!$datatable) {
				$form .= "<div class=\"card\">\n";
				$form .= "<div class=\"card-header\">\n";
				$form .= "<h3 class=\"card-title\">" . $_SESSION["ITEMS"] . "</h3>\n";
				$form .= "</div>\n";
				$form .= "<div class=\"card-body\">\n";
				$form .= "<table class=\"table table-bordered\">\n";
				$form .= "<tr>\n<th style=\"width: 10px\">#</th>\n"; //Item
				$form .= "<th>" . $_SESSION["PRODUCT_TABLE_TITLE_3"] . "</th>\n"; //Codigo
				$form .= "<th>" . $_SESSION["PRODUCT_TABLE_TITLE_2"] . "</th>\n"; //Producto
				$form .= "<th>" . $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_3"] . "</th>\n"; //Unidad
				$form .= "<th>" . $_SESSION["PRODUCT_TABLE_TITLE_6"] . "</th>\n"; //Cantidad
				$form .= "<th>" . $_SESSION["PRODUCT_TABLE_TITLE_7"] . "</th>\n"; //Precio
				$form .= "<th>" . $_SESSION["MOVEMENT_DETAIL_TABLE_TITLE_6"] . "</th>\n"; //Total
				$form .= "</tr>\n";
			}
			
			$counter = 0;
			$totalOrder = 0;
			//Recoge los resultados
			foreach($this->__getAllData() as $row) {
				//Para mostrar
				$applied = ($row[6] == 0) ? "<span class=\"badge bg-danger\">" . $_SESSION["NOT_APPLIED"] . "</span>" : "";
				$trm = ($row[16] != 1) ? "<span class=\"badge bg-primary\">" . $_SESSION["TRM_ABBRV"] . "</span>" : "";
				$conv = ($row[12] != 1) ? "<span class=\"badge bg-warning\">" . $_SESSION["CONVERSION_ABBRV"] . "</span>" : "";
				$total = $row[10] * $row[12] * $row[11] * $row[16];
				$totalOrder += $total;
				$existence = $row[18] + $row[19] - $row[20];
				$counter++;
				//Si es para una tabla
				$return .= "<tr>\n";
				$return .= "<td>$row[13]</td>\n";
				$return .= "<td>$row[14]</td>\n";
				$return .= "<td>$row[9] $applied $trm $conv</td>\n";
				$return .= "<td>$row[15]</td>\n";
				$return .= "<td>" . number_format($row[10],2,".",",") . "</td>\n";
				$return .= "<td>$ " . number_format($row[11],2,".",".") . "</td>\n";
				$return .= "<td>$ " . number_format($total,2,".",",") . "</td>\n";
				$return .= "</tr>\n";
				//Para editar
				$result .= "<tr>\n";
				$result .= "<td>$row[13]</td>\n";
				$result .= "<td>$row[14]</td>\n";
				$result .= "<td>$row[9] $applied</td>\n";
				$result .= "<td>$row[15]</td>\n";
				$result .= "<td>$ " . number_format($row[11],2,",",".") . "</td>\n";
				$result .= "<td>" . number_format($row[10],2,",",".") . "</td>\n";
				$result .= "<td>$ " . number_format($total,2,",",".") . "</td>\n";
				$result .= "<td><div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">\n";
				//Botones
				$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show($row[13],'view');\"><i class=\"fa fa-eye\"></i></button>";
				$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show($row[13],'edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
				$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"deleteItem($row[13]);\"><i class=\"fa fa-trash\"></i></button>";
				//Data
				$data = array("cbProduct"=> $row[8],
								"hfID"=> $row[8],
								"hfCODE"=> $row[14],
								"hfUNIT"=> $row[15],
								"txtEXISTENCE"=> $existence,
								"txtQUANTITY"=> $row[10],
								"txtPRICE"=> $row[11],
								"txtTOTAL"=> $total,
								"hfFactor"=> $row[12],
								"hfmoney-bill-1Factor"=> $row[16],
								"hfIdMove" => $row[0],
								"hfIdMoveDetail" => $row[3]);
				$result .= $view . $edit . $delete . "\n<input type=\"hidden\" name=\"hfRow_$row[13]\" id=\"hfRow_$row[13]\" value='" . json_encode($data) . "'>\n";
				$result .= "</div>\n</div>\n</td>\n</tr>\n";
			}
			
			//Si NO es para datatable
			if(!$datatable) {
				//Define el resultado
				$return = $form;
				//Verifica si hay datos
				if($counter == 0)
					$return .= "<tr><td colspan=\"7\" align=\"center\">" . $_SESSION["NO_DATA"] . "</td></tr>\n";
				else 
					$return .= "<tr><td colspan=\"5\" align=\"right\"><strong>" . $_SESSION["TOTAL_ORDER"] . "</strong></td><td colspan=\"2\" align=\"right\"><strong>$" . number_format($totalOrder,2,".",",") . "</strong></td></tr>\n";
				$return .= "</table>\n</div>\n</div>\n";
			}
			else {
				//Envia la forma y los datos de la tabla
				$arrResult = array("form" => $form,
									"table" => $result,
									"counter" => $counter);
				$return = $arrResult;
			}
		}
		else {
			$return = $form;
		}
			
		
		return $return;
	}
		
}
    
?>