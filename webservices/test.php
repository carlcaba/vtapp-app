<?

$urlbase = "http://localhost/vtapp/app/webservices/";

/*
$url = $urlbase . '__loginService.php';
$data = array('user' => '1034514789', 'pass' => 'iL\/u5kzdCvWmD5PZddUHyNN2WJQdq5+b15D74z2mD1A=');
*/

$token = "c6d458ff-121d-11eb-abb8-54e6f54a840f";

$url = 'http://localhost/vtapp/app/webservices/__processAssign.php';
$data = array('user' => '1034520646', 'token' => $token, 'id' => '6dba7347-0ef8-11eb-a664-54e6f54a840f', 'step' => 1);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
		'follow_location' => true		
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }

var_dump($result);
?>
