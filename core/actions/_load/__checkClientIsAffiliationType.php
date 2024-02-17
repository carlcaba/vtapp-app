<?php
//Inicio de sesion
session_name('vtappcorp_session');
session_start();
require_once("../../classes/client.php");

$is_affiliated_client = false;

try {

	// Obtener los datos enviados por la solicitud POST
	$data = json_decode(file_get_contents("php://input"), true);

	//Lógica
	$cbReference = array_key_exists('cbReference', $data) ? $data['cbReference'] : null;

	$client = new client($cbReference);
	$payment_type_id = $client->getPaymentTypeIdByID($cbReference);
	if ($payment_type_id == '5') {
		$is_affiliated_client = true;
	}

	error_log(date('d.m.Y h:i:s') . " - " . json_encode([$cbReference, $payment_type_id]) . PHP_EOL, 3, 'my-errors.log');
	$resp = array(
		'code' => '202',
		'status' => 'success',
		'message' => '',
		'data' => ['payment_type_id' => $payment_type_id],
		'is_affiliated_client' => $is_affiliated_client
	);

	//////////////////////

	//Termina
} catch (\Throwable $th) {
	$resp = array(
		'code' => $th->getCode(),
		'status' => 'error',
		'message' => $th->getMessage(),
		'data' => '',
		'is_affiliated_client' => $is_affiliated_client
	);
	error_log(date('d.m.Y h:i:s') . " - " . json_encode([$th->getMessage(), $th->getFile(), $th->getLine()]) . PHP_EOL, 3, 'my-errors.log');
}
// Convertir el array asociativo a formato JSON
$resp_json = json_encode($resp);

// Establecer las cabeceras para indicar que la respuesta es JSON
header('Content-Type: application/json');
exit($resp_json);
