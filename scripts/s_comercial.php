<?php session_start();
header('Content-type: application/json');
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
@include("../includes/class.permisos.php");
function mkfolder($path){
	if(!is_dir($path)){
		return mkdir($path, 0777,true);
	}
}
/*
$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
//*/
$dsnModelo=$dsnPmAdmon;
@include("../includes/class.modelo.php");

$r["err"]=true;

switch(@$_POST["ctrl"]){
	case 'asignarClientes':
		$id=@$_POST["idAsignacion"];
		$e=@$_POST["equipo"];
		$c=@$_POST["idCliente"];
		$sql="insert into clientes_asignacion (idAsignacion,equipo,idCliente) VALUES ('$id','$e','$c') on duplicate key update equipo='$e', idCliente='$c';";
		$r=$modelo->insertSql($sql);
	break;
	case 'lBuscaAsignacion':
		$sql="select * from clientes_asignacion where idAsignacion={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'eliminar':
		$tabla=array(
			"asignacionForm"=>array("clientes_asignacion","idAsignacion"),
		);
		$sql="delete from {$tabla[$_POST["tabla"]][0]} where {$tabla[$_POST["tabla"]][1]}={$_POST["id"]}";
		$r=$modelo->exec($sql);
	break;
	case 'cuc':
		$r["err"]=false;
		//$r["cuc"]=strtoupper(dechex(str_replace(".","",microtime(true))*1));
		$r["cuc"]=strtoupper(dechex(date("ymdHis")).rand(11,99));
		$cli=array();
		$sql="select * from cli_adminpaq where cuc is null";
		$d=$modelo->query2arr($sql);
		if(!empty($d["data"])){
			foreach($d["data"] as $dd){
				$cli[$dd["idCliente"]]=strtoupper(dechex(date("ymd",strtotime($dd["alta"]))).rand(11111,99999));
			}
			foreach($cli as $idCliente=>$cuc){
				$modelo->update("update cli_adminpaq set cuc='{$cuc}' where idCliente='$idCliente';");
			}
		}
	break;
	default:
	break;
}
echo json_encode($r);
?>