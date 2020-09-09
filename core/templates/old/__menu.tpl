<?
	require_once("core/classes/users.php");
	require_once("core/classes/interfaces.php");
	require_once("core/classes/language.php");

	$userX = new users($_SESSION['vtappcorp_userid']);
	$inter = new interfaces();
	$userX->__getInformation();
	$menu = $inter->showMenu(0,$userX->access->ID);

	$filename = $inter->getMenuInformation($_SESSION["menu_id"])["link"];
	
	$menumobile = "";
	$mainmenu = "";
	$submenus = "";
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
		$haschild = "";
		$openmenu = "";
		//Verifica si es el menu activo
		if($_SESSION["menu_id"] == $menuitem["id"]) {
			$activo = "active";
		}

		//Si hay submenu
		if(isset($menuitem["child"])) {
			$submenus = "<ul class=\"nav nav-treeview\">\n";
			foreach($menuitem["child"] as $child) {
				//Si es el submenu activo
				if($_SESSION["menu_id"] == $child["id"]) {
					$activo = "active";
					$openmenu = "menu-open";
				}
				else {
					$activo = "";
				}
				$childLink = $child["link"];
				//Verifica si hay parametros en el link
				$childLink = str_replace("{__user__}",$userX->ID,$childLink);
				$childLink = str_replace("{__script__}",$filename,$childLink);
				
				//Inicia la GUI
				$submenus .= "<li class=\"nav-item\">\n" .
							"<a href=\"" . $childLink . "\" class=\"nav-link $activo\">\n";
				//Si tiene icono
				if($child["icon"] != "") 
					$submenus .= "<i class=\"" . $child["icon"] . " nav-icon\"></i>\n";
				else 
					$submenus .= "<i class=\"fa fa-circle-o nav-icon\"></i>\n";
				//Termina la GUI
				$submenus .= "<p>" . $child["title"] . "</p>\n" .
							"</a>\n</li>\n";
			}
			$submenus .= "</ul>\n";
			$mainmenu .= "<li class=\"nav-item has-treeview $openmenu\">\n" .
						"<a href=\"#\" class=\"nav-link $activo\">\n" .
						"<i class=\"nav-icon " . $menuitem["icon"] . "\"></i>\n" .
						"<p>" . $menuitem["title"] . "<i class=\"right fa fa-angle-left\"></i></p>\n" .
						"</a>\n" .
						$submenus .
						"</li>\n";

		}
		else {
			$mainmenu .= "<li class=\"nav-item\">\n" .
						"<a href=\"$link\" class=\"nav-link $activo\">\n" .
						"<i class=\"nav-icon " . $menuitem["icon"] . "\"></i>\n" .
						"<p>" . $menuitem["title"] . "</p>\n" .
						"</a>\n" .
						"</li>\n";
		}
	}
?>
				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"><?= $mainmenu ?></ul>
				</nav>
