<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("logs.php");
require_once("configuration.php");
require_once("resources.php");

class interfaces extends table {
	//Constantes
	const TABS = "\t";
	const LINE = "\n";
	//Atributos propios
	var $clave;
	var $resources;
	var $view;
	var $color;
	
	//Constructor de la clase
	function __constructor($menu = "") {
		$this->interfaces($menu);
	}
	
	//Constructor anterior
	function interfaces($menu = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_MENU");
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Asigna los valores propios
		$this->clave = "logicaestudio.com";
		//Relacion con otras clases
		$this->resources = new resources();
		$this->view = "VIE_MENU_SUMMARY";
		$this->color = "VIE_SYSTEM_COLOR_SUMMARY";
	}
	
	//Funcion que retorna los tabs requeridos
	function makeTabs($tabs = 8) {
		$result = "";
		//Arma la variable con la cantidad de tabs
		for($i = 0; $i < $tabs; $i++) 
			$result .= self::TABS;
		//Retorna
		return $result;
	}
	
	//Retorna true SI DEBE solicitar logIn de usuario
	function verifySession($max_time) {
		try {
			//Declara el valor a regresar
			$result = 0;
			//Verifican que ya este creada la variable de sesion de usuario
			if(empty($_SESSION['vtappcorp_userid'])) {
				//Confirma al usuario
				$result = true;
			}
			else {
				//Verifica el ultimo acceso registrado (en la sesion)
				$lastDate = $_SESSION['vtappcorp_lastAccess'];
				//Toma el dateStamp del servidor
				$now = date("Y-n-j H:i:s");
				//Calcula la diferencia
				$time = (strtotime($now)-strtotime($lastDate));
				$this->error = $time;
				//Si ya expiro la sesion
				if($time >= $max_time) {
					//Registra en el log
					$log = new logs("AUTO-LOGOFF");
					$log->_add();
					// destruyo la sesion
					session_destroy(); 
					//Regenera el numero
					session_regenerate_id();	
					//La crea nuevamente
					session_name('vtappcorp_session');
					session_start();				
					//Confirma al usuario
					$_SESSION["vtappcorp_user_alert"] = $_SESSION["SESSION_EXPIRED"];
					//solicita el envio al usuario a la pag. de autenticacion
					$result = true;
				}
				else {
					//sino, actualizo la fecha de la sesion
					$this->updateLastAccess($now);
					$result = false;
				}
			}
		}
		catch (Exception $e) {
			$this->nerror = 100;
			$this->error = $e->getMessage();
		}
		//Regresa
		return $result;
	}
	
	//Funcion que actualiza la informacion del ultimo acceso
	function updateLastAccess($now = "") {
		//Verifica el parametro
		if($now == ""){
			//Lo actualiza
			$now = date("Y-n-j H:i:s");
		}
		//Actualizo la fecha de la sesi�n
		$_SESSION['vtappcorp_lastAccess'] = $now;
	}

	//Funcion que muestra las opciones del menu principal
	function showMenu($parent = 0, $access = 100) {
		//Variable a retornar
		$return = null;
		//Redefine el acceso
		$access = intval($access/10) * 10;
		//Arma la sentencia SQL
		$this->sql = "SELECT M.ID, R.RESOURCE_TEXT, M.LINK, M.ICON, M.ACCESS_ID, M.PARENT_ID, M.ORDER_ID, M.IS_BLOCKED " .
			"FROM $this->table M INNER JOIN " . $this->resources->table . " R ON (R.RESOURCE_NAME = M.RESOURCE_NAME) " .
			"WHERE R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " AND M.PARENT_ID = $parent AND M.IS_BLOCKED = FALSE AND " .
			"M.ACCESS_ID <= $access ORDER BY M.ORDER_ID";
		//Muestra las opciones
		foreach($this->__getAllData() as $row) {
			if($return == null) {
				$return = array();
			}
			//Verifica si tiene submenus
			$submenu = $this->showMenu($row[0],$access);
			//Genera el array
			$data = array("id" => $row[0],
							"title" => $row[1],
							"link" => $row[2],
							"icon" => $row[3],
							"order" => $row[6],
							"parent" => $row[5]);
			if($submenu != null) {
				$data["child"] = $submenu;
			}
			array_push($return,$data);
		}
		//Regresa
		return $return;
	}
	
