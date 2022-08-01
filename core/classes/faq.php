<?

// LOGICA ESTUDIO 2022

//Incluye las clases dependientes
require_once("table.php");

class faq extends table {
	var $view;
	var $vie2;
	
	//Constructor
	function __constructor($faq = 0) {
		$this->faq($faq);
	}
	
	//Constructor anterior
	function faq($faq = 0) {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_FAQ");
		//Inicializa los atributos
		$this->ID = $faq;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Define la vista
		$this->view = "VIE_FAQ_SUMMARY";
		$this->vie2 = "VIE_FAQ_CATEGORIES_SUMMARY";
	}
	
	//Funcion para obtener la informacion de la faq
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
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	//Funcion que activa o habilita a una faq
	function activate($activate) {
		//Ajusta la informacion
		$this->IS_BLOCKED = ($activate == "true") ? "0" : "1";
		//Realiza la actualizacion
		parent::_modify();
		//Verifica si no hubo error
		if($this->nerror > 0) {
			return false;
		}
		//Retorna 
		return true;
	}
	
	//Funcion para listar las categorias
	function listCategories() {
		$this->sql = "SELECT TOPIC, ICON, SUM(TOTAL) FROM $this->vie2";
		//Verifica el acceso
		if($_SESSION["vtappcorp_useraccessid"] < 90)
			$this->sql .= " WHERE " . $_SESSION["vtappcorp_useraccessid"] . " BETWEEN MIN_ACCESS_ID AND MAX_ACCESS_ID";
		//Completa la sentencia SQL
		$this->sql .= " GROUP BY TOPIC, ICON ORDER BY 1";		
		echo "<ul class=\"list-group list-group-flush\">\n";
		$count = 0;
		foreach($this->__getAllData() as $row) {
			echo "<li class=\"list-group-item d-flex align-items-center lst-category " . ($count == 0 ? "active" : "") . "\" data-category=\"$row[0]\" style=\"cursor: pointer;\">\n";
				echo "<i class=\"$row[1] mr-15\"></i>$row[0]<span class=\"badge badge-light badge-pill ml-15 pull-right\">$row[2]</span>\n";
			echo "</li>\n";
			$count++;
		}
		echo "</ul>\n";
	}
	
	//Funcion para listar las FAQ
	function listFAQS() {
		$this->sql = "SELECT ID, FAQ_TEXT, FAQ_ANSWER, TOPIC, REGISTERED_ON, REGISTERED_BY, MIN_ACCESS_ID_NAME, MIN_ACCESS_ID, MAX_ACCESS_ID_NAME FROM $this->view";
		//Verifica el acceso
		if($_SESSION["vtappcorp_useraccessid"] < 90)
			$this->sql .= " WHERE " . $_SESSION["vtappcorp_useraccessid"] . " BETWEEN MIN_ACCESS_ID AND MAX_ACCESS_ID";
		//Completa la sentencia SQL
		$this->sql .= " ORDER BY TOPIC, MIN_ACCESS_ID";		
		$topic = "";
		$hide = "";
		$access = 0;
		foreach($this->__getAllData() as $row) {
			$btnAns = "";
			if($topic != $row[3] || $access != $row[7]) {
				if($topic != "") {
					echo "</div></div>\n";
				}
				echo "<div class=\"card card-lg\" $hide data-category=\"$row[3]\">\n";
					echo "<h3 class=\"card-header border-bottom-0\">$row[3] " . ($_SESSION["vtappcorp_useraccessid"] < 90 ? "" : "($row[6])") . "</h3>\n";
						echo "<div class=\"accordion accordion-type-2 accordion-flush\" id=\"acc" . substr($row[3],0,3) . "_$row[7]\">\n";
				$topic = $row[3];
				$access = $row[7];
				$hide = "style=\"display: none;\"";
			}
			if($_SESSION["vtappcorp_useraccessid"] >= 90 && $row[4] == "") {
				$btnAns = "<div class=\"btn-group float-right\">\n";
				$btnAns .= "<button type=\"button\" data-toggle=\"modal\" data-target=\"#modalFAQ\" title=\"" . $_SESSION["ANSWER_FAQ"] . "\" id=\"btnAnswer\" name=\"btnAnswer\" class=\"btn btn-primary pull-right btn-sm\" onclick=\"Answer($row[0]);\">\n";
				$btnAns .= "<i class=\"fa fa-comment-dots\"></i>\n";
				$btnAns .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["ANSWER_FAQ"] . "</span>\n";
				$btnAns .= "</button>\n";
				$btnAns .= "<span class=\"number\"> &nbsp;# $row[0]</span>\n";
				$btnAns .= "</div>\n";
				$row[3] = "card-success";
			}
			else {
				$btnAns = "<span class=\"number float-right\"># $row[0]</span>\n";
			}
			$date = "<i class=\"fa fa-user\"></i> " . $row[6] . " <i class=\"fa fa-clock-o\"></i> " . date("j M Y g:i:s A",strtotime($row[5]));
			echo "<div class=\"card\" id=\"divAcc_$row[0]\">\n";
				echo "<div class=\"card-header d-flex justify-content-between\">\n";
					echo "<a class=\"collapsed\" role=\"button\" data-toggle=\"collapse\" href=\"#collapse_$row[0]\" aria-expanded=\"true\" data-id=\"$row[0]\">$row[1]</a>\n";
				echo "</div>\n";
				echo "<div id=\"collapse_$row[0]\" class=\"collapse\" data-parent=\"#acc" . substr($row[3],0,3) . "_$row[7]\" role=\"tabpanel\">\n";
					echo "<div class=\"card-body pa-15\">$row[2]</div>\n";
				echo "</div>\n";
			echo "</div>\n";
		}
		echo "</div></div>\n";
	}

	//Funcion que aumenta las vistas de la FAQ
	function addView() {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET VIEWS = VIEWS + 1, MODIFIED_BY = '" . $_SESSION["vtappcorp_userid"] . "' WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}
	
	//Funcion que agrega la respuesta a una pregunta
	function addAnswer() {
		//Arma la sentencia SQL			
		$this->sql = "UPDATE " . $this->table . " SET FAQ_ANSWER = " . $this->_checkDataType("FAQ_ANSWER") . ", MODIFIED_BY = '" . $_SESSION["vtappcorp_userid"] . "' WHERE ID = " . $this->_checkDataType("ID");
		//Verifica que no se presenten errores
		$this->executeQuery();
	}

}

?>
