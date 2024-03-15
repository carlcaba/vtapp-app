<?php
//Inicio de sesion
session_name('vtappcorp_session');
session_start();

$is_affiliated_client = false;
$data_client = null;
$client_type = '';

try {

	// Obtener los datos enviados por la solicitud POST
	$data = json_decode(file_get_contents("php://input"), true);

	//Lógica
	$cbReference = array_key_exists('cbReference', $data) ? $data['cbReference'] : null;
	$cbAccess = array_key_exists('cbAccess', $data) ? $data['cbAccess'] : null;

	if ($cbReference && $cbAccess) {

		if ($cbAccess >= 20 && $cbAccess < 60) {
			require_once("../../classes/client.php");
			$client = new client($cbAccess);
			$data_client = $client->getDataByID($cbReference);
			if ($data_client['PAYMENT_TYPE_ID'] == '5' || $data_client['PAYMENT_TYPE_ID'] == '1') {
				$is_affiliated_client = true;
				$client_type = 'client';
			}
		} elseif ($cbAccess >= 60 && $cbAccess < 90) {
			require_once("../../classes/partner.php");
			$client = new partner();
			$data_client = $client->getDataByID($cbReference);
			$is_affiliated_client = true;
			$client_type = 'partner';
		}

		// error_log(date('d.m.Y h:i:s') . " - " . json_encode($data_client) . PHP_EOL, 3, 'my-errors.log');
	}

	$resp = array(
		'code' => '202',
		'status' => 'success',
		'message' => '',
		'data' => $data_client,
		'is_affiliated_client' => $is_affiliated_client,
		'client_type' => $client_type
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