	//Funcion para generar el breadcum
	function showBreadCum($menuid) {
		//Definicion del parent
		$parent = $menuid;
		//Variable a regresar
		$return = array();
		//Ciclo
		while($parent > 0) {
			//Arma la sentencia SQL
			$this->sql = "SELECT M.ID, R.RESOURCE_TEXT, M.LINK, M.ICON, M.ACCESS_ID, M.PARENT_ID, M.ORDER_ID, M.IS_BLOCKED " .
				"FROM $this->table M INNER JOIN " . $this->resources->table . " R ON (R.RESOURCE_NAME = M.RESOURCE_NAME) " .
				"WHERE R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " AND M.ID = $parent AND M.IS_BLOCKED = FALSE ORDER BY M.ORDER_ID";
			//Obtiene los resultados
			$row = $this->__getData();
			//Si hay valores
			if($row) {
				$data = array("id" => $row[0],
								"title" => $row[1],
								"link" => $row[2]);
				//Cambia el menuid
				$parent = $row[5];
				//Agrega el resultado
				array_push($return,$data);
			}
		}
		//Retorna
		return $return;
	}
	
	//Funcion que muestra la barra inferior izquierda
	function showMenuFooter($tabs = 5) {
		$stabs = "";
		$return = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		
		$return .= "$stabs<div class=\"sidebar-footer hidden-small\">\n";
		$return .= "$stabs\t<a data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["SETTINGS"] . "\">\n";
		$return .= "$stabs\t\t<span class=\"glyphicon glyphicon-cog\" aria-hidden=\"true\"></span>\n";
		$return .= "$stabs\t</a>\n";
		$return .= "$stabs\t<a data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["FULLSCREEN"] . "\">\n";
		$return .= "$stabs\t\t<span class=\"glyphicon glyphicon-fullscreen\" aria-hidden=\"true\"></span>\n";
		$return .= "$stabs\t</a>\n";
		$return .= "$stabs\t<a data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["SECURE"] . "\">\n";
		$return .= "$stabs\t\t<span class=\"glyphicon glyphicon-eye-close\" aria-hidden=\"true\"></span>\n";
		$return .= "$stabs\t</a>\n";
		$return .= "$stabs\t<a data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["EXIT"] . "\">\n";
		$return .= "$stabs\t\t<span class=\"glyphicon glyphicon-off\" aria-hidden=\"true\"></span>\n";
		$return .= "$stabs\t</a>\n";
		$return .= "$stabs</div>\n";
		
		return $return;
	}
	
	//Funcion para devolver el acceso de un menu
	public function getAccessMenu($imenu) {
		//Arma la sentencia SQL
		$this->sql = "SELECT M.ACCESS_ID, R.RESOURCE_TEXT FROM $this->table M INNER JOIN " . $this->resources->table . 
					" R ON (R.RESOURCE_NAME = M.RESOURCE_NAME) WHERE R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . 
					" AND M.ID = $imenu";
		//Obtiene los resultados
		$row = $this->__getData();
		//Regresa
		return $row;
	}

    //Funcion para devolver el id de un menu
    public function getMenuId($link) {
        $result = 0;
		$link = str_replace("#","",$link);
		if(strpos($link,";") !== false)
			$link = substr($link,0,strpos($link,";"));
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE LINK LIKE '" . $link . "' AND ACCESS_ID <= " . $_SESSION["vtappcorp_useraccessid"];
        //Obtiene los resultados
        $row = $this->__getData();
        //Valida el resultado
        if($row) {
            $result = $row[0];
        }
		else {
			//Arma la sentencia SQL
			$this->sql = "SELECT ID FROM $this->table WHERE LINK LIKE 'dashboard.php' AND ACCESS_ID <= " . $_SESSION["vtappcorp_useraccessid"];
			//Obtiene los resultados
			$row = $this->__getData();
            $result = $row[0];
		}
        //Regresa
        return $result;
    }
	
