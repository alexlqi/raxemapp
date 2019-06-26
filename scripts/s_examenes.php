<?php session_start();
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnExamenes;
@include("../includes/class.modelo.php");
$r["err"]=true;
$ctrl=@$_POST["ctrl"];
unset($_POST["ctrl"]);


//TAG NAMES PARA MOSTRAR Y ACUALIZAR POR KEYS
$tagNames=array(
	'medName'=>'Médico',
	'medCed'=>'Cédula Profesional',
	'medSign'=>'Firma',
	'resultado'=>'RESULTADO',
	'cliente'=>'CLIENTE',
	'fecha'=>'FECHA',
	'empresa'=>'EMPRESA',
	'tipoexamen'=>'TIPO DE EXAMEN',
	'proyeccion'=>'PROYECCIÓN',
	'nombre'=>'NOMBRE COMPLETO',
	'edad'=>'EDAD',
	'folio'=>'FOLIO DE ESTUDIO',
	'e'=>'ESCOLIOSIS',
	'r'=>'ROTACIÓN',
	'bp'=>'BASCULAMIENTO PÉLVICO',
	'els'=>'EJE LUMBOSACRO',
	'be'=>'BALANCE ESPINAL',
	'eg3'=>'EJE DE GRAVEDAD L3',
	'cv'=>'CANTIDAD DE VERTEBRAS',
	'uls'=>'UNION LUMBOSACRA',
	'ce'=>'CIERRE ESPINOSAS',
	'a'=>'ARTROSIS',
	'eiv'=>'ESPACIOS IV',
	'cuv'=>'CUERPOS VERTEBRALES',
	'l'=>'LIGAMENTOS',
	'tb'=>'TEJIDOS BLANDOS',
	'h'=>'HALLAZGOS',
	'conclusion'=>'CONCLUSIÓN',
	'comentario'=>'COMENTARIO',
	'estado'=>'ESTADO',
	'ht'=>'HALLAZGOS TORAX',
	'oht'=>'OTROS HALLAZGOS DE TORAX',
	'conclusiont'=>'CONCLUSION TORAX',
	'cvNivel'=>'NIVEL',
	'eivNivel'=>'NIVEL',
	'concradcol'=>'CONCLUSIÓN RADIOLÓGICA DE COLUMNA',
	'concradtor'=>'CONCLUSIÓN RADIOLÓGICA DE TORAX',
	'riescol'=>'RIESGO DE COLUMNA',
	'riestor'=>'RIESGO DE TORAX',
	'idMedico'=>'ID MÉDICO',
);

