<?php session_start();
include_once("includes/class.view.php");

$vista=new view;

$headParam=array(
	'JQUERY',
	'JQUERYUI',
	'BOOTSTRAP',
	'DATATABLES',
	'EDWSDK',
	'CHARTJS',
	array('tipo'=>'title','content'=>APP_NAME),
);
$vista->loadHeadElems($headParam);

$body=$vista->loadInclude('partes/header.php','return',$permisos);
$body.=$vista->loadInclude('partes/loaders/romboLoader.php','return');

$seccion=@$_GET["seccion"];
$perm=$permisos->auth($seccion);
if(@$_GET["seccion"]=='' and @$_GET["sub"]==''){
	$body.=$vista->loadInclude("partes/index/index.php",'return',$permisos);
}else if($perm===true){
	$sub=(@$_GET["sub"]!="")?$_GET["sub"]:"index";
	$body.=$vista->loadInclude("partes/$seccion/$sub.php",'return',$permisos);
}else{
	$body.=$perm;
}//*/

# escribe todo
$vista->loadBody($body);
$vista->writeHTML();
unset($vista);
?>