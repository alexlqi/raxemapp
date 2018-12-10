<?php
// proceso en background
function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
} 

//obtiene el json a modo de array
function getJson($filename){
	$data=file_get_contents($filename,true);
	return json_decode($data,true);
}
function getTableHeaders($arr,$tipo='tabla'){
	$a=end($arr);
	$k=array_keys($a);
	switch($tipo){
		case 'tabla':
			$str="";
			foreach($k as $s){
				$str.='<th>'.$s.'</th>';
			}
			return $str;
		break;
		case 'array':
			return $k;
		break;
	}
}

//escribir el ultimo file en el log
function lastfile($str){
	@file_put_contents("proc/lastfile.txt", $str);
}

//RFCs a nombres
function empresas($rfc){
	$empresas=array(
		"HIN141216KK7"=>"HUMA",
		"MCO130423JZ7"=>"MERAS",
		"SES1412163Q5"=>"SEXTO",
		"MIN130514R28"=>"MILLA",
	);

	return ( in_array($rfc, array_keys($empresas)) ) ? $empresas[$rfc] : $rfc;
}

// Does not support flag GLOB_BRACE
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function getkeypath($arr, $lookup)
{
    if (array_key_exists($lookup, $arr))
    {
        return array($lookup);
    }
    else
    {
        foreach ($arr as $key => $subarr)
        {
            if (is_array($subarr))
            {
                $ret = getkeypath($subarr, $lookup);

                if ($ret)
                {
                    $ret[] = $key;
                    return $ret;
                }
            }
        }
    }

    return null;
}

/* para la funcion xmlRecursivo*/
//$treeCfg,$matrices,$ctrl
$treeCfg=array(
	"nomina"=>array(
		"cfdi:Comprobante"=>array(),
		"cfdi:Emisor"=>array("cfdi:Comprobante"),
		"cfdi:Complemento"=>array("cfdi:Comprobante"),
		"cfdi:RegimenFiscal"=>array("cfdi:Comprobante","cfdi:Emisor"),
		"cfdi:Receptor"=>array("cfdi:Comprobante"),
		"cfdi:Concepto"=>array("cfdi:Comprobante","cfdi:Conceptos"),
		"cfdi:Impuestos"=>array("cfdi:Comprobante"),
		"cfdi:Retencion"=>array("cfdi:Comprobante","cfdi:Impuestos","cfdi:Retenciones"),
		"cfdi:Traslado"=>array("cfdi:Comprobante","cfdi:Impuestos","cfdi:Traslados"),
		"nomina:Nomina"=>array("cfdi:Comprobante","cfdi:Complemento"),
		"nomina:Percepciones"=>array("cfdi:Comprobante","cfdi:Complemento","nomina:Nomina"),
		"nomina:Deducciones"=>array("cfdi:Comprobante","cfdi:Complemento","nomina:Nomina"),
		"nomina:Percepcion"=>array("cfdi:Comprobante","cfdi:Complemento","nomina:Nomina","nomina:Percepciones"),
		"nomina:Deduccion"=>array("cfdi:Comprobante","cfdi:Complemento","nomina:Nomina","nomina:Deducciones"),
		"tfd:TimbreFiscalDigital"=>array("cfdi:Comprobante","cfdi:Complemento"),
	),
);
$matrices=array("cfdi:Concepto","cfdi:Retencion","cfdi:Traslado","nomina:Percepcion","nomina:Deduccion");
$ctrl=0;
function xmlRecursivo($a,$b){
	global $tree,$treeCfg,$matrices,$ctrl,$tipoXml;
	//if(@$tipoXml==""){echo "falta definir tipoXml"; exit;}
	if($b!="#text"){
		if(isset($treeCfg[$tipoXml][$b])){
			foreach ($a->attributes as $attr) {
				if($b!="cfdi:Comprobante"){
					//para conformar el eval syntax
					$str='$tree';
					foreach ($treeCfg[$tipoXml][$b] as $key) {
						$str.='["'.$key.'"]';
					}

					//si está en matrices
					if(in_array($b, $matrices)){
						$str.='["'.$b.'"]['.$ctrl.']'.'["'.$attr->nodeName.'"]="'.$attr->nodeValue.'";';
					}else{
						$str.='["'.$b.'"]'.'["'.$attr->nodeName.'"]="'.$attr->nodeValue.'";';
					}
					eval($str);
				}else{
					$tree[$b][$attr->nodeName]=$attr->nodeValue;
				}
			}
			if($a->hasChildNodes()){
				//si tiene hijos se vuelve a usar recursivo
				foreach ($a->childNodes as $w) {
					xmlRecursivo($w,$w->nodeName);
				}
			}else{
				$ctrl++; // aumenta el numero por cada concepto en matriz
			}
		}else{
			//si no tiene configuracion de arbol XML
			foreach ($a->attributes as $attr) {
				if(in_array($b, $matrices)){
					$tree[$b][$ctrl][$attr->nodeName]=$attr->nodeValue;
				}else{
					$tree[$b][$attr->nodeName]=$attr->nodeValue;
				}
			}
			if($a->hasChildNodes()){
				//si tiene hijos se vuelve a usar recursivo
				foreach ($a->childNodes as $w) {
					xmlRecursivo($w,$w->nodeName);
				}
			}else{
				$ctrl++; // aumenta el numero por cada concepto en matriz
			}
		}
	}
	return $tree;
}

