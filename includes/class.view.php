<?php date_default_timezone_set("America/Monterrey");
if(!preg_match("/.*login\.php.*/",$_SERVER["REQUEST_URI"])){
	if(substr($_SERVER["REQUEST_URI"],-1)!=="/"){
		header("location: {$_SERVER["REQUEST_URI"]}/");
	}
}

# flujo de las páginas
# se debe de hacer en forma anidada para que se pueda escalar
# header y body perteneceran a ## html

# para configs y funciones en todas partes
@include_once "inc.config.php";
@include_once CFG_PATH;
@include_once FUNC_PATH;
@include(CLASS_PATH."class.permisos.php"); # incluye las configuraciones en toda la vista

class view {

## se declaran las variables
private $html="",$head="",$body="";
private $headParam=array(); # arreglo de parametros en head
private $headParams=""; # string de los parametros del head
private $bodyElem=array(); # arreglo de elementos en body
private $bodyElems=""; # string de los elementos en body
private $errorlog=array();
private $wd=""; # working dir

// para cargar la clase
public function __construct($wd=""){
	//siempre que se usa el html se debe de usar el utf-8
	if($wd!=""){
		$this->wd=$wd;
	}
	$this->loadHeadElems(array(
		array('tipo'=>'meta','attr'=>array('charset'=>'utf-8',)),
		array('tipo'=>'meta','attr'=>array('name'=>'viewport','content'=>'width=device-width, initial-scale=1, user-scalable=no',)),
		)
	);
}

