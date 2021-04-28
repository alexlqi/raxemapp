<?php session_start();
@ini_set("max_execution_time",0);
set_time_limit (0);

date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: application/json');
@include("../includes/class.permisos.php");
$dsnModelo=$dsnExamenes;
@include("../includes/class.modelo.php");
include_once("../includes/mpdf/vendor/autoload.php");

$folios=[44137,44138,44140,44141,44142,44143,44144,44145,44146,44147,44148,44149,44150,44151,44152,44153,44154,44155,44156,44157,44158,44159,44160,44161,44162,44163,44164,44165,44166,44167,44168,44169,44170,44171,44172,44173,44174,44175,44176,44177,44178,44179,44180,44181,44182,44183,44184,44185,44186,44187,44188,44189,44190,44191,44192,44193,44194,44195,44196,44197,44198,44199,44200,44201,44202,44203,44204,44205,44206,44207,44208,44209,44210,44211,44212,44213,44214,44215,44216,44217,44218,44219,44220,44221,44222,44223,44224,44225,44226,44227,44228,44229,44230,44231,44232,44233,44234,44235,44236,44237,44238,44239,44240,44241,44242,44243,44244,44245,44246,44247,44248,44249,44250,44251,44252,44253,44254,44255,44256,44257,44258,44259,44260,44261,44262,44263,44264,44265,44266,44267,44268,44269,44270,44271,44272,44273,44274,44275,44276,44277,44278,44279,44280,44281,44282,44283,44284,44285,44286,44287,44288,44289,44290,44291,44292,44293,44294,44295,44296,44297,44298,44299,44300,44301,44302,44303,44304,44305,44306,44307,44308,44309,44310,44311,44312,44313,44314,44315,44316,44317,44318,44319,44320,44321,44322,44323,44324,44325,44326,44327,44328,44329,44330,44331,44332,44333,44334,44335,44336,44337,44338,44339,44340,44341,44342,44343,44344,44345,44346,44347,44348,44349,44350,44351,44352,44353,44354,44355,44356,44357,44358,44359,44360,44361,44362,44363,44364,44365,44366,44367,44368,44369,44370,44371,44372,44373,44374,44375,44376,44377,44378,44379,44380,44381,44382,44383,44384,44385,44386,44387,44388,44389,44390,44391,44392,44393,44394,44395,44396,44397,44398,44399,44400,44401,44402,44403,44404,44405,44406,44407,44408,44409,44410,44411,44412,44413,44414,44415,44416,44417,44418,44419,44420,44421,44422,44423,44424,44425,44426,44427,44428,44429,44430,44431,44432,44433,44434,44435,44436,44437,44438,44439,44440,44441,44442,44443,44444,44445,44446,44447,44448,44449,44450,44451,44452,44453,44454,44455,44456,44457,44458,44459,44460,44461,44462,44463,44464,44465,44466,44467,44468,44469,44470,44471,44472,44473,44474,44475,44476,44477,44478,44479,44480,44481,44482,44483,44484,44485,44486,44487,44488,44489,44490,44491,44492,44493,44494,44495,44496,44497,44498,44499,44500,44501,44502,44503,44504,44505,44506,44507,44508,44509,44510,44511,44512,44513,44514,44515,44516,44517,44518,44519,44520,44521,44522,44523,44524,44525,44526,44527,44528,44529];
//$path="../pdfs/".time()."/"; @mkdir($path,0777,true);

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

try {
	$tmpDir= __DIR__."/pdfTmp/".time()."/";
	@mkdir($tmpDir,0777,true);
	$pdfZip = __DIR__."/pdfTmp/resultados_".date("ymd_His").".zip";
	foreach($folios as $id){
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
		$zip->addFile($file,"/".basename($file));
	}
	if($zip->close()){
		echo "Zip generado";
	}else{
		echo "No se pudo crear el zip";
	}
} catch (Exception $e) {
	var_dump($e);
}
?>