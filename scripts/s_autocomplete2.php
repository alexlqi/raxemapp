<?php session_start();
date_default_timezone_set("America/Monterrey");
header('content: application/json');
@include_once("funciones.php");
@include("../includes/class.permisos.php");

$b=@$_POST["term"];
$r[0]["id"]=NULL;
$r[0]["value"]="No hay coincidencias.";
$r[0]["label"]="No hay coincidencias.";
switch(@$_POST["ctrl"]){
	case 'autoCliente':
		$dsnModelo=$dsnPmAdmon;
		@include("../includes/class.modelo.php");
		$sql="select
			idCliente as id,
			concat('(',cc.codigo,' - ',cc.rfc,') ',razon) as value,
			concat('(',cc.codigo,' - ',cc.rfc,') ',razon) as label
		from clientes c
		inner join clientes_adminpaq cc on cc.idAdminpaq=c.idCliente
		where cc.rfc like '%{$b}%' or cc.razon like '%{$b}%' or cc.codigo like '%{$b}%';";
		$d=$modelo->query2arr($sql);
		$r=$d["data"];
	break;
	case 'pxConsulta':
		$dsnModelo=$dsnSoriana;
		@include("../includes/class.modelo.php");
		$sql="select
			idPaciente as id,
			concat('(',p.empleado,' - ',p.curp,') ',nombres,' ',paterno,' ',materno) as value,
			concat('(',p.codigo,' - ',p.rfc,') ',nombres,' ',paterno,' ',materno) as label
		from pacientes p
		where p.curp like '%{$b}%' or p.empleado like '%{$b}%' or p.nombres like '%{$b}%' or p.paterno like '%{$b}%' or p.materno like '%{$b}%'; or concat(p.paterno,' ',p.materno) like '%{$b}%'";
		$d=$modelo->query2arr($sql);
		if(!empty($d["data"])){
			$r=$d["data"];
		}
	break;
	case 'pxSoriana':
		$dsnModelo=$dsnSoriana;
		@include("../includes/class.modelo.php");
		$sql="select
			idPaciente as id,
			concat('(',p.empleado,' - ',p.curp,') ',nombres,' ',paterno,' ',materno) as value,
			concat('(',p.codigo,' - ',p.rfc,') ',nombres,' ',paterno,' ',materno) as label
		from pacientes p
		where p.curp like '%{$b}%' or p.empleado like '%{$b}%' or p.nombres like '%{$b}%' or p.paterno like '%{$b}%' or p.materno like '%{$b}%'; or concat(p.paterno,' ',p.materno) like '%{$b}%'";
		$d=$modelo->query2arr($sql);
		if(!empty($d["data"])){
			$r=$d["data"];
		}
	break;
	case 'dx':
		$dsnModelo=$dsnSoriana;
		@include("../includes/class.modelo.php");
		$sql="select
			clave as id,
			concat(clave,' - ',diagnostico) as value,
			concat(clave,' - ',diagnostico) as label
		from cie
		where clave like '%{$b}%' or diagnostico like '%{$b}%';";
		$d=$modelo->query2arr($sql);
		if(!empty($d["data"])){
			$r=$d["data"];
		}else if($d["err"]==true){
			$r[0]["id"]=NULL;
			$r[0]["value"]=$d["msg"];
			$r[0]["label"]=$d["msg"];
		}
	break;
}
echo json_encode($r);
?>