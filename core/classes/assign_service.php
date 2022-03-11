<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("service.php");
require_once("partner.php");
require_once("employee.php");
require_once("vehicle.php");

class assign_service extends table {
	var $view;
	var $service;
	var $partner;
	var $employee;
	var $vehicle;
	
	//Constructor de la clase
	function __constructor() {
		$this->assign_service();
	}
	
	//Constructor anterior
	function assign_service ($user  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_ASSIGN_SERVICE");
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		$this->service = new service();
		$this->partner = new partner();
		$this->employee = new employee();
		$this->vehicle = new vehicle();
		$this->view = "VIE_ASSIGN_SERVICE_SUMMARY";
	}
	
    //Funcion para Set del servicio
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
	
    //Funcion para Set del partner
    function setPartner($aliado) {
        //Asigna la informacion
        $this->partner->ID = $aliado;
        //Verifica la informacion
        $this->partner->__getInformation();
        //Si no hubo error
        if($this->partner->nerror == 0) {
            //Asigna el valor
            $this->PARTNER_ID = $aliado;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->PARTNER_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Aliado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el partner
    function getPartner() {
        //Asigna el valor del escenario
        $this->PARTNER_ID = $this->partner->ID;
        //Busca la informacion
        $this->partner->__getInformation();
    }

    //Funcion para Set del employee
    function setEmployee($empl) {
        //Asigna la informacion
        $this->employee->ID = $empl;
        //Verifica la informacion
        $this->employee->__getInformation();
        //Si no hubo error
        if($this->employee->nerror == 0) {
            //Asigna el valor
            $this->EMPLOYEE_ID = $empl;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->EMPLOYEE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Empleado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el partner
    function getEmployee() {
        //Asigna el valor del escenario
        $this->EMPLOYEE_ID = $this->employee->ID;
        //Busca la informacion
        $this->employee->__getInformation();
    }

    //Funcion para Set del vehiculo
    function setVehicle($vehi) {
        //Asigna la informacion
        $this->vehicle->ID = $vehi;
        //Verifica la informacion
        $this->vehicle->__getInformation();
        //Si no hubo error
        if($this->vehicle->nerror == 0) {
            //Asigna el valor
            $this->VEHICLE_ID = $vehi;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->VEHICLE_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Empleado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el partner
    function getVehicle() {
        //Asigna el valor del escenario
        $this->VEHICLE_ID = $this->vehicle->ID;
        //Busca la informacion
        $this->vehicle->__getInformation();
    }
	
	//Funcion para obtener la informacion del servicio asignado
	function __getInformation() {
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
			$this->error = $_SESSION["NOT_REGISTERED"];
			$this->nerror = 20;
		}
		else {
			//Asigna la informacion
			$this->setService($this->SERVICE_ID);
			$this->setPartner($this->PARTNER_ID);
			$this->setEmployee($this->EMPLOYEE_ID);
			$this->setVehicle($this->VEHICLE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	//Funcion para obtener la informacion por ID servicio
	function getInformationByService() {
		$this->sql = "SELECT ID FROM $this->table WHERE SERVICE_ID = " . $this->_checkDataType("SERVICE_ID");
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "UUID()";
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
        }
        else {
            //Asigna el ID
            $this->ID = $row[0];
            //Llama el metodo
            $this->__getInformation();
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
	}

}
?>