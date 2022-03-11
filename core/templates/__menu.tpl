<?
	require_once("core/classes/users.php");
	require_once("core/classes/interfaces.php");
	require_once("core/classes/language.php");

	$userX = new users($_SESSION['vtappcorp_userid']);
	$inter = new interfaces();
	$userX->__getInformation();
	$menu = $inter->showMenu(0,$userX->access->ID);
	
	$mainmenu = "";
	$openmenu = "";
	$openmenuparent = "";
	$submenus = "";
	$activo = "";
	$menuinfo = $inter->getMenuInformation($_SESSION["menu_id"]);
	$filename = $menuinfo["link"];
	$parent = $menuinfo["parent"];

	foreach($menu as $menuitem) {
		
		//Verifica el link
		if(strpos($menuitem["link"],"#") !== false && strlen($menuitem["link"]) > 1) {
			$datatarget = $menuitem["link"];
			$link = "#";
		}
		else {
			$datatarget = "";
			$link = $menuitem["link"];
			//Verifica si hay parametros en el link
			$link = str_replace("{__user__}",$userX->ID,$link);
			$link = str_replace("{__script__}",$filename,$link);
		}
		//Variable de menu activo
		$activo = "";
		$openmenu = "";
		//Verifica si es el menu activo
		if($_SESSION["menu_id"] == $menuitem["id"]) {
			$activo = "active";
			$openmenu = "menu-open";
		}
		
		//Si hay submenu
		if(isset($menuitem["child"])) {
			$mainmenu .= processChild($menuitem["child"], $menuitem["title"], $menuitem["icon"], $userX->ID, $filename, $activo, $openmenu);
		}
		else {
			//Verifica los bags
			$menuitem["title"] = search4Bag($menuitem["title"]);
			$mainmenu .= "<li class=\"nav-item\">\n" .
						"<a href=\"$link\" class=\"nav-link $activo\">\n" .
						"<i class=\"nav-icon " . $menuitem["icon"] . "\"></i>\n" .
						"<p>" . $menuitem["title"] . "</p>\n" .
						"</a>\n" .
						"</li>\n";
		}
	}
	
	function processChild($childItem, $title, $icon, $usua, $filename, &$activeChild, &$openMenuChild, $depth = 0) {
		$return = "";
		$submenusChild = "<ul class=\"nav nav-treeview\">\n";
		foreach($childItem as $innerChild) {
			//Si es el submenu activo
			if($_SESSION["menu_id"] == $innerChild["id"]) {
				$activeChild = "active";
				$openMenuChild = "menu-open";
			}
			else {
				$activeChild = "";
			}
			
			$childLink = $innerChild["link"];
			//Verifica si hay parametros en el link
			$childLink = str_replace("{__user__}",$usua,$childLink);
			$childLink = str_replace("{__script__}",$filename,$childLink);
			
			if(isset($innerChild["child"])) {
				$submenusChild .= processChild($innerChild["child"], $innerChild["title"], $innerChild["icon"],$usua,$filename,$activeChild,$openMenuChild,($depth+1));
			}
			else {
				$innerChild["title"] = search4Bag($innerChild["title"]);
				//Inicia la GUI
				$submenusChild .= "<li class=\"nav-item $openMenuChild\">\n" .
							"<a href=\"" . $childLink . "\" class=\"nav-link $activeChild\">\n";
				//Si tiene icono
				if($innerChild["icon"] != "") 
					$submenusChild .= "<i class=\"" . $innerChild["icon"] . " nav-icon\"></i>\n";
				else 
					$submenusChild .= "<i class=\"fa fa-caret-right nav-icon\"></i>\n";
				//Termina la GUI
				$submenusChild .= "<p>" . $innerChild["title"] . "</p>\n" .
							"</a>\n</li>\n";
			}
						
		}
		$submenusChild .= "</ul>\n";
		if($depth > 0) 
			$return = "<li class=\"nav-item has-treeview $openMenuChild\" data-depth=\"$depth\">\n" .
					"<a href=\"#\" class=\"nav-link \">\n" .
					"<i class=\"nav-icon " . $icon . "\"></i>\n" .
					"<p>$title<i class=\"right fa fa-angle-left\"></i></p>\n" .
					"</a>\n" . $submenusChild . "</li>\n";
		else {
			$activeParent = (strpos($submenusChild,"class=\"nav-link active\"") !== false) ? "active" : "";
			$return = "<li class=\"nav-item has-treeview $openMenuChild\" data-depth=\"$depth\">\n" .
					"<a href=\"#\" class=\"nav-link $activeParent\">\n" .
					"<i class=\"nav-icon " . $icon . "\"></i>\n" .
					"<p>$title<i class=\"right fa fa-angle-left\"></i></p>\n" .
					"</a>\n" . $submenusChild . "</li>\n";
		}
		return $return;
	}
	
	function search4Bag($title) {
		//Verifica si contiene algun bag
		if(strpos($title,"{__") !== false) {
			$datas = explode("{",$title);
			$title = $datas[0];
			$text = str_replace("__","",$datas[1]);
			$text = str_replace("}","",$text);
			$datas = explode(".",$text);
			require_once("core/classes/" . $datas[0] . ".php");
			$class = new $datas[0];
			$result = $class->{$datas[1]}();
			if(intval($result) > 0) {
				$title .= " <span class=\"right badge badge-warning badge-menu\">$result</span>";
			}
		}
		return $title;
	}
	
?>
				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"><?= $mainmenu ?></ul>
				</nav>
