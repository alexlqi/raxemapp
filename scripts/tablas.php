<?php session_start();
ini_set("max_execution_time",0);
ini_set('memory_limit', '256M');
function decodeJson(&$v,$i,$p=array()){
	if($i=="json"){
		$v=base64_decode($v);
	}
}

function cambiarColNames($arr,$colNames){
	if(!is_array(reset($arr))){$arr=array($arr);}
	$newArr=array();
	foreach($arr as $rowId=>$row){
		foreach($row as $k=>$v){
			$clave=(isset($colNames[$k])) ? $colNames[$k] : $k ;
			$newArr[$rowId][$clave]=$v;
		}
	}
	#return $arr;
	return $newArr;
}

function numeroDias($fecha1, $fecha2) {
	//determina el numero de dias que existe entre dos fechas
	//del (01-01-2005)
	$fecha1=strtotime($fecha1);
	$fecha2=strtotime($fecha2);
	return abs(ceil(($fecha2-$fecha1)/(60*60*24)));
}

function generaTabla($tablaCfg,$t='',$tableArr=array()){
	$modelo=@$tablaCfg["modelo"];
	$tabla=@$tablaCfg["tabla"];
	$permisos=@$tablaCfg["permisos"];
	$tablaStr="";
	#controles HTML
	$checked=array(
		"",
		" checked='checked' ",
	);
	
	#botones
	include("botones.php");
	
	# los filtros desde POST
	$filtros=@$_POST["data"]["filtros"];
	
	$estado=array(
		"Precaptura",
		"Pendiente",
		"Completado"
	);
	
	#etiquetas para examenes	
	$tagNames=array(
		'e'=>'ESCOLIOSIS',
		'r'=>'ROTACION',
		'bp'=>'BASCULAMIENTO PELVICO',
		'els'=>'EJE LUMBOSACRO',
		'be'=>'BALANCE ESPINAL',
		'eg3'=>'EJE GRAVEDAD L3',
		'cv'=>'CANTIDAD VERTEBRAS',
		'uls'=>'UNION LUMBOSACRA',
		'ce'=>'CIERRE ESPINOSAS',
		'a'=>'ARTROSIS',
		'eiv'=>'ESPACIOS IV',
		'cuv'=>'CUERPOS VERTEBRALES',
		'l'=>'LIGAMENTOS',
		'tb'=>'TEJIDOS BLANDOS',
		'h'=>'HALLAZGOS',
		'conclusion'=>'CONCLUSION',
		'comentario'=>'COMENTARIO',
	);
	
	#ordenamiento
	$orderCol=array(
		"lsAjax"=>array(
			"FOLIO SOLICITUD",
			"PROYECTO",
			"VACANTE",
			"FECHA",
			"NOMBRE COMPLETO",
			"TELEFONO",
			"CEDULA",
			"ESTADO",
			"CIUDAD",
			"ANTECEDENTES PENALES",
			"ANTECEDENTES LABORALES",
			"ACCIONES"
		),
		"lAsistAjax"=>array(
			"idContrato",
			"expediente",
			"zona",
			"cliente",
			"paterno",
			"materno",
			"nombre",
			"asistió?",
		),
		"lPersonasAjax"=>array(
			"id",
			"ACCIONES",
			"estado",
			"nss",
			"rfc",
			"curp",
			"paterno",
			"materno",
			"nombre",
		),
		"lContratosAjax"=>array(
			"id",
			"FOLIO",
			"Persona",
			"Zona",
			"Cliente",
			"Puesto",
			"Fecha de Ingreso",
			"¿Fijo?",
			"estatus",
			"ACCIONES",
		),
	);
	if(empty($tableArr)){
		$tableArr=array(
			"id"=>"",
			"class"=>array(),
			"data"=>array(),
		);
	}
	$viewAuth=false;
	switch($t){
		case 't': //generar tabla con tableArr
			$tablaStr=$tabla->writeTable($tableArr);
		break;
		case 'tAjax': //generar tabla con tableArr
			$tablaStr=$tabla->writeTableAjax($tableArr);
		break;
		case 'ls': //listar solicitudes
			#aquí se pondrán todas las condicionantes para los filtros
			//$whereVacante=" AND idVacante in ({$_POST["idVacante"]}) ";
			//$wheref1=" AND f1 in ({$_POST["f1"]}) ";
			$sql="SELECT 
				idSolicitud as 'FOLIO SOLICITUD',
				(select nombre from vacantes where idVacante=s.idVacante) as VACANTE,
				(select nombre from proyectos where idProyecto=(select idProyecto from vacantes v where idVacante=s.idVacante)) as PROYECTO,
				fecha AS FECHA,
				f1 as CEDULA,
				f2 as ESTADO,
				f3 as 'ANTECEDENTES PENALES'
			from solicitudes s
			WHERE 1=1;";
			$data=$modelo->query2arr($sql);
			$tableArr["id"]="vacantes";
			$tableArr["data"]=$data["data"];
			$tablaStr=$tabla->writeTable($tableArr);
		break;
		case 'lsAjax': //listar solicitudes ajax
			#aquí se pondrán todas las condicionantes para los filtros
			//$whereVacante=" AND idVacante in ({$_POST["idVacante"]}) ";
			if(!isset($tableArr["data"]["idVacante"])){
				$whereV="";
			}else if(@$tableArr["data"]["idVacante"]=="T"){
				$whereV="";
			}else{
				$whereV=" AND s.idVacante = '{$tableArr["data"]["idVacante"]}' ";
			}
			
			if(!isset($tableArr["data"]["f1"])){
				$wheref1="";
			}else if(@$tableArr["data"]["f1"]=="T"){
				$wheref1="";
			}else if(@$tableArr["data"]["f1"]=="numerico"){
				$wheref1=" AND s.f1 not in ('no','si') and f1 !='' ";
				#$wheref1="";
			}else{
				$wheref1=" AND s.f1 = '{$tableArr["data"]["f1"]}' ";
			}
			
			if(!isset($tableArr["data"]["f2"])){
				$wheref2="";
			}else if(@$tableArr["data"]["f2"]=="T"){
				$wheref2="";
			}else{
				$wheref2=" AND s.f2 = '{$tableArr["data"]["f2"]}' ";
			}
			
			$sql="SELECT 
				idSolicitud as 'FOLIO SOLICITUD',
				(select nombre from proyectos where idProyecto=(select idProyecto from vacantes v where idVacante=s.idVacante)) as PROYECTO,
				(select nombre from vacantes v where idVacante=s.idVacante) as VACANTE,
				fecha AS FECHA,
				json,
				f1 as CEDULA,
				f2 as ESTADO,
				f3 as CIUDAD,
				TRIM(trailing '.'
					from
						concat(
							if(adjuntoCV <> '',concat('download:','solicitudes|CV|',s.idSolicitud,'|Curriculum Vitae.'),''),
							if(adjuntoP <> '',concat('download2:','solicitudes|P|',s.idSolicitud,'|Papelería.'),''),
							concat('edit:/public/vacantes/',v.clave,'/',v.idVacante,'-Medico_General|',s.idSolicitud,'.')
						)
				) as ACCIONES
			from solicitudes s
			inner join vacantes v on v.idVacante=s.idVacante
			WHERE 1=1
			$wheref1
			$wheref2
			$whereV
			;";
			$data=$modelo->query2arr($sql);
			//array_walk_recursive($data["data"],"decodeJson");
			$tableArr["id"]="solicitudes";
			
			# sacamos los datos del ajax
			foreach($data["data"] as $i=>$d){
				if($d["json"]!=""){
					$json=json_decode(base64_decode($d["json"]),true);
					$data["data"][$i]["NOMBRE COMPLETO"]=$json["nombreC"];
					$data["data"][$i]["TELEFONO"]=$json["telefono"];
				}
				unset($data["data"][$i]["json"]);
			}
			
			# re ordenamos segun el ordenCol
			$dataOrder=array();
			foreach($data["data"] as $i=>$d){
				foreach($orderCol[$t] as $dd){
					if(isset($d[$dd])){
						$dataOrder[$i][$dd]=$d[$dd];
					}
				}
			}
			
			$tableArr["data"]=$dataOrder;
			$tableArr["dataSet"]=array(
				"tabla"=>"solicitudes",
				"campo"=>"adjunto",
			);
			$tablaStr=$tabla->writeTableAjax($tableArr);
		break;
		case 'lPre':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="select
				r.idResultado as ID,
				r.cliente as Cliente,
				r.fecha as Fecha,
				r.empresa as Empresa,
				r.tipoexamen as 'Tipo de examen',
				r.nombre as 'Nombre Completo',
				r.edad as Edad,
				r.folio as Folio,
				r.proyeccion as Proyeccion
			from resultados r
			where 1=1
			$whereFecha
			;";
			$data=$modelo->query2arr($sql);
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lMedicos':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			
			$sql="select
				idMedico as ID,
				idUsuario as 'User ID',
				nombre as 'Nombre Completo',
				cedula as 'Cédula Profesional',
				firma as 'Firma'
			from medicos m
			;";
			$data=$modelo->query2arr($sql);
			if(!$data["err"]){
				foreach($data["data"] as $i=>$d){
					$signaturePath="/scripts/pdftemplates/images/";
					$file=$signaturePath.$d["Firma"];
					$data["data"][$i]["Firma"]="<img src=\"$file\" height=\"30\" />";
				}
				$tableArr["data"]=$data["data"];
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lResultados':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereUser=($_SESSION["tipoUser"]>2) ? " and cliente in (select empresa from usuarios_empresa where idPanda='{$_SESSION["idpanda"]}') " : "" ;
			if($_SESSION["idpanda"]==57){$whereUser="";}
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="select 
				r.idResultado as ID,
				r.estado as 'Estado',
				r.fecha as Fecha,
				r.nombre,
				r.folio,
				r.empresa,
				r.cliente,
				r.proyeccion,
				if(m.nombre is null, 'Médico Prueba' , m.nombre) as 'Médico',
				r.e,
				r.r,
				r.bp,
				r.els,
				r.be,
				r.eg3,
				r.uls,
				r.ce,
				r.a,
				r.cuv,
				r.l,
				r.tb,
				r.h,
				concat(r.cv,' nivel: ',r.cvNivel) as cv,
				concat(r.eiv,' nivel: ',r.eivNivel) as eiv,
				r.conclusion,
				r.comentario
				from resultados r
				left join medicos m on m.idMedico=r.idMedico
				where 1=1
				$whereUser
				$whereFecha
				;";
			$data=$modelo->query2arr($sql);
			foreach($data["data"] as $k=>$d){
				$data["data"][$k]["Estado"]=$estado[$d["Estado"]];
			}
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lColumna':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="select 
				r.idResultado as ID,
				r.estado as 'Estado',
				r.fecha as Fecha,
				r.nombre,
				r.folio,
				r.empresa,
				r.cliente,
				r.proyeccion,
				if(m.nombre is null, 'Médico Prueba' , m.nombre) as 'Médico',
				r.e,
				r.r,
				r.bp,
				r.els,
				r.be,
				r.eg3,
				r.uls,
				r.ce,
				r.a,
				r.cuv,
				r.l,
				r.tb,
				r.h,
				concat(r.cv,' nivel: ',r.cvNivel) as cv,
				concat(r.eiv,' nivel: ',r.eivNivel) as eiv,
				r.conclusion,
				r.comentario
				from resultados r
				left join medicos m on m.idMedico=r.idMedico
				where r.proyeccion='AP Y LAT COLUMNA LUMBAR'
				$whereFecha
				;";
			$data=$modelo->query2arr($sql);
			foreach($data["data"] as $k=>$d){
				$data["data"][$k]["Estado"]=$estado[$d["Estado"]];
			}
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lColumnaTorax':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="select 
				r.idResultado as ID,
				r.estado as 'Estado',
				r.fecha as Fecha,
				r.nombre,
				r.folio,
				r.empresa,
				r.cliente,
				r.proyeccion,
				if(m.nombre is null, 'Médico Prueba' , m.nombre) as 'Médico',
				r.e,
				r.r,
				r.bp,
				r.els,
				r.be,
				r.eg3,
				r.uls,
				r.ce,
				r.a,
				r.cuv,
				r.l,
				r.tb,
				r.h,
				concat(r.cv,' nivel: ',r.cvNivel) as cv,
				concat(r.eiv,' nivel: ',r.eivNivel) as eiv,
				r.conclusion,
				r.comentario
				from resultados r
				left join medicos m on m.idMedico=r.idMedico
				where r.proyeccion='AP Y LAT COLUMNA LUMBAR/TORAX'
				$whereFecha
				;";
			$data=$modelo->query2arr($sql);
			foreach($data["data"] as $k=>$d){
				$data["data"][$k]["Estado"]=$estado[$d["Estado"]];
			}
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lTorax':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="select 
				r.idResultado as ID,
				r.estado as 'Estado',
				r.fecha as Fecha,
				r.nombre,
				r.folio,
				r.empresa,
				r.cliente,
				r.proyeccion,
				if(m.nombre is null, 'Médico Prueba' , m.nombre) as 'Médico',
				r.h,
				r.conclusion,
				r.comentario
				from resultados r
				left join medicos m on m.idMedico=r.idMedico
				where r.proyeccion='PA DE TORAX'
				$whereFecha
				;";
			$data=$modelo->query2arr($sql);
			foreach($data["data"] as $k=>$d){
				$data["data"][$k]["Estado"]=$estado[$d["Estado"]];
			}
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		case 'lOtrosExam':
			#$viewAuth=$permisos->authModel("tarifas",'ver');
			$whereFecha=(@$filtros["desde"]!="" and @$filtros["hasta"]!="") ? " and r.fecha between '{$filtros["desde"]} 00:00:00' and '{$filtros["hasta"]} 23:59:59' ": ' ';
			$viewAuth=true;
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="Select 
				r.idResultado as ID,
				r.estado as 'Estado',
				r.fecha as Fecha,
				r.nombre,
				r.folio,
				r.empresa,
				r.cliente,
				r.proyeccion,
				if(m.nombre is null, 'Médico Prueba' , m.nombre) as 'Médico',
				r.h,
				r.conclusion,
				r.comentario
				from resultados r
				left join medicos m on m.idMedico=r.idMedico
				where r.proyeccion not in ('AP Y LAT COLUMNA LUMBAR','PA DE TORAX','AP Y LAT COLUMNA LUMBAR/TORAX')
				$whereFecha
				;";
			$data=$modelo->query2arr($sql);
			foreach($data["data"] as $k=>$d){
				$data["data"][$k]["Estado"]=$estado[$d["Estado"]];
			}
			if(!$data["err"]){
				$tableArr["data"]=cambiarColNames($data["data"],$tagNames);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		
		#######################################################
		##### no mover estos son para que funcione pandaX #####
		#######################################################
		case 'lu': //listar usuario
			$data=$modelo->query2arr("SELECT panda,nombre,tipoUser from pandas where idSuscripcion={$_SESSION["idSuscripcion"]};");
			//$data=$modelo->query2arr("DESC pandas;");
			$tableArr["id"]="usuarios";
			$tableArr["data"]=$data["data"];
			//$tablaStr=$tabla->writeTable($tableArr);
		break;
		case 'luAjax': //listar usuario
			$viewAuth=$permisos->authModel("usuarios",'ver');
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$data=$modelo->query2arr("SELECT idpanda as 'ID Usuario',panda as Usuario,nombre as Nombre,tipoUser from pandas where idSuscripcion={$_SESSION["idSuscripcion"]} and tipoUser not in (1);");
			//$data=$modelo->query2arr("DESC pandas;");
			$tableArr["id"]="usuarios";
			if(!$data["err"]){
				$tableArr["data"]=$data["data"];
				//$tablaStr=$tabla->writeTableAjax($tableArr);
			}else{
				$tableArr["data"]=$data["data"];
				$tablaStr=$data["msg"];
			}
		break;
		case 'lPermisosAjax':
			$viewAuth=$permisos->authModel("personas",'ver');
			if($viewAuth!==true){ $tablaStr=$viewAuth; break; }
			$sql="SELECT
				idPermiso,
				tipo,
				permiso,
				nombre,
				descripcion,
				seccion,
				modulo,
				tab
			from permisos p
			where idSuscripcion={$_SESSION["idSuscripcion"]};";
			$data=$modelo->query2arr($sql);
			if(!$data["err"]){
				$tableArr["data"]=$data["data"];
				//$tablaStr=$tabla->writeTableAjax($tableArr);
			}else{
				$tablaStr=$data["msg"];
			}
		break;
		default:
		break;
	}
	if(is_array(@$tableArr["data"])){
		if(@!empty($tableArr["data"])){
			switch(@$tablaCfg["tipo"]){
				case 'dataTableAjax':
					$tablaStr=$tabla->writeDataTableAjax($tableArr);
				break;
				case 'dataTable':
					$tablaStr=$tabla->writeDataTable($tableArr);
				break;
				case 'tableAjax':
					$tablaStr=$tabla->writeTableAjax($tableArr);
				break;
				case 'table':
					$tablaStr=$tabla->writeTable($tableArr);
				break;
				case 'arrDatatable':
					$tablaStr=$tableArr;
				break;
				default:
					$tablaStr=$tabla->writeDataTableAjax($tableArr);
				break;
			}
		}
	}
	if(isset($btnArr[$t]) and $viewAuth===true){$tablaStr=$tabla->btnBuild($btnArr[$t]).$tablaStr;}
	return $tablaStr;
}
?>