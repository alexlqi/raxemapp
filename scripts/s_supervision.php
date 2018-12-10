<?php session_start();
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnPmRH;
@include("../includes/class.modelo.php");

function mkfolder($path){
	if(!is_dir($path)){
		return mkdir($path, 0777,true);
	}
}

$r["err"]=true;

$week_number = (date("w")>0) ? date("W") : date("W")-1 ;
$ctrl=@$_POST["ctrl"];
switch($ctrl){
	case 'asistencia':
		$a=$_POST["asistio"];
		$idp=$_SESSION["idpanda"];
		$idC=$_POST["idC"];
		$f=date("Y-m-d");
		$sql="call sp_asistencia('$a','$f',$idp,$idC);";
		$r=$modelo->query2arr($sql);
	break;
	case 'addEventual':
		$sql="insert into empleadosIncidencias (idContrato,idCategoria,semana) VALUES ({$_POST["idContrato"]},{$_POST["idCategoria"]},'{$_POST["semana"]}');";
		$r=$modelo->insertSql($sql);
	break;
	case 'incidenciaEventual':
		$fecha=date("Y-m-d h:i:s");
		$idCategoria=2;
		$respuestas=array();
		$fechaIncidencia=@$_POST["fechaIncidencia"];
		$semana=date("W",strtotime($fechaIncidencia));
		$dia=date("w",strtotime($fechaIncidencia));
		$sql="insert into incidencias_p (idIncidencia,idSupervisor,idConcepto,idContrato,idCliente,fecha,fechaIncidencia,semana,dia,cantidad,razon) VALUES ('{$_POST["idIncidencia"]}',{$_SESSION["idpanda"]},{$_POST["idConcepto"]},{$_POST["idContrato"]},'{$_POST["idCliente"]}','{$fecha}','$fechaIncidencia','{$semana}','$dia','{$_POST["cantidad"]}','{$_POST["razon"]}') on duplicate key update idConcepto='{$_POST["idConcepto"]}', idCliente='{$_POST["idCliente"]}', idContrato='{$_POST["idContrato"]}', cantidad='{$_POST["cantidad"]}', fecha='{$fecha}',semana='{$semana}',dia='{$dia}', razon='{$_POST["razon"]}';";
		$r=$modelo->insertSql($sql);;
	break;
	case 'tiempoExtra':
		$fecha=date("Y-m-d h:i:s");
		$idCategoria=3;
		$respuestas=array();
		$fechaIncidencia=@$_POST["fechaIncidencia"];
		$semana=date("W",strtotime($fechaIncidencia));
		$dia=date("w",strtotime($fechaIncidencia));
		$sql="insert into incidencias_p (idIncidencia,idSupervisor,idConcepto,idContrato,idCliente,fecha,fechaIncidencia,semana,dia,cantidad,razon) VALUES ('{$_POST["idIncidencia"]}',{$_SESSION["idpanda"]},{$_POST["idConcepto"]},{$_POST["idContrato"]},'{$_POST["idCliente"]}','{$fecha}','$fechaIncidencia','{$semana}','$dia','{$_POST["cantidad"]}','{$_POST["razon"]}') on duplicate key update idConcepto='{$_POST["idConcepto"]}', idCliente='{$_POST["idCliente"]}', idContrato='{$_POST["idContrato"]}', cantidad='{$_POST["cantidad"]}', fecha='{$fecha}',semana='{$semana}',dia='{$dia}', razon='{$_POST["razon"]}';";
		$r=$modelo->insertSql($sql);;
	break;
	case 'incidencia':
		$fecha=date("Y-m-d h:i:s");
		$idCategoria=$_POST["idCategoria"];
		$respuestas=array();
		$gendate = new DateTime();
		$anio=date("Y");
		foreach($_POST["i"][$idCategoria] as $sem=>$emp){
			$cols=end($emp);
			$colsArr=array_keys($cols);
			$cols=implode(",",$colsArr);
			foreach($emp as $idContrato=>$incidencias){
				foreach($incidencias as $dia=>$inc){
					if($inc["idConcepto"]=="E"){continue;}
					$gendate->setISODate($anio*1,$sem*1,$dia*1); //year , week num , day
					$fechaIncidencia=$gendate->format('Y-m-d');
					$sql="insert into incidencias_p (idSupervisor,idConcepto,idContrato,idCliente,fecha,fechaIncidencia,semana,dia,cantidad) VALUES ({$_SESSION["idpanda"]},{$inc["idConcepto"]},{$idContrato},'{$inc["idCliente"]}','{$fecha}','$fechaIncidencia','{$sem}','$dia','{$inc["cantidad"]}') on duplicate key update idConcepto='{$inc["idConcepto"]}', cantidad='{$inc["cantidad"]}', fecha='{$fecha}';";
					$respuestas[$idContrato][$sem][$dia]=$modelo->insertSql($sql);
				}
			}
		}
		$r=$respuestas;
		break;
	break;
	case 'buscaAsistenciaFijo':
		#sacamos la semana corriente
		$week_number = (date("w")>0) ? date("W") : date("W")-1 ;
		$year = date("Y");
		$periodo=array();
		for($day=0; $day<7; $day++){
			$dayP=$day+1;
			$periodo[$day]=strftime("%d-%b-%y<br>%a", strtotime($year."W".$week_number.$dayP));
		}
		$sql="select i.idConcepto,semana,idContrato,dia,idCliente,cantidad from incidencias_p i inner join cfgConceptos cfg on i.idConcepto=cfg.idConcepto where idSupervisor={$_SESSION["idpanda"]} and semana='{$week_number}' and cfg.idCategoria=1;";
		$data=$modelo->query2arr($sql);
		$inc=array();
		if(!empty($data["data"])){
			foreach($data["data"] as $k=>$d){
				$inc[$k]["i[1]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][cantidad]"]=$d["cantidad"];
				$inc[$k]["i[1]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][idConcepto]"]=$d["idConcepto"];
				$inc[$k]["i[1]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][idCliente]"]=$d["idCliente"];
			}
			$r["err"]=false;
			$r["data"]=$inc;
		}else{
			$r=$data;
		}
	break;
	case 'buscaAsistenciaEventual':
		#sacamos la semana corriente
		$week_number = (date("w")>0) ? date("W") : date("W")-1 ;
		$year = date("Y");
		$periodo=array();
		for($day=0; $day<7; $day++){
			$dayP=$day+1;
			$periodo[$day]=strftime("%d-%b-%y<br>%a", strtotime($year."W".$week_number.$dayP));
		}
		$sql="select i.idConcepto,semana,idContrato,dia,idCliente,cantidad from incidencias_p i inner join cfgConceptos cfg on i.idConcepto=cfg.idConcepto where idSupervisor={$_SESSION["idpanda"]} and semana='{$week_number}' and cfg.idCategoria=2;";
		$data=$modelo->query2arr($sql);
		$inc=array();
		foreach($data["data"] as $k=>$d){
			$inc[$k]["i[2]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][cantidad]"]=$d["cantidad"];
			$inc[$k]["i[2]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][idConcepto]"]=$d["idConcepto"];
			$inc[$k]["i[2]"."[".$d["semana"]."]"."[".$d["idContrato"]."]"."[".$d["dia"]."][idCliente]"]=$d["idCliente"];
		}
		$r["err"]=false;
		$r["data"]=$inc;
	break;
	case 'buscaTiempoExtra':
		$sql="select * from incidencias_p where idIncidencia={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'buscaIncidenciaEventual':
		$sql="select * from incidencias_p where idIncidencia={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'eliminar':
		$arrDeletes=array(
			"tiempoExtraForm"=>array("tabla"=>"incidencias_p","llave"=>"idIncidencia"),
		);
		$sql="delete from {$arrDeletes["tiempoExtraForm"]["tabla"]} where {$arrDeletes["tiempoExtraForm"]["llave"]}={$_POST["id"]};";
		$r=$modelo->insertSql($sql);
	break;
	default:
	break;
}
echo json_encode($r);
?>