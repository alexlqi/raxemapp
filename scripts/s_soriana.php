<?php session_start();
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnSoriana;
@include("../includes/class.modelo.php");

function mkfolder($path){
	if(!is_dir($path)){
		return mkdir($path, 0777,true);
	}
}
function getRandomColor($t='bg'){
	switch($t){
		case 'bg':
			return "rgba(".round((rand(50000,150000)/1000),0).','.round((rand(100000,255000)/1000),0).','.round((rand(100000,255000)/1000),0).",0.5)";
		break;
		case 's':
		default:
			return "rgba(".round((rand(0,255000)/1000),0).','.round((rand(0,255000)/1000),0).','.round((rand(0,255000)/1000),0).",0.8)";
		break;
	}
}
function buildColors($rows=1,$t='bg'){
	if($rows<=1){
		return getRandomColor($t);
	}else{
		$colors=array();
		for($c=0;$c<$rows;$c++){
			$colors[]=getRandomColor($t);
		}
		return $colors;
	}
}

$r["err"]=true;

$week_number = (date("w")>0) ? date("W") : date("W")-1;
$ctrl=@$_POST["ctrl"];
switch($ctrl){
	case 'addDx':
		$sql="insert into diagnosticos (folio,claveCie) values ('{$_POST["folio"]}','{$_POST["id"]}');";
		$r=$modelo->insertSql($sql);
		if(!$r["err"]){
			$r["dxs"][0]["id"]=$r["id"];
			$r["dxs"][0]["label"]=$_POST["label"];
		}
	break;
	case 'delDx':
		$sql="delete from diagnosticos where idDiagnostico='{$_POST["id"]}';";
		$r=$modelo->updateSql($sql);
	break;
	case 'chkCie':
		$sql="select diagnostico as dx from cie where clave='{$_POST["clave"]}';";
		$r=$modelo->query2arr($sql);
		$r["dx"]=$r["data"][0]["dx"];
	break;
	case 'consulta':
		$perm=$permisos->authModel("sorianaConsultas",'ver');
		if($perm!==true){ $tablaStr=$perm; break; }
		$sql="insert into consultas 
			(idConsulta,folio,userid,idPaciente,motivo,entrada,nota,ta,fc,fr,temp,glu,etglab,antale,subjetivo,objetivo,trarec,obs,referencia)
		values
			(
				'{$_POST["idConsulta"]}',
				'{$_POST["folio"]}',
				'{$_POST["userid"]}',
				'{$_POST["idPaciente"]}',
				'{$_POST["motivo"]}',
				'{$_POST["fecha"]}',
				'{$_POST["nota"]}',
				'{$_POST["ta"]}',
				'{$_POST["fc"]}',
				'{$_POST["fr"]}',
				'{$_POST["temp"]}',
				'{$_POST["glu"]}',
				'{$_POST["etglab"]}',
				'{$_POST["antale"]}',
				'{$_POST["subjetivo"]}',
				'{$_POST["objetivo"]}',
				'{$_POST["trarec"]}',
				'{$_POST["obs"]}',
				'{$_POST["referencia"]}'
			)
		on duplicate key update idPaciente='{$_POST["idPaciente"]}', motivo='{$_POST["motivo"]}',entrada='{$_POST["fecha"]}';";
		$r=$modelo->insertSql($sql);
	break;
	case 'valoracion':
		$sql="insert into valoraciones 
			(idValoracion,folio,userid,idPaciente,nombre,veredicto,nota,fecha,valoracion,diastole,sistole,equilibrio,fatiga)
		values
			('{$_POST["idValoracion"]}','{$_POST["folio"]}','{$_POST["userid"]}','{$_POST["idPaciente"]}','{$_POST["nombre"]}','{$_POST["veredicto"]}','{$_POST["nota"]}','{$_POST["fecha"]}','{$_POST["valoracion"]}','{$_POST["diastole"]}','{$_POST["sistole"]}','{$_POST["equilibrio"]}','{$_POST["fatiga"]}')
		on duplicate key update idPaciente='{$_POST["idPaciente"]}', nombre='{$_POST["nombre"]}', veredicto='{$_POST["veredicto"]}', motivo='{$_POST["motivo"]}';";
		$r=$modelo->insertSql($sql);
	break;
	case 'accidentes':
		$sql="insert into accidentes 
			(idAccidente,folio,userid,idPaciente,departamento,puesto,absorbe,motivo,inicioInc,finalInc,diasInc)
		values
			('{$_POST["idAccidente"]}','{$_POST["folio"]}','{$_POST["userid"]}','{$_POST["idPaciente"]}','{$_POST["departamento"]}','{$_POST["puesto"]}','{$_POST["absorbe"]}','{$_POST["motivo"]}','{$_POST["inicioInc"]}','{$_POST["finalInc"]}',ABS(DATEDIFF('{$_POST["inicioInc"]}','{$_POST["finalInc"]}')))
		;";
		$r=$modelo->insertSql($sql);
	break;
	case 'incapacidades':
		$sql="insert into incapacidades 
			(idIncapacidad,folio,userid,idPaciente,departamento,puesto,absorbe,motivo,inicioInc,finalInc,diasInc)
		values
			('{$_POST["idIncapacidad"]}','{$_POST["folio"]}','{$_POST["userid"]}','{$_POST["idPaciente"]}','{$_POST["departamento"]}','{$_POST["puesto"]}','{$_POST["absorbe"]}','{$_POST["motivo"]}','{$_POST["inicioInc"]}','{$_POST["finalInc"]}',ABS(DATEDIFF('{$_POST["inicioInc"]}','{$_POST["finalInc"]}')))
		;";
		$r=$modelo->insertSql($sql);
	break;
	case 'folio':
		$r["folio"]=$_POST["v"].str_pad($_SESSION["idpanda"],5,"0",STR_PAD_LEFT).date("ymdHis");
	break;
	case 'cargar':
		$idCliente=@$_SESSION["administracion"]["idCliente"];
		$sql="select * from clientes where idCliente = '$idCliente';";
		$r=$modelo->query2arr($sql);
	break;
	case 'enfChart':
		$sql="select
			(select diagnostico from cie where clave=d.claveCie) as padecimiento,
			count(*) as Recuento
		from diagnosticos d
		group by d.claveCie
		;";
		$labels=array();
		$datasets=array();
		$data=$modelo->query2arr($sql);
		$rows=count(@$data["data"]);
		if($rows>0){
			foreach($data["data"] as $p=>$row){
				$padecimiento=explode(" ",$row["padecimiento"]);
				if(count($padecimiento)>1){
					$labels[$p]=$padecimiento;	
				}else{
					$labels[$p]=$row["padecimiento"];
				}
				$datasets[0]["label"]="Padecimientos";
				$datasets[0]["backgroundColor"]=buildColors($rows,"bg");
				$datasets[0]["borderColor"]="rgba(120,120,120,0.8)";
				$datasets[0]["data"][$p]=$row["Recuento"];
			}
		}
		$r["labels"]=$labels;
		$r["datasets"]=$datasets;
	break;
	default:
	break;
}
echo json_encode($r);
?>