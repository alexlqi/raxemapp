<?php session_start();
date_default_timezone_set("America/Monterrey");
//header('Content-type: application/json');
@include("../includes/config.php");
$dsnModelo=$dsnPmRH;
@include("../includes/class.modelo.php");

$sql=$ands=$opt="";
$id=$andsArr=array();
switch(@$_POST["ctrl"]){
	case 'asignaUsuariosU':
		
	break;
	case 'contratos':
		if(@$_POST["estatus"]!=""){$andsArr[]="and estatus='{$_POST["estatus"]}'";}
		if(@$_POST["usuarios"]=="s"){$andsArr[]="and p.idPanda>0";}
		if(!empty($andsArr)){$ands=trim(implode(" ",$andsArr));}
		$sql="Select c.idContrato,concat(p.nombre,' ',p.paterno,' ',p.materno) from contratos c inner join personas p on p.idPersona=c.idPersona where c.estatus=1 where 1=1 $ands";
		$id=array("idContrato","nombrec");
	break;
	case 'personas':
		switch(@$_POST["usuarios"]){# usuarios
			case 's':
				$andsArr[]="and p.idPanda>0";
			break;
			case 'n':
				$andsArr[]="and (p.idPanda=0 or p.idPanda is null)";
			break;
		}
		if(!empty($andsArr)){$ands=implode(",",$andsArr);}
		$sql="Select p.idPersona,concat(p.nombre,' ',p.paterno,' ',p.materno) from personas p where p.estatus=1 where 1=1 $ands";
		$id=array("idPersona","nombrec");
	break;
}

$optArr=$modelo->query2opt($sql,$id);
$opt=$optArr["data"];
echo $opt;
?>