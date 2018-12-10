<?php 
/*
este archivo de configuración servirá para almacenar
la información de los botones que estaran disponibles
en cada vista, utilizando el SCRIPT_NAME como pivote
*/
$nav=array( //conjunto de bloques de navegación
	//bloque de modulo/submodulo
  	array(
  		"permiso"=>"administracion",
  		"modulo"=>'Administración',
  		"submodulo"=>array(
			array(ROOT_FOLDER."administracion/usuarios/","Usuarios","usuarios"),

		),
	),
	array(
  		"permiso"=>"herramientas",
  		"modulo"=>'Herramientas',
  		"submodulo"=>array(
  			array(ROOT_FOLDER."herramientas/xml/","Reporte Xml","reportexml"),
		),
	),
	array(
  		"permiso"=>"operaciones",
  		"modulo"=>'Operaciones',
  		"submodulo"=>array(
  			array(ROOT_FOLDER."operaciones/incidencias/","Incidencias","incidencias"),
			array(ROOT_FOLDER."operaciones/timbres/","Timbres Detecno","timbres"),
			
		),
	),
);
switch($_SERVER['SCRIPT_NAME']){
	case ROOT_FOLDER.'index.php':
		$arriba=array(
		  	// debe tenber esta forma: array("href","nombre"),
		);
	break;
	default:
		$arriba=array(
		  //array("href","nombre");
		//	array("#","Configurar botones"),
		);
	break;
}

?>