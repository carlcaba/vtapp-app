<?

// Version 4.0
// LOGICA ESTUDIO 2019

	define("LANGUAGE",2);
	$_SESSION["LANGUAGE"] = 2;
	setlocale(LC_TIME, "es_ES.UTF-8");
    date_default_timezone_set('America/Bogota');
	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '1');
	ini_set("log_errors", TRUE);  
	ini_set('error_log', $log_file); 

    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);	
	//error_reporting(E_ALL | E_STRICT);	
	//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);	
	
	//Incluye las clases requeridas
    require_once("connector_db.php");
	//require_once ('apache4log/Logger.php');

	//Define la clase
    abstract class table {
        //Define las variables a usar
        var $error;
        var $nerror;
        var $conx;
        var $table;
        var $sql;
        var $fields;
		private $checkResources;
        //Arreglos que definen los datos
        var $arrColTypes;
        var $arrColDatas;
        var $arrColFlags;
        var $arrColComments;
		var $arrColPrecision;
		var $arrColRelations;
        public $arrColUnique;

		//Constructor
		function __constructor($Tabla = "") {
			$this->table($Tabla);
		}
        //Constructor de la clase
        function table($TableName) {
            //Inicializa los atributos
            $this->nerror = 0;
            $this->error = "";
            $this->table = $TableName;
            $this->sql = "";
            $this->fields = 0;
			$this->checkResources = false;
            //Inicializa los arreglos
            $this->arrColTypes = array();
            $this->arrColDatas = array();
            $this->arrColFlags = array();
            $this->arrColPrecision = array();
            $this->arrColUnique = array();
            $this->arrColComments = array();
            $this->arrColRelations = array();
            //Realiza la conexion a la BD
            $this->connectIt();
            //Verifica que no haya error
            if($this->nerror > 0)
                return;
            //Completa la informacion de los atributos de la tabla
            $this->_getTableColumns();
        }

        //Destructor de la clase
        function __destruct() {
			if($this->conx != null)
				//Cierra la conexion a la BD
				$this->conx->close_it();
        }

		public static function getTableName() {
			return self::$table;
		}

        //Funcion que conecta a la BD
        private function connectIt() {
			$this->conx = connector_db::getInstance();
			if(!$this->conx->connect()) {
				$this->error = $this->conx->Error;
				$this->nerror = 10;
			}
			else {
				$this->nerror = 0;
				$this->error = "";
			}
        }
		
        //Funcion que obtiene los atributos de la tabla y los anexa al array correspondiente
        public function _getTableColumns() {
            //Verifica que no haya error
            if($this->nerror > 0)
                return;
            //Arma la sentencia SQL
            $this->sql = "SHOW FULL COLUMNS FROM $this->table";
            //Obtiene los campos
            $this->fields = 0;
            //Pasa la informacion de los nombres al arreglo
            foreach($this->__getAllData() as $row) {
                //Informacion de los nombres de los campos
                $this->arrColDatas[$row[0]] = "NDF";
				$this->arrColPrecision[$row[0]] = null;
                //Separa la informacion
                if(strpos($row[1],"(") === false)
                    //Informacion de los tipos de los campos
                    $this->arrColTypes[$row[0]] = $row[1];
                else {
                    $arrs = explode(" ",$row[1]);
                    //Informacion de los tipos de los campos
                    $this->arrColTypes[$row[0]] = substr($arrs[0],0,strpos($arrs[0],"("));
					//Verifica el tipo de precision
					if(strpos($arrs[0],",") !== false) {
						$arrP = explode(",",substr($arrs[0],0,-1));
						$this->arrColPrecision[$row[0]] = $arrP[1];
					}
                }
                //Informacion de las banderas del campo
                $this->arrColFlags[$row[0]] = $row[3] . "," . $row[4] . "," . $row[6];
                //Informacion de las columnas unicas
                $this->arrColUnique[$row[0]] = false;
                //Informacion de los comentarios
                $this->arrColComments[$row[0]] = $row[8];
				//Verifica si debe buscar los recursos
				$this->checkResources = ($row[8] == "true");
                //Asigna la variable local
                $this->__createVariable($row[0], $row[5], $this->arrColTypes[$row[0]], $this->arrColFlags[$row[0]]);
                //Aumenta el contador de campos
                $this->fields++;
            }
            //Cierra la conexion a la BD
            $this->conx->close_it();
			//Verifica las relaciones
			//$this->_getTableRelations();
        }
		
        //Funcion que obtiene las relaciones de la tabla con otras tablas
        public function _getTableRelations() {
            //Verifica que no haya error
            if($this->nerror > 0)
                return;
            //Arma la sentencia SQL
            $this->sql = "SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME ".
					" FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '" . $this->conx->database . "' ".
					" AND TABLE_NAME = '" . $this->table . "' AND NOT REFERENCED_TABLE_NAME IS NULL";
            //Pasa la informacion de los nombres al arreglo
            foreach($this->__getAllData() as $row) {
				$className = str_replace("system_","",$row[3]);
				$className = str_replace("tbl_","",$className);
				//Define la informacion
				$data = array("name" => $row[0],
								"schema" => $row[2],
								"table" => $row[3],
								"column" => $row[4],
								"class" => $className);
				$this->arrColRelations[$row[1]] = $data;
            }
            //Cierra la conexion a la BD
            $this->conx->close_it();
        }

        //Funcion que crea las variables locales con el nombre del campo
        function __createVariable($name, $default, $type, $flags) {
            //Verifica el valor por default
            if($name == "REGISTERED_BY") {
                //Asigna el usuario
                $default = isset($_SESSION['vtappcorp_userid']) ? $_SESSION['vtappcorp_userid'] : "CURRENT_USER()";
            }
            else if($name == "REGISTERED_ON") {
                //Asigna la fecha
                $default = "NOW()";
            }
            else if($default == null) {
                //Asigna el valor de acuerdo al tipo
                $default = $this->_checkDefaultValue($type, $flags);
            }
            //Asigna el valor
            $this->{$name} = $default;
        }

        //Funcion que verifica el tipo de dato y asigna su default
        function _checkDefaultValue($type, $flags) {
            //Verifica las opciones
            $option = explode(",",$flags);
            //Verifica el tipo de dato
            switch($type) {
                case "varchar":
                case "char":
                case "text":
                case "enum":
                case "blob":
                case "clob": {
                    $datas = "''";
                    break;
                }
                case "date": {
                    //Si el campo puede ser null
                    if($option[0] == "YES")
                        $datas = "NULL";
                    else
                        $datas = "CURDATE()";
                    break;
                }
                case "datetime": {
                    //Si el campo puede ser null
                    if($option[0] == "YES")
                        $datas = "NULL";
                    else
                        $datas = "NOW()";
                    break;
                }
                case "time": {
                    //Si el campo puede ser null
                    if($option[0] == "YES")
                        $datas = "NULL";
                    else
                        $datas = "CURTIME()";
                    break;
                }
                case "timestamp": {
                    //Si el campo puede ser null
                    if($option[0] == "YES")
                        $datas = "NULL";
                    else
                        $datas = "NOW()";
                    break;
                }
                default: {
                    $datas = 0;
                    break;
                }
            }
            //Regresa el valor
            return $datas;
        }

        //Funcion que verifica el tipo de dato
        public function _checkDataType($idCol, $uselike = false) {
			$uuid = preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($this->arrColDatas[$idCol]));
			if($uuid) {
				$datas = "'" . $this->arrColDatas[$idCol] . "'";						
			}
			else {
				//Verifica el tipo de dato
				switch(strtolower($this->arrColTypes[$idCol])) {
					case "text":
					case "char":
					case "mediumtext":
					case "varchar": {
						if ($this->arrColDatas[$idCol] == "CURRENT_USER()") {
							$datas = $this->arrColDatas[$idCol];
						}
						else if($this->arrColDatas[$idCol] == "UUID()") {
							$datas = $this->arrColDatas[$idCol];
						}
						else if(preg_match("/AES_(EN|DE)CRYPT/", $this->arrColDatas[$idCol])) {
							$datas = $this->arrColDatas[$idCol];
						}
						else if($this->arrColDatas[$idCol] == "NULL") {
							$datas = "NULL";
						}
						else {
							$datas = "'" . ($uselike ? "%" : "") . $this->arrColDatas[$idCol] . ($uselike ? "%" : "") . "'";
						}
						if($datas == "''''")
							$datas = "''";
						break;
					}
					case "enum": {
						$datas = "'" . $this->arrColDatas[$idCol] . "'";
						break;
					}
					case "blob": {
						$datas = "'" . $this->arrColDatas[$idCol] . "'";
						break;
					}
					case "clob": {
						$datas = "'" . $this->arrColDatas[$idCol] . "'";
						break;
					}
					case "date":
					case "datetime": {
						//Verifica que no este establecido a la fecha del sistema
						if($this->arrColDatas[$idCol] != "NULL") {
							$defaults = array("CURDATE()", "CURDATE", "CURRENT_TIMESTAMP()", "CURRENT_TIMESTAMP", "NOW()", "NOW");
							if (!in_array($this->arrColDatas[$idCol], $defaults))
								$datas = "'" . $this->arrColDatas[$idCol] . "'";
							else
								$datas = $this->arrColDatas[$idCol];
						}
						else
							$datas = $this->arrColDatas[$idCol];
						break;
					}
					case "time": {
						$datas = "'" . $this->arrColDatas[$idCol] . "'";
						break;
					}
					case "timestamp": {
						$datas = "'" . $this->arrColDatas[$idCol] . "'";
						break;
					}
					default: {
						$datas = $this->arrColDatas[$idCol];
						break;
					}
				}
			}
            //Regresa el valor
            return $datas;
        }

        //Funcion que hace una cadena con los nombres de los campos
        public function _columnNames() {
            //Inicializa la cadena
            $names = "";
            //Recorre los campos
            foreach($this->arrColDatas as $key => $value) {
                $names .= $key . ",";
            }
            //Le quita la coma final
            $names = substr($names,0,-1);
            //Lo retorna
            return $names;
        }

        //Funcion que retorna el nombre de un campo
        public function _columnName($key) {
            //Toma los valores del arreglo
            $arrKey = array_keys($this->arrColDatas);
            //Retorna el valor de la columna
            return $arrKey[$key];
        }

        //Funcion que verifica si el campo es un campo de la tabla
        public function _checkColumnName($key) {
            //Busca en los campos
            $result = @array_key_exists($key, $this->arrColTypes);
            $result = $result && @array_key_exists($key, $this->arrColDatas);
            $result = $result && @array_key_exists($key, $this->arrColComments);
            //Retorna
            return $result;
        }

        //Funcion que hace una cadena con los valores de los campos
        public function _columnDatas() {
            //Inicializa la cadena
            $datas = "";
			//Recorre los campos
			foreach($this->arrColDatas as $key => $value) {
				$datas .= $this->_checkDataType($key) . ",";
			}
			//Le quita la coma final
			$datas = substr($datas,0,-1);
            //Lo retorna
            return $datas;
        }

        //Funcion que verifica si existe informacion duplicada
        public function _checkData() {
            //Pasa el puntero del arreglo a la primera posicion
            reset($this->arrColDatas);
            //Obtiene el nombre del primar campo
            $key = key($this->arrColDatas);
            //Arma la sentencia SQL
            $this->sql = "SELECT $key FROM $this->table WHERE ";
            //Recorre los campos
            foreach($this->arrColUnique as $key => $value) {
                if($value)
                    $this->sql .= $key . " = " . $this->_checkDataType($key) . " AND ";
            }
            //Verifica si hay campos que validar
            if(substr($this->sql,-6) == "WHERE ")
                //No se han definido columnas unicas
                return false;
            //Retira el ultimo condicional
            $this->sql = substr($this->sql,0,-4);
            //Extrae el resultado
            $row = $this->__getData();
            //Verifica la informacion
            if(!$row)
                //No hay duplicados
                return false;
            else
                //Si hay duplicados
                return true;
        }

        //Funcion que localiza el ID de un registro recien adicionado
        public function _getLastId() {
            //Pasa el puntero del arreglo a la primera posicion
            reset($this->arrColDatas);
            //Obtiene el nombre del primar campo
            $key = key($this->arrColDatas);
			//Reconexion
			$reconnect = false;			   
            //Verifica si es autonumerico o no
            if(strpos($this->arrColFlags[$key],"auto_increment") === false) {
                //Arma la sentencia SQL
                $this->sql = "SELECT MAX(" . $key . ") FROM $this->table";
                $reconnect = true;
            }
            else if($this->arrColTypes[$key] == "varchar" && strpos($this->arrColFlags[$key],"NO,PRI") !== false) {
                //Arma la sentencia SQL
                $this->sql = "SELECT $key FROM $this->table WHERE $key = " . $this->_checkData($key);
                $reconnect = true;
            }
            else {
                //Arma la sentencia SQL
                $this->sql = "SELECT LAST_INSERT_ID()";
                $reconnect = false;
            }
            //Extrae el resultado
            $row = $this->__getData($reconnect);
            //Verifica la informacion
            if(!$row)
                //Asigna el valor inicial
                $this->arrColDatas[$key] = 0;
            else
                //Asigna el valor obtenido
                $this->arrColDatas[$key] = $row[0];
        }

        //Funcion que obtiene el siguiente ID
        public function _getNextId() {
            //Obtiene el ultimo ID
            $this->_getLastId();
            //Pasa el puntero del arreglo a la primera posicion
            reset($this->arrColDatas);
            //Obtiene el nombre del primer campo
            $key = key($this->arrColDatas);
            //Verifica si es autonumerico o no
            if(strpos($this->arrColFlags[$key],"auto_increment") === false)
                $this->arrColDatas[$key]++;
            else
                $this->arrColDatas[$key] = 0;
        }

        //Funcion para devolver un valor
        public function __get($key) {
            //Verifica que exista
            return array_key_exists($key, $this->arrColDatas) ? $this->arrColDatas[$key] : null;
        }

        //Funcion para ajustar un valor
        public function __set($key, $value){
            //Asigna el valor
            $this->arrColDatas[$key] = $value;
        }

        //Funcion que adiciona un registro nuevo
        public function _add($verify = true, $getid = true) {
			$checkId = true;
            //Si debe verificar la clave principal
            if($verify) {
                //Verifica el tipo de dato de la primera columna
                //Pasa el puntero del arreglo a la primera posicion
                reset($this->arrColDatas);
                //Obtiene el nombre del primer campo
                $key = key($this->arrColDatas);
                //Arma la sentencia SQL
                $this->sql = "SELECT " . $key . " FROM " . $this->table . " LIMIT 1";
                //Extrae el resultado
                $row = $this->__getData();
                //Verifica el resultado
				if($this->arrColTypes[$key] == "varchar" && strpos($this->arrColFlags[$key],"NO,PRI") !== false) {
					//Verifica si es un GUID
					if(preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($row[0]))) {
						//Arma la sentencia SQL
						$this->sql = "SELECT UUID()";
						//Extrae el resultado
						$row = $this->__getData();
						//Asigna la informacion
						$this->arrColDatas[$key] = $row[0];
						//Asigna el valor
						$checkId = false;
					}
				}
                else if(is_numeric($row[0]))
                    //Busca el siguiente ID, segun corresponda
                    $this->_getNextId();
				
                //Verifica la duplicidad de la informacion
                if($this->_checkData()) {
                    //Actualiza el error
                    $this->error = $_SESSION["MSG_DUPLICATED_RECORD"];
                    $this->nerror = 10;
                    //Regresa
                    return;
                }
            }
            //Ajusta los valores
            $this->sql = "SET NAMES utf8";
            //Ejecuta la sentencia SQL
            $this->executeQuery();
            //Arma la sentencia SQL de la insercion
            $this->sql = "INSERT INTO $this->table VALUES (" . $this->_columnDatas() . ")";
            //Ejecuta la sentencia SQL
            $this->executeQuery();
            //Verifica si hay error
            if($this->nerror == 0) {
                //Si solicita el ultimo Id
                if($getid && $checkId) {
                    //Obtiene el ultimo id
                    $this->_getLastId();
                }
            }
        }

        //Funcion que modifica la informacion de la tabla
        public function _modify() {
            //Ajusta los valores
            $this->sql = "SET NAMES utf8";
            //Ejecuta la sentencia
            $this->executeQuery();
            //Arma la sentencia SQL
            $this->sql = "UPDATE $this->table SET ";
			//Ajusta la informacion MODIFIED_BY
			//if(isset($this->MODIFIED_BY)) {
			if(array_key_exists('MODIFIED_BY',$this->arrColDatas)) {
				$this->MODIFIED_BY = ($_SESSION["vtappcorp_userid"] == "" ? $this->MODIFIED_BY : $_SESSION["vtappcorp_userid"]);
            }
			if(array_key_exists('MODIFIED_ON',$this->arrColDatas)) {
				$this->MODIFIED_ON = "NOW()";
			}
            //Recorre los valores
            foreach($this->arrColDatas as $key => $value) {
                $type = explode(",",$this->arrColComments[$key]);
                //Verifica que no sea una contraseña
                if($type[0] != "password") {
                    //Verifica que la columna no sea valor unico
                    if($value != "")
                        //Arma la sentencia SQL con los valores de la clase
                        $this->sql .= $key . " = " . $this->_checkDataType($key) . ",";
                }
            }
            //Verifica la sentencia
            if(substr($this->sql,-4) == "SET ") {
                //Nada que hacer
                $this->error = $_SESSION["NO_DATA_FOR_UPDATE"];
                $this->nerror = 20;
                //Regresa
                return;
            }
            //Quita la ultima coma
            $this->sql = substr($this->sql,0,-1);
            //Pasa el puntero del arreglo a la primera posicion
            reset($this->arrColDatas);
            //Obtiene el nombre del primar campo
            $key = key($this->arrColDatas);
            //Termina la sentencia SQL
            $this->sql .= " WHERE " . $key	. " = " . $this->_checkDataType($key);
            //Ejecuta la sentencia
            $this->executeQuery();
        }

        //Funcion que elimina (bloquea) la informacion de un registro
        public function _delete() {
            //Arma la sentencia SQL
            $this->sql = "UPDATE $this->table SET IS_BLOCKED = TRUE WHERE ID = " . $this->_checkDataType("ID");
            //Ejecuta la sentencia
            $this->executeQuery();
        }

        //Funcion que elimina la informacion de un registro
        public function _deleteForever() {
            //Arma la sentencia SQL
            $this->sql = "DELETE FROM $this->table WHERE ID = " . $this->_checkDataType("ID");
            //Ejecuta la sentencia
            $this->executeQuery();
        }

        //Funcion para consultar la informacion
        public function __getInformation() {
            //Pasa el puntero del arreglo a la primera posicion
            reset($this->arrColDatas);
            //Obtiene el nombre del primer campo
            $key = key($this->arrColDatas);
            //Arma la sentencia SQL
            $this->sql = "SELECT * FROM $this->table WHERE $key = " . $this->_checkDataType($key);
            //Extrae el resultado
            $row = $this->__getData();
            //Verifica la informacion
            if(!$row) {
                //Asigna el error
                $this->error = $_SESSION["NO_INFORMATION"];
                $this->nerror = 20;
            }
            else {
				$row = utf8_converter($row);
                //Asigna la informacion
                for($i=0;$i<count($row);$i++)
                    //Asigna el valor obtenido
                    $this->arrColDatas[$this->_columnName($i)] = $row[$i];
                //Limpia el error
                $this->error = "";
                $this->nerror = 0;
            }
        }

        //Funcion que adiciona la columna de la BD para evitar duplicados
        public function _addUniqueColumn($idCol) {
            //Asigna verdadero al arreglo
            $this->arrColUnique[$idCol] = true;
        }

        //Funcion que ejecuta una consulta DML
        public function doQuery($reconnect = true) {
            //Refresca la conexion a la BD
            if($reconnect)
                $this->connectIt();
            //Realiza la consulta
            $this->conx->do_query($this->sql);
			//Verifica errores
			if($this->conx->Errno > 0) {
				$this->error = $this->conx->Error;
				$this->nerror = $this->conx->Errno;
			}
        }

        //Funcion que ejecuta una consulta DDL
        public function executeQuery() {
            //Refresca la conexion a la BD
            $this->connectIt();
            //Ejecuta la sentencia
            $result = mysqli_query($this->conx->conex_id, $this->sql); //,$this->conx->conex_id);
            //Verifica el resultado
            if(!$result) {
                $this->nerror = abs(mysqli_errno($this->conx->conex_id));
                $this->error = mysqli_error($this->conx->conex_id);
            }
            else {
                $this->error = mysqli_affected_rows($this->conx->conex_id);
                $this->nerror = 0;
            }
            //Retorna
            return $result;
        }

        // Devuelve un array con toda la informacion de la consulta
        public function __getAllData() {
			//Verifica la sentencia SQL
			if($this->sql != "") {
				//Realiza la consulta
				$this->doQuery();
				//Declara el array a retornar
				$result = array();
				//Recorre los resultados
				while($row = @mysqli_fetch_row($this->conx->query_id)) {
					//Lo convierte
					$row = utf8_converter($row);
					//Los agrega al array
					array_push($result, $row);
				}
			}
            //Retorna
            return $result;
        }

        // Devuelve un array con la informacion de la consulta
        public function __getData($reconnect = true) {
			$row = null;
			//Verifica la sentencia SQL
			if($this->sql != "") {
				try {
					//Realiza la consulta
					$this->doQuery($reconnect);
					//Asigna el resultado
					$row = mysqli_fetch_row($this->conx->query_id);
					if($row === null)
						//Log error
						_error_log($this->error,$this->sql);
					else 
						//Lo convierte
						$row = utf8_converter($row);
				}
				catch (Exception $ex) {
					$this->nerror = 150;
					$this->error = $ex->getMessage();
					_error_log($this->error,$this->sql);
				}
			}
			//Retorna el valor de la consulta
            return $row;
        }

		//Funcion que verifica los recursos para los titulos de las tablas
		//Usado cuando se va a mostrar un formulario mediante el metodo showField
		public function completeResources() {
			//Si debe revisar los recursos
			if($this->checkResources) {
				//Define el lenguaje
				$lang = 2;
				//Verifica si el idioma esta especificado
				if(isset($_SESSION["LANGUAGE"])) {
					$lang = $_SESSION["LANGUAGE"];
				}
				//Obtiene la informacion de los recursos
				//Recorre los campos
				foreach($this->arrColDatas as $key => $value) {
					//Arma la sentencia SQL
					$this->sql = "SELECT RESOURCE_TEXT FROM TBL_SYSTEM_RESOURCE WHERE RESOURCE_NAME = '" . $this->table . "." . $key . "' AND LANGUAGE_ID = $lang";
					//Extrae el resultado
					$row = $this->__getData();
					//Verifica la informacion
					if($row) {
						$this->arrColComments[$key] = $row[0];
					}
				}
			}
		}

        //Funcion para mostrar el control de un campo
        //Parametros:
        // $field: nombre del campo
        // $stabs: cantidad de tabs
        // $icon: icono de fontawesome
        // $class: si el div contenedor del input tiene alguna clase adicional
        // $showvalue: si debe mostrar el valor actual
        // $value: valor a mostrar (diferente al valor actual)
        // $color: si corresponde a un color picker
        // $size: tamaño de bootstrap para las columnas del contenedor del input
        // $options: opciones adicionales (readonly)
		// $reso: Opciones de mostrar como recursos
		// $onlyfield: Si debe mostrar solo el campo
        public function showField($field, $stabs, $icon = "", $class = "", $showValue = false, $value = "", $color = false, $size = "6,6,12", $options = "", $reso = null, $onlyfield = false, $label = true, $disabled = true, $extra = "" ) {
            $return = "";
            $valor = "";
            //Verifica la bandera
            $flags = explode(",",$this->arrColFlags[$field]);
            //Verifica si es requerido
            $required = ($flags[0] == "NO")? " <span class=\"required\">*</span>" : " ";
            $required2 = ($flags[0] == "NO")? " required=\"required\" " : " ";
			$required3 = ($flags[0] == "NO")? " " : " class=\"not-required-field-label\"";
            //Verifica si tiene feedback
            $feedback = ($icon != "") ? "has-feedback" : "";
            //Verifica el label
            $comments = explode(",",$this->arrColComments[$field]);
            //Nombre del control
            $fieldname = "txt" . $field;
			//date class
			$dateclass = "";
			//Numeric 
			$numeric = "";
            //Valor
            if($showValue) {
                //Verifica el valor
                if($comments[0] == "password") {
                    $valor = " value=\"it's not that easy! :P\"";
                }
                else if(strpos($this->arrColTypes[$field],"date") !== false) {
					if($value == "")
						$valor = " value=\"" . date("Y-m-d", strtotime($this->arrColDatas[$field])) . "\"";
					else 
						$valor = " value=\"" . $value . "\"";
                }
                else {
                    $valor = " value=\"" . $this->arrColDatas[$field] . "\"";
                }
            }
            else {
				$valor = " value=\"" . $value . "\"";
            }
			//Verifica el valor
			$valor = str_replace("''","",$valor);
            //Verifica si es color
            if($color) {
                $class .= " input-group ";
            }
            //Verifica el tipo
            if($comments[0] == "tel") {
                $comments[0] .= "\" data-validate-length-range=\"7,10";
            }
            //Verifica si es un campo date
            if(strpos($this->arrColTypes[$field],"date") !== false) {
                $dateclass = "text";
				$dateclass2 = "input-group date";
				$dateclass3 = "nk-datapk-ctm form-elet-mg";
				$spandate = "<span class=\"input-group-addon\"></span>\n";
            }
            //Ajusta el size
            $size = explode(",",$size);
            $sizes = "col-md-" . $size[0] . " col-sm-" . $size[1] . " col-xs-" . $size[2];
			$sizes = "";
			
			if($comments[0] == "number")
				$comments[0] .= "\" step=\"any";

			//Si es solo el input field
			if($onlyfield) {
				//Si lo requiere con label
				if($label) {
					//Solo el campo
					$return = "<div class=\"form-example-int form-horizental\">\n";
					$return .= "<div class=\"row\">\n<div class=\"col-lg-2 col-md-3 col-sm-3 col-xs-12\">\n<label class=\"hrzn-fm\">" . $comments[1] . $required . "</label>\n</div>\n";
					$return .= "<div class=\"form-group $dateclass3\">\n<div class=\"col-lg-8 col-md-7 col-sm-7 col-xs-12\">\n<div class=\"nk-int-st $dateclass2\">\n" . $spandate;
					$return .= "$stabs\t\t<input id=\"$fieldname\" class=\"form-control $dateclass\" $numeric placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
					$return .= "</div>\n</div>\n</div>\n</div>\n</div>\n";
				}
				else {
					$return = "$stabs\t\t<input id=\"$fieldname\" class=\"form-control $dateclass\" $numeric placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
				}
				//retorna
				return $return;
			}

            //Genera la GUI
            $return .= "$stabs<div class=\"form-group\">\n";
            $return .= "$stabs\t<label $required3 for=\"$fieldname\">" . $comments[1] . $required . "</label>\n";
			
			//Si hay icono
			if($icon != "") {
				if(strpos($field,"CREDIT_CARD_NUMBER") !== false)
					$icon = $icon . "\" id=\"icon" . $fieldname;
				$return .= "<div class=\"input-group mb-2\"><div class=\"input-group-prepend\"><div class=\"input-group-text\"><i class=\"$icon\"></i></div></div>\n";
			}
			
			//Verifica si es un RESOURCE_NAME
			if(substr($field, 0, strlen("RESOURCE_NAME")) === "RESOURCE_NAME" && strpos($this->table,"SYSTEM_RESOURCE") === false) {
				$control = $this->table . "_" . $field;
				
				$return .= "$stabs\t\t\t<div class=\"input-group mb-3\">\n";
				$return .= "$stabs\t\t\t<div class=\"input-group-prepend bs-dropdown-to-select-group\">\n";
                $return .= "$stabs\t\t\t<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"fa fa-commenting\"></i> <span id=\"idLangName\">" . $_SESSION["SELECT_LANGUAGE"] . "</span>\n";
                $return .= "$stabs\t\t\t\t<input class=\"\" name=\"cb$control\" id=\"cb$control\" data-textbox=\"txt$control\" data-bind=\"bs-drp-sel-value\" value=\"\" type=\"hidden\"> <span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span>\n";
                $return .= "$stabs\t\t\t</button>\n";
                $return .= "$stabs\t\t\t<div class=\"dropdown-menu\">\n";
				//Verifica si hay datos
				if($reso != null) {
					//Recorre el array
					foreach($reso as $row) {
						$lId = substr($row["id"],-1);
						//Busca la informacion del lenguaje
						$this->sql = "SELECT L.ID, '' RESOURCE_TEXT, RL.RESOURCE_TEXT LANGUAGE_NAME, L.ABBREVATION " .
								"FROM TBL_SYSTEM_LANGUAGE L INNER JOIN TBL_SYSTEM_RESOURCE RL ON (RL.RESOURCE_NAME = L.LANGUAGE_NAME AND RL.LANGUAGE_ID = L.ID) " .
								"WHERE L.IS_BLOCKED = FALSE AND L.ID = $lId";
						$lan = $this->__getData();
						$return .= "$stabs\t\t\t\t<a data-value=\"$lan[0]\" class=\"dropdown-item\" href=\"#\" onclick=\"changeLanguageResourceText($lan[0],'$control','$lan[2]');\">$lan[2]</a>\n";
						$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"" . $row["id"] . "\" id=\"" . $row["id"] . "\" value=\"" . $row["value"] . "\" required=\"required\" />\n";
					}
				}
				else {
					//Busca los lenguaje
					$this->sql = "SELECT L.ID, R.RESOURCE_TEXT RESOURCE_TEXT, RL.RESOURCE_TEXT LANGUAGE_NAME, L.ABBREVATION FROM $this->table T " .
								"INNER JOIN TBL_SYSTEM_RESOURCE R ON (R.RESOURCE_NAME = T.$field) " .
								"INNER JOIN TBL_SYSTEM_LANGUAGE L ON (R.LANGUAGE_ID = L.ID) " .
								"INNER JOIN TBL_SYSTEM_RESOURCE RL ON (RL.RESOURCE_NAME = L.LANGUAGE_NAME AND RL.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . ") " .
								"WHERE L.IS_BLOCKED = FALSE AND T.$field = " . $this->_checkDataType($field);
					//Si hay valores
					$row = $this->__getData();
					//Verifica el valor
					if(!$row)
						$this->sql = "SELECT L.ID, '' RESOURCE_TEXT, RL.RESOURCE_TEXT LANGUAGE_NAME, L.ABBREVATION " .
								"FROM TBL_SYSTEM_LANGUAGE L INNER JOIN TBL_SYSTEM_RESOURCE RL ON (RL.RESOURCE_NAME = L.LANGUAGE_NAME AND RL.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . ") " .
								"WHERE L.IS_BLOCKED = FALSE";
					foreach($this->__getAllData() as $row) {
						$return .= "$stabs\t\t\t\t<a data-value=\"$row[0]\" class=\"dropdown-item\" href=\"#\" onclick=\"changeResourceText($row[0],'$control','$row[2]','idLangName');\">$row[2]</a>\n";
						$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"hf$control" . "_" . $row[0] . "\" id=\"hf$control" . "_" . $row[0] . "\" value=\"$row[1]\" required=\"required\" />\n";
					}
				}
                $return .= "$stabs\t\t\t</div>\n";
                $return .= "$stabs\t\t\t</div>\n";
                $return .= "$stabs\t\t<input id=\"txt$control\" data-hidden=\"hf$control\" class=\"form-control txtResource\" placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"txt$control\" $comments[3] $required2 $options $extra>\n";
                $return .= "$stabs\t\t\t</div>\n";
			}
			//Verifica si es identificacion
			else if(substr($field, 0, strlen("IDENTIFICATION")) === "IDENTIFICATION") {
				$control = $this->table . "_" . $field;
				
				$vals = explode("-",$this->arrColDatas[$field]);
				$valId = "";
				
				$return .= "$stabs\t\t\t<div class=\"input-group mb-3\">\n";
				$return .= "$stabs\t\t\t<div class=\"input-group-prepend bs-dropdown-to-select-group\">\n";
                $return .= "$stabs\t\t\t<button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"fa fa-address-card-o\"></i> <span id=\"idDocType\">" . ($vals[0] == "''" ? $_SESSION["SELECT_DOCUMENT_TYPE"] : $vals[0]) . "</span>\n";	
                $return .= "$stabs\t\t\t\t<input class=\"\" name=\"cb$control\" id=\"cb$control\" data-textbox=\"txt$control\" data-bind=\"bs-drp-sel-value\" value=\"{{VAL_IDENTIFICATION}}\" type=\"hidden\"> <span class=\"caret\"></span><span class=\"sr-only\">Toggle Dropdown</span>\n";
                $return .= "$stabs\t\t\t</button>\n";
                $return .= "$stabs\t\t\t<div class=\"dropdown-menu\">\n";
				//Verifica si hay datos
				if($reso != null) {
					//Recorre el array
					foreach($reso as $row) {
						$return .= "$stabs\t\t\t\t<a data-value=\"$row[0]\" class=\"dropdown-item\" href=\"#\" onclick=\"changeResourceText($row[0],'$control','$row[2]','idDocType');\">$row[1]</a>\n";
						$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"hfDocType_" . $row[0] . "\" id=\"hfDocType_" . $row[0] . "\" value=\"" . $row[2] . "\" required=\"required\" />\n";
						$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"hfDocType_" . $row[0] . "_regex\" id=\"hfDocType_" . $row[0] . "_regex\" value=\"$row[3]\" required=\"required\" />\n";
						if($vals[0] == $row[2])
							$valId = $row[0];
					}
				}
				else {
					$return .= "$stabs\t\t\t\t<a data-value=\"\" class=\"dropdown-item\" href=\"#\">" . $_SESSION["NO_INFORMATION"] . "</a>\n";
				}
				$return = str_replace("{{VAL_IDENTIFICATION}}",$valId,$return);
                $return .= "$stabs\t\t\t</div>\n";
                $return .= "$stabs\t\t\t</div>\n";
                $return .= "$stabs\t\t<input id=\"txt$control\" data-hidden=\"hf$control\" class=\"form-control txtResource\" placeholder=\"$comments[2]\" value=\"" . $vals[1] . "\" type=\"$comments[0]\" name=\"txt$control\" $comments[3] $required2 $options $extra>\n";
                $return .= "$stabs\t\t\t</div>\n";
			}
            //Verifica si es dinero
            else if(strpos($icon,"fa fa-money-bill-1") !== false) {
				$local = localeconv();
                //$return .= "$stabs\t\t\t<div class=\"input-group\">\n";
                //$return .= "$stabs\t\t\t\t<span class=\"input-group-addon\">$</span>\n";
                $return .= "$stabs\t\t\t\t<input id=\"$fieldname\" class=\"form-control \" placeholder=\"$comments[2]\" type=\"text\" name=\"$fieldname\" $comments[3] $required2 $valor $options data-smk-type=\"decimal\" data-smk-decimal-separator=\"" . $local["decimal_point"] . "\" data-smk-thousand-separator=\"" . $local["thousands_sep"] . "\" data-smk-digits-after-separator=\"" . $this->arrColPrecision[$field] . "\" $extra>\n";
                //$return .= "$stabs\t\t\t</div>\n";
            }
			else if(strpos($this->arrColTypes[$field],"date") !== false) {
				$return .= "<div class=\"input-group\">\n";
                $return .= "<div class=\"input-group-prepend\">\n";
				$return .= "<span class=\"input-group-text\"><i class=\"fa fa-calendar\"></i></span>\n</div>\n";
                $return .= "$stabs\t\t<input id=\"$fieldname\" class=\"form-control date\" placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
                $return .= "</div>\n";
			}
			//Verifica si es direccion
			else if(substr($field, 0, strlen("ADDRESS")) === "ADDRESS") {
				$return .= "<div class=\"input-group\">\n";
				$return .= "\t<div class=\"input-group-prepend\">\n";
				$return .= "\t\t<span class=\"input-group-text\"><i class=\"fa fa-map\"></i></span>\n";
				$return .= "\t</div>\n";
                $return .= "$stabs\t\t<input id=\"$fieldname\" $numeric class=\"form-control $dateclass\" placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
				$return .= "\t<div class=\"input-group-append\">\n";
				$return .= "\t\t<div class=\"input-group-text\"><a href=\"#\" onclick=\"$('#divMapModal').modal('toggle');\" title=\"Open map\"><i class=\"fa fa-map-marker\"></i></a></div>\n";
				$return .= "\t\t<input type=\"hidden\" id=\"hfLATITUDE\" name=\"hfLATITUDE\" value=\"\">\n";
				$return .= "\t\t<input type=\"hidden\" id=\"hfLONGITUDE\" name=\"hfLONGITUDE\" value=\"\">\n";
				$return .= "\t</div>\n";
				$return .= "</div>\n";
			}
			//Verifica si es direccion
			else if(strpos($field, "_ADDRESS") !== false) {
				$return .= "<div class=\"input-group\">\n";
				$return .= "\t<div class=\"input-group-prepend\">\n";
				$return .= "\t\t<span class=\"input-group-text\"><i class=\"fa fa-map\"></i></span>\n";
				$return .= "\t</div>\n";
                $return .= "$stabs\t\t<input id=\"$fieldname\" $numeric class=\"form-control $dateclass\" placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
				$return .= "\t<div class=\"input-group-append\">\n";
				$return .= "\t\t<div class=\"input-group-text\"><a href=\"#\" onclick=\"showMap('$field');\" title=\"Open map\"><i class=\"fa fa-map-marker\"></i></a></div>\n";
				//Verifica si tiene valor
				$f2 = explode("_", $field);
				if(($showValue || $valor != "") && $this->arrColDatas[$f2[0] . "_COORDINATES"] != "") {
					$arrVal = explode(",",$this->arrColDatas[$f2[0] . "_COORDINATES"]);
					$valLAT = $arrVal[0];
					$valLON = $arrVal[1];
				}
				else {
					$valLAT = "";
					$valLON = "";
				}
				if($valLAT == "''")
					$valLAT = "";
				if($valLON == "''")
					$valLON = "";
				$return .= "\t\t<input type=\"hidden\" id=\"hfLATITUDE_$field\" name=\"hfLATITUDE_$field\" value=\"$valLAT\">\n";
				$return .= "\t\t<input type=\"hidden\" id=\"hfLONGITUDE_$field\" name=\"hfLONGITUDE_$field\" value=\"$valLON\">\n";
				$return .= "\t</div>\n";
				$return .= "</div>\n";
            }
            else {
                $return .= "$stabs\t\t<input id=\"$fieldname\" $numeric class=\"form-control $dateclass\" placeholder=\"$comments[2]\" type=\"$comments[0]\" name=\"$fieldname\" $comments[3] $required2 $valor $options $extra>\n";
            }
			if($icon != "") {
				$return .= "</div>\n";
			}
            if($color) {
                $return .= "$stabs\t\t<span class=\"input-group-addon\"><i></i></span>\n";
            }
            $return .= "$stabs</div>\n";
            //retorna
            return $return;
        }


    }

    if (!function_exists('json_decode')) {
        function json_decode($content, $assoc=false) {
            require_once 'json.php';
            if ($assoc) {
                $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            }
            else {
                $json = new Services_JSON;
            }
            return $json->decode($content);
        }
    }

    if (!function_exists('json_encode')) {
        function json_encode($content) {
            require_once 'json.php';
            $json = new Services_JSON;
            return $json->encode($content);
        }
    }

    if (!function_exists('rpHash')) {
        function rpHash($value) {
            $hash = 5381;
            $value = strtoupper($value);
            for($i = 0; $i < strlen($value); $i++) {
                $hash = (($hash << 5) + $hash) + ord(substr($value, $i));
            }
            return $hash;
        }
    }

    if (!function_exists('leftShift32')) {
        // Perform a 32bit left shift
        function leftShift32($number, $steps) {
            // convert to binary (string)
            $binary = decbin($number);
            // left-pad with 0's if necessary
            $binary = str_pad($binary, 32, "0", STR_PAD_LEFT);
            // left shift manually
            $binary = $binary.str_repeat("0", $steps);
            // get the last 32 bits
            $binary = substr($binary, strlen($binary) - 32);
            // if it's a positive number return it
            // otherwise return the 2's complement
            return ($binary{0} == "0" ? bindec($binary) : -(pow(2, 31) - bindec(substr($binary, 1))));
        }
    }

    if (!function_exists('getScriptOutput')) {
        //Obtiene el resultado de ejecutar un script
        function getScriptOutput($path, $print = false) {
            //Output buffering start
            if (version_compare(PHP_VERSION, '5.4.0', '>='))
                ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS ^ PHP_OUTPUT_HANDLER_REMOVABLE);
            else
                ob_start(null, 0, false);

            //Lee el resultado
            if( is_readable($path) && $path )
                include $path;
            else
                return false;

            //Verifica si debe mostrarlo o retornarlo
            if( $print == false )
                return ob_get_clean();
            else
                echo ob_get_clean();
        }
    }
	
	if (!function_exists("Encriptar")) {
		function Encriptar($cadena) {
			if(function_exists("openssl_encrypt")) {
				if(!function_exists("secured_encrypt")) {
					function secured_encrypt($data) {
						if(!defined('FIRSTKEY'))
							define('FIRSTKEY','Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');			
						if(!defined("SECONDKEY")) 
							define('SECONDKEY','EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');
						$first_key = base64_decode(FIRSTKEY);
						$second_key = base64_decode(SECONDKEY);   
						$method = "aes-256-cbc";   
						$iv_length = openssl_cipher_iv_length($method);
						$iv = openssl_random_pseudo_bytes($iv_length);
						$first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);   
						$second_encrypted = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
						$output = base64_encode($iv.$second_encrypted.$first_encrypted);   
						return $output;       
					}
				}
				$encrypted = secured_encrypt($cadena);
			}
			return $encrypted;
		}
	}
		 
	if (!function_exists("Desencriptar")) {
		function Desencriptar($cadena) {
			if(function_exists("openssl_decrypt")) {
				if(!function_exists("secured_decrypt")) {
					function secured_decrypt($input) {
						if(!defined('FIRSTKEY'))
							define('FIRSTKEY','Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');			
						if(!defined("SECONDKEY")) 
							define('SECONDKEY','EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');
						$first_key = base64_decode(FIRSTKEY);
						$second_key = base64_decode(SECONDKEY);           
						$mix = base64_decode($input);
						$method = "aes-256-cbc";   
						$iv_length = openssl_cipher_iv_length($method);
						$iv = substr($mix,0,$iv_length);
						$second_encrypted = substr($mix,$iv_length,64);
						$first_encrypted = substr($mix,$iv_length+64);
						$data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
						$second_encrypted_new = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
						if (hash_equals($second_encrypted,$second_encrypted_new))
							return $data;
						return false;
					}
				}
				$decrypted = secured_decrypt($cadena);
			}
			return $decrypted;
		}	
	}
	
    if (!function_exists('http_response_code')) {
        //Funcion que obtiene o modifica el codigo de respuesta
        function http_response_code($code = NULL) {
            if ($code !== NULL) {
                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                        break;
                }
                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . $code . ' ' . $text);
                $GLOBALS['http_response_code'] = $code;
            }
            else {
                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            }
            return $code;
        }
    }
	
    if (!function_exists('utf8_converter')) {
		//Funcion que convierte un array a UTF8
		function utf8_converter($array) {
			if(!isset($array))
				$array = array();
			array_walk_recursive($array, function(&$item, $key) {
				if(mb_detect_encoding($item, 'utf-8', true) === false){
					$item = utf8_check($item);
				}
			});
	 		return $array;
			
		}
	}

	if(!function_exists('utf8_check')) {
        //Funcion que convierte una cadena a UTF8
        function utf8_check($string) {
            $return = "";
            if (preg_match('!!u', $string))
                return $string;
            else
                return utf8_encode($string);
        }
    }
	
	if(!function_exists("_error_log")) {
		//Funcion propia para generar errores
		function _error_log($msg, $sql = "", $log = false) {
			if($sql != "")
				$msg .= "\nSQL: " . $sql;
			$trace = debug_backtrace();
			$msg .= "\nAt line " . $trace[0]["line"] . " on " . $trace[0]["file"] . "\nStackTrace";
			foreach(array_slice($trace, 1) as $prg) {
				$msg .= "\nLine " . $prg["line"] . " -> " . $prg["file"] . " : " . $prg["function"];
			}
			if(defined("DEBUG")) 
				if(!filter_var(DEBUG, FILTER_VALIDATE_BOOLEAN))
					$msg = "DEBUG disabled!\n\n" . $msg;
				else
					$msg = "DEBUG enabled!\n\n" . $msg;
			else
				$msg = "DEBUG not defined!\n\n" . $msg;
			
			/*
			if($log) {
				Logger::configure($_SERVER['DOCUMENT_ROOT'] . '/config.xml');
				$logger = Logger::getLogger("VTAPPLogger");
				$logger->debug($msg);
			}
			else 
			*/
				error_log($msg);
		}
	}	
?>