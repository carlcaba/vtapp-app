<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('_error_log', $log_file); 

	//Realiza la operacion
	require_once("../core/classes/configuration.php");
	_error_log("Starting socket server " . " at " . date("Ymd H:i:s"));
	
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		$protocol = 'https://';
	else
		$protocol = 'http://';
	
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$website = $protocol . "localhost";

	$folder = $conf->verifyValue("SITE_ROOT");
	$chatfolder = ($folder == "" ? "/" : $folder) .  $conf->verifyValue("CHAT_DIRECT_SOCKET");
	$port = $conf->verifyValue("CHAT_DIRECT_PORT");
	$scriptname = $conf->verifyValue("CHAT_DIRECT_SCRIPT");
	
	$host = explode("/",$website)[2];
	
	$null = NULL; 

	_error_log("Reading parameters " . " at " . date("Ymd H:i:s"));

	/* Allow the script to hang around waiting for connections. */
	set_time_limit(0);

	/* Turn on implicit output flushing so we see what we're getting
	 * as it comes in. */
	ob_implicit_flush();

	//Crear socket
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	//Puerto reusable
	socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

	//Ajuntar socket a puerto
	socket_bind($socket, 0, $port);

	//Escucha del puerto
	socket_listen($socket);

	//Crear y adicionar a la lista
	$clients = array($socket);
	$myClient = array();

	//Ejecutar por siempre
	while (true) {
		//Multiples conexiones
		$changed = $clients;
		//Retorna los recursos del socket a changed
		socket_select($changed, $null, $null, 0, 10);
	
		//Si hay un nuevo socket
		if (in_array($socket, $changed)) {
			
			$socket_new = socket_accept($socket); //accpet new socket
			array_push($myClient,array('handle' => $socket_new,
							'rooms' => array()));
			$clients[] = $socket_new; //add socket to client array

			_error_log(print_r($clients,true) . " at " . date("Ymd H:i:s"));
			_error_log(print_r($myClient,true) . " at " . date("Ymd H:i:s"));
			
			$header = socket_read($socket_new, 1024); //read data sent by the socket
			perform_handshaking($header, $socket_new, $host, $port, $chatfolder); //perform websocket handshake
		
			socket_getpeername($socket_new, $ip); //get ip address of connected socket
			
			$msg = array('type' => 'sys', 
						'message' => $ip . ' conectado');
			_error_log(print_r($msg,true) . " at " . date("Ymd H:i:s"));
			
			$response = mask(json_encode($msg)); //prepare json data
			send_message($response); //notify all users about new connection
		
			//make room for new socket
			$found_socket = array_search($socket, $changed);
			unset($changed[$found_socket]);
		}
	
		//loop through all connected sockets
		foreach ($changed as $changed_socket) {	
			//check for any incomming data
			while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
				$received_text = unmask($buf); //unmask data
				$tst_msg = json_decode($received_text, true); //json decode 
				$user_name = $tst_msg['name']; //sender name
				$user_message = $tst_msg['message']; //message text
				$user_color = $tst_msg['color']; //color
				$user_to = $tst_msg['to'];
				$user_img = $tst_msg['image'];
				$user_from = $tst_msg['from'];
				$user_imgfrom = $tst_msg['image_from'];
			
				$msg = array('type' => 'usr', 
							'name' => $user_name, 
							'message' => $user_message,
							'color' => $user_color,
							'to' => $user_to,
							'image' => $user_img,
							'from' => $user_from,
							'image_from' => $user_imgfrom);
				_error_log(print_r($msg,true) . " at " . date("Ymd H:i:s"));
				
				//prepare data to be sent to client
				$response_text = mask(json_encode($msg));
				send_message($response_text); //send data
				break 2; //exist this loop
			}
			$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
			if ($buf === false) { // check disconnected client
				// remove client for $clients array
				$found_socket = array_search($changed_socket, $clients);
				socket_getpeername($changed_socket, $ip);
				unset($clients[$found_socket]);
				
				$msg = array('type' => 'sys', 
							'message' => $ip . ' desconectado');
				_error_log(print_r($msg,true) . " at " . date("Ymd H:i:s"));
				
				//notify all users about disconnected connection
				$response = mask(json_encode($msg));
				send_message($response);
			}
		}
	}
	// close the listening socket
	socket_close($socket);

	//Declaracion de funciones usadas
	
	//Enviar mensaje
	function send_message($msg)	{
		global $clients;
		foreach($clients as $changed_socket) 
			@socket_write($changed_socket,$msg,strlen($msg));
		return true;
	}

	//Decodificar mensaje enviado
	function unmask($text) {
		$length = ord($text[1]) & 127;
		if($length == 126) {
			$masks = substr($text, 4, 4);
			$data = substr($text, 8);
		}
		elseif($length == 127) {
			$masks = substr($text, 10, 4);
			$data = substr($text, 14);
		}
		else {
			$masks = substr($text, 2, 4);
			$data = substr($text, 6);
		}
		$text = "";
		for ($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}

	//Codificar mensaje enviado
	function mask($text) {
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);
		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCn', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCNN', $b1, 127, $length);
		return $header.$text;
	}

	//Generar cabeceras de confianza con el cliente
	function perform_handshaking($receved_header,$client_conn, $host, $port, $folder) {
		$headers = array();
		$lines = preg_split("/\r\n/", $receved_header);
		foreach($lines as $line) {
			$line = chop($line);
			if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
				$headers[$matches[1]] = $matches[2];
		}

		$secKey = $headers['Sec-WebSocket-Key'];
		$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		"WebSocket-Origin: $host\r\n" .
		"WebSocket-Location: ws://" . $host . ":" . $port . $folder . "/server.php\r\n".
		"Sec-WebSocket-Accept:$secAccept\r\n\r\n";

		_error_log("Handshaking " . $upgrade . " at " . date("Ymd H:i:s"));
		
		socket_write($client_conn,$upgrade,strlen($upgrade));
	}	

?>