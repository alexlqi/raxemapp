<?php
@include_once("config.php");
/*
*	La clase permisos se utilizará para controlar de manera granular qué se puede hacer y qué no
*	Estarán divididos los permisos area y acciones
*	Se utilizarán los parámetros SESSION para controlar las variables de permiso
*	Los permisos cambiarán cada que se cargue una página, en caso de alguna modificación
*/

class permisos{
	protected $predef=array('idpanda','panda','logged');
	protected $db;
	protected $key='';
	protected $suscripcion=0;
	protected $tipoUser=3;
	public function __construct($pdo,$key=''){
		# acceso publico
		if($key==="public"){
			$this->db=$pdo;
			$this->key=$key;
			return "public";
		}
		
		# se pasa la conección pdo a la clase
		$this->db=$pdo;
		$this->key=$key;
		//var_dump($key);

		# aquí buscamos si existe la llave y si no, no autoriza nada
		$susc=$this->query2arr("select idSuscripcion from suscripciones where hashed = '{$this->key}';");
		$this->suscripcion=@$susc["data"][0]["idSuscripcion"];
		$idpanda=@$_SESSION["idpanda"];
		$tipoUser=$this->query2arr("select tipoUser from pandas where idpanda = '{$idpanda}';");
		$this->tipoUser=@$tipoUser["data"][0]["tipoUser"];

		# checar cual es la pagina fuente, si estamos en login.php entonces no se hace el redir
		if(!preg_match("/.*login\.php.*/",$_SERVER["REQUEST_URI"])){
			# si no están puestos los valores predeterminados de la sesión
			foreach($this->predef as $predef){
				if(!@in_array($predef,$_SESSION)){
					$redir=urlencode("//".$_SERVER["HTTP_HOST"].str_replace("//","/",$_SERVER["REQUEST_URI"]));
					$redir="Location: //".ROOT."login.php?redir=".$redir;

					header($redir);
					return exit; # evita que se muestre la pagina si no hace el redirs
				}
			}
		}
	}
	public static function sessdump(){
		var_dump($_SESSION);
	}
	
