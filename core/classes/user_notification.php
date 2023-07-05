<?

// LOGICA ESTUDIO 2020

//Incluye las clases dependientes
require_once("table.php");
require_once("users.php");
require_once("service.php");

class user_notification extends table {
	var $user;
	var $service;
	var $view;

	//Constructor de la clase
	function __constructor($usr = "") {
		$this->user_notification($usr);
	}
	
	//Constructor anterior
	function user_notification($usr = "") {
		//Llamado al constructor padre
		parent::table("TBL_NOTIFICATION");
		//Valores por defecto
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->service = new service();
		$this->user = new users();
		$this->view = "VIE_USER_NOTIFICATION_SUMMARY";		
	}

    //Funcion para Set el usuario
    function setUser($value) {
        //Asigna la informacion
        $this->user->ID = $value;
        //Verifica la informacion
        $this->user->__getInformation();
        //Si no hubo error
        if($this->user->nerror == 0) {
            //Asigna el valor
            $this->USER_ID = $value;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->USER_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Usuario " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el user
    function getUser() {
        //Asigna el valor
        $this->USER_ID = $this->user->ID;
        //Busca la informacion
        $this->user->__getInformation();
    }

    //Funcion para Set el servicio
    function setService($servicio) {
        //Asigna la informacion
        $this->service->ID = $servicio;
        //Verifica la informacion
        $this->service->__getInformation();
        //Si no hubo error
        if($this->service->nerror == 0) {
            //Asigna el valor
            $this->SERVICE_ID = $servicio;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->SERVICE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Servicio " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el servicio
    function getService() {
        //Asigna el valor del escenario
        $this->SERVICE_ID = $this->service->ID;
        //Busca la informacion
        $this->service->__getInformation();
    }

    //Funcion para generar las notificaciones sin leer
    function getUnread() {
		//Arma la sentencia SQL
        $this->sql = "SELECT COUNT(ID) FROM " . $this->table . " WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND IS_READ = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			//Retorna el valor
			return $row[0];
		}
		else {
			return 0;
		}
    }

    //Funcion para obtener los datos de las notificaciones 
    function getDataUnread() {
		//Arma la sentencia SQL
        $this->sql = "SELECT ID, SERVICE_ID FROM " . $this->table . " WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND IS_READ = FALSE";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"service_id" => $row[1]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
    }
	
	//Funcion para obtener la notificacion del usuario
	function getInformationByUserService() {
		//Arma la sentencia SQL
        $this->sql = "SELECT ID FROM " . $this->table . " WHERE USER_ID = " . $this->_checkDataType("USER_ID") . " AND SERVICE_ID = " . $this->_checkDataType("SERVICE_ID");
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			//Asigna el ID
			$this->ID = $row[0];
			//Busca la informacion
			$this->__getInformation();
		}
		else {
			$this->nerror = 201;
			$this->error = "Notification " . $_SESSION["NOT_REGISTERED"];
		}
	}

	function updateStep($step) {
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET STEP = " . $step . ", IS_READ = TRUE WHERE ID = " . $this->_checkDataType("ID");
		//Ejecuta la sentencia
		$this->executeQuery();
	}

	function setRead() {
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET IS_READ = TRUE WHERE ID = " . $this->_checkDataType("ID");
		//Ejecuta la sentencia
		$this->executeQuery();
	}

	function decline() {
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET IS_BLOCKED = TRUE WHERE ID = " . $this->_checkDataType("ID");
		//Ejecuta la sentencia
		$this->executeQuery();
	}
	
	function getServicesAutoBid() {
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM VIE_AUTO_START_BID_SUMMARY WHERE ACTIVATED = 'TRUE' AND MINUTOS >= MINUTES";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"time_elapsed" => intval($row[1]),
							"time_to_notify" => intval($row[3]),
							"partner_id" => $row[4],
							"partner_name" => $row[5]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}
	
	function disableNotifications() {
		//Arma la sentencia sql
		$this->sql = "UPDATE " . $this->table . " SET IS_BLOCKED = TRUE, STEP = 999, IS_READ = 1 WHERE SERVICE_ID = " . $this->_checkDataType("SERVICE_ID") . " AND IS_BLOCKED = FALSE";
		//Ejecuta la sentencia
		$this->executeQuery();
	}
	
}	

?>