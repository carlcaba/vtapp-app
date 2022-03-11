<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("client.php");
require_once("order_state.php");

class order extends table {
	var $resources;
	var $view;
	var $view2;
	var $client;
	var $state;
	
	//Constructor
	function __constructor($order = "") {
		$this->order($order);
	}
	
	//Constructor anterior
	function order($order = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_ORDER");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->INTERNAL_NUMBER = $order;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->client = new client();
		$this->state = new order_state();
		$this->view = "VIE_ORDER_SUMMARY";		
		$this->view2 = "VIE_DETAIL_ORDER_SUMMARY";		
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
            $this->ID_CLIENT = $client;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_CLIENT = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Client " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el cliente
    function getClient() {
        //Asigna el valor del empleado
        $this->ID_CLIENT = $this->client->ID;
        //Busca la informacion
        $this->client->__getInformation();
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
            $this->ID_STATE = $state;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_STATE = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "State " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el estado
    function getState() {
        //Asigna el valor del empleado
        $this->ID_STATE = $this->state->ID;
        //Busca la informacion
        $this->state->__getInformation();
    }	
	
	//Funcion para obtener la informacion de la orden
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
			$this->setClient($this->ID_CLIENT);
			$this->setState($this->ID_STATE);
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
	
	//Funcion que retorna el resumen por orden
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["ORDER_ID", "INTERNAL_NUMBER", "COMPANY_NAME", "COUNTRY", "STATE_NAME", "REGISTERED_ON", "REGISTERED_BY", "ITEMS", "TOTAL", "ID_STATE", "BADGE"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
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
			"data" => array());
			
		$disabled = "";
		if($_SESSION["vtappcorp_useraccessid"] <= 50)
			$disabled = "disabled";
		
		//Arma la sentencia SQL
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == $fields[0]) {
						$state = intval($aRow[9]);
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view-order.php');\"><i class=\"fa fa-eye\"></i></button>";
						//Verifica si puede editarla o no
						if($state < 3)
							$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','editorder.php');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						else 
							$edit = "";
						//Verifica si puede procesarla o no
						if($state < 5)
							$process = "<button type=\"button\" class=\"btn btn-success\" title=\"" . $_SESSION["PROCESS"] . "\" onclick=\"show('" . $aRow[$i] . "','processorder.php');\" $disabled><i class=\"fa fa-cogs\"></i></button>";
						else
							$process = "";
						//Verifica si puede entregarla o no
						if($state == 4)
							$deliver = "<button type=\"button\" class=\"btn btn-success\" title=\"" . $_SESSION["DELIVER"] . "\" onclick=\"show('" . $aRow[$i] . "','deliverorder.php');\" $disabled><i class=\"fa fa-truck\"></i></button>";
						else
							$deliver = "";
						//Verifica si puede cancelarla o no
						if($state < 5)
							$cancel = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["CANCEL"] . "\" onclick=\"show('" . $aRow[$i] . "','cancelorder.php');\" $disabled><i class=\"fa fa-times-circle\"></i></button>";
						else 
							$cancel = "";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $view . $edit . $process . $deliver . $cancel . "</div></div>";
						$row[$aColumnsBD[$i]] = $aRow[$i];
						$row[$aColumnsBD[count($aColumnsBD)-1]] = $action;
					}
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"];
				}
				else if($aColumnsBD[$i] == "ID") {
					$first = "<input type=\"checkbox\" class=\"flat\" name=\"table_records\" value=\"" . $this->inter->Encriptar($aRow[0]) . "\" data-name=\"$aRow[1]\">";
					$row[$aColumnsBD[$i]] = $first;
				}
				else if($aColumnsBD[$i] == "STATE_NAME") {
					$row[$aColumnsBD[$i]] = "<span class=\"" . $aRow[9] . "\">" . $aRow[$i] . "</span>";
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
		if($_SESSION["vtappcorp_useraccessid"] > 50)
			$readonly = array("", "readonly=\"readonly\"", "", "");
		else
			$readonly = array("", "readonly=\"readonly\"", "disabled", "disabled");
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		$onlyfield = true;
		//Definicion de la forma
		$form = "";

		//Cadena a retornar
		$form .= $this->showField("INTERNAL_NUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++], null);
		
		$date = $showvalue ? (($this->REGISTERED_ON != "NOW()") ? $this->REGISTERED_ON : date("Y-m-d")) : date("Y-m-d");
		$form .= $this->showField("REGISTERED_ON", "$stabs\t", "", "", $showvalue, $date, false, "9,9,12", $readonly[$cont++], null);
		
		$form .= "$stabs\t<div class=\"form-group\">\n";
		$form .= "$stabs\t\t<label>" . $_SESSION["CLIENT"] . " *</label>\n";
		$form .= "$stabs\t\t\t<select class=\"form-control\" style=\"width: 100%;\" id=\"cbClient\" name=\"cbClient\" " . $readonly[$cont++] . ">\n";
		$form .= $this->client->showOptionList(8,$showvalue ? $this->ID_CLIENT : "");
		$form .= "$stabs\t\t\t</select>\n";
		$form .= "$stabs\t</div>\n";
		
		if($action != "new") {
			//Arma la sentencia sql para mostrar los items
			$this->sql = "SELECT ID_ORDER, INTERNAL_NUMBER, REGISTERED_ON, DETAIL_ORDER_ID, COMPANY_NAME, PRODUCT_ID, PRODUCT_NAME, " . //0-6
						"QUANTITY, PRICE, FACTOR, ID_ORDER_ROW, CODE, UNIT, TOTAL, " . //7-13
						"money-bill-1_FACTOR, money-bill-1TYPE, QTY_DELIVERED, QTY_PROCESSED, ID_STATE, STATE_NAME, BADGE " . //14-20
						"FROM $this->view2 " .
						"WHERE ID_ORDER = '" . $this->ID . "' AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " ORDER BY ID_ORDER_ROW";

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
				$badge = "<span class=\"" . $row[20] . "\">" . $row[19] . "</span>";
				$trm = ($row[14] != 1) ? "<span class=\"badge bg-primary\">" . $_SESSION["TRM_ABBRV"] . "</span>" : "";
				$conv = ($row[9] != 1) ? "<span class=\"badge bg-warning\">" . $_SESSION["CONVERSION_ABBRV"] . "</span>" : "";
				$total = $row[7] * $row[8] * $row[9] * $row[14];
				$totalOrder += $total;
				$counter++;
				//Si es para una tabla
				$return .= "<tr>\n";
				$return .= "<td>$row[10]</td>\n";
				$return .= "<td>$row[11]</td>\n";
				$return .= "<td>$row[6] $badge $trm $conv</td>\n";
				$return .= "<td>$row[12]</td>\n";
				$return .= "<td>" . number_format($row[7],2,".",",") . "</td>\n";
				$return .= "<td>$ " . number_format($row[8],2,".",".") . "</td>\n";
				$return .= "<td>$ " . number_format($total,2,".",",") . "</td>\n";
				$return .= "</tr>\n";
				//Para editar
				$result .= "<tr>\n";
				$result .= "<td>$row[10]</td>\n";
				$result .= "<td>$row[11]</td>\n";
				$result .= "<td>$row[6]</td>\n";
				$result .= "<td>$row[12]</td>\n";
				$result .= "<td>$ " . number_format($row[7],2,",",".") . "</td>\n";
				$result .= "<td>" . number_format($row[8],2,",",".") . "</td>\n";
				$result .= "<td>$ " . number_format($total,2,",",".") . "</td>\n";
				$result .= "<td><div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">\n";
				//Botones
				$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show($row[10],'view');\"><i class=\"fa fa-eye\"></i></button>";
				$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show($row[10],'edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
				$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"deleteItem($row[10]);\"><i class=\"fa fa-trash\"></i></button>";
				//Data
				$data = array("cbProduct"=> $row[8],
								"hfID"=> $row[8],
								"hfCODE"=> $row[14],
								"hfUNIT"=> $row[15],
								"txtQUANTITY"=> $row[10],
								"txtPRICE"=> $row[11],
								"txtTOTAL"=> $total,
								"hfFactor"=> $row[12],
								"hfmoney-bill-1Factor"=> $row[16],
								"hfIdOrder" => $row[0],
								"hfIdOrderDetail" => $row[3]);
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