<?
	/*
/usr/local/bin/php /home1/logicaadmin/public_html/vtapp/jobs/__startBidAuto.php

/opt/cpanel/ea-php70/root/etc /home1/logicaadmin/public_html/vtapp/jobs/__startBidAuto.php

/usr/local/bin/php /home1/logicaadmin/public_html/vtapp/jobs/__startBidAuto.php

/opt/cpanel/lib/curl -s https://logicaestudio.com/vtapp/jobs/__startBidAuto.php &> /dev/null

/usr/local/bin/ea-php70 /home1/logicaadmin/public_html/vtapp/jobs/__startBidAuto.php &> /dev/null

Radicado Bancolombia e-prepago 8010694922 15-06-2021

/home1/logicaadmin/public_html/vtapp/jobs/autobid.pl


*---------------------------------
working script @logicaestudio

/usr/bin/ea-php73 /home/logicaadmin/public_html/vtapp/jobs/__startBidAuto.php >/dev/null 2>&1



	echo "Server Home -> " . $_SERVER['HOME'];
	echo "<br />";
	
	echo "Home GetEnv -> " . getenv("HOME");
	echo "<br />";
	
	echo "Path -> " . $_SERVER["PATH"];
	echo "<br />";
	
	print_r($_SERVER);

	echo "<br />";
	
	foreach (get_loaded_extensions() as $i => $ext) {
		echo $ext .' => '. phpversion($ext). '<br/>';
	}
	
	echo phpversion();
	echo "<br />";
	
	echo floatval(phpversion());
	echo "<br />";
*/
	
	ini_set('display_errors', '1');
	
	$date = date("YmdHis");
	$hex = dechex(round(floatval($date),0));
	$dec = hexdec($hex);
	
	echo $date;
	echo "<br />";
	
	echo $hex;
	echo "<br />";
	
	echo $dec;
	
	//Timing executation time of script
	$startTime = microtime(true); //get time in micro seconds(1 millionth)
	usleep(250); 
	$endTime = microtime(true);

	echo "milliseconds to execute:". ($endTime-$startTime)*1000;
	echo "<br />";
	
	echo round(microtime(true) * 1000);
	echo "<br />";
	
	echo floor(microtime(true) * 1000);
	echo "<br />";

?>


