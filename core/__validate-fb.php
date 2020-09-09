<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    include("__load-resources.php");
	
	//Incluye las clases requeridas
	require_once("classes/configuration.php");
	include_once("classes/interfaces.php");
	include_once("classes/resources.php");

	$conf = new configuration("FB_APP_ID");
	$appId = $conf->verifyValue();	//Facebook App ID
	$conf = new configuration("FB_APP_SECRET");
	$appSecret = $conf->verifyValue();	//Facebook App Secret
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$conf = new configuration("SITE_ROOT");
	$site_root = $conf->verifyValue();
	$conf = new configuration("FB_CALLBACK");
	$callBack = $conf->verifyValue();
	$redirectURL = $website . $siteroot . $callBack;	//Callback URL
	
	$inter = new interfaces();
	
	//Facebook SDK
	require_once("Facebook/autoload.php");
	//FB Libraries
	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookAuthorizationException;
	use Facebook\GraphObject;
	use Facebook\Entities\AccessToken;
	use Facebook\HttpClients\FacebookCurlHttpClient;
	use Facebook\HttpClients\FacebookHttpable;
	
	$fb = new Facebook\Facebook([
		'app_id' => "{$appId}",
		'app_secret' => "{$appSecret}",
		'default_graph_version' => 'v2.9',
	]);

	$helper = $fb->getRedirectLoginHelper();

	try {
		$accessToken = $helper->getAccessToken();
	} 
	catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["FB_GRAPH_ERROR"] . $e->getMessage();
		//Lo redirecciona
		$inter->redirect($site_root);
	}
	catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When Graph returns an error
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["FB_SDK_ERROR"] . $e->getMessage();
		//Lo redirecciona
		$inter->redirect($site_root);
	}

	if(!isset($accessToken)) {
		if ($helper->getError()) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["FB_ERROR"] . $helper->getError() . "<br />" .
						$_SESSION["FB_ERROR_CODE"] . $helper->getErrorCode() . "<br />" .
						$_SESSION["FB_ERROR_REASON"] . $helper->getErrorReason() . "<br />" .
						$_SESSION["FB_ERROR_DESCRIPTION"] . $helper->getErrorDescription();
		}
		else {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["BAD_REQUEST"];
		}
		//Lo redirecciona
		$inter->redirect($site_root);
	}
	
	$request = $fb->request('GET', '/me', ['fields' => 'id,name,email']);
	$request->setAccessToken($accessToken);

	// Send the request to Graph
	try {
		$response = $fb->getClient()->sendRequest($request);
	}
	catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["FB_GRAPH_ERROR"] . $e->getMessage();
		//Lo redirecciona
		$inter->redirect($site_root);
	}
	catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When Graph returns an error
		$_SESSION["vtappcorp_user_alert"] = $_SESSION["FB_SDK_ERROR"] . $e->getMessage();
		//Lo redirecciona
		$inter->redirect($site_root);
	}

	$graphNode = $response->getGraphNode();
	$fbId = $graphNode['id'];
	$fbName = $graphNode['name'];
	$fbEmail = $graphNode['email'];
	
	require_once("classes/users.php");
	$usua = new users($fbEmail);
	$usua->__getInformation();
	
	//Verify the error
	if($usua->nerror > 0) {
		//Explodes the name
		$name = explode(" ",$fbName);
		//Verify the name
		switch(count($name)) {
			case 1: 
				$firstName = $name[0];
				$lastName = "FACEBOOK_USER";
				break;
			case 2:
				$firstName = $name[0];
				$lastName = $name[1];
				break;
			case 3:
				$firstName = $name[0] . " " . $name[1];
				$lastName = $name[2];
				break;
			default:
				$firstName = $name[0] . " " . $name[1];
				$lastName = $name[2] . " " . $name[3];
		}
		//Create the user
		$usua->ID = $fbEmail;
		$usua->THEPASSWORD = $usua->generatePassword();
		$usua->NAME = $firstName;
		$usua->LASTNAME = $lastName;
		$usua->EMAIL = $fbEmail;
		$usua->setAccess(50);
		$usua->FIRST_TIME = "TRUE";
		$usua->CHANGE_PASSWORD = "TRUE";
		$usua->WP_USER = "FALSE";
		$usua->LINKEDIN_USER = "FALSE";
		$usua->FACEBOOK_USER = "TRUE";
		//Add the user
		$usua->__add("FB");
		//Verify the result
		if($usua->nerror > 0) {
			//Si es error de correo
			if($usua->nerror != 18)
				//Confirma mensaje al usuario
				$_SESSION["vtappcorp_user_alert"] = $usua->nerror . ". " . $usua->error;
			else 
				$_SESSION["vtappcorp_user_alert"] = $usua->nerror . ". " . $usua->error;
		}
	}
	$_SESSION["vtappcorp_user_message"] = $_SESSION["USER_PASSWORD_OK"];
	$_SESSION['vtappcorp_username'] = $usua->NAME . " " . $usua->LASTNAME;
	$_SESSION['vtappcorp_userid'] = $usua->ID;
    $_SESSION['vtappcorp_useraccessid'] = intval($usua->ACCESS_ID/10) * 10;
	$_SESSION['vtappcorp_fb_access_token'] = (string)$accessToken;
	$_SESSION['vtappcorp_fb_id'] = $fbId;
	$_SESSION["vtappcorp_appname"] = $usua->access->APP_TITLE;
    $_SESSION['vtappcorp_useraccess'] = $usua->access->PREFIX;
    $_SESSION['vtappcorp_referenceid'] = $usua->REFERENCE;
    $_SESSION['vtappcorp_location'] = "";
	
	$link = $website . $site_root . $usua->access->LINKTO;

	//Actualiza la hora de acceso
	$inter->updateLastAccess();
	
	//Crea el nuevo LOG
	require_once("classes/logs.php");
	$log = new logs("FB-LOGIN");
	//Adiciona la transaccion
	$log->_add();
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$_SESSION["vtappcorp_user_alert"] = $log->error;
	
	//Lo redirecciona
	$inter->redirect($link);
?>
