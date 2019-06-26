<?php session_start();
date_default_timezone_set("America/Monterrey");
@include_once("funciones.php");
header('Content-type: text/plain');
@include("../includes/config.php");
$dsnModelo=$dsnExamenes;
@include("../includes/class.modelo.php");
$r["err"]=true;

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
	'oht'=>'OTROS HALLAZGOS DE TORAX'
	'conclusiont'=>'CONCLUSION TORAX',
	'cvNivel'=>'NIVEL',
	'eivNivel'=>'NIVEL',
	'concradcol'=>'CONCLUSIÓN RADIOLÓGICA DE COLUMNA',
	'concradtor'=>'CONCLUSIÓN RADIOLÓGICA DE TORAX',
	'riescol'=>'RIESGO DE COLUMNA',
	'riestor'=>'RIESGO DE TORAX',
	'idMedico'=>'ID MÉDICO',
);

if(@$_GET["id"]==""){exit;}

$sql="select
	r.*,
	concat(r.cv,if(r.cvNivel is not null,concat(' Nivel: ',r.cvNivel),'')) as cv,
	concat(r.eiv,if(r.eivNivel is not null,concat(' Nivel: ',r.eivNivel),'')) as eiv,
	m.nombre as 'medName',
	m.cedula as 'medCed',
	m.firma as 'medSign'
from resultados r
inner join medicos m on r.idMedico=m.idMedico
where idResultado='{$_GET["id"]}';";
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
$mpdf->Output($pdfTmp,'I');
?>