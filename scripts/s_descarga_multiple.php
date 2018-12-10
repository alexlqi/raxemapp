<?php session_start();
date_default_timezone_set("America/Monterrey");
ini_set("max_execution_time",0);
ini_set('memory_limit', '256M');

@include_once("funciones.php");
@include("../includes/class.permisos.php");
$dsnModelo=$dsnExamenes;
@include("../includes/class.modelo.php");

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
	'conclusiont'=>'CONCLUSION TORAX',
	'cvNivel'=>'NIVEL',
	'eivNivel'=>'NIVEL',
	'idMedico'=>'ID MEDICO',
);

$tmpDir= __DIR__."/pdfTmp".time()."/";
mkdir($tmpDir);
if(@$_GET["d"]!=""){
	$v=array(
		"kia"=>"450,451,452,453,454,455,456,457,459,460,461,462,464,465,466,467,468,469,471,473,474,475,476,479,480,481,482,483,484,486,487,488,489,491,494,495,497,498,499,626,627,628,629,630,631,633,634,635,636,637,638,639,640,641,642,643,644,645,646,647,648,649,650,651,652,653,654,655,659,660,661,662,663,664,665,666,667,668,671,674,675,677,678,679,680,681,682,683,684,685,686,687,688,690,691,692,693,694,695,696,697,698,699,700,701,702,703,704,705,706,707,708,709,710,711,712,713,714,717,718,719,720,721,722,723,724,725,727,728,729,730,731",
		"navistar"=>"526,598,599,600,602,603,604,605,606,607,611,612,613,614,617,618,620,689",
	);
	
	$pdfZip = "{$_GET["d"]}_".date("ymd_His").".zip";
	$ids=explode(",",$v[$_GET["d"]]);
}else if(@!empty($_POST["ids"])){
	$pdfZip = "resultados_".date("ymd_His").".zip";
	$ids=$_POST["ids"];
}else{
	echo '<script>(function(){window.close();})();</script>';exit;
}
foreach($ids as $id){
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
	$pdfTmp = $tmpDir."{$nombre}.pdf";
	$mpdf = new mPDF('utf-8', 'Letter', 0, '', '11mm', '11mm', '11mm', '11mm', 0, 0);
	#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>');
	#$mpdf->SetHTMLFooter('<div style="border-top:1px solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>','E');
	$mpdf->WriteHTML(parsePdfTemplate($template,$q["data"][0],$tagNames));
	$mpdf->Output($pdfTmp,'F');
}
$zip=new ZipArchive;
$zip->open($pdfZip, ZipArchive::CREATE);
foreach(rglob($tmpDir."*.*") as $file){
	$zip->addFile($file,basename($file));
}
if($zip->close()){
	header("Content-type: application/zip"); 
	header("Content-Disposition: attachment; filename=$pdfZip");
	header("Content-length: " . filesize($pdfZip));
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	readfile("$pdfZip");
};
//rmdir($tmpDir);
?>