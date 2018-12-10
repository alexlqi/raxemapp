<?php
include("../includes/phpexcel/PHPExcel.php");
include("../includes/phpexcel/PHPExcel/IOFactory.php");

#leemos los datos
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load("rx.xlsx");
$objWorksheet = $objPHPExcel->getActiveSheet();
$data=$objWorksheet->toArray();

#escribimos el excel
$objPHPExcel = new PHPExcel();
$objPHPExcel = $objReader->load("plantilla.xlsx");
$objWorksheet = $objPHPExcel->getActiveSheet();

#celdas en negrita
$negritas=array(
	'B3','E3','B8:B13','B22','B30:B31','E43','G43',
);

#creamos la correspondencia
$corr=array(
	'C9'=>'A', #FECHA
	'F3'=>'B', #MOTIVO
	'C3'=>'E', #EMPRESA
	'C11'=>'G', #EDAD
	'C10'=>'F', #NOMBRE
	'C8'=>'D', #FOLIO
	'C24'=>'H', #EJE COL AP
	'C25'=>'I', #CUERPOS
	'F26'=>'J', #ESPACIOS
	'C27'=>'K', #EJE COL LAT
	'C28'=>'L', #TEJIDOS
	'C30'=>'M', #DX COLUMNA
	'C31'=>'N', #COMENTARIO
	'C18'=>'O', #ALT TORAX
	'C19'=>'P', #DX TORAX
);

$cols=range('A', 'Z');
array_shift($data); # quitamos el primero que es de los titulos
$objWorksheet->getColumnDimension('A')->setWidth(4);
$objWorksheet->getColumnDimension('B')->setWidth(16);
$objWorksheet->getColumnDimension('C')->setWidth(10);
$objWorksheet->getColumnDimension('D')->setWidth(6);
$objWorksheet->getColumnDimension('J')->setWidth(4);

##configuracions de celdas
#titulo
$objWorksheet->mergeCells('C5:G5');
$objWorksheet->getStyle('C5')->getAlignment()->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)); 
# info evaluamos
$objWorksheet->mergeCells('B15:I17'); 
$objWorksheet->getStyle('B15')->getAlignment()->setWrapText(true); 
# detalles
$objWorksheet->mergeCells('B33:I34'); 
$objWorksheet->getStyle('B33')->getAlignment()->setWrapText(true);
# certificado  por
$objWorksheet->mergeCells('C41:I42'); 
$objWorksheet->getStyle('C41')->getAlignment()->setWrapText(true)->applyFromArray(array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)); 
#resultados Columna
$objWorksheet->getStyle('B24:B28')->getFont()->setSize(8); #->setBold(true)

foreach($negritas as $celda){
	$objWorksheet->getStyle($celda)->getFont()->setBold(true); #->setBold(true)
}

# las imagenes
$logo = imagecreatefromjpeg('images/logo.jpg');
$firma = imagecreatefromjpeg('images/firma.jpg');
// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('ProMedic');
$objDrawing->setImageResource($logo);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(100);
$objDrawing->setCoordinates('H2');
$objDrawing->setWorksheet($objWorksheet);

$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Firma');
$objDrawing->setDescription('ProMedic');
$objDrawing->setImageResource($firma);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setWidth(60);
$objDrawing->setCoordinates('B38');
$objDrawing->setWorksheet($objWorksheet);

# los valores
foreach($data as $row=>$rows){
	#quitar todos los que no tienen folio rastreo
	$folio=$rows[array_search('D', $cols)];
	if(!$folio>0){continue;}
	foreach($corr as $celda=>$col){
		$value=$rows[array_search($col, $cols)];
		if($col=="A"){
			$value=date("d/m/Y",($value*86400)-(70*365+19)*86400);
		}
		$objWorksheet->SetCellValue($celda, $value);
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	@mkdir("generados/{$folio}");
	$objWriter->save("generados/{$folio}/Radiografias.xlsx");
}
?>