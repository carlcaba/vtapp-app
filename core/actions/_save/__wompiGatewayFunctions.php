<?
	function getAcceptanceToken($url, $pubkey) {
		//Resultado
		$result = array("success" => true,
						"token" => null,
						"status" => "",
						"message" => "");
		$url .= $pubkey;
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json'));
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
			$dataToReturn = curl_exec($ch);

			if($dataToReturn === false) {
				throw new Exception(curl_error($ch));
			}
			curl_close($ch);
			$data = json_decode($dataToReturn);
			if(property_exists($data, 'error')) {
				throw new Exception(implode(",",$data->error->messages->number) . "<br>Origin:" . $dataToReturn);
			}
			$result["token"] = $data;
			$result["status"] = "Ok";
		}
		catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = "Error getting acceptance token: " . $ex->getMessage();
		}
		return $result;
	}
	
	function getCardToken($quota, $urlToken, $pubkey) {
		//Resultado
		$result = array("success" => true,
						"token" => "",
						"status" => "",
						"message" => "");
		//Proceso de solicitud de token
		$cardData = array("number" => $quota->CREDIT_CARD_NUMBER, 
						"cvc" => $quota->VERIFICATION_CODE, 
						"exp_month" => explode("/",$quota->DATE_EXPIRATION)[0], 
						"exp_year" => explode("/",$quota->DATE_EXPIRATION)[1], 
						"card_holder" => $quota->CREDIT_CARD_NAME); 
		$accTok = null;
		$headers = array ('authorization: Bearer ' . $pubkey);
		$status = "";
		$transaction = "";
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlToken);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cardData));
			$tokenCard = curl_exec($ch);

			if($tokenCard === false) {
				throw new Exception(curl_error($ch));
			}
			//Convierte el token a objeto
			$tokenObj = json_decode($tokenCard);
			if(property_exists($tokenObj, 'error')) {
				throw new Exception(implode(",",$tokenObj->error->messages->number) . "<br>Origin:" . $tokenCard);
			}			
			curl_close ($ch);
			$result["status"] = $tokenObj->status;
			$result["token"] = $tokenObj->data->id;
		}
		catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = "Error getting card token: " . $ex->getMessage();
		}
		return $result;
	}
	
	function generateTransaction($quota, $token, $accTok, $urlTranx, $pubkey, $urlRet) {
		$result = array("success" => true,
						"transaction" => null,
						"status" => "",
						"message" => "");
		$transaction = "";
		$headers = array ('authorization: Bearer ' . $pubkey);
		//Arma la trama de la transaccion
		$dataTx = array("acceptance_token" => $accTok,
						"amount_in_cents" => ($quota->AMOUNT * 100),
						"currency" => "COP",
						"customer_email" => $quota->client->EMAIL,
						"payment_method" => array("type" => "CARD",
													"token" => $token,
													"installments" => $quota->DIFERRED_TO),
						//"payment_source_id" => 1234,
						"redirect_url" =>  $urlRet,
						"reference" => $quota->ID,
						"customer_data" => array("phone_number" => $quota->client->CELLPHONE,
													"full_name" => $quota->CREDIT_CARD_NAME),
						"shipping_address" => array("address_line_1" => $quota->client->ADDRESS,
													"address_line_2" => $quota->client->ADDRESS,
													"country" => "CO",
													"region" => "Cundinamarca",
													"city" => $quota->client->city->CITY_NAME,
													"name" => $quota->client->CONTACT_NAME,
													"phone_number" => $quota->client->CELLPHONE_CONTACT,
													"postal_code" => "110011")
					);
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlTranx);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataTx));
			
			$transaction = curl_exec($ch);

			if($transaction === false) {
				throw new Exception(curl_error($ch));
			}
			curl_close ($ch);
		}
		catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = "Error generating transaction: " . $ex->getMessage();
		}				

		$result["dataSent"] = json_encode($dataTx);
		$result["url"] = $urlTranx;
		$result["TrxId"] = "";

		//Verifica el éxito
		if($result["success"]) {
			//Verifica si hay transaccion creada
			if($transaction != "") {
				$transObj = json_decode($transaction);
				//Verifica si hay error
				if(property_exists($transObj, 'error')) {
					$result['success'] = false;
					$result['message'] = $transaction;
				}
				else {
					$result["transaction"] = json_decode($transaction);
					$result["status"] = $transObj->data->status;
					$result["TrxId"] = $transObj->data->id;
				}
			}
			else {
				$result['success'] = false;
				$result['message'] = "Error generating transaction: No transaction response";
			}
		}
		return $result;
	}
	
	function checkTransaction($id, $url, $pubkey, $prvkey) {
		//Resultado
		$result = array("success" => true,
						"status" => "",
						"message" => "");
		$url .= $id;
		$headers = array ('authorization: Bearer ' . $pubkey, 'accept: application/json');
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
			$dataToReturn = curl_exec($ch);

			if($dataToReturn === false) {
				throw new Exception(curl_error($ch));
			}
			curl_close($ch);
			$data = json_decode($dataToReturn);
			if(property_exists($data, 'error')) {
				throw new Exception(implode(",",$data->error->messages->number) . "<br>Origin:" . $dataToReturn);
			}
			$result["status"] = $data->data->status;
			if($result["status"] != "APPROVED") {
				$result["success"] = false;
				$result["message"] = "Error: Not approved";
			}
			$result["data"] = $dataToReturn;
		}
		catch (Exception $ex) {
			$result['success'] = false;
			$result['message'] = "Error checking transaction: " . $ex->getMessage();
		}
		return $result;
	}
?>