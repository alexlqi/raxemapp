<?php @session_start();
header('Content-type: application/json');

@include_once '../includes/permisos.php';
@include_once '../includes/uuid.php';

$operdb="rw";
include_once ('../includes/class.modelo.php');

switch ($_POST["ctrl"]) {
	case 'putIncidencia':
		array_push($_POST, $_FILES);
		echo json_encode($_POST);
	break;

	case 'addFile':
		if(!empty($_FILES)){
			//move_uploaded_file($_FILES["tmp_name"], destination)
			$archivo=$_FILES["file"];
			$files["uuid"]=strtoupper(UUID::v4());
			$files["name"]=$archivo["name"];
			$files["mime"]=$archivo["type"];
			$files["tmp_name"]=$archivo["tmp_name"];
			$r=addFile($files);
			$r["uuid"]=$files["uuid"];
			
			echo json_encode($r);
		}
	break;
	
	case 'delFile':
		$r=delFile($_POST["uuid"]);
		echo json_encode($r);
	break;

	default:
	# code...
	break;
}

function addFile($archivo){
	global $modelo;
	$uuid=$archivo["uuid"];
	$name=$archivo["name"];
	$mime=$archivo["mime"];
	$data=base64_encode(file_get_contents($archivo["tmp_name"]));
	$sql="call sp_addFile('$uuid','$name','$mime','$data');";
	$r=$modelo->query2array($sql);
	return $r;
}

function delFile($files){
	global $modelo;
	$uuid=$_POST["uuid"];
	$sql="call sp_delFile('$uuid');";
	$r=$modelo->query2array($sql);
	return $r;
}

?>