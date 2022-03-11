<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/directchat.php");
	
	//Variable del codigo
	$result = array('success' => true,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"count" => 0);
	
	//Asigna la informacion
	$chat = new directchat($_SESSION["vtappcorp_userid"]);
	
	$messages = intval($chat->getTotalCount());
	$panel = $chat->showPanel(0,true);
	$chats = $chat->showLastChats(true);
	
	$result["success"] = ($chat->nerror == 0);
	$result["message"] = sprintf($_SESSION["YOU_HAVE_CHAT_MESSAGE"],$messages);
	$result["messages"] = $messages > 0 ? $panel : "";
	$result["directchats"] = $messages > 0 ? $chats : "";
	$result["count"] = $messages;
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

	
?>