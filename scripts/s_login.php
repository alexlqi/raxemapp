<?php session_start();

//include("c_usuarios.php"); //contiene las configuraciones de login
include("../includes/class.permisos.php"); //contiene las configuraciones de login

switch(@$_POST["ctrl"]){
	case 'login':
		//se graban los datos
		$redir=$_GET["redir"]; //viene del request
		
		//se consulta a la base de datos
		if($login=$permisos->login(@$_POST)){
			//se guardan las variables de sesión
			$_SESSION=$login["user"];
			$_SESSION["logged"]=true;
			$sesion=$permisos->query("call sp_sesiones('abrir',{$login["user"]["idpanda"]},'".session_id()."');");
		}else{
			session_destroy();
		}
		header("location: $redir");
	break;
	case 'logoff':
		//@unlink("../pem/".$_SESSION["panda"]);
		//@unlink("../pem/auth_".$_SESSION["idpanda"]);
		//quitar sesion del model
		$sesion=$permisos->query("call sp_sesiones('cerrar',".$_SESSION["idpanda"].",'".session_id()."');");
		session_destroy();
	break;
}

if(@$_GET["logoff"]==1){
	//quitar permisos de la app
	//@unlink("../pem/".$_SESSION["panda"]);
	//@unlink("../pem/auth_".$_SESSION["idpanda"]);

	//quitar sesion del model
	$sesion=$permisos->query("call sp_sesiones('cerrar',".$_SESSION["idpanda"].",'".session_id()."');");
	//var_dump($sesion);
	session_destroy();
	//header("location: $root");
}

?>