function atributos($item,$elem){
	global $arbol;
	$time=date("Y_m_d_H");
	if ($item->hasAttributes()) {
		if(!is_array($elem)){
			foreach ($item->attributes as $attr) {
				$arbol[$elem][$attr->nodeName]=$attr->nodeValue;
			}
		}else{
			#armar el str del array
			$arr='$arbol';
			foreach($elem as $ind){
				$arr.='["'.$ind.'"]';	
			}
			if(!file_exists($time."_eval.log")){file_put_contents($time."_eval.log", "");}
			foreach ($item->attributes as $attr) {
				$str=$arr.'["'.$attr->nodeName.'"]="'.$attr->nodeValue.'";';
				file_put_contents("logs/".$time."_eval.log", "tamaño de arbol: ".count($arbol)."\n");
				#echo $str."<br />";
				eval($str);
			}
			
		}
	}
	return $arbol;
}
function checkXML($file){
	rename($file,str_replace(" ","_",$file));
	$file=str_replace(" ","_",$file);
	$str=file_get_contents($file,true);
	$str=trim($str,"\t\n\r ,?¿!¡");
	$str = iconv('UTF-8', 'UTF-8//IGNORE', $str);
	file_put_contents($file,$str,LOCK_EX);
	return $file;
}
function vardump($v){
	echo "<pre>";var_dump($v);echo "</pre>";
}
function procesar($arr){
	global $cliPath,$provPath;
	
	mkfolder($cliPath);
	mkfolder($provPath);
	
	$serie=(@$arr["cfdi:Comprobante"]["serie"]!="") ? "_".@$arr["cfdi:Comprobante"]["serie"]:"";
	$folio=(@$arr["cfdi:Comprobante"]["folio"]!="") ? "_".@$arr["cfdi:Comprobante"]["folio"]:"";
	
	if($arr["cfdi:Emisor"]["rfc"]=="MPI0311149Y9"){
	#Es una factura de clientes
		$tipo="cli";
		$filename=$cliPath.$arr["cfdi:Emisor"]["rfc"].$serie.$folio.".json";
	}else{
	#Es una factura de proveedores
		$tipo="prov";
		$filename=$provPath.$arr["cfdi:Emisor"]["rfc"].$serie.$folio.".json";
	}
	$myfile = fopen($filename, "w");
	fwrite($myfile,json_encode($arr));
	fclose($myfile);
	chmod($filename,0666);
	#file_put_contents($filename,json_encode($arr));
	#chmod($filename,0777);
	
	return array($tipo,$arr["cfdi:Emisor"]["rfc"].$serie.$folio);
}

function resguardar($file,$path,$newname){
	if(!is_dir($path)){
		mkfolder($path);
	}
	return rename($file,$path.$newname.".xml");
}

function logs($path,$filename,$str){
	if(!file_exists($path.$filename)){file_put_contents($path.$filename, "");}
	file_put_contents($path.$filename, date("Y-m-d H:i:s").": ".$str."\n",LOCK_EX+FILE_APPEND);
}

function mkfolder($path){
	if(!is_dir($path)){
		mkdir($path, 0777,true);
	}
}

function arr2csv($arr){
	if(is_array($arr)){
		$columnas=reset($arr);
		$columnas=array_keys($columnas);
		$csv=acsv($columnas);
		$csv.=acsv($arr);
		return $csv;
	}else{
		return false;
	}
}

