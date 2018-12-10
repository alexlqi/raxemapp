<?php @session_start();
@include_once("config.php");
/*
	Este metodo servirá para revisar los permisos escritos en el archivo pem
	luego los usará para confrontar con la funcion permisos
*/
$rootFolder="/".$classCfg["rootFolder "]."/";
if(@$_SESSION["idpanda"]==""){
	$redir=urlencode("//".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	$redir="Location: //".$_SERVER["HTTP_HOST"].$rootFolder."login.php?redir=".$redir;
	header($redir);
}

function permisos($tipo='',$permiso=''){
	$r=false;
	switch ($tipo) {
		case 'sms':
			# code...
			$resp='<script>$(document).ready(function(e){(function(){notificacion({content:"No tiene permisos para ver este apartado"});})();});</script>';
		break;
		case 'lec':
			# code...
			$resp='<script>$(document).ready(function(e){((function(){notificacion({content:"No tiene permisos de lectura."});})();});</script>';
		break;
		case 'esc':
			# code...
			$resp='<script>$(document).ready(function(e){((function(){notificacion({content:"No tiene permisos de escritura"});})();});</script>';
		break;
	}
	//dara respuesta si no tiene permiso si no hay permiso de sms, lectura o escritura
	$permisos=@json_decode(file_get_contents(__DIR__."/../pem/".$_SESSION["panda"]),true);
	if(!empty($permisos)){
		if(!isset($permisos[$tipo][$permiso])){
			$r='<script>$(document).ready(function(e){(function(){notificacion({content:"No existe este permiso"});})();});</script>';
		}else if(@$permisos[$tipo][$permiso]==0){
			//revisa si no hay permisos
			$r=$resp;
		}
	}else{
		$r='<script>$(document).ready(function(e){(function(){notificacion({content:"Este usuario no tiene permisos"});})();});</script>';
	}
	return $r;
}
?>