	private function regresa(){
		$regresa="";
		if(@$_GET["mrw"]>0){
			$mrw=$_GET["mrw"];
			for ($i=$mrw; $i > 0; $i--) { 
				$regresa.="../";
			}
		}

		return $regresa;
	}

## head: contiene las etiquetas del header
	public function loadHeadElems($elems){
		$t=time();
		## $elems es un array con la siguiente estructura
		/* $elems
			'tipo' => (script|link|meta|title)
			'attr' => array('attr'=>'value')
			'content' => (null,str)
		*/
		## si $elems es string y es JQUERY | JQUERYUI | BOOTSTRAP | DATATABLES entonces vamos a procesarlos
		if(!is_array($elems)){
			//graba el error en el errorlog
			$cargar=true;
			switch($elems){
				case 'JQUERY':
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'js/jquery.min.js',));
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'js/jquery.numeric.js',));
				break;
				case 'JQUERYUI':
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'js/jquery-ui.min.js',));
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/jquery-ui.min.css',));
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/jquery-ui.structure.min.css',));
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/jquery-ui.theme.min.css',));
				break;
				case 'BOOTSTRAP':
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/bootstrap/css/bootstrap.min.css',));
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/bootstrap/css/bootstrap-theme.min.css',));
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'css/bootstrap/js/bootstrap.min.js'));
				break;
				case 'DATATABLES':
					$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'DataTables/datatables.min.css?'.$t,));
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'DataTables/datatables.min.js'));
				break;
				case 'EDWSDK':
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'js/func_enthalpy.js?'.time()));
				break;
				case 'MATERIALIZE':
					$headParam[]=array('tipo'=>'css','attr'=>array("href"=>$this->regresa()."css/materialize/css/materialize.min.css","media"=>"screen,projection"));
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'css/materialize/js/materialize.min.js'));
				break;
				case 'JQUERYALERTS':
					$headParam[]=array('tipo'=>'css','attr'=>array("href"=>$this->regresa()."js/jqueryalerts/jquery.alerts.css","media"=>"screen,projection"));
					$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().'js/jqueryalerts/jquery.alerts.js'));
				break;
				default:
					//aquí identifica si hay algun script js o css para incluirlo sino se usa el error log
					$cargar=false;
					if(!$this->loadHeadFile($elems)){
						$this->errorLog(array('view::loadHeadElems()',"object type",'001','El archivo no es un array'));
					}
				break;
			}
			if($cargar){
				$this->loadHeadElems($headParam);
			}
		}else{ //pero si es array...
			//graba en headParams (string) después de trabajarlo
			foreach($elems as $elem){
				//trabajamos primero los parámetros
				$attrs=array();
				
				$attr="";
				// si es un array el nodo attr de elems
				if(is_array(@$elem["attr"])){
					foreach($elem["attr"] as $attr=>$value){ 
						if(in_array($attr, array("src","href")) ){
							$value=$this->wd.$value;
						}
						$attrs[]="$attr='$value'";
					}
					$attr=implode(' ',$attrs);
				}else if(is_string(@$elem)){ 
					//si es string entonces se usa este mismo metodo
					$this->loadHeadElems($elem); //esto implica que es una array de los predefinidos o los scripts solos
				}
				
				//se usa un switch para trabajar el tipo de elemento en head
				if(is_array(@$elem)){
					switch(@$elem["tipo"]){
						case 'script': // para los scripts javascript
							$this->headParams.="<script type=\"text/javascript\" $attr>".@$elem["content"]."</script>";
						break;
						case 'css': // para los links css
							$this->headParams.="<link type=\"text/css\" rel=\"stylesheet\" $attr />";
						break;
						case 'link': // para otros tipos de links
							$this->headParams.="<link $attr />";
						break;
						case 'meta': // para etiquetas meta
							$this->headParams.="<meta $attr />";
						break;
						case 'title': // para escribir el title
							$this->headParams.="<title $attr>".@$elem["content"]."</title>";
						break;
						default:
							// escribe en el error log y se salta el elemento para continuar
							$this->errorLog(array('view::loadHeadElems()',"Element Type",'001',"Elemento html desconocido"));
							continue;
						break;
					}
				}
			}
		// aquí se escriben todos los elementos del head dentro de la etiqueta head
		$this->head='<head>'.$this->headParams.'</head>';
		}// termina if si hay elementos en los head elements
	}

	private function loadHeadFile($file){
		$fileExplode=explode(".", $file);
		if(count($fileExplode)>0){
			//si existe un file extention y ese file extention esta entre js y css
			$tipo=end($fileExplode);
			if(in_array($tipo, array("js","css","JS","CSS"))){
				//es js o css
				switch($tipo){
					case 'js':
					case 'JS':
						// carga los JavaScripts
						$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa().$file));
					break;
					case 'css':
					case 'CSS':
						// varga los CSS
						$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().$file,));
					break;
				}
				$this->loadHeadElems($headParam);
				return true;
			}else{
				return false;
			}
		}else{
			//como
			return false;
		}
	}
	
	public function loadBody($body){
		$this->body.=$body;
	}
	
	public function loadInclude($path, $output='add', $params=array()){
		// $output sera add, print, return
		/*
			El paradigma de las variables pasadas al script en cuestion será a través del array $params
			este se colocará antes del include $path para que sean utilizados por el script.
		*/
		ob_start();
	
		if( is_readable($path) && $path ){
			include $path;
		}else{
			return FALSE;
		}
		
		switch($output){
			case 'print':
				echo ob_get_clean();
			break;
			case 'return':
				return ob_get_clean();
			break;
			case 'add':
			default:
				$this->body.=ob_get_clean();
			break;
		}
	}
	
	public function writeHTML(){
		//se incluyen los ultimos css y js
		//declaramos los elementos del head
		$t=time();
		$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/init.structure.css?'.$t));
		$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/init.style.css?'.$t));
		$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/init.animation.css?'.$t));
		$headParam[]=array('tipo'=>'css','attr'=>array('href'=>$this->regresa().'css/bootstrap-toggle.min.css?'.$t));
		$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa()."js/init.js?".$t));
		$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa()."js/bootstrap-toggle.min.js?".$t));
		$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>$this->regresa()."js/root.php"));
		//cargamos los elementos en la vista
		$this->loadHeadElems($headParam);
		$this->head.='<link rel="apple-touch-icon" sizes="57x57" href="/css/images/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/css/images/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/css/images/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/css/images/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/css/images/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/css/images/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/css/images/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/css/images/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/css/images/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/css/images/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/css/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/css/images/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/css/images/favicon-16x16.png">
		<link rel="manifest" href="/css/images/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/css/images/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">';
		echo '<!doctype html><html>'.$this->head.'<body onLoad="">'.$this->body.'</body></html>';
	}
	
	private function errorLog($err=array()){
		$this->errorlog[]=array(
			'time'=>date('Y-m-d H:i:s'),
			'source'=>@$err[0],
			'tipo'=>@$err[1],
			'errid'=>@$err[2],
			'msg'=>@$err[3],
		);
	}
	public function getErrorLog(){
		var_dump($this->errorlog);
	}
}

//funciones de la vista
function regresa($num=0){
	$regresa="";
	if(@$_GET["mrw"]>0){
		$mrw=($num==0) ? $_GET["mrw"] : $num;
		for ($i=$mrw; $i > 0; $i--) { 
			$regresa.="../";
		}
	}

	return $regresa;
}
?>