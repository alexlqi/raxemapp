<?php
session_start(hash('md2',time()));
/*
	El paradigma de esta vista consiste en separar la carga del header y
	del body, porque se tratan de distinta manera.
	
	Para el header se utiliza un array para declarar los elementos y sus atributos;
	una vez declarados se cargan a la vista por cada página. Esto permite crear configuraciones
	predeterminadas y reciclar utilizando constantes, pueden incluirse dentro de la vista
	como el default de la clase.
	
	En el body el paradigma cambia, se utilizará de dos maneras, generando el codigo html usando
	PHP, y cargando archivos PHP que contienen otras partes del código. Al final, todo se juntará
	en el master donde estará cargada la vista y bootstrap junto con el custom css moldeará
	el aspecto de la aplicación
	
	Trabajo pendiente
	- añadir jqueryPlot
	- o bien utilizar la plantilla superflat
	- hace falta crear la clase tablas, que será auxiliar a la hora de hacer las tablas para
	  jQuery DataTables
	- Crear la clase del controlador de la aplicación
*/

//inicializamos la vista
include_once("includes/class.view.php");
$vista=new view;

//declaramos los elementos del head
$headParam=array(
	'JQUERY',
	'JQUERYUI',
	'BOOTSTRAP',
	'DATATABLES',
	'EDWSDK',
	'js/init.js',
	array('tipo'=>'title','content'=>APP_NAME),
);
//cargamos los elementos en la vista
$vista->loadHeadElems($headParam);

//al utilizar load include con el parametro return estamos haciendo que lea el archivo y lo traiga en forma de variable
//se pueden pasar variables usando $params[]
$body=$vista->loadInclude('partes/header.php','return',$permisos);
$body.=$vista->loadInclude("partes/index/index.php",'return',$permisos);
/*
	hace falta pensar en alguna forma de que los recursos como conexiones a bases de datos y los motores de tablas
	queden de alguna manera establecidos en una base compartida por todas las clases y que se puedan reciclar y 
	modificar de manera rápida.

	Para lo cual hace falta crear una clase modelo que haga que se comunique con la base de datos y que se creen perfiles
	de clase para que se tenga acceso a diferentes tipos de permisos de la base de datos y así trabajar más seguro en la app

	Se llamará procesamiento granular, que hará uso de estas clases para poder procesar la datos I/O.
*/

//cargamos el body en la vista
$vista->loadBody($body);

//escribimos el html
$vista->writeHTML();

//liberamos la memoria
unset($vista);
?>