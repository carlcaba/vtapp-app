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


*/

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
	
	echo date("Y-n-j H:i:s");
?>


