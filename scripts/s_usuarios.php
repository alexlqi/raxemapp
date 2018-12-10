<?php session_start();
header('Content-type: application/json');
include("../includes/class.permisos.php");
$dsnModelo=$dsnPandaRW;
include("../includes/class.modelo.php");

$tablas=array(
	"pandaxUsers"=>array(
		"pandas", # tabla
		"idPanda", # primary key
	),
);

function cleanPandita(&$val,$key){
	if($key=="pandita"){$val='';}
}

$r=$no_permite=array("err"=>true,"msg"=>"No tiene permisos de ejecución.");
$ctrl=@$_POST["ctrl"];
unset($_POST["ctrl"]);
switch($ctrl){
	case 'm': //modificar permiso
		if($p=$permisos->auth("modificar_permisos")===true){
			$r=$permisos->query2arr("call sp_modAuth('$pandaKey',{$_POST["idPerm"]},{$_POST["idPanda"]},{$_POST["st"]});");
			if(!$r["err"]){
				$r["msg"]="Permisos actualizados.";
			}
		}else{

		}
	break;
	case 'n': //nuevo permiso
		$p=$_POST["p"];
		$n=$_POST["n"];
		$d=$_POST["d"];
		$t=$_POST["t"];
		$sql="call sp_crearPerm('$pandaKey','$t','$p','$n','$d');";
		$r=$permisos->query2arr($sql,"Permiso creado correctamente.");
	break;
	case 'lu': //listar usuario
		echo $sql="select * from pandas where idSuscripcion={$_POST["idSuscripcion"]};";
		$r=$permisos->query2arr($sql);
	break;
	case 'nu': //nuevo usuario
		/*$panda=$_POST["p"];
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
		}//*/
		$_POST["pandita"]=hash('tiger128,3', $_POST["pandita"]);
		$r=$modelo->array2insert("pandas",$_POST,'',array_keys($_POST));
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
	case 'eliminar': //nuevo servicio
		$sql="delete from {$tablas[$_POST["tabla"]][0]} where {$tablas[$_POST["tabla"]][1]}={$_POST["id"]};";
		$r=$permisos->query($sql,'Registro eliminado correctamente');
	break;
	case 'lBuscaUsuarios':
		$sql="select * from pandas where idPanda={$_POST["id"]};";
		$r=$permisos->query2arr($sql);
		array_walk_recursive($r,"cleanPandita");
		$r["data"]=$r["data"][0];
	break;
	default:
	break;
}

echo json_encode($r);
?>