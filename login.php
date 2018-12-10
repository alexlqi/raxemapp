<?php session_start();
//en esta sección se configuran las variables de conexión para las clases

if(@$_SESSION["logged"]==true){header("location: {$_GET["redir"]}");}

include_once("includes/class.view.php");

$vista=new view;
$vista->loadHeadElems('JQUERY');
$vista->loadHeadElems('EDWSDK');
$vista->loadHeadElems('BOOTSTRAP');
$vista->loadHeadElems('JQUERYUI');
//$headParam[]=array('tipo'=>'script','attr'=>array('type'=>'text/javascript','src'=>'js/init.js'));
//$vista->loadHeadElems($headParam);

//$vista->loadBody("aquí se usará el codigo para login");
$params["redir"]=$_GET["redir"];
$vista->loadInclude("partes/login/login.php",'add',$params);

$vista->writeHTML();
?>