function acsv($arr){ ## solo se pueden 1 o 2 niveles
	$csv="";
	if(is_array(end($arr))){
		foreach ($arr as $d) {
			$csv.=implode(",", $d)."\n";
		}
	}else{
		$csv=implode(",", $arr)."\n";
	}
	return $csv;
}

function wmiWBemLocatorQuery( $query ) {
    if ( class_exists( '\\COM' ) ) {
        try {
            $WbemLocator = new \COM( "WbemScripting.SWbemLocator" );
            $WbemServices = $WbemLocator->ConnectServer( '127.0.0.1', 'root\CIMV2' );
            $WbemServices->Security_->ImpersonationLevel = 3;
            // use wbemtest tool to query all classes for namespace root\cimv2
            return $WbemServices->ExecQuery( $query );
        } catch ( \com_exception $e ) {
            echo $e->getMessage();
        }
    } elseif ( ! extension_loaded( 'com_dotnet' ) )
        trigger_error( 'It seems that the COM is not enabled in your php.ini', E_USER_WARNING );
    else {
        $err = error_get_last();
        trigger_error( $err['message'], E_USER_WARNING );
    }

    return false;
}


function getSystemMemoryInfo( $output_key = '' ) {
    $keys = array( 'MemTotal', 'MemFree', 'MemAvailable', 'SwapTotal', 'SwapFree' );
    $result = array();

    try {
        $wmi_found = false;
        if ( $wmi_query = wmiWBemLocatorQuery( 
            "SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem" ) ) {
            foreach ( $wmi_query as $r ) {
                $result['MemFree'] = $r->FreePhysicalMemory*1;
                $result['MemAvailable'] = $r->FreeVirtualMemory*1;
                $result['SwapFree'] = $r->TotalSwapSpaceSize*1;
                $result['SwapTotal'] = $r->TotalVirtualMemorySize*1;
                $result['MemTotal'] = $r->TotalVisibleMemorySize*1;
                $wmi_found = true;
            }
        }
    } catch ( Exception $e ) {
        echo $e->getMessage();
    }
    return empty( $output_key ) || ! isset( $result[$output_key] ) ? $result : $result[$output_key];
}

function getProcList(){
	//obtiene el tasklist
	$tasklist=shell_exec("tasklist");
	$tasklist=explode("\n", $tasklist);
	return $tasklist;
}
function getProcCount(){
	//obtiene el tasklist
	$tasklist=shell_exec("tasklist");
	$tasklist=explode("\n", $tasklist);
	$tasklist=count($tasklist)-3;
	return $tasklist;
}

function entreFecha($fecha,$rango=array()){
	//$rango es el rango de fecha
	$fecha=strtotime($fecha);
	$fini=strtotime($rango[0]);
	$ffin=strtotime($rango[1]);
	//echo "$fecha $fini $ffin<br>";
	if($fecha>=$fini and $fecha<=$ffin){
		return true;
	}else{
		return false;
	}
}

function voltearFecha($fecha){
	$strFecha=$fecha;
	$fecha=explode("/", $fecha);
	if(isset($fecha[2])){
		return $fecha[2]."-".$fecha[1]."-".$fecha[0];
	}else{
		return false;
	}
}

function fecha2time($fecha){
	//la fecha tiene que estar en formato dd/mm/aaaa
	return strtotime(voltearFecha($fecha));
}

function attr2array($str){
	//convierte los atributos del nodo xml en un array y lo devuelve como array
	//$str=str_replace("\" ", "\"¬", utf8_decode($str));
	$str=str_replace("\" ", "\"¬", $str);
	$attrStr=explode("¬", $str);
	$attrs=array();
	foreach ($attrStr as $i => $v) {
		//convierte cada ***="***" en un array y lo suma a attrs
		$attr=explode("=", $v);
		$attrs[$attr[0]]=str_replace("\"", "", $attr[1]);
	}
	return $attrs;
}

function quitarComas($str){
	$str=html_entity_decode(utf8_decode($str));
	$str=str_replace(",", "", $str);
	$str=str_replace("\n", " | ", $str);
	return $str;
}

