<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class affiliate_subscription extends table
{
	var $resources;

	//Constructor
	function __constructor($document_type = "")
	{
		$this->affiliate_subscription($document_type);
	}

	//Constructor anterior
	function affiliate_subscription($document_type = '')
	{
		//Llamado al constructor padre
		parent::table("TBL_AFFILIATE_SUBSCRIPTION");
		//Inicializa los atributos
		$this->RESOURCE_NAME = $document_type;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
	}

	//Funcion que muestra el texto del resource
	function getResource()
	{
		//Lenguaje establecido
		$lang = $_SESSION["LANGUAGE"];
		//Arma la sentencia SQL
		$this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
			"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$result = "";
		//Obtiene los resultados
		$row = $this->__getData();
		//Registro no existe
		if ($row) {
			$result = $row[0];
		}
		//Retorna
		return $result;
	}

	function dataForm($action, $tabs = 5)
	{
		$resources = new resources();
		$stabs = "";
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for ($i = 0; $i < $tabs; $i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if ($action == "new") {
			$readonly = array(
				"", "", "disabled",
				"", "",
				"", "", "", "disabled",
				"", "", ""
			);
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newQuota.php";
		} else if ($action == "edit") {
			$readonly = array(
				"readonly=\"readonly\"", "disabled", "disabled",
				"disabled", "disabled",
				"disabled", "disabled", "", "disabled",
				"disabled", "disabled", "disabled", "disabled",
				"disabled", "disabled", "disabled"
			);
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editQuota.php";
		} else {
			$readonly = array(
				"disabled", "disabled",
				"disabled", "disabled", "disabled",
				"disabled", "disabled", "disabled",
				"disabled", "disabled",
				"disabled", "disabled", "disabled",
				"disabled", "disabled", "disabled", "disabled",
				"disabled", "disabled", "disabled"
			);
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteQuota.php";
		}

		//Variable a regresar
		$return = array(
			"tabs" => $stabs,
			"readonly" => $readonly,
			"actiontext" => $actiontext,
			"link" => $link,
			"showvalue" => true
		);
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores en un option
	// function showOptionList($tabs = 8, $selected = 0, $lang = 0)
	// {
	// 	//Verifica el lenguaje
	// 	if ($lang == 0) {
	// 		//Lenguaje establecido
	// 		$lang = $_SESSION["LANGUAGE"];
	// 	}
	// 	//Arma la cadena con los tabs requeridos
	// 	for ($i = 0; $i < $tabs; $i++)
	// 		$stabs .= "\t";
	// 	//Arma la sentencia SQL
	// 	$this->sql = "SELECT A.ID, R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
	// 		"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
	// 	//Variable a retornar
	// 	$return = "";
	// 	//Recorre los valores
	// 	foreach ($this->__getAllData() as $row) {
	// 		if (!mb_detect_encoding($row["1"], 'utf-8', true)) {
	// 			//Guarda la informacion en GLOBALS
	// 			$row[1] = utf8_encode($row[1]);
	// 		}
	// 		//Si la opcion se encuentra seleccionada
	// 		if ($row[0] == $selected)
	// 			//Ajusta al diseño segun GUI
	// 			$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
	// 		else
	// 			//Ajusta al diseño segun GUI
	// 			$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
	// 	}
	// 	//Retorna
	// 	return $return;
	// }

	//Funcion que obtiene un tipo de documento por sigla
	// function getIdByShortName($name)
	// {
	// 	//Arma la sentencia SQL
	// 	$this->sql = "SELECT D.ID FROM $this->table D WHERE D.ABBRV = '$name' AND D.IS_BLOCKED = FALSE";
	// 	//Obtiene los resultados
	// 	$row = $this->__getData();
	// 	//Valida el resultado
	// 	if (!$row) {
	// 		return 0;
	// 	} else {
	// 		return $row[0];
	// 	}
	// }

	//Funcion que obtiene un tipo de documento por sigla
	// function getInformationByShortName($field = "ABBRV", $lang = 0)
	// {
	// 	//Verifica el lenguaje
	// 	if ($lang == 0) {
	// 		//Lenguaje establecido
	// 		$lang = $_SESSION["LANGUAGE"];
	// 	}
	// 	if ($field == "ABBRV")
	// 		//Arma la sentencia SQL
	// 		$this->sql = "SELECT D.ID FROM $this->table D WHERE D." . $field . " = " . $this->_checkDataType($field);
	// 	else
	// 		//Arma la sentencia SQL
	// 		$this->sql = "SELECT A.ID FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
	// 			"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND R.RESOURCE_TEXT = " . $this->_checkDataType($field);
	// 	//Obtiene los resultados
	// 	$row = $this->__getData();
	// 	//Valida el resultado
	// 	if (!$row) {
	// 		//Genera el error
	// 		$this->nerror = 10;
	// 		$this->error = $_SESSION["NOT_REGISTERED"];
	// 	} else {
	// 		//Asigna los atributos
	// 		$this->ID = $row[0];
	// 		//Obtiene la informacion
	// 		$this->__getInformation();
	// 	}
	// }

	//Funcion que retorna la informacion en rows
	function getDataToForm($lang = 0)
	{
		//Verifica el lenguaje
		if ($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT D.ID, R.RESOURCE_TEXT, D.ABBREVIATION, D.REGEX FROM $this->table D INNER JOIN " . $this->resources->table . " R " .
			"ON (R.RESOURCE_NAME = D.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND D.IS_BLOCKED = FALSE ORDER BY D.ID";
		//Retorna
		return $this->__getAllData();
	}

	//Funcion que despliega los valores para el webservice
	// function listData($lang = 0)
	// {
	// 	//Verifica el lenguaje
	// 	if ($lang == 0) {
	// 		//Lenguaje establecido
	// 		$lang = $_SESSION["LANGUAGE"];
	// 	}
	// 	//Arma la sentencia SQL
	// 	$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, R.LANGUAGE_ID, A.ABBREVIATION FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
	// 		"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
	// 	//Variable a retornar
	// 	$return = array();
	// 	//Recorre los valores
	// 	foreach ($this->__getAllData() as $row) {
	// 		$data = array(
	// 			"id" => $row[0],
	// 			"document_type" => $row[1],
	// 			"language" => $row[2],
	// 			"abbreviation" => $row[3]
	// 		);
	// 		array_push($return, $data);
	// 	}
	// 	//Retorna
	// 	return $return;
	// }
}
