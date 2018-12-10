<?php
function isLandscape($file){
	list($width, $height) = getimagesize($file);
	return ($width > $height) ? true : false;
}
function cols($col){
	$tagNames=array(

	);
	$cols=array_keys($tagNames);
	return (in_array($col,$cols)) ? $tagNames[$col] : $col;
}
function tabla($cols=array(),$cfgs=array(),$tableData=array()){
	global $tagNames;
	$class=(@$cfgs["class"]!="") ? $cfgs["class"] : "body" ;
	$nCols=count($cols);
	$colWidth=(100/$nCols);
	if(count($cols)>0){
		switch(@$cfgs["formato"]){
			case 'textbox':
				$t='';
				foreach($cols as $c){
					$t.='<h4 class="display-5">'.$tagNames[$c].'</h4>';
					$t.='<p class="display-5 textbox">'.$tableData[$c].'</p>';
				}
			break;
			case 'v':
				$t='<table class="'.$class.'" cellpadding="0" cellspacing="0">';
				foreach($cols as $c){
					$t.='<tr>';
					$t.='<th class="tabla-data" align="left" style="width:'.$colWidth.'%;">'.$tagNames[$c].'</th><td class="tabla-data display-5" align="left" style="width:'.$colWidth.'%;">'.$tableData[$c].'</td>';
					$t.='</tr>';
				}
			break;
			case 'h':
			default:
				$t='<table class="'.$class.'" cellpadding="0" cellspacing="0">';
				$t.='<tr>';
				foreach($cols as $c){
					$t.='<th class="tabla-data" style="width:'.$colWidth.'%;">'.$tagNames[$c].'</th>';
				}
				$t.='</tr>';
				$t.='<tr>';
				foreach($cols as $c){
					$t.='<td class="tabla-data">'.$tableData[$c].'</td>';
				}
				$t.='</tr>';
			break;
		}
	}
	$t.='</table>';
	echo $t;
}
?>