    //Funcion para devolver el parent id de un menu
    public function getParentMenuId($link) {
        $result = 0;
		$link = str_replace("#","",$link);
		if(strpos($link,";") !== false)
			$link = substr($link,0,strpos($link,";"));
        //Arma la sentencia SQL
        $this->sql = "SELECT PARENT_ID FROM $this->table WHERE LINK = '$link' AND ACCESS_ID <= " . $_SESSION["vtappcorp_useraccessid"];
        //Obtiene los resultados
        $row = $this->__getData();
        //Valida el resultado
        if($row) {
            $result = $row[0];
        }
        //Regresa
        return $result;
    }

    //Funcion para devolver el registro de un menu
    public function getMenuInformation($id) {
		$result = null;
        //Arma la sentencia SQL
        $this->sql = "SELECT ID, TITLE, LINK, ICON, ACCESS_ID, MENU_NAME, PARENT_ID, ORDER_ID, IS_BLOCKED ".
					"FROM $this->view WHERE ID = $id AND LANGUAGE_ID = " . $_SESSION["LANGUAGE"]; 
        //Obtiene los resultados
        $row = $this->__getData();
        //Valida el resultado
        if($row) {
            $result = array("id" => $row[0],
							"title" => $row[1],
							"link" => $row[2],
							"icon" => $row[3],
							"access" => $row[4],
							"access_name" => $row[5],
							"parent" => $row[6],
							"order" => $row[7],
							"blocked" => $row[8]);
        }
        //Regresa
        return $result;
    }	

	//Funcion para mostrar los accesos directos
	function showDirectAccess($tabs = 8) {
		$return = "";
		$stabs = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Instancia la clase config
		$conf = new configuration("MENU_CAPS");
		//Verifica la configuracion
		$caps = $conf->verifyValue();
		//Muestra las opciones
		foreach($this->directaccess->getOptionList() as $row) {
			//Verifica la configuracion
			if($caps)
				$menu = mb_convert_case($row[1], MB_CASE_UPPER, "UTF-8");
			else
				$menu = mb_convert_case($row[1], MB_CASE_TITLE, "UTF-8");
			//Verifica si tiene icono
			if($row[3] != "")
				//Muestra GUI
				$return .= "$stabs<li><a href=\"$row[2]\"><i class=\"$row[3]\"></i> $menu</a></li>\n";
			else
				//Muestra GUI
				$return .= "$stabs\t<li><a href=\"$row[2]\">$menu</a></li>\n";
		}
		//Regresa
		return $return;
	}
	
