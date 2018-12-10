<?php session_start();
$public=false;
$dsnPublic1=array(
	'mysql:host=localhost; dbname=promedic_rh; charset=utf8;',
	'promedicRH',
	'promedicRH',
	array(
		PDO::ATTR_EMULATE_PREPARES=>false,
		PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES => true,
	),
);
$dsnPublic=$dsnPublic1;
include_once("includes/class.view.php");
$vista=new view;

$headParam=array(
	'JQUERY',
	'JQUERYUI',
	'BOOTSTRAP',
	'DATATABLES',
	'EDWSDK',
	'JQUERYALERTS',
	'js/init.js',
	array('tipo'=>'title','content'=>'Acceso público'),
);
$vista->loadHeadElems($headParam);

$body=$vista->loadInclude('partes/header.php','return',$permisos);

$seccion="public";
$sub=(@$_GET["sub"]!="")?$_GET["sub"]:"index";
$body.=$vista->loadInclude("partes/$seccion/$sub.php",'return',$permisos);

# escribe todo
$vista->loadBody($body);
$vista->writeHTML();
unset($vista);
?>