##FUNCIONES PARA CONVERTIR A LETRA LOS IMPORTES
function unidad($numuero){
	switch ($numuero)
	{
		case 9:
		{
			$numu = "NUEVE";
			break;
		}
		case 8:
		{
			$numu = "OCHO";
			break;
		}
		case 7:
		{
			$numu = "SIETE";
			break;
		}		
		case 6:
		{
			$numu = "SEIS";
			break;
		}		
		case 5:
		{
			$numu = "CINCO";
			break;
		}		
		case 4:
		{
			$numu = "CUATRO";
			break;
		}		
		case 3:
		{
			$numu = "TRES";
			break;
		}		
		case 2:
		{
			$numu = "DOS";
			break;
		}		
		case 1:
		{
			$numu = "UN";
			break;
		}		
		case 0:
		{
			$numu = "";
			break;
		}		
	}
	return $numu;	
}

function decena($numdero){
	
		if ($numdero >= 90 && $numdero <= 99)
		{
			$numd = "NOVENTA ";
			if ($numdero > 90)
				$numd = $numd."Y ".(unidad($numdero - 90));
		}
		else if ($numdero >= 80 && $numdero <= 89)
		{
			$numd = "OCHENTA ";
			if ($numdero > 80)
				$numd = $numd."Y ".(unidad($numdero - 80));
		}
		else if ($numdero >= 70 && $numdero <= 79)
		{
			$numd = "SETENTA ";
			if ($numdero > 70)
				$numd = $numd."Y ".(unidad($numdero - 70));
		}
		else if ($numdero >= 60 && $numdero <= 69)
		{
			$numd = "SESENTA ";
			if ($numdero > 60)
				$numd = $numd."Y ".(unidad($numdero - 60));
		}
		else if ($numdero >= 50 && $numdero <= 59)
		{
			$numd = "CINCUENTA ";
			if ($numdero > 50)
				$numd = $numd."Y ".(unidad($numdero - 50));
		}
		else if ($numdero >= 40 && $numdero <= 49)
		{
			$numd = "CUARENTA ";
			if ($numdero > 40)
				$numd = $numd."Y ".(unidad($numdero - 40));
		}
		else if ($numdero >= 30 && $numdero <= 39)
		{
			$numd = "TREINTA ";
			if ($numdero > 30)
				$numd = $numd."Y ".(unidad($numdero - 30));
		}
		else if ($numdero >= 20 && $numdero <= 29)
		{
			if ($numdero == 20)
				$numd = "VEINTE ";
			else
				$numd = "VEINTI".(unidad($numdero - 20));
		}
		else if ($numdero >= 10 && $numdero <= 19)
		{
			switch ($numdero){
			case 10:
			{
				$numd = "DIEZ ";
				break;
			}
			case 11:
			{		 		
				$numd = "ONCE ";
				break;
			}
			case 12:
			{
				$numd = "DOCE ";
				break;
			}
			case 13:
			{
				$numd = "TRECE ";
				break;
			}
			case 14:
			{
				$numd = "CATORCE ";
				break;
			}
			case 15:
			{
				$numd = "QUINCE ";
				break;
			}
			case 16:
			{
				$numd = "DIECISEIS ";
				break;
			}
			case 17:
			{
				$numd = "DIECISIETE ";
				break;
			}
			case 18:
			{
				$numd = "DIECIOCHO ";
				break;
			}
			case 19:
			{
				$numd = "DIECINUEVE ";
				break;
			}
			}	
		}
		else
			$numd = unidad($numdero);
	return $numd;
}

	function centena($numc){
		if ($numc >= 100)
		{
			if ($numc >= 900 && $numc <= 999)
			{
				$numce = "NOVECIENTOS ";
				if ($numc > 900)
					$numce = $numce.(decena($numc - 900));
			}
			else if ($numc >= 800 && $numc <= 899)
			{
				$numce = "OCHOCIENTOS ";
				if ($numc > 800)
					$numce = $numce.(decena($numc - 800));
			}
			else if ($numc >= 700 && $numc <= 799)
			{
				$numce = "SETECIENTOS ";
				if ($numc > 700)
					$numce = $numce.(decena($numc - 700));
			}
			else if ($numc >= 600 && $numc <= 699)
			{
				$numce = "SEISCIENTOS ";
				if ($numc > 600)
					$numce = $numce.(decena($numc - 600));
			}
			else if ($numc >= 500 && $numc <= 599)
			{
				$numce = "QUINIENTOS ";
				if ($numc > 500)
					$numce = $numce.(decena($numc - 500));
			}
			else if ($numc >= 400 && $numc <= 499)
			{
				$numce = "CUATROCIENTOS ";
				if ($numc > 400)
					$numce = $numce.(decena($numc - 400));
			}
			else if ($numc >= 300 && $numc <= 399)
			{
				$numce = "TRESCIENTOS ";
				if ($numc > 300)
					$numce = $numce.(decena($numc - 300));
			}
			else if ($numc >= 200 && $numc <= 299)
			{
				$numce = "DOSCIENTOS ";
				if ($numc > 200)
					$numce = $numce.(decena($numc - 200));
			}
			else if ($numc >= 100 && $numc <= 199)
			{
				if ($numc == 100)
					$numce = "CIEN ";
				else
					$numce = "CIENTO ".(decena($numc - 100));
			}
		}
		else
			$numce = decena($numc);
		
		return $numce;	
}

	function miles($nummero){
		if ($nummero >= 1000 && $nummero < 2000){
			$numm = "MIL ".(centena($nummero%1000));
		}
		if ($nummero >= 2000 && $nummero <10000){
			$numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
		}
		if ($nummero < 1000)
			$numm = centena($nummero);
		
		return $numm;
	}

	function decmiles($numdmero){
		if ($numdmero == 10000)
			$numde = "DIEZ MIL";
		if ($numdmero > 10000 && $numdmero <20000){
			$numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));		
		}
		if ($numdmero >= 20000 && $numdmero <100000){
			$numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));		
		}		
		if ($numdmero < 10000)
			$numde = miles($numdmero);
		
		return $numde;
	}		

	function cienmiles($numcmero){
		if ($numcmero == 100000)
			$num_letracm = "CIEN MIL";
		if ($numcmero >= 100000 && $numcmero <1000000){
			$num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));		
		}
		if ($numcmero < 100000)
			$num_letracm = decmiles($numcmero);
		return $num_letracm;
	}	
	
	function millon($nummiero){
		if ($nummiero >= 1000000 && $nummiero <2000000){
			$num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
		}
		if ($nummiero >= 2000000 && $nummiero <10000000){
			$num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
		}
		if ($nummiero < 1000000)
			$num_letramm = cienmiles($nummiero);
		
		return $num_letramm;
	}	

	function decmillon($numerodm){
		if ($numerodm == 10000000)
			$num_letradmm = "DIEZ MILLONES";
		if ($numerodm > 10000000 && $numerodm <20000000){
			$num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));		
		}
		if ($numerodm >= 20000000 && $numerodm <100000000){
			$num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));		
		}
		if ($numerodm < 10000000)
			$num_letradmm = millon($numerodm);
		
		return $num_letradmm;
	}

	function cienmillon($numcmeros){
		if ($numcmeros == 100000000)
			$num_letracms = "CIEN MILLONES";
		if ($numcmeros >= 100000000 && $numcmeros <1000000000){
			$num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));		
		}
		if ($numcmeros < 100000000)
			$num_letracms = decmillon($numcmeros);
		return $num_letracms;
	}	

	function milmillon($nummierod){
		if ($nummierod >= 1000000000 && $nummierod <2000000000){
			$num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
		}
		if ($nummierod >= 2000000000 && $nummierod <10000000000){
			$num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
		}
		if ($nummierod < 1000000000)
			$num_letrammd = cienmillon($nummierod);
		
		return $num_letrammd;
	}	
			
		
