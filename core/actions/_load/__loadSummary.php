<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	/**
	 * Pull a particular property from each assoc. array in a numeric array, 
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	function pluck($a, $prop) {
		$out = array();

		for($i = 0,$len = count($a);$i < $len;$i++) {
			$out[] = $a[$i][$prop];
		}
		return $out;
	}
	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_exec()
	 *
	 * @param  array &$a    Array of bindings
	 * @param  *      $val  Value to bind
	 * @param  int    $type PDO field type
	 * @return string       Bound key to be used in the SQL where this parameter
	 *   would be used.
	 */
	function bind(&$a,$val,$type) {
		$key = ':binding_' . count($a);

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}

	
    //Captura las variables
    if(empty($_POST['class'])) {
        //Verifica el GET
        if(empty($_GET['class'])) {
            exit();
		}
		else {
            $class = $_GET['class'];
			$fields = $_GET['field'];
			$options = $_GET['options'];
		}
    }
    else {
		$class = $_POST['class'];
		$fields = $_POST['field'];
		$options = $_POST['options'];
    }
	
	require_once("../../classes/" . $class . ".php");

	$result = array();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Arma el array de columnas
		$cont=0;
		$aColumnsBD = array();
		foreach(explode(",",$fields) as $field) {
			$data = array("db" => $field, "dt" => $cont);
			array_push($aColumnsBD,$data);
			$cont++;
		}
		$dtColumns = pluck($aColumnsBD,'dt');
		$dbColumns = pluck($aColumnsBD,'db');
		
		//Paginado
		$sLimit = "";
		if(isset($_GET['start']) && $_GET['length'] != -1 ) {
			$sLimit = "LIMIT " . intval($_GET['start']) . ", " . intval($_GET['length']);
		}

		$sOrder = "";
		//Ordenamiento	
		if(isset($_GET['order']) && count($_GET['order'])) {
			$orderBy = array();

			for($i = 0,$ien = count($_GET['order']);$i < $ien;$i++) {
				// Convert the column index into the column data property
				$columnIdx = intval($_GET['order'][$i]['column']);
				$requestColumn = $_GET['columns'][$columnIdx];
				
				$columnIdx = array_search($requestColumn['data'],$dbColumns);
				$column = $dbColumns[$columnIdx];

				if($requestColumn['orderable'] == 'true') {
					$dir = $_GET['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';

					$orderBy[] = '`' . $column . '` ' . $dir;
				}
			}

			$sOrder = 'ORDER BY ' . implode(', ', $orderBy);
		}
		
		//Filtrado
		$sWhere = "";
		$globalSearch = array();
		$columnSearch = array();
		if(isset($_GET['search']) && $_GET['search']['value'] != '') {
			$str = str_replace(" ","%",$_GET['search']['value']);

			for($i = 0,$ien = count($_GET['columns']);$i < $ien;$i++) {
				$requestColumn = $_GET['columns'][$i];
				$columnIdx = array_search($requestColumn['data'],$dbColumns);
				$column = $dbColumns[$columnIdx];

				if($requestColumn['searchable'] == 'true') {
					$globalSearch[] = "`" . $column . "` LIKE '%" . $str . "%'";
				}
			}
		}
		
		// Individual column filtering
		if(isset($_GET['columns'])) {
			for($i = 0,$ien = count($_GET['columns']);$i < $ien;$i++) {
				$requestColumn = $_GET['columns'][$i];
				$columnIdx = array_search($requestColumn['data'],$dtColumns);
				$column = $dbColumns[$columnIdx];

				$str = str_replace(" ","%",$requestColumn['search']['value']);

				if($requestColumn['searchable'] == 'true' && $str != '') {
					$columnSearch[] = "`" . $column . "` LIKE '%" . $str . "%'"; 
				}
			}
		}

		if(count($globalSearch)) {
			$sWhere = '(' . implode(' OR ', $globalSearch) . ')';
		}

		if(count($columnSearch)) {
			$sWhere = $sWhere === '' ? implode(' AND ',$columnSearch) : $sWhere . ' AND ' . implode(' AND ',$columnSearch);
		}

		if ($sWhere !== '') {
			$sWhere = 'WHERE ' . $sWhere;
		}

		//Asigna la informacion
		$sxemp = new $class();
		
		$msgs = $sxemp->showSummary($dbColumns,$sWhere,$sOrder,$sLimit,$options);

        //Exitosa
		$result = (empty($msgs)) ? $sxemp->sql : $msgs;
	}
	else {
        $result = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
