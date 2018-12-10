<?php session_start();
header('Content-type: application/json');
date_default_timezone_set("America/Monterrey");
@include("../includes/config.php");
@include("funciones.php");
@include("respuestasHtml.php");
$dsnModelo=$dsnPmRHVacantes;
@include("../includes/class.modelo.php");
$r=$no_permite=array("err"=>true,"msg"=>"No tiene permisos de ejecución.");

if(@$_POST["ctrl"]!=""){
	$ctrl=$_POST["ctrl"];
}else if(@$_POST["proyecto"]!=""){
	$ctrl=$_POST["proyecto"];
}
switch($ctrl){
	case 's':
		if($_POST["fase"]==1){
			if(!validaCurp(@$_POST["curp"])){
				$r["err"]=true;
				$r["msg"]="Por favor verifique que su CURP esté escrito correctamente.";
				break;
			}
			if(!validaTelefono(@$_POST["telefono"])){
				$r["err"]=true;
				$r["msg"]="Por favor verifique que el teléfono sea de 10 dígitos";
				break;
			}
			$insertData[0]["idVacante"]=@$_POST["idVacante"];
			$insertData[0]["fecha"]=date("Y-m-d H:i:s");
			$insertData[0]["json"]=base64_encode(json_encode($_POST,JSON_UNESCAPED_UNICODE));
			$insertData[0]["curp"]=strtoupper(@$_POST["curp"]);
			$insertData[0]["mimeCV"]=$archivo=@$_FILES["adjuntoCV"]["type"][0];
			$insertData[0]["nombreCV"]=$archivo=@$_FILES["adjuntoCV"]["name"][0];
			$insertData[0]["adjuntoCV"]=base64_encode(@file_get_contents(@$_FILES["adjuntoCV"]["tmp_name"][0]));
			$insertData[0]["f1"]=@$_POST["cedulaProfesional"];
			$insertData[0]["f2"]=@$_POST["estado"];
			$insertData[0]["f3"]=@$_POST["ciudad"];
			$insertData[0]["f4"]=@$_POST["antecedentesLaborales"];
			$insertData[0]["f5"]=@$_POST["antecedentesPenales"];
			$r=$modelo->array2insert("solicitudes",$insertData);
			if(!$r["err"]){
				$folio=$modelo->query2arr("select idSolicitud from solicitudes where idVacante='{$_POST["idVacante"]}' and curp='{$_POST["curp"]}';");
				if(!$folio["err"]){
					$data["url"]=$_SERVER["HTTP_REFERER"]."{$folio["data"][0]["idSolicitud"]}/";
					$data["fase"]=$_POST["fase"];
					$r["html"]=respuestaHtml("s",$data);
				}
			}
		}else if($_POST["fase"]==2){
			$insertData[0]["idSolicitud"]=@$_POST["idSolicitud"];
			$insertData[0]["fecha"]=date("Y-m-d H:i:s");
			$insertData[0]["json"]=base64_encode(json_encode($_POST,JSON_UNESCAPED_UNICODE));
			$insertData[0]["mimeP"]=$archivo=@$_FILES["adjuntoP"]["type"][0];
			$insertData[0]["nombreP"]=$archivo=@$_FILES["adjuntoP"]["type"][0];
			$insertData[0]["adjuntoP"]=base64_encode(@file_get_contents(@$_FILES["adjuntoP"]["tmp_name"][0]));
			$insertData[0]["f1"]=@$_POST["cedulaProfesional"];
			$insertData[0]["f2"]=@$_POST["estado"];
			$insertData[0]["f3"]=@$_POST["ciudad"];
			$insertData[0]["f4"]=@$_POST["antecedentesLaborales"];
			$insertData[0]["f5"]=@$_POST["antecedentesPenales"];
			$r=$modelo->array2insert("solicitudes",$insertData,"Solicitud actualizada correctamente"," on duplicate key update fecha=%fecha%, json=%json%, mimeP=%mimeP%, adjuntoP=%adjuntoP%, f1=%f1%, f2=%f2%, f3=%f3%, f4=%f4%, f5=%f5% ",2);
			if(!$r["err"]){
				$data["fase"]=$_POST["fase"];
				$r["html"]=respuestaHtml("s",$data);
			}
		}
	break;
	case 's1':
		if($_POST["fase"]==1){
			if(!validaCurp(@$_POST["curp"])){
				$r["err"]=true;
				$r["msg"]="Por favor verifique que su CURP esté escrito correctamente.";
				break;
			}
			if(!validaTelefono(@$_POST["telefono"])){
				$r["err"]=true;
				$r["msg"]="Por favor verifique que el teléfono sea de 10 dígitos";
				break;
			}
			$_POST["dias"]=implode(", ",@$_POST["dias"]);
			$_POST["horarios"]=implode(", ",@$_POST["horarios"]);
			$insertData[0]["idVacante"]=@$_POST["idVacante"];
			$insertData[0]["fecha"]=date("Y-m-d H:i:s");
			$insertData[0]["json"]=base64_encode(json_encode($_POST,JSON_UNESCAPED_UNICODE));
			$insertData[0]["curp"]=strtoupper(@$_POST["curp"]);
			$insertData[0]["mimeCV"]=$archivo=@$_FILES["adjuntoCV"]["type"][0];
			$insertData[0]["nombreCV"]=$archivo=@$_FILES["adjuntoCV"]["name"][0];
			$insertData[0]["adjuntoCV"]=base64_encode(@file_get_contents(@$_FILES["adjuntoCV"]["tmp_name"][0]));
			$insertData[0]["f1"]=@$_POST["cedulaProfesional"];
			$insertData[0]["f2"]=@$_POST["estado"];
			$insertData[0]["f3"]=@$_POST["ciudad"];
			$insertData[0]["f4"]=@$_POST["antecedentesLaborales"];
			$insertData[0]["f5"]=@$_POST["antecedentesPenales"];
			
			//$r=$_POST; break;
			$r=$modelo->array2insert("solicitudes",$insertData);
			if(!$r["err"]){
				$folio=$modelo->query2arr("select idSolicitud from solicitudes where idVacante='{$_POST["idVacante"]}' and curp='{$_POST["curp"]}';");
				if(!$folio["err"]){
					$data["url"]=$_SERVER["HTTP_REFERER"]."{$folio["data"][0]["idSolicitud"]}/";
					$data["fase"]=$_POST["fase"];
					$r["html"]=respuestaHtml("s",$data);
				}
			}
		}else if($_POST["fase"]==2){
			$insertData[0]["idSolicitud"]=@$_POST["idSolicitud"];
			$insertData[0]["fecha"]=date("Y-m-d H:i:s");
			$insertData[0]["json"]=base64_encode(json_encode($_POST,JSON_UNESCAPED_UNICODE));
			$insertData[0]["mimeP"]=$archivo=@$_FILES["adjuntoP"]["type"][0];
			$insertData[0]["nombreP"]=$archivo=@$_FILES["adjuntoP"]["type"][0];
			$insertData[0]["adjuntoP"]=base64_encode(@file_get_contents(@$_FILES["adjuntoP"]["tmp_name"][0]));
			$insertData[0]["f1"]=@$_POST["cedulaProfesional"];
			$insertData[0]["f2"]=@$_POST["estado"];
			$insertData[0]["f3"]=@$_POST["ciudad"];
			$insertData[0]["f4"]=@$_POST["antecedentesLaborales"];
			$insertData[0]["f5"]=@$_POST["antecedentesPenales"];
			$r=$modelo->array2insert("solicitudes",$insertData,"Solicitud actualizada correctamente"," on duplicate key update fecha=%fecha%, json=%json%, mimeP=%mimeP%, adjuntoP=%adjuntoP%, f1=%f1%, f2=%f2%, f3=%f3%, f4=%f4%, f5=%f5% ",2);
			if(!$r["err"]){
				$data["fase"]=$_POST["fase"];
				$r["html"]=respuestaHtml("s",$data);
			}
		}
	break;
	case 'bSolicitud':
		$vacante=@$_POST["vacante"];
		$curp=@$_POST["curp"];
		$r=$modelo->query2arr("select idSolicitud from solicitudes where idVacante='$vacante' and curp='$curp';");
		if(!$r["err"]){
			$r["url"]=$_SERVER["HTTP_REFERER"]."{$r["data"][0]["idSolicitud"]}/";
		}
	break;
	default:
	break;
}

echo json_encode($r);
?>