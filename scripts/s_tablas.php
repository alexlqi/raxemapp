<?php session_start();
date_default_timezone_set("America/Monterrey");
ini_set('memory_limit', '256M');
header('Content-type: application/json');
@include("../includes/class.permisos.php");
@include("../includes/class.table.php");
@include("tablas.php");
$tabla=new tables;

$ctrl=@$_POST["ctrl"];
$tipo='dataTableAjax'; //dataTableAjax | dataTable | table | dataTable
#se selecciona la base de datos segun el ctrl
switch($ctrl){
	case 'lu':
	case 'luAjax':
	case 'lPermisosAjax':
		$dsnModelo=$dsnPandaRW;
	break;
	default:
		$dsnModelo=$dsnExamenes;
	break;
}
@include("../includes/class.modelo.php");

$tablaCfg=array(
	"modelo"=>$modelo,
	"tabla"=>$tabla,
	"permisos"=>$permisos,
	"tipo"=>$tipo,
);

$data=(!empty($_POST["data"])) ? $_POST["data"] : array();
$r["tabla"]=generaTabla($tablaCfg,$ctrl,$data);

echo json_encode($r,JSON_UNESCAPED_UNICODE);
?>