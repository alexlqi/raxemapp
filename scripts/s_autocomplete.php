<?php session_start();
header('content: application/json');
$path=$_SERVER['DOCUMENT_ROOT']."sdk";
$operdb="pr";
include("$path/includes/class.modelo.php");
include("$path/includes/class.permisos.php");

$r=array();
$sql="";

if(!@$_SESSION["logged"]){echo json_encode($r);}
if(!$permisos->permiso("grant")){echo json_encode($r);};

/*
$r=array(
	array(
		"id"=>"",
		"label"=>"",
		"value"=>"",
	),
);
*/
$b=@$_GET["term"];
switch(@$_GET["ctrl"]){
	case 'p':
		$sql="SELECT permiso as label, permiso as value, descripcion FROM pandas_permisos WHERE permiso like '%".$b."%' GROUP BY permiso;";
	break;
}

$r=$modelo->query2array($sql);
if(!$r["err"]){$r=$r["data"];}
echo json_encode($r);
?>