<?

// LOGICA ESTUDIO 2022

//Incluye las clases dependientes
require_once("table.php");

class faq extends table {
	var $view;
	
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
	
	//Funcion para listar las FAQ
	function listFAQS() {
		$this->sql = "SELECT ID, FAQ_TEXT, TEXT_ANSWER, COLOR, FAQ_ANSWER, REGISTERED_ON, REGISTERED_BY FROM $this->view";
		echo "<div class=\"col-12\" id=\"accordion\">\n";
		foreach($this->__getAllData() as $row) {
			$btnAns = "";
			$outline ="card-outline";
			if($_SESSION["vtappcorp_useraccessid"] >= 90 && $row[4] == "") {
				$btnAns = "<div class=\"btn-group float-right\">\n";
				$btnAns .= "<button type=\"button\" data-toggle=\"modal\" data-target=\"#modalFAQ\" title=\"" . $_SESSION["ANSWER_FAQ"] . "\" id=\"btnAnswer\" name=\"btnAnswer\" class=\"btn btn-primary pull-right btn-sm\" onclick=\"Answer($row[0]);\">\n";
				$btnAns .= "<i class=\"fa fa-comment-dots\"></i>\n";
				$btnAns .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["ANSWER_FAQ"] . "</span>\n";
				$btnAns .= "</button>\n";
				$btnAns .= "</div>\n";
				$row[3] = "card-success";
			}
			$date = "<i class=\"fa fa-user\"></i> " . $row[6] . " <i class=\"fa fa-clock-o\"></i> " . date("j M Y g:i:s A",strtotime($row[5]));
			echo "<div class=\"card $row[3] $outline\">\n";
				echo "<a class=\"d-block w-100\" data-id=\"$row[0]\" data-answer=\"" . (($btnAns != "") ? "true" : "false") . "\" data-toggle=\"collapse\" href=\"#collapse_$row[0]\">\n";
					echo "<div class=\"card-header\">\n";
						echo "<h4 class=\"card-title w-100\">\n";
							echo "<div class=\"direct-chat-infos clearfix\"><span class=\"direct-chat-timestamp float-left\">$date &nbsp;</span></div>\n";
							echo "$row[1]\n";
							echo "<input type=\"hidden\" name=\"txtQuestion_$row[0]\" id=\"txtQuestion_$row[0]\" value=\"$row[1]\" />\n";
							echo $btnAns;
						echo "</h4>\n";
					echo "</div>\n";
				echo "</a>\n";
				echo "<div id=\"collapse_$row[0]\" class=\"collapse\" data-parent=\"#accordion\">\n";
					echo "<div class=\"card-body\">\n";
						echo "$row[2]\n";
					echo "</div>\n";
				echo "</div>\n";
			echo "</div>\n";
			$count++;
		}
		echo "</div>\n";
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
