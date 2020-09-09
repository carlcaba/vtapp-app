<?
	require_once("core/classes/interfaces.php");	
	
	$inter = new interfaces();
	
	$breadcum = $inter->showBreadCum($_SESSION["menu_id"]);
	
	foreach ($breadcum as $clave => $fila) {
		$ids[$clave] = $fila['id'];
	}	
	
	if(!empty($ids))
		array_multisort($ids, SORT_ASC, $breadcum);	
?>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="dashboard.php"><?= $_SESSION["MENU_1"] ?></a></li>
<?
	foreach($breadcum as $item) {
		if(strpos($item["title"],"{__") !== false) {
			$datas = explode("{",$item["title"]);
			$item["title"] = $datas[0];
			$text = str_replace("__","",$datas[1]);
			$text = str_replace("}","",$text);
			$datas = explode(".",$text);
		}

		if($item["id"] != $_SESSION["menu_id"]) 
			echo "<li class=\"breadcrumb-item\"><a href=\"" . $item["link"] . "\">" . $item["title"] . "</a></li>\n";
		else 
			echo "<li class=\"breadcrumb-item active\">" . $item["title"] . "</li>";
	}
?>					
							</ol>
						</div>
