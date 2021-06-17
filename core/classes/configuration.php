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
		parent::tabla("TBL_SYSTEM_CONFIGURATION");
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
		$this->sql = "SELECT ID, KEY_NAME, IF(ENCRYPTED,AES_DECRYPT(KEY_VALUE,'" . $this->inter->clave . "'),KEY_VALUE) VALOR, KEY_TYPE " .
					"FROM $this->table WHERE KEY_NAME = " . $this->_checkDataType("KEY_NAME") . " AND IS_BLOCKED = FALSE";
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
}

?>