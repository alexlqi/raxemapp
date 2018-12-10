<?php session_start();
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnPmAdmon;
@include("../includes/class.modelo.php");

function mkfolder($path){
	if(!is_dir($path)){
		return mkdir($path, 0777,true);
	}
}

$r["err"]=true;

$week_number = (date("w")>0) ? date("W") : date("W")-1;
$ctrl=@$_POST["ctrl"];
switch($ctrl){
	case 'eligeCliente':
		$_SESSION["administracion"]["idCliente"]=@$_POST["idCliente"];
		$r["err"]=false;
	break;
	case 'cargar':
		$idCliente=@$_SESSION["administracion"]["idCliente"];
		$sql="select * from clientes where idCliente = '$idCliente';";
		$r=$modelo->query2arr($sql);
	break;
	default:
	break;
}
echo json_encode($r);
?>