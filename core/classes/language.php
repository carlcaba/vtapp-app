<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("table.php");

class language extends table {

	//Constructor
	function __constructor($language = "") {
		$this->language($language);
	}
	
	//Constructor anterior
	function language($language = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_LANGUAGE");
		//Inicializa los atributos
		$this->LANGUAGE_NAME = $language;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
	}

	//Funcion que muestra el texto del resource
	function getResource($lang = 0) {
		if($lang == 0) 
			$lang = LANGUAGE;
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN TBL_SYSTEM_RESOURCE R " .
            "ON (R.RESOURCE_NAME = A.LANGUAGE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = " . $this->_checkDataType("ID");
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
	
	//Funcion que despliega los valores en un option
	function showOptionList($tabs = 8,$selected = 0, $showAll = false, $reto = false) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT L.ID, R.RESOURCE_TEXT FROM $this->table L " .
					"INNER JOIN TBL_SYSTEM_RESOURCE R ON (R.RESOURCE_NAME = L.LANGUAGE_NAME AND R.LANGUAGE_ID = " . LANGUAGE . ")" .
                    ((!$showAll) ? " WHERE L.IS_BLOCKED = FALSE" : "") . " ORDER BY 2";
		$result = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				if($reto)
					$result .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
				else
					//Ajusta al diseño segun GUI
					echo "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
			else
				if($reto)
					$result .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
				else
					//Ajusta al diseño segun GUI
					echo "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
		}
		if($reto)
			return $result;
	}
	
	//Funcion que muestra los lenguajes disponibles
	function showLanguages() {
		//Arma la sentencia SQL
		$this->sql = "SELECT L.ID, R.RESOURCE_TEXT, (SELECT COUNT(R1.ID) FROM TBL_SYSTEM_RESOURCE R1 WHERE R1.LANGUAGE_ID = L.ID) RESOURCES FROM $this->table L " .
					"INNER JOIN TBL_SYSTEM_RESOURCE R ON (R.RESOURCE_NAME = L.LANGUAGE_NAME AND R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . ")" .
					"WHERE L.IS_BLOCKED = FALSE GROUP BY L.ID, R.RESOURCE_TEXT ORDER BY L.ID";
		//Variable resultado
		$result = "<div class=\"dropdown-menu dropdown-menu-lg dropdown-menu-right\">\n";
		$result .= "<span class=\"dropdown-item dropdown-header\">" . $_SESSION["AVAILABLE_LANGUAGES"] . "</span>\n";
		$max = $this->getMaxResources();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$icon = ($row[0] == $_SESSION["LANGUAGE"]) ? "fa-check-square" : "fa-square-o"; 
			$link = ($row[0] != $_SESSION["LANGUAGE"]) ? "onclick=\"changeLanguage($row[0]);\"" : "";
			$result .= "<div class=\"dropdown-divider\"></div>\n";
			$result .= "<a href=\"#\" class=\"dropdown-item\" $link>\n<i class=\"fa $icon mr-2\"></i> " . $row[1] . 
						"<span class=\"float-right text-muted text-sm\">" . sprintf("%.0f%%", (($row[2] / $max) * 100)) . "</span>\n</a>\n";
		}
		//Completa el GUI
		$result .= "</div>\n";
		return $result;
	}
	
	function getMaxResources() {
		$this->sql = "SELECT L.ID, R.RESOURCE_TEXT, (SELECT COUNT(R1.ID) FROM TBL_SYSTEM_RESOURCE R1 WHERE R1.LANGUAGE_ID = L.ID) RESOURCES FROM $this->table L " .
					"INNER JOIN TBL_SYSTEM_RESOURCE R ON (R.RESOURCE_NAME = L.LANGUAGE_NAME AND R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . ")" .
					"WHERE L.IS_BLOCKED = FALSE GROUP BY L.ID, R.RESOURCE_TEXT ORDER BY 3 DESC";
        //Variable a retornar
        $result = 0;
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            $result = $row[2];
        }
        //Retorna
        return $result;
					
	}
	
	//Funcion que muestra los lenguajes como opción
	function showOptionLanguages($action = "downloadCSV", $include = true) {
		//Arma la sentencia SQL
		$this->sql = "SELECT L.ID, R.RESOURCE_TEXT FROM $this->table L " .
					"INNER JOIN TBL_SYSTEM_RESOURCE R ON (R.RESOURCE_NAME = L.LANGUAGE_NAME AND R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . ")" .
					"WHERE L.IS_BLOCKED = FALSE";
		//Verifica si debe incluir el actual
		if(!$include)
			$this->sql .= " AND L.ID <> " . $this->_checkDataType("ID");
		//Termina la sentencia sql
		$this->sql .= " ORDER BY L.ID";
		//Variable resultado
		$result = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$result .= "<a class=\"dropdown-item\" href=\"#\" onclick=\"$action($row[0]);\">$row[1]</a>";
		}
		return $result;
	}
	
	//Funcion que obtiene la informacion del lenguaje por nombre
	function getInformationByName($lang) {
		$result = -1;
		//Arma la sentencia SQL
		$this->sql = "SELECT L.ID FROM $this->table L INNER JOIN TBL_SYSTEM_RESOURCE R ON (L.LANGUAGE_NAME = R.RESOURCE_NAME) " .
				"WHERE R.RESOURCE_TEXT = '$lang' LIMIT 1";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Asigna el error
			$this->nerror = 20;
			$this->error = $_SESSION["RESOURCE_NOT_FOUND"];
		}
		else {
			//Asigna el valor
			$result = $row[0];
			$this->ID = $result;
			$this->__getInformation();
		}
		//Regresa el valor
		return $result;
	}
	
	//Funcion que obtiene la informacion del lenguaje por la abreviacion
	function getInformationByAbbr($lang) {
		$result = -1;
		//Arma la sentencia SQL
		$this->sql = "SELECT L.ID FROM $this->table L WHERE L.ABBREVATION LIKE '$lang%' ORDER BY L.ID LIMIT 1";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Asigna el error
			$this->nerror = 20;
			$this->error = $_SESSION["RESOURCE_NOT_FOUND"];
		}
		else {
			//Asigna el valor
			$result = $row[0];
			$this->ID = $result;
			$this->__getInformation();
		}
		//Regresa el valor
		return $result;
	}

	//Funcion para leer la informacion
	function __getInformation() {
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
			$this->error = $_SESSION["RESOURCE_NOT_FOUND"];
			$this->nerror = 20;
		}
		else {
			$this->LANGUAGE_NAME = $this->getResource();
		}
	}
	
	//Funcion que activa el lenguaje
    function activate() {
        //Arma la sentencia SQL
        $this->sql = "UPDATE " . $this->table . " SET IS_BLOCKED = FALSE WHERE ID = " . $this->_checkDataType("ID");
        //Verifica que no se presenten errores
        $this->executeQuery();
    }
	
	
}

?>