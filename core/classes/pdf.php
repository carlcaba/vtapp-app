<?
//Define el path de las fuentes
define('FPDF_FONTPATH','font/');

//Incluye las clases necesarias
require_once("connector_db.php");
require_once("pdf/fpdf.php");

class myPDF extends FPDF {
	var $ProcessingTable=false;
	var $aCols=array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;
	var $title;
	var $conx;
	var $error;
	var $nerror;
	
	//Constructor de la clase
	function __constructor() {
		$this->pdf();
	}
	
	//Constructor anterior
	function pdf() {
		//Inicializa los atributos
		$this->error = "";
		$this->nerror = 0;
		$this->conx = new connector_db();
		$this->connectIt();
	}
	
	//Destructor de la clase	
	function __destruct() {
		//Cierra la conexion a la BD
		$this->conx->close_it();
	}
	
	//Funcion que conecta a la BD
	function connectIt() {
		//Revisa que no esté previamente conectado
		if(isset($this->conx)) {
			$this->conx->close_it();
			unset($this->conx);
		}
			
		$this->conx = new connector_db();
		if(!$this->conx->connect()) {
			$this->error = $this->conx->Error;
			$this->nerror = 10;
		}
		else {
			$this->nerror = 0;
			$this->error = "";
		}
	}		
	
	function Header() {
		//Logo
		$this->Image('images/prisma_logo.jpg',10,8);
		//Ajusta fuente
		$this->SetFont('Arial','B',16);
		$this->Ln(2);
		$this->Cell(0,6,utf8_decode("Gerencia Gestión de Información de Red y Servicios"),0,1,'R');
		$this->Ln(5);
		$this->Cell(0,6,$this->title,0,1,'R');
		$this->Ln(10);
		//Muestra la cabecera de ser necesario
		if($this->ProcessingTable)
			$this->TableHeader();
	}
	
	function TableHeader() {
		$this->SetFont('Arial','B',12);
		$this->SetX($this->TableX);
		$fill=!empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
		$this->Ln();
	}
	
	function Row($data) {
		$this->SetX($this->TableX);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],10,$data[$col['f']],1,0,$col['a'],$fill);
		$this->Ln();
		$this->ColorIndex=1-$ci;
	}
	
	function CalcWidths($width,$align) {
		//Calcula los anchos de columna
		$TableWidth=0;
		foreach($this->aCols as $i=>$col) {
			$w=$col['w'];
			if($w==-1)
				$w=$width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w=$w/100*$width;
			$this->aCols[$i]['w']=$w;
			$TableWidth+=$w;
		}
		//Calcula la abcisa de la tabla de acuerdo a la orientacion
		if($align=='C')
			$this->TableX=max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX=$this->lMargin;
	}
	
	function AddCol($field=-1,$width=-1,$caption='',$align='L') {
		//Adiciona una columna a la tabla
		if($field==-1)
			$field=count($this->aCols);
		$this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align);
	}
	
	function Table($query,$prop=array()) {
		//Reconecta a la BD
		$this->connectIt();
		//Realiza la consulta
		$this->conx->do_query($query);
		//Asigna el Id del query
		$res = $this->conx->query_id;
		//Adiciona todas las columnas si ninguna fue especificada
		if(count($this->aCols)==0) {
			$nb=mysql_num_fields($res);
			for($i=0;$i<$nb;$i++)
				$this->AddCol();
		}
		//Obtiene el nombre de las columnas cuando no se especifican
		foreach($this->aCols as $i=>$col) {
			if($col['c']=='') {
				if(is_string($col['f']))
					$this->aCols[$i]['c']=ucfirst($col['f']);
				else
					$this->aCols[$i]['c']=ucfirst(mysql_field_name($res,$col['f']));
			}
		}
		//Manejo de las propiedades
		if(!isset($prop['width']))
			$prop['width']=0;
		if($prop['width']==0)
			$prop['width']=$this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align']='C';
		if(!isset($prop['padding']))
			$prop['padding']=$this->cMargin;
		$cMargin=$this->cMargin;
		$this->cMargin=$prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor']=array();
		$this->HeaderColor=$prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1']=array();
		if(!isset($prop['color2']))
			$prop['color2']=array();
		$this->RowColors=array($prop['color1'],$prop['color2']);
		//Calcula el ancho de las columnas
		$this->CalcWidths($prop['width'],$prop['align']);
		//Imprime la cabecera
		$this->TableHeader();
		//Imprime las filas
		$this->SetFont('Arial','',9);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;
		while($row=mysql_fetch_array($res))
			$this->Row($row);
		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		$this->aCols=array();
	}
	
	//Pie de pagina
	function Footer() {
		//Posicion a 1.5 cm del final de pagina
		$this->SetY(-15);
		//Ajuste de fuente
		$this->SetFont('Arial','I',8);
		//Numero de pagina
		$this->Cell(0,10,'Usuario: ' . $_SESSION['prisma_userid'],0,0,'L');
		$this->Cell(0,10,'Fecha: ' . date("d M Y h:i A"),0,0,'R');
//		$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
}
?> 
