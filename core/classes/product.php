<?

// LOGICA ESTUDIO 2018

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("area.php");
require_once("category.php");
require_once("conversion.php");
require_once("phpqrcode/qrlib.php");
require_once("phpbarcode/BarcodeGenerator.php");
require_once("phpbarcode/BarcodeGeneratorPNG.php");

class product extends table {
	var $resources;
	var $view;
	var $area;
	var $category;
	var $conversion;
	
	//Constructor
	function __constructor($product = "") {
		$this->product($product);
	}
	
	//Constructor anterior
	function product($product  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_PRODUCT");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $product;
		$this->CODE = $this->getNextCode();
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->area = new area();
		$this->category = new category();
		$this->conversion = new conversion();
		$this->view = "VIE_PRODUCT_SUMMARY";		
	}

	//Funcion que muestra el texto del resource
	function getResource() {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = " . $this->_checkDataType("ID");
        //Variable a retornar
        $result = "";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            $result = $row[0];
        }
        //Retorna
        return $result;
	}
	
	//Funcion que busca el nombre del area
	function getResourceById($id) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = $id";
        //Variable a retornar
        $result = "";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            $result = $row[0];
        }
        //Retorna
        return $result;
	}
	
    //Funcion para Set el area
    function setArea($area) {
        //Asigna la informacion
        $this->area->ID = $area;
        //Verifica la informacion
        $this->area->__getInformation();
        //Si no hubo error
        if($this->area->nerror == 0) {
            //Asigna el valor
            $this->ID_AREA = $area;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_AREA = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Area " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el area
    function getArea() {
        //Asigna el valor del escenario
        $this->ID_AREA = $this->area->ID;
        //Busca la informacion
        $this->area->__getInformation();
    }
	
    //Funcion para Set la categoria
    function setCategory($category) {
        //Asigna la informacion
        $this->category->ID = $category;
        //Verifica la informacion
        $this->category->__getInformation();
        //Si no hubo error
        if($this->category->nerror == 0) {
            //Asigna el valor
            $this->ID_CATEGORY = $category;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ID_CATEGORY = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Category " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get la categoria
    function getCategory() {
        //Asigna el valor del escenario
        $this->ID_CATEGORY = $this->category->ID;
        //Busca la informacion
        $this->category->__getInformation();
    }	
	
	//Funcion para obtener proximo codigo
	function getNextCode() {
		//Arma la sentencia SQL
		$this->sql = "SELECT MAX(CONVERT(CODE,UNSIGNED INTEGER)) FROM $this->table";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return sprintf('%04d', ($return + 1));	
	}
	
	//Funcion para contar las ordenes
	function getTotalCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = 0";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}

	//Funcion para generar el QR CODE
	function generateQRCode() {
		return QRcode::png($this->ID);
	}
	
	//Funcion para generar el cdigo de barras
	function generateBarCode($field = "CODE") {
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		return '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($this->$field, $generator::TYPE_CODE_128)) . '">';
	}
	
	//Funcion que activa o habilita a un producto
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
	
	//Funcion que registra un movimiento
	function registerMove($type, $qty, $factor = 1) {
		//Factor
		$quantity = (($qty * $factor) * ($type == 1 ? 1 : -1));
		//Modifica el valor
		$this->QUANTITY += $quantity;
		//Lo modifica
		$this->sql = "UPDATE " . $this->table . " SET QUANTITY = " . $this->_checkDataType("QUANTITY") . " WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Desglosa la especificacion
	function expandSpecification($specification) {
		//Define el retorno
		$return = array(0 => null,
						1 => null,
						2 => null);
		//Verifica que no tenga comas
		$specification = str_replace(",",".",$specification);
		//Si es una especificacion válida tiene una x
		if(strpos($specification,"x") !== false) {
			//Divide la especificacion
			$arrDat = explode("x",$specification);
			//Verifica la informacion
			if(count($arrDat) > 0) {
				//Lo recorre y ajusta
				foreach($arrDat as $key => $value) {
					//Quita los espacios y otros caracteres que no son numeros
					$return[$key] = preg_replace('/[^\\d.]+/', '', $value);
				}
			}
		}
		return $return;
	}
	
	//Funcion que revisa la conversion
	function checkConversion($observation, $specification) {
		//Asigna el valor
		$this->conversion->MATERIALTYPE = $observation;
		//Busca el valor en la especificacion
		$specs = $this->expandSpecification($specification);
		//Verifica la observacion
		switch($observation) {
			case "Sechskantmaterial":
			case "Vierkantmaterial":
			case "Rundmaterial":
				//Asigna los valores
				$this->conversion->HEIGHT = $specs[0];
				break;
			case "Flachstahl":
				//Asigna los valores
				$this->conversion->WIDTH = $specs[0];
				$this->conversion->HEIGHT = $specs[1];
				break;
		}
		//Verifica los valores
		$this->conversion->getInformationByOtherInfo();
	}
		
	//Funcion que despliega los valores en un producto
	function showOptionList($tabs = 8,$selected = "", $lang = 0, $adddata = false, $client = false) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT A.PRODUCT_ID, A.PRODUCT_NAME, A.QUANTITY, A.PRICE, A.money-bill-1TYPE, A.CODE, A.SPECIFICATION, A.OBSERVATION, A.money-bill-1_TO, A.VALUE_TO " . //,  A.CONVERSION_ID, A.WIDTHUNIT, A.HEIGHTUNIT, A.WEIGHTUNIT " .
				"FROM $this->view A WHERE A.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Verifica si es mostrar el listado para clientes
		if($client && ($cate = $this->category->getClientCategory()) != "")
			$this->sql .= " AND ID_CATEGORY = '$cate'";	
		echo $this->sql;
		//Variable a retornar
		$return = "";
		$add = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			if($adddata) {
				$und = "UND";
				$factor = 1;
				//Si hay observacion
				if($row[7] != "") {
					//Verifica la conversion
					$this->checkConversion($row[7], $row[6]);
					//Si no hay error
					if($this->conversion->nerror == 0) {
						//Actualiza los valores
						$factor = $this->conversion->FACTOR;
						$und = $this->conversion->FACTORUNIT;
					}
				}
				$add = "data-product-quantity=\"" . $row[2] . "\" data-product-price=\"" . $row[3] . "\" data-product-money-bill-1type=\"" . $row[4] . "\" data-code=\"" . $row[5] . "\" data-unit=\"$und\" " .
					"data-factor=\"" . $factor . "\" data-factormoney-bill-1=\"" . $row[9] . "\" data-factormoney-bill-1conversion=\"" . $row[8] . "\"";
			}
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected $add>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' $add>" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion que despliega los valores en un producto
	function showOptionJSON($tabs = 8,$selected = 0, $lang = 0, $adddata = false) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT A.PRODUCT_ID, A.PRODUCT_NAME, A.QUANTITY, A.PRICE, A.money-bill-1TYPE, A.CODE, A.SPECIFICATION, A.OBSERVATION, A.money-bill-1_TO, A.VALUE_TO " . //,  A.CONVERSION_ID, A.WIDTHUNIT, A.HEIGHTUNIT, A.WEIGHTUNIT " .
				"FROM $this->view A WHERE A.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = array("results" => array());
		$add = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			$more = null;
			if($adddata) {
				$und = "UND";
				$factor = 1;
				//Si hay observacion
				if($row[7] != "") {
					//Verifica la conversion
					$this->checkConversion($row[7], $row[6]);
					//Si no hay error
					if($this->conversion->nerror == 0) {
						//Actualiza los valores
						$factor = $this->conversion->FACTOR;
						$und = $this->conversion->FACTORUNIT;
					}
				}
				$more = array("product-quantity" => $row[2],
								"product-price" => $row[3],
								"product-money-bill-1type" => $row[4],
								"code" => $row[5],
								"unit" => $und,
								"factor" => $factor,
								"factormoney-bill-1" => $row[9],
								"factormoney-bill-1conversion" => $row[8]);
			}
			$data = array("id" => $row[0],
						"text" => $row[1],
						"selected" => ($row[0] == $selected),
						"datas" => $more);
			array_push($return["results"],$data);
		}
		//Retorna
		return $return;
	}

	//Funcion que retorna el resumen por producto
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["PRODUCT_ID", "PRODUCT_NAME", "CODE", "SPECIFICATION", "TRADE", "QUANTITY", "PRICE", "money-bill-1TYPE", "OBSERVATION", "FIELD", "AREA_NAME", "CATEGORY_NAME", "REGISTERED_ON", "REGISTERED_BY", "IS_BLOCKED", "LANGUAGE_ID"];
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		else
			$sWhere .= " WHERE LANGUAGE_ID = " . $_SESSION["LANGUAGE"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(PRODUCT_ID) FROM $this->view $sWhere";
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
					if($aColumnsBD[$i] == "PRODUCT_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[14])
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$qrcode = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["QR_CODE"] . "\" onclick=\"QRCode('" . $aRow[$i] . "','" . $aRow[1] . "');\"><i class=\"fa fa-qrcode\"></i></button>";
						$barcode = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["BAR_CODE"] . "\" onclick=\"BarCode('" . $aRow[$i] . "','" . $aRow[1] . "');\"><i class=\"fa fa-barcode\"></i></button>";
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $qrcode . $barcode . $activate . $view . $edit . $delete . "</div></div>";
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
		$valcode = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "", "", "readonly=\"readonly\"", "", "", "", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$valcode = $this->getNextCode();
			$link = "core/actions/_save/__newProduct.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "readonly=\"readonly\"", "", "", "", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editProduct.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteProduct.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmProduct\" name=\"frmProduct\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ID_AREA"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" " . $readonly[$cont++] . ">\n";
		$return .= $this->area->showOptionList(8,$showvalue ? $this->ID_AREA : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ID_CATEGORY"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbCategory\" name=\"cbCategory\" " . $readonly[$cont++] . ">\n";
		$return .= $this->category->showOptionList(8,$showvalue ? $this->ID_CATEGORY : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= $this->showField("CODE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("RESOURCE_NAME", "$stabs\t", "", "", $showvalue, $this->getResource(), false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("SPECIFICATION", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("TRADE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("QUANTITY", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("PRICE", "$stabs\t", "fa fa-money-bill-1", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("money-bill-1TYPE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("OBSERVATION", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("FIELD", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);

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