	//Funcion que redirecciona la pagina, tiene en cuenta el protocolo, los parametros y demas 
	function redirect($to,$code=301) {
		//Modifica si esta corriendo sobre servidor Windows
		if (!isset($_SERVER['DOCUMENT_ROOT']))
			$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF']) ) );

		$location = null;
		$sn = $_SERVER['SCRIPT_NAME'];
		$cp = dirname($sn);
		if (substr($to,0,4)=='http')
			$location = $to; // Absolute URL
		else {
			$schema = $_SERVER['SERVER_PORT']=='443'?'https':'http';
			$host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
			if (substr($to,0,1)=='/')
				$location = "$schema://$host$to";
			else if (substr($to,0,1)=='.') { // Relative Path
				$location = "$schema://$host/";
				$pu = parse_url($to);
				$cd = dirname($_SERVER['SCRIPT_FILENAME']).'/';
				$np = realpath($cd.$pu['path']);
				$np = str_replace($_SERVER['DOCUMENT_ROOT'],'',$np);
				$location.= $np;
				if ((isset($pu['query'])) && (strlen($pu['query'])>0)) $location.= '?'.$pu['query'];
			}
			else {
			    $to = $cp . "/" . $to;
                $location = "$schema://$host$to";
            }
		}
		$hs = headers_sent();
		$vvar = explode("//",$location);
		_error_log($location);
		if(count($vvar) > 1){
			$location = $vvar[0] . "//";
			$oarr = array_shift($vvar);
			$location .= implode("/",$vvar);
		}
		_error_log($location);
		if(!$hs) {
			header("Location: $location");
			//header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			exit(0);
		}
		else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="' . $location . '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
			echo '</noscript>';			
			//die("Redirect failed. Please click on this <a href=\"$location\">link</a>");
		} 
	}

	//Funcion que convierte la fecha a un formato especifico
	//$fecha es una fecha valida en formato MySQL yyyy-mm-dd
	//El valor por defecto del formato es d m y = 1 Ene 2008
	//Parametros de formato de fecha:	d Dia del mes 1..31
	//									n Numero del mes 01..12
	//									m Nombre del mes abreviado Ene..Dic
	//									M Nombre del mes completo Enero..Diciembre
	//									Y Numero del a�o 4 digitos
	//									y Numero del a�o 2 digitos
	function cfecha($fecha = "",$format="d m Y",$separator=" ",$hour = true) {
		//Verifica si se envio una fecha
		if ($fecha == "")
			//Toma por defecto la fecha del sistema
			$fecha = date("Y-m-d");
		//Verifica si la fecha viene con hora
		if(strpos($fecha,":") === false)
			//determina la hora
			$hora = "";
		else {
			//Separa la fecha y la hora
			$f = explode(" ",$fecha);
			//Las reasigna
			$fecha = $f[0];
			$hora = $f[1];
		}
		//Separa los datos de la fecha
		$f=explode("-",$fecha);
		//Obtinene el numero del mes
		$nummes=(int)$f[1];
		//Arma los arreglos de los meses de acuerdo al formato
		$mes1 = $_SESSION["MONTHS_ABBRV"];
		$mes2 = $_SESSION["MONTHS"];
		$mes1 = explode("-",$mes1);
		$mes2 = explode("-",$mes2);
		$desfecha = "";
		//Separa el formato
		$for = explode(" ",$format);
		//Verifica el formato requerido
		for($i=0;$i<count($for);$i++) {
			if($for[$i] == "d")
				$desfecha .= "$f[2]" . $separator;
			if($for[$i] == "m") 
				$desfecha .= "$mes1[$nummes]" . $separator;
			if($for[$i] == "M")
				$desfecha .= "$mes2[$nummes]" . $separator;
			if($for[$i] == "n")
				$desfecha .= sprintf("%02d",$nummes) . $separator;
			if($for[$i] == "Y")
				$desfecha .= "$f[0]" . $separator;
			if($for[$i] == "y")
				$desfecha .= substr($f[0],-2) . $separator;
		}
		//Verifica si la fecha venia con hora y la devuelve igual
		if($hora == "")
			$return = substr($desfecha,0,-1);
		else {
			if($hour) 
				$return = substr($desfecha,0,-1) . " $hora";
			else
				$return = substr($desfecha,0,-1);
		}
		return $return;
				
	}
	
	//Funcion que calcula la diferencia entre dos fechas (en dias)
	//Fechas que recibe son variables tipo date de PHP
	function date_diff($date1,$date2) {
		$timedifference=$date2-$date1;
		$corr=date("I",$date2)-date("I",$date1);
		$timedifference+=$corr;
		return $timedifference/86400;
	}
	
	//Funcion que reemplaza los caracteres especiales de acuerdo al lenguaje
	function replaceTags($txt,$charset = false,$nochng = false) {
		//caracteres a buscar
		$srch = array("�","�","�","�","�","�","�","�","�","�","�","�","�","�");
		//caracteres para reemplazar
		if(!$charset)
			$repl = array("&#x00E1;","&#x00E9;","&#x00ED;","&#x00F3;","&#x00FA;","&#x00FC;","&#x00DC;","&#x00F1;","&#x00C1;","&#x00C9;","&#x00CD;","&#x00D3;","&#x00DA;","&#x00D1;");
		else
			$repl = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&uuml;","&Uuml;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;");
		//Verifica si no lo debe cambiar
		if($nochng)
			$repl = array("a","e","i","o","u","u","U","nh","A","E","I","O","U","NH");
		//cadena a devolver
		$cadena = $txt;
		//realiza la busqueda
		$arr_size=count($srch);
		//reemplaza los caracteres
		for($i=0;$i<$arr_size;$i++) {
			$cadena = str_replace($srch[$i],$repl[$i],$cadena);
		}
		return($cadena);
	}
	
	//Funcion que recibe una fecha y le cambia formato
	//$fecha es una fecha valida en formato dd/mm/yyyy
	//$separator es el separador del parametro $fecha
	//$format es el formato (PHP) en que la fecha sera devuelta, por defecto es la de MySQL (Y-m-d) 
	function changeFormat($fecha,$format="Y-m-d",$separator="/") {
		//Verifica si se envio una fecha
		if (!$fecha)
			//Toma por defecto la fecha del sistema
			$fecha = date("d/m/Y");
		//Verifica si la fecha viene con hora
		if(strpos($fecha,":") === false)
			//determina la hora
			$hora = "";
		else {
			//Separa la fecha y la hora
			$f = explode(" ",$fecha);
			//Verifica si viene con formato AM/PM
			if(strpos($fecha,"m") === false)
				$hora = $f[1];
			else
				$hora = "$f[1] $f[2]";
			//Las reasigna
			$fecha = $f[0];
		}
			
		//Separa la fecha
		$fec = explode($separator,$fecha);
		//La convierte a formato PHP
		//Verifica si hay hora
		if($hora != "") {
			$hor = explode(":",$hora);
			//Verifica si es AM o PM
			if(strpos($hor[1],"a") === false)
				$add = 12;
			else
				$add = 0;
			$date = mktime(intval($hor[0])+$add,intval($hor[1]),0,intval($fec[1]),intval($fec[0]),intval($fec[2]));
		}
		else
			$date = mktime(0,0,0,intval($fec[1]),intval($fec[0]),intval($fec[2]));
		//Retorna la cadena correspondiente
		return date($format,$date);	
	}
	
	/*	
	Una funcion simple y poderosa para encriptar y desencriptar con el metodo XOR
	sin una clave conocida. La clave esta implicita y es definida por la cadena a
	encriptar en s�, de caracter en caracter. Hay 4 formas de componer la clave
	desconocida para el caracter en el algoritmo.
	1. El codigo ascii de cada caracter de la cadena en cuestion
	2. La posicion en la cadena del caracter a encriptar
	3. La longitud de la cadena que incluye el caracter
	4. Una formula especial adicionada por el programado para el algoritmo calcular la 
		clave a usar
	*/
	function Encrypt_Decrypt($str) {
		//Calcula la longitud de la cadena
		$len_msg=strlen($str);
		//Inicializa la variable a regresar
		$str_enc_msg="";
		//Recorre la cadena caracter por caracter
		for($pos = 0;$pos<$len_msg;$pos++) {
			$key = (($len_msg+$pos)+1); //(+5 or *3 o ^2)
			//Se requere realizar el modulo porque no debe ser mayor a 128
			$key = (128+$key) % 128;
			$byte = substr($str, $pos, 1);
			$ascii = ord($byte);
			//Operacion XOR
			$byteXor = $ascii ^ $key;
			$byteEnc = chr($byteXor);
			$str_enc_msg .= $byteEnc;
			//La forma corta de hacer lo mismo en una sola linea
//			$str_enc_msg .= chr((ord(substr($str, $pos, 1))) ^ ((255+(($len_msg+$pos)+1)) % 255));
		}
		return $str_enc_msg;
	}
	
	//Funcion de apoyo para la function encrypt
	function encode_base64($sData) {
		$sBase64 = base64_encode($sData);
		return strtr($sBase64, '+/', '-_');
	}

	//Funcion de apoyo para la function decrypt
	function decode_base64($sData) {
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64);
	}

	//Funcion que encripta una cadena de acuerdo a la clave	
	function encrypt($sData,$sKey = "L0g1c4357ud10") {
		$sResult = '';
		for($i = 0; $i < strlen($sData); $i ++) {
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
			$sChar    = chr(ord($sChar) + ord($sKeyChar));
			$sResult .= $sChar;
		}
		return $this->encode_base64($sResult);
	}
	
	//Funcion que desencripta una cadena de acuerdo a la clave
	function decrypt($sData,$sKey = "L0g1c4357ud10") {
		$sResult = '';
		$sData   = $this->decode_base64($sData);
		
		for($i = 0; $i < strlen($sData); $i ++) {
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
			$sChar    = chr(ord($sChar) - ord($sKeyChar));
			$sResult .= $sChar;
		}
		return $sResult;
	}
	
	function Encriptar($cadena){
		$key = $this->clave;
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encrypted;
	}
	 
	function Desencriptar($cadena){
		$key = $this->clave;
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $decrypted;
	}	
	
	//Funcion para encriptar desde la BD
	function EncryptBD($data) {
		//Resultado
		$result = $data;
		$this->sql = "SELECT AES_ENCRYPT('$data','" . $this->clave . "')";
        //Obtiene los resultados
        $row = $this->__getData();
        //Valida el resultado
        if($row) {
            $result = $row[0];
		}			
		else {
			$result = $this->sql;
        }
        //Regresa
        return $result;
	}

	//Funcion para desencriptar desde la BD
	function DecryptBD($data) {
		//Resultado
		$result = $data;
		$this->sql = "SELECT AES_DECRYPT('$data','" . $this->clave . "')";
        //Obtiene los resultados
        $row = $this->__getData();
        //Valida el resultado
        if($row) {
            $result = $row[0];
        }
		else {
			$result = $this->sql;
		}
        //Regresa
        return $result;
	}

	
	//Funcion que genera una clave aleatoria
	function getUniqueKey($length = 0) {
		$code = md5(uniqid(rand(), true));
		if ($length > 0) 
			return substr($code, 0, $length);
		else 
			return $code;
	}
	
	//Funcion que devuelve el valor numerico de una cadena valida como numero
	function floatValue($value) {
    	return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,2}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value));
	} 
	
	//Funcion para detectar el browser
	function browserDetection( $which_test ) {
		//Inicializa variables
		$browser_name = '';
		$browser_number = '';
		//Obtiene el Agente del Usuario
		$browser_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
		//Arma el arreglo browser
		// values [0]= user agent identifier, lowercase, [1] = dom browser, [2] = shorthand for browser,
		$a_browser_types = array(
			array('opera', true, 'op' ),
			array('msie', true, 'ie' ),
			array('konqueror', true, 'konq' ),
			array('webkit', true, 'webkit' ),
			array('gecko', true, 'moz' ),
			array('mozilla/4', false, 'ns4' ),
			array('other', false, 'other' )
		);
		$i_count = count($a_browser_types);
		for ($i = 0; $i < $i_count; $i++) {
			$s_browser = $a_browser_types[$i][0];
			$b_dom = $a_browser_types[$i][1];
			$browser_name = $a_browser_types[$i][2];
			//Si el identificador es encontrado en la cadena
			if (stristr($browser_user_agent, $s_browser)) {
				if ( $browser_name == 'moz' )
					$s_browser = 'rv';
				$browser_number = $this->browserVersion( $browser_user_agent, $s_browser );
				break;
			}
		}
		//Que variable retorna
		if ( $which_test == 'browser' )
			return $browser_name;
		elseif ( $which_test == 'number' )
			return $browser_number;
		//Retorna los dos valores	
		elseif ( $which_test == 'full' ) {
			$a_browser_info = array( $browser_name, $browser_number );
			return $a_browser_info;
		}
	}
	
	//Funcion que detecta la version del browser. USADA POR LA FUNCION ANTERIOR
	function browserVersion( $browser_user_agent, $search_string ) {
		//Maxima longitud para la version
		$string_length = 8;
		//Inicializacion, regresa '' si no la encuentra
		$browser_number = '';
	
		//El parametro que se llama determina que se regresa
		$start_pos = strpos( $browser_user_agent, $search_string );
		
		// start the substring slice 1 space after the search string
		$start_pos += strlen( $search_string ) + 1;
		
		// slice out the largest piece that is numeric, going down to zero, if zero, function returns ''.
		for ( $i = $string_length; $i > 0 ; $i-- ) {
			//Asegurar que es un numero
			if ( is_numeric( substr( $browser_user_agent, $start_pos, $i ) ) ) {
				$browser_number = substr( $browser_user_agent, $start_pos, $i );
				break;
			}
		}
		return $browser_number;
	}
	
	//Funcion que convierte un array en una linea CSV
	function array_to_csv($data,$delimeter = ",") {
		$ret = "";
		//Concatena los valores del array en una cadena
		for($i=0;$i<count($data);$i++)
			$ret.="$data[$i] " . $delimeter;
		//La retorna
		return(substr($ret,0,-1));
	}	
	
	function encrypt_openssl($msg) {
		$key = $this->clave;
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encryptedMessage = openssl_encrypt($msg, 'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING  , $iv);
		return $iv . $encryptedMessage;
	}

	function decrypt_openssl($data) {
		$key = $this->clave;
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($data, 0, $iv_size);
		$data = substr($data, $iv_size);
		return openssl_decrypt($data, 'AES-256-CBC', $key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING  , $iv);
	}	

}
?>