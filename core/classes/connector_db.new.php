<?

// LOGICA ESTUDIO 2019

class connector_db {
	/* variables de conexion */
	var $database;
	var $server;
	var $user;
	var $password;
	var $port;
	var $ssl;
	var $cert;
	var $datas = array("prod" => array("db" => "vtapcorp",
										"host" => "mysql.vtapp.com",
										"port" => 3306,
										"user" => "vtappcorp_user",
										"pass" => "pSJaFhAYkFZqCPB5PTt",
										"ssl" => true,
										"cert" => ""),
						"test" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_u",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => true,
										"cert" => ""),
						"deve" => array("db" => "vtappcorp",
										"host" => "127.0.0.1",
										"port" => 3306,
										"user" => "root",
										"pass" => "C4s0ft17",
										"ssl" => false,
										"cert" => "")
						);
var $dataAlt = array(
		//LOCAL ALTERNATE CONNECTIONS
						"deve0" => array("db" => "vtappcorp",
										"host" => "127.0.0.1",
										"port" => 3306,
										"user" => "VTAPPCORP_USER",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => false,
										"cert" => ""),
						"deve1" => array("db" => "vtappcorp",
										"host" => "127.0.0.1",
										"port" => 3306,
										"user" => "VTAPPCORP_USER_2",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => false,
										"cert" => ""),
		//TEST ALTERNATE CONNECTIONS
						"test0" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_1",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => true,
										"cert" => ""),
						"test1" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_2",
										"pass" => "Vt4ppC0rp0r1t3$",										
										"ssl" => true,
										"cert" => ""),
						"test2" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_3",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => true,
										"cert" => ""),
						"test3" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_4",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => true,
										"cert" => ""),
						"test4" => array("db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_5",
										"pass" => "Vt4ppC0rp0r1t3$",
										"ssl" => true,
										"cert" => ""),
	//PRODUCTION ALTERNATE CONNECTIONS
						"prod0" => array("db" => "vtapcorp",
										"host" => "mysql.vtapp.com",
										"port" => 3306,
										"user" => "searchrace_user",
										"pass" => "pSJaFhAYkFZqCPB5PTt",
										"ssl" => true,
										"cert" => "")
						);

	/* identificador de conexion y consulta */
	var $conex_id = 0;
	var $query_id = 0;
	
	var $env = "deve";
	var $maxAlt = 0;
	/* numero de error y texto error */
	var $Errno = 0;
	var $Error = "";

	//Constructor
	function __constructor() {
		$this->connector_db();
	}
	
	function __destruct() {
		//try to close the MySql connection
		@mysqli_close($this->conex_id);
	}	
	
	//Constructor anterior
    function connector_db() {
		$this->env = "deve";
		$this->conex_id = 0;
		$this->maxAlt = $this->getMaxAlternateConnections();
		foreach($this->datas as $key => $value) {
			if($key == $this->env) {
				//Completa la informacion de conexion
				$this->database = $value["db"];
				$this->server = $value["host"];
				$this->user = $value["user"];
				$this->password = $value["pass"];
				$this->port = $value["port"];
				$this->ssl = $value["ssl"];
				$this->cert = $value["cert"];
				break;
			}
		}
	}
	
	//Cambiar la conexion
	function changeConnection($id) {
		$envi = $this->env . $id;
		foreach($this->dataAlt as $key => $value) {
			if($key == $envi) {
				//Completa la informacion de conexion
				$this->database = $value["db"];
				$this->server = $value["host"];
				$this->user = $value["user"];
				$this->password = $value["pass"];
				$this->port = $value["port"];
				$this->ssl = $value["ssl"];
				$this->cert = $value["cert"];
				break;
			}
		}
	}
	
	/*Conexion a la base de datos*/
	function connect() {
		$cant = 0;
		$doIt = ($this->conex_id === 0);
		if(!is_int($this->conex_id))
			$doIt = $doIt || @!mysqli_ping($this->conex_id);
		if($doIt) {
			//Check environment
			if($this->ssl) {
				$this->conex_id = mysqli_init();
				if (!$this->conex_id) {
					$this->Error = "Ha fallado mysql_init";
					$this->Errno = 5;
					return 0;
				}
				if (!$this->conex_id->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true)) {
					$this->Error = 'Falló la configuración de MYSQLI_INIT_COMMAND';
					$this->Errno = 6;
					return 0;
				}
				if (!$this->conex_id->ssl_set(NULL, NULL, $this->cert, NULL, NULL)) {
					$this->Error = 'Falló la configuración del SSL';
					$this->Errno = 7;
					return 0;
				}
				$this->conex_id->real_connect($this->server, $this->user, $this->password, $this->database, $this->port);
			}
			else {
				// Conectamos al server local
				$this->conex_id = mysqli_connect($this->server, $this->user, $this->password, $this->database, $this->port);
			}
			$this->Errno = mysqli_connect_errno();
			if($this->Errno == 1040) {
				//Suspender la ejecución 5 segundos
				sleep(5);
			}
			if(!$this->conex_id) {
				if($this->Errno == 1203 || $this->Errno == 1040)  {
					while(!$this->conex_id) {
						if($cant == $this->maxAlt) 
							break;
						$this->changeConnection($cant);
						$cant++;
						//Verify SSL
						if($this->ssl) {
							if (!$this->conex_id->ssl_set(NULL, NULL, $this->cert, NULL, NULL)) {
								$this->Error = 'Falló la configuración del SSL';
								$this->Errno = 7;
							}
							else 
								$this->conex_id = $this->conex_id->real_connect($this->server, $this->user, $this->password, $this->database, $this->port);
						}
						else {
							$this->conex_id = mysqli_connect($this->server, $this->user, $this->password, $this->database, $this->port);
						}
					}
					if(!$this->conex_id) {
						$this->Error = "Ha fallado la conexi&oacute;n a la base de datos -> " . mysqli_connect_error();
						$this->Errno = 18;
						return 0;
					}
				}
				else {
					$this->Error = "Ha fallado la conexi&oacute;n a la base de datos -> " . mysqli_connect_error();
					$this->Errno = 10;
					return 0;
				}
			}
			//Hacer el UTF-8
			@mysqli_query($this->conex_id, "SET NAMES 'ISO 8859-1'");
		}
		/* Si hemos tenido �xito conectando devuelve el identificador de la conexi�n, sino devuelve 0 */
		return $this->conex_id;
	}

	/* Funcion que calcula el maximo de conexiones alternas */
	function getMaxAlternateConnections() {
		$cant = 0;
		foreach($this->dataAlt as $key => $value) {
			if(strpos($key, $this->env) !== false)
				$cant++;
		}
		return $cant;
	}
	
	/* Ejecuta una consulta */
	function do_query($sql = "") {
		if ($sql == "") {
			$this->Error = "No ha especificado una consulta SQL";
			return 0;
		}
	
		//ejecutamos la consulta
		$this->query_id = @mysqli_query($this->conex_id, $sql); //, $this->conex_id);
		if (!$this->query_id) {
			$this->Errno = abs(mysqli_errno($this->conex_id));
			$this->Error = mysqli_error($this->conex_id);
		}
		else {
			$this->Errno = 0;
			$this->Error = "";
		}

		/* Si hemos tenido �xito en la consulta devuelve el identificador de la conexi�n, sino devuelve 0 */
		return $this->query_id;
	}

	/* Devuelve el n�mero de campos de una consulta */
	function cant_fields() {
		return mysqli_num_fields($this->query_id);
	}

	/* Devuelve el n�mero de registros de una consulta */
	function cant_regs() {
		return mysqli_num_rows($this->query_id);
	}
	
	/* Devuelve el nombre de un campo de una consulta */
	function field_name($numcampo) {
		//Contador
		$cont = 0;
		while ($property = mysqli_fetch_field($this->query_id)) {
			$result = $property->name;
			if($numcampo == $cont)
				break;
			$cont++;
		}			
		return $result;
//		return mysqli_field_name(, $numcampo);
	}
	
	/* Cierra la conexion a MySQL */
	function close_it() {
		//Libera los resultados
		@mysqli_free_result($this->query_id);
		//Cierra la BD
		@mysqli_close($this->conex_id);
	}

} //fin de la clase

?>