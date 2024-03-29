<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("order.php");
require_once("product.php");

class order_detail extends table {
	var $resources;
	var $view;
	var $_order;
	var $product;
	var $TOTAL;
	
	//Constructor
	function __constructor($order_detail = "") {
		$this->_order_detail($order_detail);
	}
	
	//Constructor anterior
	function order_detail ($order_detail  = '') {
		//Llamado al constructor padre
		parent::table("TBL_DETAIL_ORDER");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->_order = new order();
		$this->product = new product();
		$this->TOTAL = 0;
		$this->view = "VIE_DETAIL_ORDER_SUMMARY";		
	}
	
    //Funcion para Set la orden
    function setOrder($order) {
        //Asigna la informacion
        $this->_order->ID = $order;
        //Verifica la informacion
        $this->_order->__getInformation();
        //Si no hubo error
        if($this->_order->nerror == 0) {
            //Asigna el valor
            $this->ID_ORDER = $order;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_ORDER = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Order " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la orden
    function getOrder() {
        //Asigna el valor del movimiento
        $this->ID_ORDER = $this->_order->ID;
        //Busca la informacion
        $this->_order->__getInformation();
    }	
	
    //Funcion para Set el producto
    function setProduct($product) {
        //Asigna la informacion
        $this->product->ID = $product;
        //Verifica la informacion
        $this->product->__getInformation();
        //Si no hubo error
        if($this->product->nerror == 0) {
            //Asigna el valor
            $this->ID_PRODUCT = $product;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_PRODUCT = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Product " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el producto
    function getProduct() {
        //Asigna el valor del producto
        $this->ID_PRODUCT = $this->product->ID;
        //Busca la informacion
        $this->product->__getInformation();
    }		
	
	//Funcion para calcular el total
	function getTotalOrder() {
		//Arma la sentencia SQL
		$this->sql = "SELECT SUM(QUANTITY*PRICE*FACTOR*money-bill-1_FACTOR) FROM $this->view";
		//Obtiene los resultados
        $row = $this->__getData();
		//Total a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
		return $return;	
	}

	//Funcion para calcular el maximo orden
	function getMaxOrder() {
		//Arma la sentencia SQL
		$this->sql = "SELECT MAX(ID_ORDER_ROW) FROM $this->view WHERE ID_ORDER = " . $this->_checkDataType("ID_ORDER");
        //Obtiene los resultados
        $row = $this->__getData();
		//Total a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
		return $return;	
	}

	//Funcion para calcular la cantidad procesada
	function QtyProcessed() {
		//Arma la sentencia SQL
		$this->sql = "SELECT SUM(QYT_PROCESSED), SUM(QUANTITY) FROM $this->table WHERE ID_ORDER = " . $this->_checkDataType("ID_ORDER");
        //Obtiene los resultados
        $row = $this->__getData();
		//Total a retornar
		$return = true;
        //Registro existe
        if($row)
			$return = ($row[0] == $row[1]);
		return $return;	
	}
	
	//Funcion para calcular la cantidad entregada
	function QtyDelivered() {
		//Arma la sentencia SQL
		$this->sql = "SELECT SUM(QYT_DELIVERED), SUM(QUANTITY) FROM $this->table WHERE ID_ORDER = " . $this->_checkDataType("ID_ORDER");
        //Obtiene los resultados
        $row = $this->__getData();
		//Total a retornar
		$return = true;
        //Registro existe
        if($row)
			$return = ($row[0] == $row[1]);
		return $return;	
	}
	
	//Funcion que despliega los valores en las ordenes
	function showMovementList($tabs = 8,$selected = 0) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT ID_ORDER, INTERNAL_NUMBER, REGISTERED_ON, COMPANY_NAME " .
				"FROM $this->view WHERE IS_BLOCKED = FALSE ORDER BY REGISTERED_ON";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . " (" . $row[2] . " " . $row[3] . ")</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . " (" . $row[2] . " " . $row[3] . ")</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion para mostrar la tabla de datos
	function showTable($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT MOVEMENT_ID, INTERNAL_NUMBER, MOVE_DATE, DETAIL_ID, MOVEMENT_TYPE_ID, RESOURCE_TEXT, EMPLOYEE_ID, APPLIED, IS_BLOCKED, EMPLOYEE_NAME, PRODUCT_ID, FACTOR, ID_ORDER, CODE, PRODUCT_NAME, UNIT, " .
			"QUANTITY, PRICE, FACTOR, QUANTITY*PRICE*FACTOR*money-bill-1_FACTOR, money-bill-1_FACTOR FROM $this->view WHERE LANGUAGE_ID = $lang AND MOVEMENT_ID = " . $this->_checkDataType("ID_MOVEMENT") . " ORDER BY ID_ORDER";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("cbProduct" => $row[10],
						"hfID" => $row[10],
						"hfCODE" => $row[13], 
						"hfUNIT" => $row[15],
						"txtEXISTENCE" => 0,
						"txtQUANTITY" => $row[16],
						"txtPRICE" => $row[17],
						"txtTOTAL" => $row[19],
						"hfFactor" => $row[18],
						"hfmoney-bill-1Factor" => $row[20]);			
			$ids = $row[12];
			$buttons = "<div class=\"btn-toolbar\" role=\"toolbar\">";			
			$buttons .= "<div class=\"btn-group notika-group-btn\">";
			$buttons .= "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show($ids,'view');\"><i class=\"fa fa-eye\"></i></button>";
			$buttons .= "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show($ids,'edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
			$buttons .= "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"delete($ids);\"><i class=\"fa fa-trash\"></i></button>";
			$buttons .= "<input type=\"hidden\" name=\"hfRow_$ids\" id=\"hfRow_$ids\" value='" . json_encode($data) . "'/>";
			$buttons .= "</div></div>";
			//Completa la fila
			$return .= "<tr><td>$ids</td><td>$row[13]</td><td>$row[14]</td><td>$row[15]</td><td>$ " . number_format($row[17],2,".",",") . "</td><td>$row[16]</td><td>$ " . number_format($row[19],2,".",",") . "</td><td>$buttons</td></tr>";
		}
		return $return;

	}

	//Funcion que lista la tabla
	function listTable($lang = 0, $currency = "") {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT ID_ORDER, INTERNAL_NUMBER, REGISTERED_ON, DETAIL_ORDER_ID, APPLIED, IS_BLOCKED, PRODUCT_ID, FACTOR, " . //0-7
					"ID_ORDER_ROW, CODE, PRODUCT_NAME, UNIT, QUANTITY, PRICE, FACTOR <> 1 AS FCH, " . //8-14
					"TOTAL, (money-bill-1_FACTOR <> 1) money-bill-1_FACTOR, money-bill-1TYPE " . //15-17
					"FROM $this->view " .
					"WHERE LANGUAGE_ID = $lang AND ID_ORDER = " . $this->_checkDataType("ID_ORDER") . 
					" ORDER BY ID_ORDER_ROW";
		//Variable a retornar
		$return = "";
		$this->TOTAL = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$this->TOTAL += $row[15];
			$badgeTRM = $row[16] == 0 ? "" : "<span class=\"badge bg-primary\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["TRM_MSG"] . "\">" . $_SESSION["TRM_ABBRV"] . "</span>";
			$badgeFAC = $row[14] == 0 ? "" : "<span class=\"badge bg-warning\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["CONVERSION_MSG"] . "\">" . $_SESSION["CONVERSION_ABBRV"] . "</span>";
			$badgeAPP = $row[4] == 1 ? "" : "<span class=\"badge bg-danger\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["APPLIED_MSG"] . "\">" . $_SESSION["APPLIED_ABBRV"] . "</span>";
			$return .= "<tr>\n";
			$return .= "<td>$row[8]</td>\n";
			$return .= "<td>$row[9]</td>\n";
			$return .= "<td>$row[10] $badgeFAC $badgeTRM $badgeAPP</td>\n";
			$return .= "<td>$row[11]</td>\n";
			$return .= "<td>" . number_format($row[12],4,".",",") . "</td>\n";
			$return .= "<td>$row[21] $ " . number_format($row[13],2,".",",") . "</td>\n";
			$return .= "<td>$currency $ " . number_format($row[14],2,".",",") . "</td>\n";
			$return .= "</tr>\n";
		}
		return $return;
	}

	/*
	********************
	NO ESTA IMPLEMENTADA
	********************
	*/
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
			$readonly = array("readonly=\"readonly\"", "", "", "", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newMovementOutput.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__newMovementOutput.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__newMovementOutput.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form class=\"form-horizontal form-label-left\" id=\"frmMovementOutput\" name=\"frmMovementOutput\" role=\"form\">\n";
		//Muestra la GUI
		
		$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label class=\"control-label col-md-3 col-sm-3 col-xs-12\">" . $this->arrColComments["ID_PRODUCT"] . " *</label>\n";
		$return .= "$stabs\t\t<div class=\"col-md-9 col-sm-9 col-xs-12\">\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbProduct\" name=\"cbProduct\" " . $readonly[$cont++] . ">\n";
		$return .= $this->product->showOptionList(8,$showvalue ? $this->ID_PRODUCT : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t\t</div>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= $this->showField("QUANTITY", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++], $reso);
		$return .= $this->showField("PRICE", "$stabs\t", "fa fa-money-bill-1", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}

	//Funcion para mostrar las barras de progreso al lado de la grafica
	//Ordenes por bodega
	function showWarehouseProgress($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Conteo de ordenes
		$orders = $this->_order->getTotalOrders();
		//Arma la sentencia SQL
		$this->sql = "SELECT ID_AREA, AREA_NAME, TITLE, COUNT(DISTINCT INTERNAL_NUMBER) " .
				"FROM $this->view WHERE WAREHOUSE = 1 AND LANGUAGE_ID = $lang GROUP BY ID_AREA, AREA_NAME, TITLE ORDER BY COUNT(DISTINCT INTERNAL_NUMBER), TITLE LIMIT 3";
		//Variable a retornar
		$return = "";
		$colors = ["bg-primary", "bg-danger", "bg-warning", "bg-success"];
		$counter = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$return .= "<div class=\"progress-group\">" . sprintf($_SESSION["WAREHOUSE_TITLE_NAME"],$row[2]) . "\n";
			$return .= "<span class=\"float-right\"><b>" . number_format($row[3],0,".",",") . "</b>/" . number_format($orders,0,",",".") . "</span>\n";
			$return .= "<div class=\"progress progress-sm\">\n";
			$return .= "<div class=\"progress-bar " . $colors[$counter++] . "\" style=\"width: " . ceil(($row[3]/$orders) * 100) . "%\"></div>\n</div>\n</div>\n";
		}
		$rest = $orders - $counter;
		if($rest > 0) {
			//Completa para 100%
			$return .= "<div class=\"progress-group\">" . $_SESSION["OTHER_AREAS"] . "\n";
			$return .= "<span class=\"float-right\"><b>" . number_format($rest,0,".",",") . "</b>/" . number_format($orders,0,",",".") . "</span>\n";
			$return .= "<div class=\"progress progress-sm\">\n";
			$return .= "<div class=\"progress-bar " . $colors[$counter] . "\" style=\"width: " . ceil(($rest/$orders) * 100) . "%\"></div>\n</div>\n</div>\n";
		}
		return $return;
	}
	
	private function isInArray($array, $value, $key = "") {
		$return = false;
		foreach($array as $clave => $valor) {
			if($key != "") {
				$return = ($clave == $key && $valor == $value);
				if($return)
					return $return;
			}
			else 
				if($valor == $value) 
					return $clave;
		}
		return $return;
	}
	
	private function getTotalRecordsForGraph() {
		//Obtiene el total de datos
		$this->sql = "SELECT COUNT(DISTINCT A.YEARMONTH), COUNT(DISTINCT A.TITLE) FROM (" . $this->sql . ") A";
        //Obtiene los resultados
        return $this->__getData();
	}
	

	function getChartTitle($lang = 0, $back = 6) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Genera la sentencia SQL
		$this->sql = "SELECT MIN(MOVE_DATE) ". 
				"FROM $this->view WHERE MOVE_DATE > DATE_SUB(NOW(), INTERVAL $back MONTH) AND LANGUAGE_ID = $lang";
		$return = $_SESSION["ORDERS"] . ": ";
        //Registro existe
        if(!$row) {
			$timestamp = strtotime($row[0]);
			$return .= date("j M,Y",$timestamp) . " - ";
		}
		//Completa con el dia actual
		$return .= date("j M,Y");
		//Retorna 
		return $return;
	}
	
	//Funcion para generar los datos del grafico de inicio al dashboard
	function showGraphData($lang = 0, $back = 6) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Genera la sentencia SQL
		$this->sql = "SELECT MONTH(MOVE_DATE) MONTH_NUMBER, YEAR(MOVE_DATE) YEAR, MONTHNAME(MOVE_DATE) MONTH_NAME, TITLE, " .
					"SUM(QUANTITY*PRICE*FACTOR*money-bill-1_FACTOR) TOTAL, CONCAT(YEAR(MOVE_DATE),LPAD(MONTH(MOVE_DATE),2,'0')) YEARMONTH ".
				"FROM $this->view WHERE MOVE_DATE > DATE_SUB(NOW(), INTERVAL $back MONTH) AND LANGUAGE_ID = $lang " . 
				"GROUP BY MONTH(MOVE_DATE), YEAR(MOVE_DATE), MONTHNAME(MOVE_DATE), TITLE, CONCAT(YEAR(MOVE_DATE),LPAD(MONTH(MOVE_DATE),2,'0')) " .
				"ORDER BY YEAR(MOVE_DATE), MONTH(MOVE_DATE), TITLE";
						
		//Obtiene todos los datos
		$allData = $this->__getAllData();
		//Obtiene el total de registros
		$regs = $this->getTotalRecordsForGraph();
		//Si no es nulo
		$meses = ($regs) ? $regs[0] : 1;
		$areas = ($regs) ? $regs[1] : 1;

		$result = new graphDashboard($meses,$areas);
		//Recorre la data
		foreach($allData as $row) {
			//Define el mes
			$monthName = $row[2] . " " . $row[1];
			//Define el indice del dato
			$indexData = $result->isLabelDefined($monthName);
			//Escribe la informacion
			$index = $result->isDataDefined($row[3],$indexData,$row[4]);
			//Si no lo encuentra
			if($index == -1) {
				$data = new dataSet($meses);
				$data->label = $row[3];
				$data->data[$indexData] = floatval($row[4]);
				array_push($result->datasets,$data);
			}
		}

/*
		//Genera el resultado
		$result = array("labels" => array(),
						"datasets" => array());
		
		$initData = array_fill(0,$meses,0);
		
		//Recorre los valores
		foreach($allData as $row) {
			//Obtiene el indice del mes
			$index = $this->isInArray($result["labels"],$row[2] . " " . $row[1]);
			//Si no esta en el array de labels
			if($index === false) {
				//Lo crea
				array_push($result["labels"],$row[2] . " " . $row[1]);
				$index = key(end($result["labels"]));
			}
			
			$indexData = false;
			
			//Si existe en el array de datasets
			foreach($result["datasets"] as $dataset) {
				$indexData = $this->isInArray($dataset,$row[3],"label");
				//Si el dato NO esta en datasets
				//if($indexData === false)
				//	$indexData = true;
			}
			
			if(!$indexData) {
				//Lo agrega
				$data = array("label" => $row[3],
								"fillColor" => '#dee2e6',
								"strokeColor" => '#ced4da',
								"pointColor" => '#ced4da',
								"pointStrokeColor" => '#c1c7d1',
								"pointHighlightFill" => '#fff',
								"pointHighlightStroke" => 'rgb(220,220,220)',
								"data" => array($row[4]));
				//Lo agrega
				array_push($result["datasets"],$data);
			}
		}
*/	
		return json_encode($result);
	}
}

