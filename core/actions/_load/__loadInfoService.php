<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	$filename = basename(__FILE__);	
	
	require_once("../../__check-session.php");
	
	$resCheck = checkSession($filename,true);

    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $id = $_GET['id'];
		}
    }
    else {
		$id = $_POST['id'];
    }
	
	if(!$resCheck["success"]) {
		$result = "<script>location.reload();</script>";
		goto _End;
	}
	
	require_once("../../classes/service.php");
	
	$result = "<div class=\"col-md-12\">
            <div class=\"card card-outline card-primary\">
              <div class=\"card-header\">
                <h3 class=\"card-title\">__TITLE__</h3>
              </div>
              <div class=\"card-body\">__BODY__</div>
			  <div class=\"card-footer\">__FOOTER__</div>
            </div>
          </div>";

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	$title = $_SESSION["NOT_SUCCESS"];
	$body = $_SESSION["NO_DATA_FOR_VALIDATE"];
	$footer = "";

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Instancia la clase
		$service = new service();
		
		//Asigna el servicio
		$service->ID = $id;
		$service->__getInformation();
		if($service->nerror > 0) {
			$body = $service->error;
			goto _End;
		}
		
		$aRow = $service->getAditionalData();
		
		$title = "<i class=\"" . $service->state->ICON . "\"></i> <strong>" . $_SESSION["STATUS"] . ":</strong> " . $service->state->getResource();
		$body = "<strong>" . $_SESSION["SERVICE_TABLE_TITLE_4"] . ":</strong> " . $service->REQUESTED_ADDRESS . "<br />";
		$ppr = $service->request_zone->getParentZoneName();
		$body .= "<strong>" . $_SESSION["SERVICE_TABLE_TITLE_5"] . ":</strong> " . $service->request_zone->ZONE_NAME . " ($ppr)<br />";
		$pdr = $service->deliver_zone->getParentZoneName();
		$body .= "<strong>" . $_SESSION["SERVICE_TABLE_TITLE_7"] . ":</strong> " . $service->DELIVER_ADDRESS . "<br />";
		$body .= "<strong>" . $_SESSION["SERVICE_TABLE_TITLE_8"] . ":</strong> " . $service->deliver_zone->ZONE_NAME . " ($pdr)<br />";
		$body .= "<strong>" . $_SESSION["SERVICE_TABLE_TITLE_3"] . ":</strong> " . $service->REQUESTED_BY . "<br />";
		$body .= "<strong>" . $_SESSION["CLIENT"] . ":</strong> " . $service->client->CLIENT_NAME . "<br />";
		$body .= "<strong>" . $_SESSION["AREA_TABLE_TITLE_8"] . ":</strong> " . date("d-M-Y h:nn", strtotime($service->REGISTERED_ON)) . "<br />";
		$body .= "<strong>" . $_SESSION["PACKAGE_TYPE"] . ":</strong> " . $service->type->getResource() . "<br />";
		if($aRow[15] != "")
			$body .= sprintf($_SESSION["DELIVER_TIME"], $aRow[15], $aRow[16]) . "<br />";
		if($aRow[17] != "")
			$body .= "<strong>" . $_SESSION["CLIENT_PAYMENT_TYPE"] . ":</strong> " . $aRow[17] . "<br />";
		if($aRow[18] != "")
			$body .= "<strong>" . $_SESSION["PARTNER"] . ":</strong> " . $aRow[18] . "<br />";
		
		$maxtime = mktime($service->TIME_FINISH_TO_DELIVER,0,0,intval(date("m")),intval(date("d")),intval(date("Y")));
		$now = mktime(intval(date("H")),0,0,intval(date("m")),intval(date("d")),intval(date("Y")));
		$hourdiff = round(($maxtime - $now)/3600, 1);
		$times = array(1,2,3);
		$istrue = false;
		foreach($times as $key => $value) {
			if($hourdiff == $value) {
				$deltime = $value . " " . $_SESSION["HOUR"];
				break;
			}
			else if($key == 2 && $hourdiff > $value) {
				$deltime = sprintf($_SESSION["TIME_PICK_UP"],$times[$key - 1],$value,$_SESSION["HOURS"]);				
				break;
			}
		}
		
		$nxtstate = $service->state->getNextState();
		$body .= "<br /><small><strong>" . $_SESSION["NEXT_STATE"] . ":</strong> " . $service->state->getResourceById($nxtstate) . "</small><br />";		
		$body .= "<small><strong>" . $_SESSION["AREA_TABLE_TITLE_1"] . ":</strong> $id</small><br />";		
		$body .= "<small><strong>" . $_SESSION["TIME_FOR_DELIVER"] . ":</strong> $deltime</small><br />";		
		
		//Verifica el estado para activar o desactivar
		if($service->IS_BLOCKED)
			$activate = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["ACTIVATE"] . "\" type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $service->ID . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
		else 
			$activate = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["DEACTIVATE"] . "\" type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $service->ID . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
		
		$view = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["VIEW"] . "\" type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $service->ID . "','view');\"><i class=\"fa fa-eye\"></i></button>";
		$edit = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["EDIT"] . "\" type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $service->ID . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
		$delete = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["DELETE"] . "\" type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $service->ID . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
		$actBid = ($aRow[11] == "0" && $aRow[12] == "1") ? "" : "disabled";
		$actPay = ($aRow[12] == "1") ? "disabled" : "";
		$payed = "";
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL")
			$bid = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["NOTIFY"] . "\" type=\"button\" class=\"btn btn-success\" name=\"bidBtn" . $aRow[0] . "\" id=\"bidBtn" . $aRow[0] . "\" title=\"" . $_SESSION["NOTIFY"] . "\" onclick=\"startBid('" . $service->ID . "');\" $actBid><i class=\"fa fa-gavel\"></i></button>";
		else if($_SESSION["vtappcorp_useraccess"] == "GOD") {
			$bid = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["NOTIFY"] . "\" type=\"button\" class=\"btn btn-success\" name=\"bidBtn" . $aRow[0] . "\" id=\"bidBtn" . $aRow[0] . "\" title=\"" . $_SESSION["NOTIFY"] . "\" onclick=\"startBid('" . $service->ID . "');\" $actBid><i class=\"fa fa-gavel\"></i></button>";
			$payed = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["MARK_AS_PAYED"] . "\" type=\"button\" class=\"btn btn-default\" name=\"payBtn" . $aRow[0] . "\" id=\"payBtn" . $aRow[0] . "\" title=\"" . $_SESSION["MARK_AS_PAYED"] . "\" onclick=\"markAsPayed('" . $service->ID . "');\" $actPay><i class=\"fa fa-credit-card\"></i></button>";
		}
		else	
			$bid = "";
		if($aRow[11] == 1) {
			$assign = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["ASSIGN"] . "\" type=\"button\" class=\"btn btn-default\" name=\"assBtn" . $aRow[0] . "\" id=\"assBtn" . $aRow[0] . "\" title=\"" . $_SESSION["ASSIGN"] . "\" onclick=\"assign('" . $service->ID . "');\" $actBid><i class=\"fa fa-motorcycle\"></i></button>";
		}
		else {
			$assign = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["INFORMATION"] . "\" type=\"button\" class=\"btn btn-default\" name=\"assBtn" . $aRow[0] . "\" id=\"assBtn" . $aRow[0] . "\" title=\"" . $_SESSION["INFORMATION"] . "\" onclick=\"information('" . $service->ID . "');\" $actBid><i class=\"fa fa-street-view\"></i></button>";
		}
		$history = "<button data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . $_SESSION["TIMELINE"] . "\" type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["TIMELINE"] . "\" onclick=\"location.href = 'service-log.php?id=" . $service->ID . "';\"><i class=\"fa fa-history\"></i></button>";
		
		$footer = "<div class=\"btn-toolbar float-right\" role=\"toolbar\"><div class=\"btn-group\">" . $history . $activate . $payed . $bid . $assign . $view . $edit . $delete . "</div></div>";
	}
	else {
        $body = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	_End:
	$result = str_replace("__TITLE__",$title,$result);
	$result = str_replace("__BODY__",$body,$result);
	$result = str_replace("__FOOTER__",$footer,$result);
	//Termina
	exit($result);

	
?>
