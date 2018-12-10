<?php //PHP ADODB document - made with PHAkt 2.8.2
$funcLoaded=true;
@define("ROOT",$_SERVER["HTTP_HOST"]."/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
@define("ROOT_FOLDER","/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
@define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"].explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
@define("PARTES_PATH",$_SERVER["DOCUMENT_ROOT"].explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/partes/");
@define("PARTES_URL","//".$_SERVER["HTTP_HOST"]."/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/partes/");
@define("CLASS_PATH", ROOT_PATH."includes/");
@define("FUNC_PATH", ROOT_PATH."scripts/funciones.php");

function dynRoot($actual){
  return dirname($actual)."/";
}
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function reArray(&$array) {
    $file_ary = array();
    $file_count = count($array[0]);
    $file_keys = array_keys($array);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $array[$key][$i];
        }
    }
    return $file_ary;
}
function parsePdfTemplate($template,$data=array(),$cols=array()){
	if(is_file($template)){
    $tableData=$data;
    @include_once(__DIR__."/pdftemplates/function_templates.php");
    $phpTmpFile=__DIR__."/pdftemplates/".uniqid().".php";
    $html=file_get_contents($template);
    preg_match_all("/\{\{ *(\$|%%|%|@)(.*) *}}/i",$html,$blades);
    $blades=reArray($blades);
    foreach($blades as $b){
      switch($b[1]){
        case '%':
          if(isset($data[$b[2]])){
            $html=str_replace($b[0],@$data[$b[2]],$html);
          }else{
            $html=str_replace($b[0],"N/A",$html);
          }
        break;
        case '%%':
          if(isset($cols[$b[2]])){
            $html=str_replace($b[0],@$cols[$b[2]],$html);
          }else{
            $html=str_replace($b[0],"N/A",$html);
          }
        break;
        case '@':
          $html=str_replace($b[0],"<?php {$b[2]}; ?>",$html);
        break;
      }
    }
    file_put_contents($phpTmpFile,$html);
		ob_start();
		include($phpTmpFile);
		$html=ob_get_contents();
		ob_end_clean();
    @unlink($phpTmpFile);
		return $html;
	}else{
		return false;
	}
}

function urlAjaxPartes($archivo=''){
  global $seccion;
  $seccion = (@$seccion!="") ? $seccion : "" ;
  //var_dump(PARTES_URL.$seccion."/".$archivo);
  return PARTES_URL.$seccion."/".$archivo;
}

function includeNivelPartes($path,$dir='',$nivel=2){
  //var_dump(is_file($dir."/".regresa($nivel).$path));
  return $dir."/".regresa($nivel).$path;
}

function fechadmy ($fecha) {
	list( $anio, $mes, $dia) = split( '[/.-]', $fecha );
	return $dia.'/'.$mes.'/'.$anio;
}
/*function fechaymd ($fecha) {
	list( $dia, $mes, $anio) = split( '[/.-]', $fecha );
	return trim($anio).'-'.$mes.'-'.$dia;
}//*/
function title($appName=""){
	$a=trim($_SERVER["REQUEST_URI"],"/");
	$a=str_replace(".php", "", $a);
	$a=explode("/",$a);
	unset($a[0]);
	return @$appName." <small><i>( ".implode(" > ",$a)." )</i></small>";
}

function fechaymd ($fecha) {
	$fecha = explode("/", $fecha );
	return trim($fecha[2]).'-'.$fecha[1].'-'.$fecha[0];
}

function termincap($fecha,$numdias) {
	//determina la fecha de termino de la incapacidad
	//la fecha se recibe en el formato dd-mm-aaaa o dd/mm/aaaa
	list($anio, $mes, $dia) = split( '[/.-]', $fecha );
	//la fecha se regresa en formato aaaa-mm-dd
	return gmdate("Y-m-d",mktime(0,0,0,$mes,$dia+$numdias-1,$anio));
}
function termausent($fecha,$numdias) {
	//determina la fecha de termino de la incapacidad
	//la fecha se recibe en el formato dd-mm-aaaa o dd/mm/aaaa
	list( $dia, $mes, $anio) = split( '[/.-]', $fecha );
	//la fecha se regresa en formato aaa-mm-dd
	return gmdate("Y-m-d",mktime(0,0,0,$mes,$dia+$numdias-1,$anio));
}
function incapvigente ($fecha,$numdias) {
	//fecha se recibe en el formato yyyy-mm-dd
	
	$term = termincap($fecha,$numdias); //term regresa en el formato yyyy-mm-dd
	
	$fechater = str_replace("-","/",fechadmy($term));
	$hoy = date('d-m-Y');
	//determina si la incapacidad esta vigente al dia de hoy
	return (numdias($fechater,$hoy) > 1) ? "N" : "S";
	//return numdias($fechater,$hoy);
	/*si el valor regresado es:
	menor a 1: tiene incapacidad vigente
	igual a 1: ultima dia de la incapacidad
	mayor a 1 o igual a 2: ya se pueden realizar movtos
	*/
}
function ausentvigente ($fecha,$numdias) {
	$term = termincap($fecha,$numdias);
	$fechater = fechadmy($term);
	$hoy = date('d-m-Y');
	//determina si la incapacidad esta vigente al dia de hoy
	$n = (numdias($fechater,$hoy) == 1) ? "B" : ((numdias($fechater,$hoy) > 1) ? "M" : "X");
	
	return $n;
	/*si el valor regresado es:
	menor a 1: tiene incapacidad vigente
	igual a 1: ultima dia de la incapacidad
	mayor a 1 o igual a 2: ya se pueden realizar movtos
	*/
}

function dinero ($cantidad,$signo) {
	$c = "";
	if ($signo == "S")
		$c .= '$';
	return $c.= number_format($cantidad,2,'.',',');
}
function numdias($fecha1, $fecha2) {
	//determina el numero de dias que existe entre dos fechas
	//del (01-01-2005)
	$fecha1=date("d-m-Y",strtotime($fecha1));
	$fecha2=date("d-m-Y",strtotime($fecha2));
	list( $dia1, $mes1, $anyo1) = split( '[/.-]', $fecha1);
	/*
	$dia1 = strtok($fecha1, "-");
	$mes1 = strtok("-");
	$anyo1 = strtok("-");
	*/
	//al (15-01-2005)
	list( $dia2, $mes2, $anyo2) = split( '[/.-]', $fecha2);
	/*
	$dia2 = strtok($fecha2, "-");
	$mes2 = strtok("-");
	$anyo2 = strtok("-");
	*/
	
	$num_dias = 0;
	if ($anyo1 < $anyo2) {
		$dias_anyo1 = date("z", mktime(0,0,0,12,31,$anyo1)) - date("z", mktime(0,0,0,$mes1,$dia1,$anyo1));
		$dias_anyo2 = date("z", mktime(0,0,0,$mes2,$dia2,$anyo2));
		$num_dias = $dias_anyo1 + $dias_anyo2;
                return $num_dias+2;
	} else {
		$num_dias = date("z", mktime(0,0,0,$mes2,$dia2,$anyo2)) - date("z", mktime(0,0,0,$mes1,$dia1,$anyo1));
                return $num_dias+1;
	}
	
	
}
function ultimodia($anio,$mes){
   if (((fmod($anio,4)==0) and (fmod($anio,100)!=0)) or (fmod($anio,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   } 
   switch($mes) { 
       case 1: return 31; break; 
       case 2: return $dias_febrero; break; 
       case 3: return 31; break; 
       case 4: return 30; break; 
       case 5: return 31; break; 
       case 6: return 30; break; 
       case 7: return 31; break; 
       case 8: return 31; break; 
       case 9: return 30; break; 
       case 10: return 31; break; 
       case 11: return 30; break; 
       case 12: return 31; break; 
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
function ceros($n,$max) {
	$e = "0";
	$esp = "";
	if ($n != $max) {
		for ($i=0; $i<$max-$n; $i++)
			$esp .= $e;
		return $esp;
	} else {
		return $esp;
	}
}
function getRealIP()
{

   if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
   {
      $client_ip =
         ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
               $_ENV['REMOTE_ADDR']
               :
               "unknown" );

      // los proxys van añadiendo al final de esta cabecera
      // las direcciones ip que van "ocultando". Para localizar la ip real
      // del usuario se comienza a mirar por el principio hasta encontrar
      // una dirección ip que no sea del rango privado. En caso de no
      // encontrarse ninguna se toma como valor el REMOTE_ADDR

      $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

      reset($entries);
      while (list(, $entry) = each($entries))
      {
         $entry = trim($entry);
         if ( preg_match("/^([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+)/", $entry, $ip_list) )
         {
            // http://www.faqs.org/rfcs/rfc1918.html
            $private_ip = array(
                  '/^0\\./',
                  '/^127\\.0\\.0\\.1/',
                  '/^192\\.168\\..*/',
                  '/^172\\.((1[6-9])|(2[0-9])|(3[0-1]))\\..*/',
                  '/^10\\..*/');

            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

            if ($client_ip != $found_ip)
            {
               $client_ip = $found_ip;
               break;
            }
         }
      }
   }
   else
   {
      $client_ip =
         ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
               $_ENV['REMOTE_ADDR']
               :
               "unknown" );
   }

   return $client_ip;

}
## para codificar el xmlstr
function htmlnumericentities($str){
	$convmap = array(0xA1, 0xff, 0, 0xff);
	return htmlspecialchars_decode(mb_encode_numericentity($str, $convmap, "UTF-8"));
}
function numericentitiesHtml($str){
	$convmap = array(0xA1, 0xff, 0, 0xff);
	//return mb_decode_numericentity($str, $convmap, "UTF-8");
	return htmlspecialchars_decode(mb_decode_numericentity($str, $convmap, "UTF-8"));
}
function htmlnumericentitiesRecursive(&$val,$key){
  $convmap = array(0xA1, 0xff, 0, 0xff);
  return htmlspecialchars_decode(mb_encode_numericentity($str, $convmap, "UTF-8"));
}
function numericentitiesHtmlRecursive(&$val,$key){
  $convmap = array(0xA1, 0xff, 0, 0xff);
  if(is_string($val)){
    //return mb_decode_numericentity($str, $convmap, "UTF-8");
    $val=htmlspecialchars_decode(mb_decode_numericentity($val, $convmap, "UTF-8"));
  }
}
## para convertir a base64 en array_walk_recursive
function toBase64(&$val,$key){
	if(in_array($key, array("passBytes","cerBytes","keyBytes"))){
		$val=base64_encode($val);
	}
}
function base64($str){
	return base64_encode($str);
}
function debase64($str){
	return base64_decode($str);
}
function jsonBase64($arr){
	if(is_array($arr)){
		return base64(json_encode($arr,JSON_UNESCAPED_UNICODE));
	}
}
function jsonHtmlRec(&$val){
  //$val=htmlentities($val);
  $val=utf8_encode($val);
}
function dejsonHtmlRec(&$val){
  //$val=htmlentities($val);
  $val=utf8_decode($val);
}
function deJsonBase64($str){
	return json_decode(base64_decode($str),true);
}
## para arreglar NSS proveniente de excel
function arreglaNss($nss){
	if(strlen($nss)<=10){
		return str_pad($nss, 11, "0", STR_PAD_LEFT);
	}else{
		return $nss."";
	}
}
## para convertir las celdas datenumber a date
function number2date($n){
	if(is_float($n) || is_integer($n)){
		$dateTime = new DateTime("1899-12-30 + $n days");
		return $dateTime->format("d/m/Y");
	}else{
		return $n;
	}
}
function elimina_acentos($text){
    $patron = array (
        // Espacios, puntos y comas por guion
        //'/[\., ]+/' => ' ',

        // Vocales
        '/\+/' => '',
        '/&agrave;/' => 'a',
        '/&egrave;/' => 'e',
        '/&igrave;/' => 'i',
        '/&ograve;/' => 'o',
        '/&ugrave;/' => 'u',

        '/&aacute;/' => 'a',
        '/&eacute;/' => 'e',
        '/&iacute;/' => 'i',
        '/&oacute;/' => 'o',
        '/&uacute;/' => 'u',

        '/ó/' => 'o',
        '/Ó/' => 'O',

        '/&acirc;/' => 'a',
        '/&ecirc;/' => 'e',
        '/&icirc;/' => 'i',
        '/&ocirc;/' => 'o',
        '/&ucirc;/' => 'u',

        '/&atilde;/' => 'a',
        '/&etilde;/' => 'e',
        '/&itilde;/' => 'i',
        '/&otilde;/' => 'o',
        '/&utilde;/' => 'u',

        '/&auml;/' => 'a',
        '/&euml;/' => 'e',
        '/&iuml;/' => 'i',
        '/&ouml;/' => 'o',
        '/&uuml;/' => 'u',

        '/&auml;/' => 'a',
        '/&euml;/' => 'e',
        '/&iuml;/' => 'i',
        '/&ouml;/' => 'o',
        '/&uuml;/' => 'u',

        // Otras letras y caracteres especiales
        '/&aring;/' => 'a',
        //'/&ntilde;/' => 'n',
        //'/&Ntilde;/' => '_|N|_',
        //'/&Ntilde;/' => htmlnumericentities('Ñ'),
        //'/&Ntilde;/' => utf8_encode(htmlnumericentities('Ñ')),
        //'/Ñ/' => utf8_decode("Ñ"),
        //'/&Ntilde;/' => utf8_decode("Ñ"),
        //'/Ñ/' => htmlspecialchars("Ñ"),
        //'/&Ntilde;/' => htmlspecialchars("Ñ"),

        // Agregar aqui mas caracteres si es necesario

    );

    $text = preg_replace(array_keys($patron),array_values($patron),$text);
    return $text;
}
function validaCurp($curp){
	return preg_match('/[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]/i', $curp);
}
function validaRfc($rfc){
	return preg_match('/[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]{3}/i', $rfc);
}
function validaTelefono($tel){
	return preg_match('/[0-9]{10}/i', $tel);
}
function array_slice_assoc($array,$keys) {
    return array_intersect_key($array,array_flip($keys));
}
function decodeUtf8($str,$n=1){

}
function decodeUtf8Recursive(&$val,$key,$n=1){
  if(is_string($val)){
    $str = $val;
    $ctrl=0;
    do{
      $str=utf8_decode($str);
      $ctrl++;
    }while($ctrl<$n);
    $val = $str;
  }
}
function fixBadUnicode($str) {
  return str_replace("\u00c3\u2018", "Ñ", $str);
}
function encodeUtf8Recursive(&$val,$key,$n=1){
  if(is_string($val)){
    $str = $val;
    $ctrl=0;
    do{
      $str=utf8_encode($str);
      $ctrl++;
    }while($ctrl<$n);
    $val = $str;
  }
}
function acsv($arr){ ## solo se pueden 1 o 2 niveles
	$csv="";
	if(is_array(end($arr))){
		$header=array_keys(end($arr));
		$header=implode(",", $header)."\n";
		$csv.=$header;
		foreach ($arr as $d) {
			$csv.=implode(",", $d)."\n";
		}
	}else{
		$header=array_keys($arr);
		$header=implode(",", $header)."\n";
		$csv.=$header;
		$csv.=implode(",", $arr)."\n";
	}
	return $csv;
}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
function permisoSub($params,$file){
	$permiso=$params->auth($file);
	if($permiso!==true){
		echo $permiso;
		return 2;
	}
}

function vardump($v){
	echo "<pre>";var_dump($v);echo "</pre>";
}
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function removeDirectory($path){
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
 	return;
}
function validaZip($f){
	#abre el archivo y lo 
	$finfo=new finfo(FILEINFO_MIME_TYPE);
	return ($finfo->file($f)=="application/zip" and filesize($f)>0);

}
function stripAccents($string){
	$caracteres=str_split($string);
	if(in_array('Ñ',$caracteres)){
		$string=str_replace("Ñ","N",$string);
	}elseif(in_array('ñ',$caracteres)){
		$string=str_replace("ñ","n",$string);
	}
	
	return strtr($string,
		'àáâãäçèéêëìíîïòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝ',
		'aaaaaceeeeiiiiooooouuuuyyAAAAACEEEEIIIIOOOOOUUUUY'
	);
}
?>