function convertir($numero){
	$cantidad = explode(".",$numero);
	$centavos = @$cantidad[1];
	if ($centavos == 0)
		$centavos = "00"; 
	
	//$numf = milmillon($numero);
	$numf = milmillon($cantidad[0]);
	if ($centavos != 1) {
	return $numf." PESOS ".$centavos."/100 M.N.";
	}else{
	return $numf." PESOS ".$centavos."0"."/100 M.N.";
	}
}

function convertirdll($numero){
	$cantidad = explode(".",$numero);
	$centavos = $cantidad[1];
	if (strlen($cantidad[1]) == 0)
		$centavos = "00"; 
	
	//$numf = milmillon($numero);
	$numf = milmillon($cantidad[0]);
	if (strlen($cantidad[1]) != 1) {
	return $numf." DOLARES ".$centavos."/100 USD";
	}else{
	return $numf." DOLARES ".$centavos."0"."/100 USD";
	}
}
function espacios($n,$max) {
	$e = " ";
	$esp = "";
	if ($n != $max) {
		for ($i=0; $i<$max-$n; $i++)
			$esp .= $e;
		return $esp;
	} else {
		return $esp;
	}
}
function dinero ($cantidad,$signo) {
	$c = "";
	if ($signo == "S")
		$c .= '$';
	return $c.= number_format($cantidad,2,'.',',');
}
?>