class graphDashboard {
	var $labels = array();
	var $datasets = array();
	
	function graphDashboard($months, $datas) {
		$this->labels = array_fill(0,$months,"");
		$this->datasets = array();//array_fill(0,$datas,new dataSet($months));
	}
	
	function isLabelDefined($value) {
		//Posicion incial
		$index = -1;
		$empty = -1;
		//Recorre los valores
		foreach($this->labels as $key => $label) {
			//Si encuentra el label
			if($label == $value) {
				//Define el indice
				$index = $key;
				break;
			}
			//Verifica posicion donde escribir
			$empty = ($empty == -1) ? ($label == "" ? $key : $empty) : $empty;
		}
		//Si no lo encuentra
		if($index == -1) {
			//Redefine al principio
			$index = $empty;
			$this->labels[$index] = $value;
		}
		return $index;
	}
	
	//$value = Nombre del label a buscar
	//$indexData = Indice de la posicion donde escribir $valueData
	function isDataDefined($value,$indexData,$valueData) {
		//Posicion incial
		$index = -1;
		//Recorre los valores
		foreach($this->datasets as $key => $data) {
			//Si encuentra el label
			if($data->label == $value) {
				//Define el indice
				$index = $key;
				//Termina
				return $index;
			}
		}
		//Retorna
		return $index;
	}
}

class dataSet {
	var $label = "";
	var $fillColor = "#dee2e6";
	var $strokeColor = "#ced4da";
	var $pointColor = "#c1c7d1";
	var $pointStrokeColor = "#fff";
	var $pointHighlightFill = "rgb(220,220,220)";
	var $pointHighlightStroke = "";
	var $data = array();
	
	function dataSet($months) {
		$this->label = "";
		$this->fillColor = "#" . $this->randomColor();
		$this->strokeColor = "#" . $this->randomColor();
		$this->pointColor = "#" . $this->randomColor();
		$this->pointStrokeColor = "#" . $this->randomColor();
		$this->pointHighlightFill = "rgb(" . mt_rand(0,255) . "," . mt_rand(0,255) . "," . mt_rand(0,255) . ")";
		$this->pointHighlightStroke = "";
		$this->data = array_fill(0,$months,0);
	}
	
	private function randomColorPart() {
		return str_pad(dechex(mt_rand(0,255)),2,'0',STR_PAD_LEFT);
	}
	
	private function randomColor() {
		return $this->randomColorPart() . $this->randomColorPart() . $this->randomColorPart();
	}
}

?>
