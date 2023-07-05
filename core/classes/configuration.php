<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("interfaces.php");

class configuration extends table {
	//Relacion con otras clases
	var $inter;
	
	//Constructor
	function __constructor($key = "") {
		$this->configuration($key);
	}
	
	//Constructor anterior
	function configuration($key = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_CONFIGURATION");
		//Inicializa los atributos
		$this->KEY_NAME = $key;
		//Especifica los valores unicos
		$this->_addUniqueColumn("KEY_NAME");
		//Clases relacionadas
		$this->inter = new interfaces();
	}

	//Funcion que carga los valores parametrizables desde la BD
	function loadValues() {
		//Elimina los valores anteriores, si existen
		$this->unloadValues();
		//Busca los valores de la tabla configuracion
		$this->sql = "SELECT ID, KEY_NAME, IF(ENCRYPTED,AES_DECRYPT(KEY_VALUE,'" . $this->inter->clave . "'),KEY_VALUE) VALOR, KEY_TYPE " .
				"FROM $this->table WHERE LOAD_INIT = TRUE AND IS_BLOCKED = FALSE";	
		$this->sql = "SELECT ID, KEY_NAME, KEY_VALUE, KEY_TYPE, ENCRYPTED " .
				"FROM $this->table WHERE LOAD_INIT = TRUE AND IS_BLOCKED = FALSE";	
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica si el dato está encriptado
			if($row[4]) {
				$row[2] = Desencriptar($row[2]);
			}
			//Verifica el tipo de dato
			/*	0: numerico
				1: texto
				2: booleano */
			switch($row[3]) {
				case 0: {
					$_SESSION[$row[1]] = intval($row[2]);
					break;
				}
				case 1: {
					//Verifica que no esté encriptado
					$_SESSION[$row[1]] = $row[2];
					break;
				}
				case 2: {
					$_SESSION[$row[1]] = ($row[2]=="TRUE")?true:false;
					break;
				}
			}
		}
	}
	
	//Funcion que descarga los valores de la memoria
	function unloadValues() {
		//Busca los valores de la tabla configuracion
		$this->sql = "SELECT KEY_NAME FROM $this->table WHERE IS_BLOCKED = FALSE";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			unset($_SESSION[$row[0]]);
		}
	}

	//Funcion que verifica los valores parametrizables desde la BD
	function verifyValue($value = '') {
		//Declara el valor a regresar
		$result = 0;
		//Verifica el valor del parametro
		if($value != '')
			$this->KEY_NAME = $value;
		//Busca los valores de la tabla configuracion
		/*
		$this->sql = "SELECT ID, KEY_NAME, IF(ENCRYPTED,AES_DECRYPT(KEY_VALUE,'" . $this->inter->clave . "'),KEY_VALUE) VALOR, KEY_TYPE " .
					"FROM $this->table WHERE KEY_NAME = " . $this->_checkDataType("KEY_NAME") . " AND IS_BLOCKED = FALSE";
		*/
		$this->sql = "SELECT ID, KEY_NAME, KEY_VALUE, KEY_TYPE, ENCRYPTED " .
					"FROM $this->table WHERE KEY_NAME = " . $this->_checkDataType("KEY_NAME") . " AND IS_BLOCKED = FALSE";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica si el dato está encriptado
			if($row[4]) {
				$row[2] = Desencriptar($row[2]);
			}
			//Verifica el tipo de dato
			/*	0: numerico
				1: texto
				2: booleano */
			switch($row[3]) {
				case 0: {
					$result = intval($row[2]);
					break;
				}
				case 1: {
					//Verifica que no esté encriptado
					$result = $row[2];
					break;
				}
				case 2: {
					$result = (strtolower($row[2]) == "true") ? true : false;
					break;
				}
			}
		}
		//Verifica si es el theme de la aplicacion
		if($this->KEY_NAME == "APPTHEME") {
			//Verifica si esta setteada la cookie
			if(isset($_COOKIE['vtappcorpTheme']))
				$result = $_COOKIE['vtappcorpTheme'];
		}
		//Retorna el valor
		return $result;
	}
	
	//Funcion que calcula el path adicional como site root
	function getSiteRoot() {
		return substr($this->verifyValue("RETURN_HOME"),strlen($this->verifyValue("WEB_SITE"))-1);
	}
	
	//Funcion que muestra la configuracion
	function showConfig($access) {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, KEY_NAME, IF(ENCRYPTED,AES_DECRYPT(KEY_VALUE,'" . $this->inter->clave . "'),KEY_VALUE) VALOR, KEY_TYPE, " .
				"ENCRYPTED, LOAD_INIT FROM $this->table WHERE IS_BLOCKED = FALSE AND ACCESS_TO <= $access";	
		$this->sql = "SELECT ID, KEY_NAME, KEY_VALUE, KEY_TYPE, ENCRYPTED, LOAD_INIT FROM $this->table WHERE IS_BLOCKED = FALSE AND ACCESS_TO <= $access";	
		//Inicia el contador
		$cont = 1;
		$color = "";
		//Recorre el resultado
		foreach($this->__getAllData() as $row) {
			$cont = $row[0];
			//Verifica si el dato está encriptado
			if($row[4]) {
				$row[2] = Desencriptar($row[2]);
			}
			//Ajusta el diseño segun GUI
			echo "\t\t\t\t<tr>\n";

			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div align='center'>\n";
			echo "\t\t\t\t\t\t\t<input type='radio' name='opt$cont' id='opt$cont' value='edit,$row[0]' onclick='showEdit($cont,true);' title='Habilitar la edicion' />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";

			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div align='center'>\n";
			echo "\t\t\t\t\t\t\t<input type='radio' name='opt$cont' id='opt$cont' value='delete,$row[0]' onclick='showEdit($cont,false);' title='Eliminar este parametro' />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";

			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div id='showname$cont' align='center' name='showname$cont'>$row[1]</div>\n";
			echo "\t\t\t\t\t\t<div id='name$cont' style='display:none' align='center' name='name$cont'>\n";
			echo "\t\t\t\t\t\t\t<input name='txName$row[0]' type='text' id='txName$row[0]' title='Ingrese el nombre del parametro' size='20' maxlength='100' value='$row[1]' />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";

			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div id='showvalue$cont' align='center' name='showvalue$cont'>$row[2]</div>\n";
			echo "\t\t\t\t\t\t<div id='value$cont' style='display:none' align='center' name='value$cont'>\n";
			echo "\t\t\t\t\t\t\t<input name='txValue$row[0]' type='text' id='txValue$row[0]' title='Ingrese el valor del parametro' size='50' maxlength='100' value='$row[2]' />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";
			
			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div id='showtype$cont' align='center' name='showtype$cont'>";
			//Muestra el tipo de dato
			switch($row[3]) {
				case 0:	{	//Numerico
					echo "NUM";
					break;
				}
				case 1:	{	//Texto
					echo "TEXTO";
					break;
				}
				case 2: {	//Booleano
					echo "BOOL";
					break;
				}
			}
			echo "</div>\n";
			echo "\t\t\t\t\t\t<div id='type$cont' style='display:none' align='center' name='type$cont'>\n";
			echo "\t\t\t\t\t\t\t<select name='cbType$cont' id='cbType$cont' title='Seleccione el tipo de dato'>\n";
			echo "\t\t\t\t\t\t\t\t<option value='-1'>--NDEF</option>\n";
			echo "\t\t\t\t\t\t\t\t<option value='0'";
			if($row[3]==0)
				echo " selected";
			echo ">Num</option>\n";
			echo "\t\t\t\t\t\t\t\t<option value='1'";
			if($row[3]==1)
				echo " selected";
			echo ">Texto</option>\n";
			echo "\t\t\t\t\t\t\t\t<option value='2'";
			if($row[3]==2)
				echo " selected";
			echo ">Bool</option>\n";
			echo "\t\t\t\t\t\t\t</select>\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";
			
			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div id='showencr$cont' align='center' name='showencr$cont'>";
			if($row[4])
				echo $_SESSION["MSG_YES"];
			else
				echo $_SESSION["MSG_NO"];
			echo "</div>\n";
			echo "\t\t\t\t\t\t<div id='encr$cont' style='display:none' align='center' name='encr$cont'>\n";
			echo "\t\t\t\t\t\t\t<input type='checkbox' name='chkEnc$cont' id='chkEnc$cont' value='TRUE' onclick='showEdit($cont,false);' title='Parametro encriptado'";
			if($row[4])
				echo " checked";
			echo " />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";

			echo "\t\t\t\t\t<td $color height='32' class='normalText'>\n";
			echo "\t\t\t\t\t\t<div id='showinit$cont' align='center' name='showinit$cont'>";
			if($row[5])
				echo "Si";
			else
				echo "No";
			echo "</div>\n";
			echo "\t\t\t\t\t\t<div id='init$cont' style='display:none' align='center' name='init$cont'>\n";
			echo "\t\t\t\t\t\t\t<input type='checkbox' name='chkIni$cont' id='chkIni$cont' value='TRUE' onclick='showEdit($cont,false);' title='Cargar al iniciar'";
			if($row[5])
				echo " checked";
			echo " />\n";
			echo "\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t</td>\n";
			
			//Verifica el color de la siguente fila
			if($color == "")
				//Cambia el color de la fila
				$color = "bgcolor='#EBEDEC'";
			else
				//Cambia el color de la fila
				$color = "";
			//Incrementa el contador
			$cont++;
		}
	}
	
	//Funcion que modifica un parametro
	function modifyValue() {
		//Arma la sentencia SQL
		$this->sql = "UPDATE $this->table SET KEY_NAME = " . $this->_checkDataType("KEY_NAME") . ", KEY_VALUE = ";
		//verifica si el dato es encriptado
		if($this->ENCRYPTED == "TRUE") {
			//$this->sql .= "AES_ENCRYPT('$this->KEY_VALUE','" . $this->inter->clave . "'), ";
			$this->KEY_VALUE = Encriptar($this->KEY_VALUE);
		}
		$this->sql .= $this->_checkDataType("KEY_VALUE") . ", ";
		//Continua con la sentencia SQL
		$this->sql .= "KEY_TYPE = " . $this->_checkDataType("KEY_TYPE") . ", ENCRYPTED = " . $this->_checkDataType("ENCRYPTED") . ", LOAD_INIT = " . $this->_checkDataType("LOAD_INIT") . 
				", IS_BLOCKED = " . $this->_checkDataType("IS_BLOCKED") . " WHERE ID_ROW = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Funcion que retorna el resumen por usuario
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(ID) FROM $this->table $sWhere";
		//Verifica el where
		if($sWhere != "")
			$sWhere .= " AND ACCESS_TO <= " . $_SESSION["vtappcorp_useraccessid"];
		else
			$sWhere .= " WHERE ACCESS_TO <= " . $_SESSION["vtappcorp_useraccessid"];
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
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->table $sWhere $sOrder $sLimit";
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD);$i++) {
				if(strpos($aColumnsBD[$i],"ID") !== false) {
					//Verifica el estado para activar o desactivar
					if($aRow[5])
						$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
					else 
						$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
					
					$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
					$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
					$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
											
					$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $view . $edit . $delete . "</div></div>";
					$row[$aColumnsBD[$i]] = $aRow[$i];
					$row[$aColumnsBD[count($aColumnsBD)]] = $action;
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"];
				}
				else if($aColumnsBD[$i] == "ENCRYPTED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_YES"] : $_SESSION["MSG_NO"];
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
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$addId = false;
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", "", "", "", "", "", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newConfiguration.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "disabled=\"disabled\"", "disabled=\"disabled\"", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editConfiguration.php";
			$addId = true;
		}
		else {
			$readonly = array("readonly=\"readonly\"", "readonly=\"readonly\"", "disabled=\"disabled\"", "disabled=\"true\"", "disabled=\"true\"");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteConfiguration.php";
			$addId = true;
		}
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		$encrypted = ($this->ENCRYPTED == 1) ? "checked" : "";
		//variable a retornar
		$return = "$stabs<form id=\"frmConfiguration\" name=\"frmConfiguration\" role=\"form\">\n";
		//Muestra la GUI
		$return .= $this->showField("KEY_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("KEY_VALUE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["KEY_TYPE"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbKeyType\" name=\"cbKeyType\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t<option value=\"0\">" . $_SESSION["NUMERIC_DATA_TYPE"] . "</option>\n";
		$return .= "$stabs\t\t\t<option value=\"1\">" . $_SESSION["TEXT_DATA_TYPE"] . "</option>\n";
		$return .= "$stabs\t\t\t<option value=\"2\">" . $_SESSION["BOOL_DATA_TYPE"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ENCRYPTED"] . " *</label>\n";
		$return .= "$stabs\t\t\t\t\t" . $_SESSION["MSG_NO"] . " <input type=\"checkbox\" class=\"js-switch\" id=\"chkEncrypted\" name=\"chkEncrypted\" $encrypted " . $readonly[$cont++] . "/> " . $_SESSION["MSG_YES"] . "\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["IS_BLOCKED"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbBlocked\" name=\"cbBlocked\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t\t<option value=\"FALSE\"" . ($this->IS_BLOCKED ? "" : " selected") . ">" . $_SESSION["ACTIVE"] . "</option>\n";
		$return .= "$stabs\t\t\t\t<option value=\"TRUE\"" . ($this->IS_BLOCKED ? " selected" : "") . ">" . $_SESSION["IS_BLOCKED"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		if($addId)
			$return .= "$stabs\t<input type=\"hidden\" id=\"hfId\" name=\"hfId\" value=\"$this->ID\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}
	
}

?>