<?php session_start();
header('Content-type: application/json');
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
@include("../includes/class.permisos.php");
function mkfolder($path){
	if(!is_dir($path)){
		return mkdir($path, 0777,true);
	}
}
/*
$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
//*/
$dsnModelo=$dsnPmRH;
@include("../includes/class.modelo.php");

$r["err"]=true;

switch(@$_POST["ctrl"]){
	case 'cambiarNombresPDFs':
		if(!@empty($_FILES["f"])){
			include '../includes/pdfparser/vendor/autoload.php';
			$parser = new \Smalot\PdfParser\Parser();
			$total=0;
			$file_ary = reArrayFiles($_FILES["f"]);
			
			$zip=new ZipArchive;
			foreach($file_ary as $id=>$file){
				# generamos los folders
				$outPath[$id]=sys_get_temp_dir()."/".time()."/";
				$pathRep[$id]=$outPath[$id]."corregidos/";
				$nombres[$id]=@$file["name"];
				# unzip
				$zip->open($file["tmp_name"]);
				$zip->extractTo($outPath[$id]);
				$zip->close();
				
				# aqui se usan solo PDFS
				$pdfs=rglob($outPath[$id]."*.[Pp][Dd][Ff]");
				foreach($pdfs as $filename){
					$pdf = $parser->parseFile($filename);
					$text = $pdf->getText();
					$lineas = explode("\n",$text);
					# se cambian los nombres de los archivos
					$nFolder=trim($pathRep[$id],"\\");
					@mkdir($nFolder);
					preg_match("/[0-9]+([a-z][a-z ]*)+/is", $text,$tipo1);
					if(!in_array($tipo1[1], array("Hora","Reg Pat","F"))){
						$text=$tipo1[1];
						$text=str_replace("ñ","n",$text);
						$text=str_replace("Ñ","N",$text);
						$text=utf8_decode(utf8_encode($text));
						$text=stripAccents($text);
						$nombre=str_replace(" ","_",strtoupper($text)).".pdf";
					}else{
						$text=preg_grep("/Periodo/", $lineas);
						$text=$text[key($text)];
						$text=str_replace("ñ","n",$text);
						$text=str_replace("Ñ","N",$text);
						$text=utf8_decode(utf8_encode($text));
						
						$text=stripAccents($text);
						$nombre=str_replace(" ","_",strtoupper(substr($text,strpos($text," - ")+3,strpos($text,"Periodo")-(strpos($text," - ")+3)))).".pdf";
					}
					$replace="{$nFolder}/".$nombre;
					@rename($filename,$replace);
				}
			}
			
			# zipeamos todos los archivos en un solo archivo
			$correcto=tempnam(sys_get_temp_dir(),'PDF').".zip";
			$zip->open($correcto, ZipArchive::CREATE);
			foreach($pathRep as $id=>$folder){
				$path=explode(".",$nombres[$id]);
				foreach(rglob($folder."*.*") as $file){
					$newName=str_replace("\\","",$path[0]."/".basename($file));
					$zip->addFile($file,$newName);
				}
			}
			$zip->close();
			$r["archivo"]=base64_encode($correcto);
		}
	break;
	case 'partirNombres':
		@include("../includes/class.table.php");
		@include_once("tablas.php");
		$tabla=new tables;
		
		$lineas=explode("\n",trim($_POST["nn"]));
		if(empty($lineas)){$r["tabla"]="No hay nombres para separar";break;}
		
		$nombres=array();
		$nombresC=array();
		$ctrl=0;
		foreach($lineas as $linea){ //obtiene le nombre de el csv linea por linea
			$columnas=explode(",",$linea);
			if(@$columnas[0]=="") continue;
			//quitar espacios dobles y espacios al principio y al final
			
			$nombrec=trim(str_replace("  "," ",$columnas[0]));
			$nombres[$ctrl]["nombrec"]=$nombrec;
			$palabras=explode(" ",$nombrec);
			$contador=count($palabras);
			$nombres[$ctrl]["palabras"]=$contador;
			$nombres[$ctrl]["separado"]=$palabras;
			$ctrl++;
		}
		
		//separa las palabras del nombre y los acomoda por paterno materno y nombres
		
		foreach($nombres as $id=>$row){
			if($row["palabras"]>3){ //para nombres que tienen más de 3 palabras
				if(preg_match("/^(DE|DEL) .*/",$row["nombrec"],$respA) || preg_match("/.* (DE|DEL) .*/",$row["nombrec"],$respB)){
					//esto checa si tiene apellidos de, del, de la; y los procesa.
					
					//para cuando están al principio DE y DEL
					if(!empty($respA)){
						$key=array_search($respA[1],$row["separado"]);
						switch($respA[1]){
							case 'DE':
								$nextKey=$key+1;
								$nextNextKey=$key+2;
								if(in_array($row["separado"][$nextKey],array("LA","LOS","LAS"))){
									//si el que sigue es La LOS LAS
									$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey]." ".$row["separado"][$nextNextKey];
									unset($nombres[$id]["separado"][$nextKey]);
									unset($nombres[$id]["separado"][$nextNextKey]);
									$nombres[$id]["separado"][$key]=$conjunto;
								}else{
									//si el siguiente no es LA LOS LAS entonces es otro apellido
									$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey];
									unset($nombres[$id]["separado"][$nextKey]);
									$nombres[$id]["separado"][$key]=$conjunto;
								}
							break;
							case 'DEL':
								$nextKey=$key+1;
								$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey];
								unset($nombres[$id]["separado"][$nextKey]);
								$nombres[$id]["separado"][$key]=$conjunto;
							break;
						}
					}
					
					//para cuando están en otra posición
					if(!empty($respB)){
						$key=array_search($respB[1],$row["separado"]);
						switch($respB[1]){
							case 'DE':
								$prevKey=$key-1;
								$nextKey=$key+1;
								$nextNextKey=$key+2;
								if(in_array($row["separado"][$nextKey],array("LA","LOS","LAS"))){
									//si el que sigue es La LOS LAS
									$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey]." ".$row["separado"][$nextNextKey];
									unset($nombres[$id]["separado"][$nextKey]);
									unset($nombres[$id]["separado"][$nextNextKey]);
									$nombres[$id]["separado"][$key]=$conjunto;
								}elseif($row["separado"][$prevKey]=="MONTES"){
									//para el caso montes de oca
									$conjunto=$row["separado"][$prevKey]." ".$row["separado"][$key]." ".$row["separado"][$nextKey];
									unset($nombres[$id]["separado"][$key]);
									unset($nombres[$id]["separado"][$nextKey]);
									$nombres[$id]["separado"][$prevKey]=$conjunto;
								}else{
									//si el siguiente no es LA LOS LAS entonces es otro apellido
									$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey];
									unset($nombres[$id]["separado"][$nextKey]);
									$nombres[$id]["separado"][$key]=$conjunto;
								}
							break;
							case 'DEL':
								$nextKey=$key+1;
								$conjunto=$row["separado"][$key]." ".$row["separado"][$nextKey];
								unset($nombres[$id]["separado"][$nextKey]);
								$nombres[$id]["separado"][$key]=$conjunto;
							break;
						}
					}
				}else{
					//los que no estan con esos nombres DE DEL etc...
					
				} // termina el if preg_match
			}elseif($row["palabras"]==2){
				// para los que son de dos palabras
				
				$nombres[$id]["separado"][0]=$nombres[$id]["separado"][0];
				$nombres[$id]["separado"][2]=$nombres[$id]["separado"][1];
				$nombres[$id]["separado"][1]="";
				
			} // termina if para mayores a 3 palabras
		} // termina foreach para trabajar los apellidos
		
		foreach($nombres as $id=>$row){
			$nombresC[$id]["nombrec"]=$row["nombrec"];
			if(@$_POST["numprimero"]=="S"){
				if(reset($row["separado"])>0){
					$nombresC[$id]["numero"]=array_shift($row["separado"]);
				}else{
					$nombresC[$id]["numero"]="";
				}
			}
			if(@$_POST["numprimero"]=="S"){
				$nombresC[$id]["Nombre Completo"]=implode(" ",$row["separado"]);
			}else{
				$nombresC[$id]["paterno"]=array_shift($row["separado"]);
				$nombresC[$id]["materno"]=array_shift($row["separado"]);
				$nombresC[$id]["nombres"]=implode(" ",$row["separado"]);
			}
		}
		
		//var_dump($nombresC);
		$tArr["id"]="partidos";
		$tArr["data"]=$nombresC;
		$r["tabla"]=generaTabla(array("tabla"=>$tabla),'tAjax',$tArr);
		$r["err"]=false;
	break;
	case 'altaPersona':
		if(validaCurp(@$_POST["curp"]) || validaRfc(@$_POST["rfc"])){
			$sql="call sp_personas('alta','{$_POST["idPersona"]}','{$_POST["nss"]}','{$_POST["rfc"]}','{$_POST["curp"]}','{$_POST["paterno"]}','{$_POST["materno"]}','{$_POST["nombre"]}');";
			$r=$modelo->query2arr($sql);
		}else{
			$sql="call sp_personas('alta','{$_POST["idPersona"]}','{$_POST["nss"]}','{$_POST["rfc"]}','{$_POST["curp"]}','{$_POST["paterno"]}','{$_POST["materno"]}','{$_POST["nombre"]}');";
			$r=$modelo->query2arr($sql);
		}
	break;
	case 'eliminar':
		$tablaKey=array(
			"supervisores"=>array("supervisores","idSupervisor",),
			"tarifasForm"=>array("tarifas","idTarifa",),
		);
		$sql="delete from {$tablaKey[$_POST["tabla"]][0]} where {$tablaKey[$_POST["tabla"]][1]}={$_POST["id"]};";
		$r=$modelo->exec($sql);
	break;
	case 'lBuscaPersona':
		$sql="select * from personas where idPersona={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'lBuscaUsuarioAsignado':
		$sql="select idPersona,idPanda from personas where idPersona={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'lBuscaSupervisor':
		$sql="select idSupervisor,idPanda,idZona from supervisores where idSupervisor={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'addTarifas':
		$sql="insert into tarifas (idTarifa,idPanda, idCliente, idConcepto, fecha, semana, dia, monto,estatus)
		VALUES ('{$_POST["idTarifa"]}','{$_SESSION["idpanda"]}',{$_POST["idCliente"]},{$_POST["idConcepto"]},'{$_POST["fecha"]}','{$_POST["semana"]}','{$_POST["dia"]}',{$_POST["monto"]},'{$_POST["estatus"]}')
		on duplicate key update idCliente={$_POST["idCliente"]},idConcepto={$_POST["idConcepto"]},fecha='{$_POST["fecha"]}',semana='{$_POST["semana"]}',dia='{$_POST["dia"]}',monto={$_POST["monto"]},estatus='{$_POST["estatus"]}';";
		$r=$modelo->insertSql($sql);
	break;
	case 'usuarioPersona':
		$sql="insert into personas (idPersona, idPanda) VALUES ({$_POST["idPersona"]},{$_POST["idPanda"]}) on duplicate key update idPanda={$_POST["idPanda"]};";
		$r=$modelo->insertSql($sql);
	break;
	case 'supervisores':
		$sql="insert into supervisores (idSupervisor, idPanda, idZona) VALUES ('{$_POST["idSupervisor"]}','{$_POST["idPanda"]}','{$_POST["idZona"]}') on duplicate key update idZona={$_POST["idZona"]};";
		$r=$modelo->insertSql($sql);
	break;
	case 'quitarUsuarioPersona':
		$sql="update personas SET idPanda = NULL where idPersona={$_POST["id"]};";
		$r=$modelo->updateSql($sql);
	break;
	case 'altaContrato':
		$idPersona=@$_POST["idPersona"];
		$sql="call sp_contratos('alta','{$_POST["idContrato"]}','{$_POST["folio"]}','{$idPersona}','{$_POST["idZona"]}','{$_POST["idCliente"]}','{$_POST["idPuesto"]}','{$_POST["fechaIngreso"]}','{$_POST["fechaAntiguedad"]}','{$_POST["montoFijo"]}');";
		$r=$modelo->query2arr($sql);
		if(!$r["err"]){
			$r["msg"]="Registro Modificado";
		}
	break;
	case 'lBuscaTarifa':
		$sql="select * from tarifas where idTarifa={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'lBuscaContrato':
		$sql="select * from contratos where idContrato={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	case 'lBuscaSolicitud':
		$sql="SELECT
			concat('/public/vacantes/',v.clave,'/',s.idVacante,'-',replace(p.nombre,' ','_'),'/',s.idSolicitud) as url,
			s.idSolicitud,
			s.idVacante,
			v.clave,
			p.nombre
		FROM solicitudes s
		inner join vacantes v on s.idVacante=v.idVacante
		inner join puestos p on p.idPuesto=v.idPuesto
		where s.idSolicitud={$_POST["id"]}
		;";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	default:
	break;
}
echo json_encode($r);
?>