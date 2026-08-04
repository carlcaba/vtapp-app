<?php
//Realiza la operacion
require_once("../../classes/affiliation_rate.php");
require_once("../../classes/affiliate_subscription.php");
require_once("../../classes/quota.php");
require_once("../../classes/client.php");

// $dataSubscription = json_decode($_POST['dataSubscription']);

function registerSubscriptionData($dataSubscription, $datas)
{
    try {
        $dataSubscription = json_decode($dataSubscription);

        // error_log(date('d.m.Y h:i:s') . " - " . print_r($dataSubscription, true) . PHP_EOL, 3, 'my-errors.log');
        // error_log(date('d.m.Y h:i:s') . " - " . print_r($datas, true) . PHP_EOL, 3, 'my-errors.log');

        /** Datos de facturación */
        $client_id = $dataSubscription->dataBillingData->client_id;
        $legal_representative = $dataSubscription->dataBillingData->legal_representative;

        /** Datos de la tarjeta */
        $credit_card_number = $dataSubscription->dataCardDetails->txtCREDIT_CARD_NUMBER;
        $valid_card = $dataSubscription->dataCardDetails->hfValidCard;
        $credit_card_name = $dataSubscription->dataCardDetails->txtCREDIT_CARD_NAME;
        $date_expiration = $dataSubscription->dataCardDetails->txtDATE_EXPIRATION;
        $verification_code = $dataSubscription->dataCardDetails->txtVERIFICATION_CODE;
        $total_subscription = $dataSubscription->totalSubscription;

        /** Guardando suscripción */
        $affiliate_subscription = new affiliate_subscription();
        $affiliate_subscription->CLIENT_ID = $client_id;
        $affiliate_subscription->DETAILED_PLAN = '';
        $affiliate_subscription->AMOUNT = $total_subscription;
        $affiliate_subscription->START_DATE = 'NOW()';
        $affiliate_subscription->CREDIT_CARD_NUMBER = str_replace(' ', '', $credit_card_number);
        $affiliate_subscription->CREDIT_CARD_NAME = $credit_card_name;
        $affiliate_subscription->DATE_EXPIRATION = $date_expiration;
        $affiliate_subscription->VERIFICATION_CODE = $verification_code;
        $affiliate_subscription->CARD_STATUS = $valid_card === 'true' ? 'valid' : 'invalid';
        $affiliate_subscription->_add("", LANGUAGE);

        //Si hay error
        if ($affiliate_subscription->nerror > 0) {
            $sql = $affiliate_subscription->sql;
            //Si es error de correo
            if ($affiliate_subscription->nerror != 18)
                //Confirma mensaje al usuario
                $message = $affiliate_subscription->nerror . ". " . $affiliate_subscription->error;
            else
                $message = $affiliate_subscription->nerror . ". " . $affiliate_subscription->error;
            $error = true;
            error_log(date('d.m.Y h:i:s') . " - " . print_r([$sql, $message], true) . PHP_EOL, 3, 'my-errors.log');
        }

        /** Guarda la suscripción en la tabla quota */
        $quota = new quota();
        $quota->setType('6');
        $quota->CLIENT_ID = $client_id;
        $quota->AMOUNT = $total_subscription;
        $quota->USED = 0;
        $quota->CREDIT_CARD_NUMBER = str_replace(' ', '', $credit_card_number);
        $quota->CREDIT_CARD_NAME = $credit_card_name;
        $quota->DATE_EXPIRATION = $date_expiration;
        $quota->VERIFICATION_CODE = $verification_code;
        $quota->DIFERRED_TO = 1;
        $quota->PAYMENT_ID = '';
        $quota->IS_PAYED = 'FALSE';
        $quota->IS_VERIFIED = strtoupper($valid_card);
        $quota->IS_REPEATED = 'TRUE';
        $quota->PERIOD = 'M';
        $quota->LAST_DATE = "DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $quota->IS_BLOCKED = "FALSE";
        $quota->_add();

        //Si hay error
        if ($quota->nerror > 0) {
            $sql = $quota->sql;
            //Si es error de correo
            if ($quota->nerror != 18)
                //Confirma mensaje al usuario
                $message = $quota->nerror . ". " . $quota->error;
            else
                $message = $quota->nerror . ". " . $quota->error;
            $error = true;
            error_log(date('d.m.Y h:i:s') . " - " . print_r([$sql, $message], true) . PHP_EOL, 3, 'my-errors.log');
        }



        /** Detalles de la suscripción */
        foreach ($dataSubscription->dataPersonalizePlan as $key => $rates) {
            $affiliation_rate = new affiliation_rate();
            $affiliation_rate->RESOURCE_NAME = $rates->resource_name;
            $affiliation_rate->CLIENT_ID = $client_id;
            $affiliation_rate->SUBSCRIPTION_ID = $affiliate_subscription->ID;
            $affiliation_rate->QUANTITY_USERS = $rates->quantities;
            $affiliation_rate->COST = $rates->unit_value;
            $affiliation_rate->_add("", LANGUAGE);
        }

        //Agrega representante legal al cliente
        $client = new client();
        $client->ID = $client_id;
        //Consulta la información
        $client->__getInformation();

        $client->LEGAL_REPRESENTATIVE = $legal_representative;
        $client->_modify();

        return $client_id;
    } catch (\Throwable $th) {
        error_log(date('d.m.Y h:i:s') . " - " . json_encode([$th->getMessage(), $th->getFile(), $th->getLine()]) . PHP_EOL, 3, 'my-errors.log');
    }
}
