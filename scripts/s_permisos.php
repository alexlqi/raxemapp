<?php session_start();
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnPandaRW;
@include("../includes/class.modelo.php");
$r=$no_permite=array("err"=>true,"msg"=>"No tiene permisos de ejecución.");

switch(@$_POST["ctrl"]){
	case 'm': //modificar permiso
		if($p=$permisos->auth("modificar_permisos")===true){
			$tipo=$_POST["tipo"];
			if($tipo=="vista"){
				# para la vista
				$p='';
			}else{
				$p=$_POST["p"];
			}
			$sql="call sp_modAuth('$pandaKey','$tipo','$p','{$_POST["idPerm"]}','{$_POST["idPanda"]}','{$_POST["st"]}');";
			$r=$permisos->query2arr($sql);
			if(!$r["err"]){
				$r["msg"]="Permisos actualizados.";
			}//*/
		}else{

		}
	break;
	case 'ns': //nuevo permiso
		$hashed=$permisos->query2arr("select hashed from suscripciones where idSuscripcion='{$_POST["idSuscripcion"]}';")["data"][0]["hashed"];
		$idPermiso=@$_POST["idPermiso"];
		$p=@$_POST["permiso"];
		$n=@$_POST["nombre"];
		$d=@$_POST["descripcion"];
		$t=@$_POST["tipo"];
		$secP=@$_POST["sec"];
		if($t=="sec"){$secP=$p;}
		$modP=@$_POST["sub"];
		$tabP=@$_POST["tab"];
		$sql="call sp_crearPerm('$hashed','$idPermiso','$t','$p','$n','$d','$secP','$modP','$tabP');";
		$r=$permisos->query2arr($sql,"Permiso creado correctamente.");
	break;
	case 'l': //listar permisos por usuario
	break;
	case 'lu':
		$idSusc=$_POST["idSusc"];
		$r=$modelo->query2opt("select * from pandas where idSuscripcion=$idSusc;",array('idpanda','panda'));
		$r["data"]="<option disabled='disabled' selected='selected'>Elige un usuario</option>".$r["data"];
	break;
	case 'nu': //nuevo usuario
		$panda=$_POST["p"];
		$pandita=hash('tiger128,3', $_POST["pp"]);
		$tipo=$_POST["t"];
		$nombre=$_POST["n"];
		$pId=$permisos->query2arr("select hashed from suscripciones where idSuscripcion='{$_POST["idSuscripcion"]}';")["data"][0]["hashed"];
		$sql="call sp_nuevoUser('$pId','$panda','$pandita','$tipo','$nombre');";
		$r=$permisos->query2arr($sql,'Usuario guardado correctamente');
		if($r["err"]){
			# algo
		}elseif($r["data"][0]["idpanda"]==''){ 
			$r["err"]=true;
			$r["msg"]="No existe el pandaKey proporcionado.";
		}
	break;
	case 'ns': //nuevo servicio
		$nombre=$_POST["n"];
		$hashed=hash('ripemd256', $_POST["hash"]);
		$sql="call sp_nuevoServicio('$nombre','$hashed');";
		$r=$permisos->query2arr($sql,'Servicio creado correctamente');
		if(!$r["err"] and $r["data"][0]["hashed"]!=""){
			# se gneró correctamente el nuevo servicio y se procederá a crear el admin
			$panda="admin";
			$pandita=hash('tiger128,3', "admin");
			$tipo=1;
			$nombre="Administrador";
			$sql="call sp_nuevoUser('{$r["data"][0]["hashed"]}','$panda','$pandita','$tipo','$nombre');";
			$r=$permisos->query2arr($sql,'Servicio y usuario admin creado correctamente.');
		}
	break;
	case 'lBuscaPermisos':
		$sql="select * from permisos where idPermiso={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'eliminar':
		$sql="delete from {$_POST["tabla"]} where idPermiso={$_POST["id"]};";
		$r=$modelo->exec($sql);
	break;
	default:
	break;
}

echo json_encode($r);
?>