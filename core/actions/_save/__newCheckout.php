<?
	namespace Kushki;
	use kushki\lib\Amount;
	use kushki\lib\Kushki;
	use kushki\lib\KushkiEnvironment;
	use kushki\lib\Transaction;
	use kushki\lib\ExtraTaxes;
	use kushki\lib\KushkiLanguage;
	use kushki\lib\KushkiCurrency;
	use service;
	use payment;
	use rate;
		
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Clases requeridas
	require_once("../../classes/kushki/autoload.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'data' => null,
                    'link' => 'services.php');

	$data = json_decode($_POST["serviceData"]);
	$merchId = $data->hfMERCH_ID;
	$language = KushkiLanguage::ES;
	$currency = KushkiCurrency::COP; 
	$environment = KushkiEnvironment::TESTING;

	$kushki = new Kushki($merchId, $language, $currency, $environment);

	$token = $_POST["kushkiToken"]; 
	$meses = intval($_POST["kushkiDeferred"]);
	$datas = json_decode($_POST["serviceData"]);
	$monto = floatval($datas->hfPRICE);
	$subtotalIva = 0.0;
	$iva = 0.0;
	$subtotalIva0 = 0.0;
	$ice = 0.0;
	$amount = new Amount($subtotalIva, $iva, $subtotalIva0, $ice);
	if($meses > 0) {
		$transaccion = $kushki->deferredCharge($token, $monto, $meses);
	}
	else {
		$transaccion = $kushki->charge($token, $amount);
	}

	$result["success"] = $transaccion->isSuccessful();
	$result["data"] = $transaccion;

	if($transaccion->isSuccessful()){
		$result["message"] = $_SESSION["PAYMENT_SUCCESSFUL"];
	} 
	else {
		$result["message"] = $_SESSION["ERROR_ON_PAYMENT"] . "<br />" . $transaccion->getResponseCode() . ": " . $transaccion->getResponseText();
	}

	//Recalcula el precio
	require_once("../../classes/rate.php");
	$rate = new rate();
	$rate->getValueByDistance($datas->hfDISTANCE);

	//Guarda el servicio y el pago
	require_once("../../classes/service.php");
	$service = new service();
	
	$service->setUser($datas->hfUSER_ID);
	$service->setClient($datas->cbClient);
	$service->REQUESTED_BY = $datas->txtREQUESTED_BY;
	$service->REQUESTED_EMAIL = $datas->txtREQUESTED_EMAIL;
	$service->REQUESTED_PHONE = $datas->txtREQUESTED_PHONE;
	$service->RREQUESTED_CELLPHONE = $datas->txtREQUESTED_CELLPHONE;
	$service->REQUESTED_IP = $datas->txtREQUESTED_IP;
	$service->REQUESTED_ADDRESS = $datas->txtREQUESTED_ADDRESS;
	$service->setRequestZone($datas->cbZoneRequestSub);
	$service->DELIVER_DESCRIPTION = $datas->txtDELIVER_DESCRIPTION;
	$service->OBSERVATION = $datas->txtOBSERVATION;
	$service->DELIVER_TO = $datas->txtDELIVER_TO;
	$service->DELIVER_EMAIL = $datas->txtDELIVER_EMAIL;
	$service->DELIVER_PHONE = $datas->txtDELIVER_PHONE;
	$service->DELIVER_CELLPHONE = $datas->txtDELIVER_CELLPHONE;
	$service->DELIVER_ADDRESS = $datas->txtDELIVER_ADDRESS;
	$service->setDeliverZone($datas->cbZoneDeliverSub);
	$service->setDeliveryType($datas->cbDeliverType);
	$service->ROUND_TRIP = $datas->cbRoundTrip;
	$service->PRICE = $service->ROUND_TRIP == "true" ? $rate->ROUND_TRIP : $rate->RATE;
	$service->setState($datas->cbState); 
	
	$service->_add();

	if($service->nerror > 0) {
		$result["message"] .= "<br />" . $service->error; 
	}
	else {
		require_once("../../classes/payment.php");
		$payment = new payment();
		//Adiciona la informacion
		$payment->setClient($datas->cbClient);
		$payment->REFERENCE_ID = $service->ID;
		$payment->setType($service->client->PAYMENT_TYPE_ID);
		$payment->setState(intval($transaction->details->responseCode));
		$payment->TRANSACTION_ID = $transaction->details->transactionId;
		$payment->GATEWAY = "Kushki";
		$payment->URL_GATEWAY = $environment;
		$payment->IP_CLIENT = $transaction->details->ip;
		$payment->RISK = "";
		$payment->RESPONSE = "";
		$payment->RESPONSE_TRACE = json_encode($transaction);
		$payment->PAYMENT_METHOD = "CreditCard";
		$payment->PAYMENT_METHOD_TYPE = $transaction->details->paymentBrand;
		$payment->PAYMENT_REQUESTED = $transaction->details->requestAmount;
		$payment->PAYMENT_VALUE = $transaction->details->approvedTransactionAmount;
		$payment->PAYMENT_TAX_PERCENT = 0;
		$payment->PAYMENT_TAX = $transaction->details->ivaValue;
		$payment->PAYMENT_VALUE_ADD = $transaction->details->iceValue;
		$payment->AUTHORIZATION_CODE = $transaction->details->ticketNumber;
		$payment->AUTHORIZATION_ADDITIONAL_CODE= $transaction->details->processorId;
		$payment->PAYMENT_ENTITY= $transaction->details->acquirerBank;
		
		$payment->PAYER_EMAIL = $service->REQUESTED_EMAIL;
		$payment->PAYER_NAME = $service->REQUESTED_BY;
		$payment->PAYER_IDENTIFICATION = $datas->client->IDENTIFICATION;
		$payment->PAYER_PHONE = $service->txtREQUESTED_PHONE;

		$payment->OBSERVATION = "SERVICE:" . $service->type->getResource();

		$payment->IS_BLOCKED = "FALSE";
		//Lo adiciona
		$payment->_add();

		//Si hay error
		if($payment->nerror > 0) {
			//Confirma mensaje al usuario
			$result["message"] .= "<br />" . $payment->error; 
		}
	}

	if($result["success"]) {
		$_SESSION["vtappcorp_user_message"] = $_SESSION["SERVICE_REGISTERED"] . "<br />" . $result["messasge"];
	}
	else {
		$_SESSION["vtappcorp_user_alert"] = $result["message"];
	}
?>
<script type="text/javascript">location.href = '../../../<?= $result["link"] ?>';</script>