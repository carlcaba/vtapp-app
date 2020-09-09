<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("movement_detail.php");
require_once("resources.php");
require_once("PHPExcel.php");

class report1 extends table {
	var $movement;
	var $resources;
	
	//Constructor
	function __constructor() {
		$this->report1();
	}
	
	//Constructor anterior
	function report1() {
		//Llamado al constructor padre
		parent::tabla("VIE_REPORT1_SUMMARY");
		//Inicializa los atributos
		//Relaciones con otras clases
		$this->movement = new movement_detail();
		$this->resources = new resources();
	}

	//Funcion para mostrar la forma de filtrado
	function showFilterForm() {
		$return = "
									<form id=\"frmFilter\" name=\"frmFilter\" role=\"form\">
										<div class=\"form-group\">
											<label>" . $_SESSION["PRODUCT"] . "<span class=\"required\">*</span></label>
											<select class=\"form-control select2-id\" id=\"PRODUCT_ID\" name=\"PRODUCT_ID\" style=\"width: 100%;\">
												<option value=\"*\" selected>" . $_SESSION["ALL_PRODUCTS"] . "</option>";
		$return .= $this->movement->product->showOptionList(8,"",0,false);
		$return .= "
											</select>
										</div>
										<div class=\"form-group\">
											<label>" . $_SESSION["DATE_RANGE"] . "</label>
											<div class=\"input-group\">
												<div class=\"input-group-prepend\">
													<span class=\"input-group-text\">
														<i class=\"fa fa-calendar\"></i>
													</span>
												</div>
												<input type=\"text\" class=\"form-control float-right\" id=\"MOVE_DATE\" name=\"MOVE_DATE\" autocomplete=\"off\">
												<div class=\"input-group-append\">
													<button type=\"button\" class=\"btn btn-default float-right\" id=\"daterange-btn\">
														<i class=\"fa fa-caret-down\"></i>
													</button>
												</div>												
											</div>
										</div>
										<div class=\"form-group\">
											<label for=\"chkSummary\">
												<input type=\"radio\" class=\"chkICheck\" checked id=\"chkSummary\" name=\"chkSummary\">
												" . $_SESSION["BALANCE_SUMMARY"] . "	
											</label>
										</div>
										<div class=\"form-group\">
											<label for=\"chkSummary\">
												<input type=\"radio\" class=\"chkICheck\" disabled id=\"chkDetailed\" name=\"chkDetailed\">
												<span class=\"disabled\">" . $_SESSION["MOVE_SUMMARY"] . "</span>
											</label>
										</div>
										<input type=\"hidden\" name=\"hfReport\" id=\"hfReport\" value=\"report1\" />
										<input type=\"hidden\" name=\"hfClass\" id=\"hfClass\" value=\"report1\" />
									</form>\n";
		return $return;
	}
	
