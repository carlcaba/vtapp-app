<?php
// Your ID and token
$authToken = 'Basic c29hcF91c2VyQGV0Yi50ZXN0OkIwZzB0QA==';

// The data to send to the API
$postData = array(
        "inventoryType"=>"106-00123",
		"serialNumber" =>  "862860101010101111",
        "quantity" => 1,
        "XI_macn" => "2763871910801",
        "invtype" => "106-00123"
);

// Setup cURL
$ch = curl_init('https://api.etadirect.com/rest/ofscCore/v1/resources/etb.tecnico2/inventories');
curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: '.$authToken,
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($postData)
));

// Send the request
$response = curl_exec($ch);

// Check for errors
if($response === FALSE){
    die(curl_error($ch));
}

// Decode the response
$responseData = json_decode($response, TRUE);

// Print the date from the response
print_r($responseData);

?>