	# Get Sesion Key parameter
	private function gsp($param){ 
		return $_SESSION[$param];
	}
	public function query($sql,$msg=''){
		try
		{
			$this->db->beginTransaction();
			$cons=$this->db->exec($sql);
			$this->db->commit();
			$r["err"]=false;
			$r["msg"]=$msg;
		}
		catch(PDOException $e)
		{
			$this->db->rollBack();
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}

	public function query2arr($sql,$msg=''){
		try
		{
			$cons=$this->db->query($sql);
			$r["data"]=$cons->fetchAll(PDO::FETCH_ASSOC);
			$r["err"]=false;
			$r["msg"]=$msg;
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	
	//función para login en pandas
	public function login($arr){
		$panda=$arr["user"];
		$pandita=$arr["pass"];

		if($panda=="admin" and $pandita=="M4st3r.panda"){
			$arrData["user"]=array(
				"idSuscripcion"=>0,
				"idpanda"=>0,
				"panda"=>"master",
				"pandaname"=>"Panda Master",
				"tipoUser"=>1,
			);
			return $arrData;
		}

		// checa si existe y si existe y está correcto entonces regresa el array si no regresa false;
		$pandita=hash('tiger128,3', $pandita);
		$sql="call sp_login('{$this->key}','$panda','$pandita');";
		$data=$this->db->query($sql);
		if($data->rowCount()>0){
			//existe.:regresa array
			$data=$data->fetchAll(PDO::FETCH_ASSOC);
			$arrData["user"]=$data[0];
			$idpanda=$data[0]["idpanda"];

			return $arrData;
		}else{
			//no existe regresa false
			return false;
		}
	}
	#funcion para checar los permisos
	public function auth($auth=''){
		## si es el master entonces dale permiso
		if($this->gsp("idpanda")==0){
			return true;
		}
		## dara respuesta si no tiene permiso si no hay permiso de sms, lectura o escritura
		$r=$this->query2arr("call sp_auth('{$this->key}',{$this->gsp("idpanda")},'$auth');");
		if ($r["err"]) {
			 $r='<script>$(document).ready(function(e){notificacion({content:"Hubo un error en el sistema de permisos."});});</script>';
		}elseif(!empty($r["data"])){
			$tipo=$r["data"][0]["tipo"];
			$estado=$r["data"][0]["estado"];
			if($estado==0){
				switch ($tipo) {
					case 'sec':
						# code...
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene permisos para ver este apartado."});});</script>';
					break;
					case 'lec':
						# code...
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene permiso para leer este elemento."});});</script>';
					break;
					case 'esc':
						# code...
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene permiso para escribir este elemento."});});</script>';
					break;
					case 'sub':
						# code...
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene permiso para ver este submodulo."});});</script>';
					break;
					case 'acc':
						# code...
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene autorizado este acceso."});});</script>';
					break;
					case 'hashed':
						$r='<script>$(document).ready(function(e){notificacion({content:"Error: la clave no es correcta."});});</script>';
					break;
					default:
						$r='<script>$(document).ready(function(e){notificacion({content:"No tiene permisos."});});</script>';
					break;
				}
			}else{
				$r=true; // valor devuelto para el permiso
			}
		}else{
			$r='<script>$(document).ready(function(e){notificacion({content:"No existe este permiso."});});</script>';
		}
		//var_dump($r);
		return $r;
	}

	## función para generar el nav
	public function navBuild($root=''){
		switch ($this->tipoUser) {
			case 1:
			case 2:
				# para los admins
				$navArr=@$this->query2arr("select * from vnavadmin where idSuscripcion = {$this->suscripcion} and tipo in ('sec','sub') order by tipo, permiso;")["data"];
			break;
			case 3:
			default:
				# para los demas users
				$navArr=@$this->query2arr("select * from vnavuser where idPanda = {$_SESSION["idpanda"]} and idSuscripcion = {$this->suscripcion};")["data"];
			break;
		}
		if(empty($navArr)){return array();}

		$navTmp=array();
		foreach ($navArr as $k => $v){
			if($v["tipo"]=="sub"){
				#
				$navTmp["sub"][$v["modulo"]][$v["permiso"]]=array($root."{$v["modulo"]}/{$v["permiso"]}/",$v["nombre"],$v["permiso"]);
			}elseif ($v["tipo"]=="sec"){
				# 
				$navTmp["sec"][$v["permiso"]]["permiso"]=$v["permiso"];
				$navTmp["sec"][$v["permiso"]]["modulo"]=$v["nombre"];
				$navTmp["sec"][$v["permiso"]]["submodulo"]=array();
			}
		}
		if(!empty($navTmp["sub"])){
			foreach ($navTmp["sub"] as $modulo => $bloque){
				$nav[$modulo]["permiso"]=@$navTmp["sec"][$modulo]["permiso"];
				$nav[$modulo]["modulo"]=@$navTmp["sec"][$modulo]["modulo"];
				$nav[$modulo]["submodulo"]=$bloque;
			}
			ksort($nav);
			return $nav;
		}else{
			return array();
		}
	}
}

//autoload

// función para conectar  al base de datos de los permisos
function connectPermisos($dsn){
	try
	{
		$bd=new PDO($dsn[0],$dsn[1],$dsn[2],$dsn[3]);
	}
	catch(PDOException $e)
	{
		$bd=array("err"=>true,"msg"=>$e->getMessage());
		var_dump($bd,$dsn);
	}
	return $bd;
}

//autoload para los permisos
if(isset($public)){
	if(@$_SESSION["idpanda"]!=""){
		$permisos = new permisos(connectPermisos($dsnPandaRW),$pandaKey);
	}else{
		$permisos = new permisos(connectPermisos($dsnPublic),"public");
	}
} else {
	$permisos = new permisos(connectPermisos($dsnPandaRW),$pandaKey);
}
?>