	//Funcion para mostrar los titulos de la tabla
	function showHeaders($lang = 0, $array = false) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Variable a retornar
		$return = $array ? array() : "";
		//Arma la sentencia SQL
		$this->sql = "SELECT RESOURCE_TEXT, RESOURCE_NAME FROM " . $this->resources->table . " WHERE RESOURCE_NAME LIKE 'REPORT1%' AND LANGUAGE_ID = $lang ".
			"ORDER BY CAST(SUBSTRING_INDEX(RESOURCE_TEXT, '.', 1) AS DECIMAL)";
		//Define los anchos
		$widths = [10,10,10,5,10,5,5,25,10,15];
		$cont = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$title = explode(".",$row[0]);
			$field = explode(".",$row[1]);
			//Si debe devolver un array
			if($array) {
				$data = array("id" => $title[0],
								"value" => $title[1],
								"name" => $field[1], 
								"show" => ($title[2] == "true"),
								"type" => $title[3]);
				array_push($return,$data);
			}
			else {
				if($title[2] == "true")
					$return .= "<th width=\"" . $widths[$cont++] . "%\">" . $title[1] . "</th>\n";
			}
		}
		return $return;
	}
	
	//Funcion para mostrar la informacion filtrada
	function filterData($reporte = "report1", $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Obtiene los headers
		$headers = $this->showHeaders($lang,true);
		//Arma la sentencia SQL
		$this->sql = "SELECT ";
		//Agrega los campos
		foreach($headers as $header) {
			if($header["show"])
				$this->sql .= $header["name"] . ",";
		}
		//Remueve la ultima coma
		$this->sql = substr($this->sql,0,-1);
		//Completa la sentencia sql
		$this->sql .= " FROM $this->table WHERE LANGUAGE_ID = $lang ";
		//Verifica los parametros
		if($reporte == "report1") {
			//Verifica el producto
			if($this->PRODUCT_ID != "" && $this->PRODUCT_ID != "*")
				$this->sql .= "AND PRODUCT_ID = " . $this->_checkDataType("PRODUCT_ID") . " ";
			if($this->MOVE_DATE != "") {
				$dates = explode(" / ",$this->MOVE_DATE);
				if(count($dates) > 1)
					$this->sql .= "AND MOVE_DATE BETWEEN '" . $dates[0] . "' AND '" . $dates[1] . "' ";
				else
					$this->sql .= "AND MOVE_DATE = " . $this->_checkDataType("MOVE_DATE") . " ";
			}
		}
		//Completa la sentencia SQL
		$this->sql .= "ORDER BY MOVE_DATE DESC";
		//Define el resultado
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$cont = 0;
			$data = array();
			foreach($headers as $value) {
				if($value["show"]) {
					$valor = $row[$cont++];
					if($value["type"] == "number") {
						$valor = number_format($valor,2,".",",");
					}
					else if($value["type"] == "currency") {
						$valor = "$ " . number_format($valor,2,".",",");
					}
					$data[$value["name"]] = $valor;
				}
			}
			array_push($return,$data);
		}
		return $return;
	}
	
	private function showHeader($headers,$name) {
		$show = false;
		foreach($headers as $header) {
			if ($name == $header["name"]) {
				$show = $header["show"];
				break;
			}
		}
		return $show;
	}

	//Funcion para mostrar el reporte impreso
	function showPrint($company, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Definiciones
		$subtitle2 = "";
		$dateCriteria = " ";
		if($this->MOVE_DATE != "") {
			$dates = explode(" / ",$this->MOVE_DATE);
			$subtitle2 = $_SESSION["REPORT1_SUBTITLE"];
			if(count($dates) > 1) {
				$subtitle2 = sprintf($subtitle2,$dates[0],$dates[1]);
				$dateCriteria = "AND MOVE_DATE BETWEEN '" . $dates[0] . "' AND '" . $dates[1] . "' ";
			}
			else {
				$subtitle2 = sprintf($subtitle2,$dates[0],"");
				$dateCriteria = "AND MOVE_DATE = " . $this->_checkDataType("MOVE_DATE") . " ";
			}
		}
		$generated = sprintf($_SESSION["GENERATED_BY"], $_SESSION["vtappcorp_userid"], date("Y-m-d"));
		
		//Titulos
		$return = "
			<!-- title row -->
			<div class=\"row\">
				<div class=\"col-12\">
					<h2 class=\"page-header text-center\">$company</h2>
					<h3 class=\"page-header text-center\">" . $_SESSION["REPORT1_TITLE"] . "</h3>"
					. ($subtitle2 != "" ? "<h3 class=\"page-header text-center\">$subtitle2</h3>" : "") .
				"</div>
				<!-- /.col -->
			</div>";
			
		//Subtitulo 3
		$return .= "
			<!-- title row -->
			<div class=\"row\">
				<div class=\"col-12\">
					<p class=\"page-header text-right\">$generated</p>
				</div>
				<!-- /.col -->
			</div>";
			
		//Subtitulo 3
		$return .= "
			<!-- title row -->
			<div class=\"row\">
				<div class=\"col-12\">
					<h4 class=\"page-header text-center\">" . $_SESSION["REPORT1_SUBTITLE_2"] . "</h4>
				</div>
				<!-- /.col -->
			</div>";
		
		//Filter data
		//Contents
		$return .= "
			<!-- Table row -->
			<div class=\"row\">
				<div class=\"col-12 table-responsive\">
					<table class=\"table table-striped\">
					<thead>
						<tr>";
		$return .= $this->showHeaders();
		$return .= "	</tr>
					</thead>
					<tbody>";

		//Obtiene los headers
		$headers = $this->showHeaders($lang,true);
		//Arma la sentencia SQL
		$this->sql = "SELECT ";
		//Agrega los campos
		foreach($headers as $header) {
			if($header["show"])
				$this->sql .= $header["name"] . ",";
		}
		//Remueve la ultima coma
		$this->sql = substr($this->sql,0,-1);
		//Completa la sentencia sql
		$this->sql .= " FROM $this->table WHERE LANGUAGE_ID = $lang ";
		//Verifica el producto
		if($this->PRODUCT_ID != "" && $this->PRODUCT_ID != "*")
			$this->sql .= "AND PRODUCT_ID = " . $this->_checkDataType("PRODUCT_ID") . " ";
		//Verifica los criterios de fecha
		$this->sql .= $dateCriteria;
		//Completa la sentencia SQL
		$this->sql .= "ORDER BY MOVE_DATE DESC";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$cont = 0;
			$return .= "<tr>";
			foreach($headers as $value) {
				if($value["show"]) {
					$valor = $row[$cont++];
					if($value["type"] == "number") {
						$valor = number_format($valor,2,".",",");
					}
					else if($value["type"] == "currency") {
						$valor = "$ " . number_format($valor,2,".",",");
					}
					$return .= "<td>$valor</td>";
				}
			}
			$return .= "</tr>";
		}
		$return .= "</tbody>
					</table>
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->";
		return $return;
	}	

	//Funcion para exportar a excel
	function exportToExcel(&$objPHPExcel, $filename, $company, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		
		//Definiciones
		$initCol = 66;
		$initRow = 7;
		
		$subtitle2 = "";
		$dateCriteria = " ";
		if($this->MOVE_DATE != "") {
			$dates = explode(" / ",$this->MOVE_DATE);
			$subtitle2 = $_SESSION["REPORT1_SUBTITLE"];
			if(count($dates) > 1) {
				$subtitle2 = sprintf($subtitle2,$dates[0],$dates[1]);
				$dateCriteria = "AND MOVE_DATE BETWEEN '" . $dates[0] . "' AND '" . $dates[1] . "' ";
			}
			else {
				$subtitle2 = sprintf($subtitle2,$dates[0],"");
				$dateCriteria = "AND MOVE_DATE = " . $this->_checkDataType("MOVE_DATE") . " ";
			}
		}
		$generated = sprintf($_SESSION["GENERATED_BY"], $_SESSION["vtappcorp_userid"], date("Y-m-d"));
		//Obtiene los headers
		$headers = $this->showHeaders($lang,true);
		
		//Agrega los titulos
		$objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B1', $company)
                            ->setCellValue('B2', $_SESSION["REPORT1_TITLE"])
                            ->setCellValue('B3', $subtitle2)
                            ->setCellValue('B4', $generated)
                            ->setCellValue('B5', $_SESSION["REPORT1_SUBTITLE_2"]);
							
		//Columnas
		$col = $initCol;
		//Agrega los titulos del reporte
		foreach($headers as $header)
			if($header["show"]) {
				$cell = chr($col++) . '6';
				$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($cell, $header["value"]);
			}
		
		//Arma la sentencia SQL
		$this->sql = "SELECT ";
		//Agrega los campos
		foreach($headers as $header) {
			if($header["show"])
				$this->sql .= $header["name"] . ",";
		}
		//Remueve la ultima coma
		$this->sql = substr($this->sql,0,-1);
		//Completa la sentencia sql
		$this->sql .= " FROM $this->table WHERE LANGUAGE_ID = $lang ";
		//Verifica el producto
		if($this->PRODUCT_ID != "" && $this->PRODUCT_ID != "*")
			$this->sql .= "AND PRODUCT_ID = " . $this->_checkDataType("PRODUCT_ID") . " ";
		//Verifica los criterios de fecha
		$this->sql .= $dateCriteria;
		//Completa la sentencia SQL
		$this->sql .= "ORDER BY MOVE_DATE DESC";
		//Contadores
		$fila = $initRow;
		$colu = $initCol;
		$cont = 0;

		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Si no es la primera fila
			if($fila > $initRow) {
				//Define los rangos
				$rangeOr = chr($initCol) . $initRow . ":" . chr($colu-1) . $initRow;
				$rangeDe = chr($initCol) . $fila . ":" . chr($colu-1) . $fila;
				//Inserta nueva fila
				$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle($rangeOr), $rangeDe);				
				//Copia los formatos
				for($iCol = $initCol; $iCol < $colu; $iCol++) {
					$celda = chr($iCol) . $initRow;
					$fmt = $objPHPExcel->getActiveSheet()->getStyle($celda)->getNumberFormat()->getFormatCode();
					$objPHPExcel->getActiveSheet()->getStyle(chr($iCol) . $fila)->getNumberFormat()->setFormatCode($fmt);
				}
				//Reestablece la columna
				$colu = $initCol;
				//Reestablece el contador
				$cont = 0;
			}
			//Muestra los valores
			foreach($headers as $value) {
				if($value["show"]) {
					$cell = chr($colu++) . $fila;
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($cell, $row[$cont++]);
				}
			}
			//Aumenta la fila
			$fila++;
		}
	}
}

?>
