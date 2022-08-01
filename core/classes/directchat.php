<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("users.php");

class directchat extends table {
	var $view;
	
	//Constructor
	function __constructor($destiny = "", $message = "") {
		$this->directchat($destiny,$message);
	}
	
	//Constructor anterior
	function directchat($destiny = "", $message = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_DIRECT_CHAT");
		//Inicializa los atributos
		$this->IP_SENDER = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$this->SENDER = $_SESSION['vtappcorp_userid'];
		$this->DESTINY = $destiny;
		$this->MESSAGE = $message;
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Define la vista
		$this->view = "VIE_DIRECT_CHAT_SUMMARY";
	}
	
	//Funcion para contar los mensajes
	function getTotalCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE DESTINY = " . $this->_checkDataType("DESTINY") . " AND READED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}
	
	//Funcion para contar los mensajes enviados
	function getTotalSentCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE SENDER = " . $this->_checkDataType("SENDER") . " AND READED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}	

	//Funcion para verificar el ultimo chat
	function getLastChat() {
		//Arma la sentencia SQL
		$this->sql = "SELECT DESTINY FROM $this->table WHERE SENDER = " . $this->_checkDataType("SENDER") . " ORDER BY REGISTERED_ON DESC LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = "";
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}

	function elapsedTime($timestamp, $precision = 2) {
		$time = time() - $timestamp;
		$a = array($_SESSION["DECADE"] => 315576000, 
					$_SESSION["YEAR"] => 31557600, 
					$_SESSION["MONTH"] => 2629800, 
					$_SESSION["WEEK"] => 604800, 
					$_SESSION["DAY"] => 86400, 
					$_SESSION["HOUR"] => 3600, 
					$_SESSION["MINUTE"] => 60, 
					$_SESSION["SECOND"] => 1);
		$i = 0;
		foreach($a as $k => $v) {
			$$k = floor($time/$v);
			if ($$k) $i++;
			$time = $i >= $precision ? 0 : $time - $$k * $v;
			$s = $$k > 1 ? 's' : '';
			$$k = $$k ? $$k.' '.$k.$s.' ' : '';
			@$result .= $$k;
		}
		return $result ? $result : $_SESSION["NOW"];
	}	
	
	//Funcion que modifica un registro
	function updateRead() {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET READED = TRUE, READED_ON = NOW() WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Funcion para mostrar la barra de chats
	function showPanel($lang = 0, $refresh = false) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, SENDER, DESTINY, MESSAGE, PRIORITY, REGISTERED_ON, USER_NAME, ACCESS_ID " .
				"FROM $this->view WHERE DESTINY = " . $this->_checkDataType("DESTINY") . " AND READED = FALSE " .
				"ORDER BY REGISTERED_ON DESC LIMIT 5";
		$chats = "";
		$total = 0;
		$linkTo = "directchat.php";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$total++;
			$priority = $row[4] == 1 ? " <span class=\"float-right text-sm text-danger\"><i class=\"fa fa-star\"></i></span>" : "";
			//Imagen del usuario
			$avatar = "img/users/" . $row[1] . ".jpg";
			if(!file_exists($avatar)) {
				//verifica el tipo de usuario
				if($row[7] == 70)
					$avatar = "img/users/msgr.jpg";
				else
					$avatar = "img/users/user.jpg";
			}
			$time = strtotime($row[5]);
			$elap = explode(" ", $this->elapsedTime($time));
			
			$chats .= "<a href=\"#\" class=\"dropdown-item\">\n";
			$chats .= "<div class=\"media\">\n";
			$chats .= "<img src=\"$avatar\" class=\"img-size-50 mr-3 img-circle\">\n";
			$chats .= "<div class=\"media-body\">\n";
			$chats .= "<h3 class=\"dropdown-item-title\">" . ucwords(strtolower($row[6])) . $priority . "</h3>\n";
			$chats .= "<p class=\"text-sm\">" . substr($row[3],0,25) . "...</p>\n";
			$chats .= "<p class=\"text-sm text-muted\"><i class=\"fa fa-clock-o mr-1\"></i> $elap[0] $elap[1]</p>\n";
			$chats .= "</div>\n";
			$chats .= "</div>\n";
			$chats .= "</a>\n";
			$chats .= "<div class=\"dropdown-divider\"></div>\n";
		}
		if($total > 0) {
			$return = $chats;
			$return .= "<a href=\"$linkTo\" class=\"dropdown-item dropdown-footer\" id=\"aStartChat\">" . $_SESSION["SEE_ALL"] . " " . $_SESSION["MESSAGES"] . "</a>\n";
		}
		else {
			$return = "<div class=\"dropdown-divider\"></div>\n" .
						"<a href=\"#\" class=\"dropdown-item text-center\">" . $_SESSION["DONT_HAVE"] . " " . $_SESSION["MESSAGES"] . "</a>\n" .
						"<div class=\"dropdown-divider\"></div>\n" .						
						"<a href=\"directchat.php\" class=\"dropdown-item dropdown-footer\" id=\"aStartChat\">" . $_SESSION["START_A_CHAT"] . "</a>\n";
		}
		if(!$refresh) {
			$return = "<div class=\"dropdown-menu dropdown-menu-lg dropdown-menu-right\" id=\"topAreaDirectChat\">\n" .
					$return . 
					"</div>\n";
		}
		return $return;
	}
	
	//Funcion para mostrar la forma
	function showForm($userChat = "") {
		$result = "<form method=\"post\" id=\"frmDirectChat\" onsubmit=\"return sendDirectChat();\">\n";
		$result .= "<div class=\"input-group\">\n";
		$result .= "<input type=\"text\" placeholder=\"" . $_SESSION["TYPE_MESSAGE"] . "\" class=\"form-control\" id=\"txtMESSAGE\" name=\"txtMESSAGE\">\n";
		$result .= "<span class=\"input-group-append\">\n";
		$result .= "<button type=\"button\" class=\"btn btn-success\" id=\"btnSendDirectChat\" name=\"btnSendDirectChat\" onclick=\"sendDirectChat();\">" . $_SESSION["SEND"] . "</button>\n";
		$result .= "<input type=\"hidden\" id=\"txtDESTINY\" name=\"txtDESTINY\" value=\"$userChat\" />\n";
		$result .= "</span>\n</div>\n</form>\n";
		return $result;
	}
	
	//Funcion para mostrar los ultimos chats
	function showLastChats($ajax = false) {
		$sWhere = "(SENDER = " . $this->_checkDataType("SENDER");
		if($this->DESTINY != "" && $this->DESTINY != $this->SENDER) 
			$sWhere .= "AND DESTINY = " . $this->_checkDataType("DESTINY") . ") ";
		else
			$sWhere .= "OR DESTINY = '" . $_SESSION["vtappcorp_userid"] . "') ";
		///Arma la sentencia SQL
		$this->sql = "SELECT ID, SENDER, DESTINY, MESSAGE, PRIORITY, REGISTERED_ON, USER_NAME " .
				"FROM $this->view WHERE READED = FALSE AND $sWhere ORDER BY REGISTERED_ON DESC";
		//Variable a retornar
		$result = "<div class=\"direct-chat-messages\" id=\"directChatMessages\">\n";
		$total = 0;
		$lastuser = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$total++;
			
			$userId = $row[1];
			//Si es un mensaje enviado por el usuario
			if($row[1] != $_SESSION["vtappcorp_userid"]) {
				$usuario = $row[6] ? ucwords(strtolower($row[6])) : $row[1];
				$align = "";
				$alignUser = "float-left";
				$alignTime = "float-right";
				$lastuser = ($lastuser == "") ? $row[1] : $lastuser;
			}
			else {
				$align = "right";
				$alignUser = "float-right";
				$alignTime = "float-left";
				//$usuario = $_SESSION["ME"] . " " . $_SESSION["TO"] . " " . $row[2];
				$usuario = $_SESSION["TO"] . " " . $row[2];
			}

			//Imagen del usuario
			$avatar = "img/users/" . $userId . ".jpg";
			
			if($ajax) {
				$pathImg = explode("/", $_SERVER["HTTP_REFERER"]);
				array_splice($pathImg,-1);
				$newPath = implode("/",$pathImg);
				$avatar = $newPath . "/img/users/" . $userId . ".jpg";
				if($fh = fopen($avatar)) {
					close($fh);
				}
				else {
					$avatar = "img/users/user.jpg";
				}				
			}
			else {
				//Verifica si existe
				if(!file_exists($avatar)) 
					$avatar = "img/users/user.jpg";
			}
			//Arma la gui
			$result .= "<div class=\"direct-chat-msg $align\">\n";
			$result .= "<div class=\"direct-chat-info clearfix\">\n";
			$result .= "<span class=\"direct-chat-name $alignUser\">$usuario</span>\n";
			$result .= "<span class=\"direct-chat-timestamp $alignTime\">" . date("d M, g:i A",strtotime($row[5])) . "</span>\n";
			$result .= "</div>\n";
			$result .= "<img class=\"direct-chat-img\" src=\"$avatar\" alt=\"$userId\">\n";
			//$result .= "<object data=\"img/users/user.jpg\" type=\"image/jpg\" class=\"direct-chat-img\"><img class=\"direct-chat-img\" src=\"$avatar\" alt=\"$userId\"></object>\n";
			$result .= "<div class=\"direct-chat-text\">$row[3]</div>\n";
			$result .= "</div>\n";
		}
		//Default message
		/*
		if($total == 0) {
			$result .= "<div class=\"direct-chat-msg\">\n<div class=\"direct-chat-info clearfix\">\n";
			$result .= "<span class=\"direct-chat-name float-left\"> Admin</span>\n";
            $result .= "<span class=\"direct-chat-timestamp float-right\">" . date("d M, g:i A") ."</span>\n</div>\n";
			$result .= "<img class=\"direct-chat-img\" src=\"img/users/admin.jpg\" alt=\"Admin\">\n";
			$result .= "<div class=\"direct-chat-text\">" . $_SESSION["DEFAULT_CHAT_MESSAGE"] . "</div>\n</div>\n";
		}
		else {
			$result .= "<input type=\"hidden\" id=\"hfLastChatUser\" name=\"hfLastChatUser\" value=\"$lastuser\" />\n";
		}
		*/
		return $result . "</div>\n";  
	}
	
	//Funcion para mostrar los usuarios en linea
	function getUsersOnLine() {
		///Arma la sentencia SQL
		$this->sql = "SELECT USER_ID, FULL_NAME, EMAIL, ACCESS_NAME, LAST_LOGIN, ACCESS_ID " .
				"FROM VIE_USER_SUMMARY WHERE ON_LINE = TRUE ORDER BY 1 DESC";
		//Variable a retornar
		$result = "<div class=\"direct-chat-contacts\">\n<ul class=\"contacts-list\">";
		$total = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$total++;
			$result .= "<li>\n";
			$result .= "<a href=\"#\">\n";
			$result .= "<img class=\"contacts-list-img\" src=\"" . users::getUserPictureExternal($row[0],$row[5]) . "\" alt=\"$row[0]\">\n";
			$result .= "<div class=\"contacts-list-info\">\n";
			$result .= "<span class=\"contacts-list-name\">\n";
			$result .= "$row[1]\n";
			$result .= "<small class=\"contacts-list-date float-right\">" . date("Y-m-d", strtotime($row[4])) . "</small>\n";
			$result .= "</span>\n";
			$result .= "<span class=\"contacts-list-msg\">$row[3] - $row[2]</span>\n";
			$result .= "</div>\n";
			$result .= "</a>\n";
			$result .= "</li>\n";
		}
		//Default message
		if($total == 0) {
			$result .= "<li>\n";
			$result .= "No hay usuarios conectados";
			$result .= "</li>\n";
		}
		return $result . "</ul>\n</div>\n";
		
	}
}

?>
	