switch($ctrl){
	case 'result2pdf':
		$sql="select
			r.*,
			concat(r.cv,if(r.cvNivel is not null,concat(' Nivel: ',r.cvNivel),'')) as cv,
			concat(r.eiv,if(r.eivNivel is not null,concat(' Nivel: ',r.eivNivel),'')) as eiv,
			m.nombre as 'medName',
			m.cedula as 'medCed',
			m.firma as 'medSign'
		from resultados r
		inner join medicos m on r.idMedico=m.idMedico
		where idResultado='{$_POST["id"]}';";
		$q=$modelo->query2arr($sql);
		
		$templates=array(
			"AP Y LAT COLUMNA LUMBAR"=>"listaColumna",
			"AP Y LAT COLUMNA LUMBAR/TORAX"=>"listaColumnaTorax",
			"PA DE TORAX"=>"listaTorax",
		);
		$template=(isset($templates[$q["data"][0]["proyeccion"]])) ? "pdftemplates/{$templates[$q["data"][0]["proyeccion"]]}.php" : "pdftemplates/listaOtrosExam.php" ;
		
		include_once("../includes/mpdf/vendor/autoload.php");
		$pdfTmp = tempnam(sys_get_temp_dir(), "PDF");
		$mpdf = new mPDF('utf-8', 'Letter', 0, '', '11mm', '11mm', '11mm', '11mm', 0, 0);
		#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>');
		#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>','E');
		$mpdf->WriteHTML(parsePdfTemplate($template,$q["data"][0],$tagNames));
		$mpdf->Output($pdfTmp,'F');
		$nombre=str_replace(" ","_",$q["data"][0]["nombre"]);
		$r["download"]=trim(base64_encode("{$pdfTmp}@{$nombre}.pdf@inline"),"="); //@inline attachment
		$r["err"]=false;
	break;
	case 'multiResult2pdf':
		$tmpDir= __DIR__."/pdfTmp/";
		mkdir($tmpDir);
		$pdfZip = "resultados_".date("ymd_His").".zip";
		foreach($_POST["id"] as $id){
			$sql="select
				r.*,
				concat(r.cv,if(r.cvNivel is not null,concat(' Nivel: ',r.cvNivel),'')) as cv,
				concat(r.eiv,if(r.eivNivel is not null,concat(' Nivel: ',r.eivNivel),'')) as eiv,
				m.nombre as 'medName',
				m.cedula as 'medCed',
				m.firma as 'medSign'
			from resultados r
			inner join medicos m on r.idMedico=m.idMedico
			where idResultado='$id';";
			$q=$modelo->query2arr($sql);
			
			$templates=array(
				"AP Y LAT COLUMNA LUMBAR"=>"listaColumna",
				"AP Y LAT COLUMNA LUMBAR/TORAX"=>"listaColumnaTorax",
				"PA DE TORAX"=>"listaTorax",
			);
			$template=(isset($templates[$q["data"][0]["proyeccion"]])) ? "pdftemplates/{$templates[$q["data"][0]["proyeccion"]]}.php" : "pdftemplates/listaOtrosExam.php" ;
			
			include_once("../includes/mpdf/vendor/autoload.php");
			$nombre=$q["data"][0]["nombre"];
			$pdfTmp = __DIR__."/pdfTmp/{$nombre}.pdf";
			$mpdf = new mPDF('utf-8', 'Letter', 0, '', '11mm', '11mm', '11mm', '11mm', 0, 0);
			#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>');
			#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>','E');
			$mpdf->WriteHTML(parsePdfTemplate($template,$q["data"][0],$tagNames));
			$mpdf->Output($pdfTmp,'F');
		}
		$zip=new ZipArchive;
		$zip->open($pdfZip, ZipArchive::CREATE);
		foreach(rglob($folder."*.*") as $file){
			$zip->addFile($file,"/".basename($file));
		}
		if($zip->close()){
			$r["download"]=trim(base64_encode("{$pdfZip}@".basename($pdfZip)."@inline"),"="); //@inline attachment
			$r["err"]=false;
		};
	break;
	case 'addMed':
		$uuid=uniqid();
		if(!empty($_FILES["firma"])){
			# si hay un archivo entonces guarda la firma
			$sign=file_get_contents($_FILES["firma"]["tmp_name"][0]);
			$blob=base64_encode($sign);
			$tipo=$_FILES["firma"]["type"][0];
			$name=$_FILES["firma"]["name"][0];
			$n=explode(".",$name);
			$ext=array_pop($n);
			$name=$uuid.".".$ext;
			$modelo->insertSql("insert into uploads VALUES ('$uuid','$tipo','$name','$blob');");
			$signaturePath=__DIR__."/pdftemplates/images/";
			@mkdir($signaturePath);
			file_put_contents($signaturePath.$uuid.".".$ext,$sign);
			$_POST["firma"]=$name;
		}else{
			unset($_POST["firma"]);
		}
		$r=$modelo->array2insert("medicos",$_POST,'Registro añadido/actualizado correctamente',array('nombre','cedula','firma'));
	break;
	case 'addPre':
		$r=$modelo->array2insert("resultados",$_POST,'',array_keys($tagNames));
	break;
	case 'addColumna':
		$med=$modelo->query2arr("select if(idMedico is not null,idMedico,0) as idMedico from medicos where idUsuario='{$_SESSION["idpanda"]}';");
		$_POST["idMedico"]=@$med["data"][0]["idMedico"];
		$r=$modelo->array2insert("resultados",$_POST,'Registro añadido/actualizado correctamente',array_keys($tagNames));
	break;
	case 'addColumnaTorax':
		$med=$modelo->query2arr("select if(idMedico is not null,idMedico,0) as idMedico from medicos where idUsuario='{$_SESSION["idpanda"]}';");
		$_POST["idMedico"]=@$med["data"][0]["idMedico"];
		$r=$modelo->array2insert("resultados",$_POST,'Registro añadido/actualizado correctamente',array_keys($tagNames));
	break;
	case 'addTorax':
		$med=$modelo->query2arr("select if(idMedico is not null,idMedico,0) as idMedico from medicos where idUsuario='{$_SESSION["idpanda"]}';");
		$_POST["idMedico"]=@$med["data"][0]["idMedico"];
		$r=$modelo->array2insert("resultados",$_POST,'',array_keys($tagNames));
	break;
	case 'addOtros':
		$med=$modelo->query2arr("select if(idMedico is not null,idMedico,0) as idMedico from medicos where idUsuario='{$_SESSION["idpanda"]}';");
		$_POST["idMedico"]=@$med["data"][0]["idMedico"];
		$r=$modelo->array2insert("resultados",$_POST,'',array_keys($tagNames));
	break;
	case 'cargar':
		$idCliente=@$_SESSION["administracion"]["idCliente"];
		$sql="select * from clientes where idCliente = '$idCliente';";
		$r=$modelo->query2arr($sql);
	break;
	case 'eliminar':
		$tablas=array(
			"columnaForm"=>array("resultados","idResultado"),
			"columnaToraxForm"=>array("resultados","idResultado"),
			"toraxForm"=>array("resultados","idResultado"),
			"otrosForm"=>array("resultados","idResultado"),
			"medicosForm"=>array("medicos","idMedico"),
		);
		$r=$modelo->updateSql("delete from {$tablas[$_POST["tabla"]][0]} where {$tablas[$_POST["tabla"]][1]}='{$_POST["id"]}';");
	break;
	case 'lBuscaResultado':
		$sql="select * from resultados where idResultado={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"][0]=array_filter($r["data"][0],function($value){ return $value!=NULL; });
		$r["data"]=$r["data"][0];
	break;
	case 'lBuscaMed':
		$sql="select * from medicos where idMedico={$_POST["id"]};";
		$r=$modelo->query2arr($sql);
		$r["data"]=$r["data"][0];
	break;
	default:
	break;
}
@file_put_contents(__DIR__."/../debug/examenes_".time().".txt", json_encode(get_defined_vars(),JSON_PRETTY_PRINT));
echo json_encode($r);
?>