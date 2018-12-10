<?php
// clase para tablas
/*
* se pondrá la clase datatables para que genere el DataTable
* forma del array para las tablas
*	$tabla=array(
*		'id'=>'datos1',
*		'class'=>array('datatables',),
*		'data'=>array(
*			array(
*				'columna 1'=>'Dato de columna1',
*			),
*		)
*	);
* forma del array para las tablas con más información en el TD
*	$tabla=array(
*		'id'=>'datos1',
*		'class'=>array('datatables',),
*		'data'=>array(
*			array(
*				'colname'=>'columna 1',
*				'value'=>'Dato de columna1',
*				'id'=>'Dato de columna1',
*				'class'=>'Dato de columna1',
*			),
*		)
*	);
*
*/

class tables {
	private function colSet($arr){
	/*	
	*	colSet($arr): función para escribir los títulos de las columnas
	*	
	*/
		if(is_array($arr)){
			/* 
			*	si encuentra que algun elemento del array es un array entonces se detiene y regresa un error
			*	si todo son array entonces se procede a tomarlo como el detalle de tablas
			*/
			$total=count($arr);
			$elemArr=0;
			$hfNames=array();
			foreach($arr as $elem){
				//var_dump($elem);
				//if(is_array($elem)){return false; break;}
				if(is_array($elem)){
					if(!in_array($elem["colname"],$hfNames)){
						array_push($hfNames,$elem["colname"]);
					}
					$elemArr++;
				}
			}
			
			if($total==$elemArr){
				//entonces es el detalle de tablas
				return array('thead'=>$this->wrapTag($hfNames,'head'),'tfoot'=>$this->wrapTag($hfNames,'foot'),);
			}elseif($elemArr==0){
				//ninguno es array y se procede a usarlo en forma generica
				$nombres=array_keys($arr);
				return array('thead'=>$this->wrapTag($nombres,'head'),'tfoot'=>$this->wrapTag($nombres,'foot'),);
			}else{
				//alguno es array y debe omitirse
			}
			
			// si $arr es array y no tiene ningun otro array debajo
		}
	}
	public function btnBuild($btnArr=array(),$permisos=true){
		$btnStr="<div class='botonera col-md-12'>";
		if(!empty($btnArr)){
			foreach($btnArr as $btnData){
				if($permisos===true){
					$btnStr.=$this->btnTable($btnData["tipo"],$btnData);
				}
			}
		}
		$btnStr.="</div>";
		return $btnStr;
	}
	public function btnTable($tipo,$dataBtn=array()){
		$dataSetArr=$eventSetArr=array();
		$dataSet="";
		$eventSet="";
		$btn="";
		$btnId=@$dataBtn["cfg"]["id"];
		if(!empty($dataBtn)){
			# aqui se generará el dataset para cada uno
			if(!empty($dataBtn["dataSet"])){
				foreach($dataBtn["dataSet"] as $i=>$v){
					$dataSetArr[]='data-'.$i.'="'.$v.'"';
				}
				$dataSet=implode(" ",$dataSetArr);
			}
			if(!empty($dataBtn["eventSet"])){
				foreach($dataBtn["eventSet"] as $i=>$v){
					$eventSetArr[]=$i.'="'.$v.'"';
				}
				$eventSet=implode(" ",$eventSetArr);
			}
			$texto=(@$dataBtn["cfg"]["texto"]!="")? "<span style='margin-left:5px;'>{$dataBtn["cfg"]["texto"]}</span>" : '' ;
			# se creará un botón por tipo
			//$id=$dataBtn["dataset"]["id"];
			$btn="<button id='{$btnId}' class=\"btn btn-{$dataBtn["cfg"]["color"]} botonTabla\" $dataSet $eventSet><span class=\"glyphicon glyphicon-{$dataBtn["cfg"]["glyph"]}\"></span>$texto</button>";
		}else{
			return false;
		}
		return $btn;
	}
	static public function btnTableStc($dataBtn=array()){
		$dataSetArr=$eventSetArr=array();
		$dataSet="";
		$eventSet="";
		$btn="";
		$btnId=@$dataBtn["cfg"]["id"];
		if(!empty($dataBtn)){
			# aqui se generará el dataset para cada uno
			if(!empty($dataBtn["dataSet"])){
				foreach($dataBtn["dataSet"] as $i=>$v){
					$dataSetArr[]='data-'.$i.'="'.$v.'"';
				}
				$dataSet=implode(" ",$dataSetArr);
			}
			if(!empty($dataBtn["eventSet"])){
				foreach($dataBtn["eventSet"] as $i=>$v){
					$eventSetArr[]=$i.'="'.$v.'"';
				}
				$eventSet=implode(" ",$eventSetArr);
			}
			# se creará un botón por tipo
			//$id=$dataBtn["dataset"]["id"];
			$btn="<button id='{$btnId}' class=\"btn btn-{$dataBtn["cfg"]["color"]} botonTabla\" $dataSet $eventSet><span class=\"glyphicon glyphicon-{$dataBtn["cfg"]["glyph"]}\"></span></button>";
		}else{
			return false;
		}
		return $btn;
	}
	
	public function createButton($tipo,$dataBtn=array()){
		$dataSet="";
		$abbr=@$dataBtn[3];
		if(!empty($dataBtn)){
			switch($tipo){
				case 'download':
				case 'download2':
					$dataSet="data-tabla='{$dataBtn[0]}' data-tipo='{$dataBtn[1]}' data-id='{$dataBtn[2]}'";
				break;
				case 'edit':
					$dataSet="data-tabla='{$dataBtn[0]}' data-id='{$dataBtn[1]}'";
				break;
				case 'edit2':
					$dataSet=implode(" ",$dataBtn);
					//$dataSet="data-tabla='{$dataBtn[0]}' data-id='{$dataBtn[1]}'";
				break;
			}
		}
		$glifos=array(
			"create"=>'<span class="glyphicon glyphicon-pencil tablaAccion" '.$dataSet.' onclick=""></span>',
			"edit"=>'<span class="glyphicon glyphicon-pencil tablaAccion" '.$dataSet.' onclick="accionTabla(\'edit\',this);"></span>',
			"edit2"=>'<span class="glyphicon glyphicon-pencil tablaAccion" '.$dataSet.' onclick="accionTabla(\'edit2\',this);"></span>',
			"update"=>'<span class="glyphicon glyphicon-repeat tablaAccion" '.$dataSet.' onclick=""></span>',
			"save"=>'<span class="glyphicon glyphicon-floppy-disk tablaAccion" '.$dataSet.' onclick=""></span>',
			"delete"=>'<span class="glyphicon glyphicon-trash tablaAccion" '.$dataSet.' onclick=""></span>',
			"remove"=>'<span class="glyphicon glyphicon-remove tablaAccion" '.$dataSet.' onclick=""></span>',
			"open"=>'<span class="glyphicon glyphicon-open-file tablaAccion" '.$dataSet.' onclick=""></span>',
			"close"=>'<span class="glyphicon glyphicon-remove tablaAccion" '.$dataSet.' onclick=""></span>',
			"upload"=>'<span class="glyphicon glyphicon-upload tablaAccion" '.$dataSet.' onclick="accionTabla(\'upload\',this);"></span>',
			"download"=>'<abbr title="'.$abbr.'"><span class="glyphicon glyphicon-download tablaAccion" '.$dataSet.' onclick="accionTabla(\'download\',this);"></span></abbr>',
			"download2"=>'<abbr title="'.$abbr.'"><span class="glyphicon glyphicon-circle-arrow-down tablaAccion" '.$dataSet.' onclick="accionTabla(\'download\',this);"></span></abbr>',
		);
		return (in_array($tipo,array_keys($glifos))) ? @$glifos[$tipo] : $dataBtn[0];
	}
	
	private function wrapTag($arr,$tipo,$dataSetArr=array()){
	/* 
	*	wrapTag es para hacer los rows para cada parte thead, tfoot y tbody.	
	*/
		$tag="";
		switch($tipo){
			case 'head':
				// recorre todo el array para crear los renglones
				$rows="<tr>";				
				foreach($arr as $i=>$v){
					if(is_array($v)){
						// si $v es array entonces son varios renglones
						// no procede porque debe ser solamente un renglon
						$rows='<tr><th colspan="100">Error de matriz</th></tr>';
						break; 
					}else{
						//si no es array entonces es un solo renglón y procede
						$rows.='<th>'.$v.'</th>';
					}
				}
				$rows.="</tr>";
				$tag='<thead>'.$rows.'</thead>';
			break;
			case 'foot':
				// recorre todo el array para crear los renglones
				$rows="<tr>";
				foreach($arr as $i=>$v){
					if(is_array($v)){
						// si $v es array entonces son varios renglones
						// no procede porque debe ser solamente un renglon
						$rows='<tr><th colspan="100">Error de matriz</th></tr>';
						break;
					}else{
						//si no es array entonces es un solo renglón y procede
						$rows.='<th>'.$v.'</th>';
					}
				}
				$rows.="</tr>";
				$tag='<tfoot>'.$rows.'</tfoot>';
			break;
			case 'body':
				// recorre todo el array para crear los renglones
				$rows="";
				if(!empty($dataSetArr)){
					$tabla=array();
					foreach($dataSetArr as $campo=>$valor){
						$tabla[]="data-$campo=\"$valor\"";
					}
					$tabla=implode(" ", $tabla);
				}else{
					$tabla="";
				}
				foreach($arr as $i=>$v){
					if(is_array($v)){
						// si $v es array entonces son varios renglones
						// si no es array entonces es un solo renglón
						$priKey=reset($v);
						$rows.="<tr $tabla data-id=\"$priKey\">";
						foreach($v as $ii=>$vv){
							if(!is_array($vv)){
								//checar si $ii es ACCIONES
								$rows.='<td>';
								if(strtoupper($ii)=="ACCIONES"){
									#revisa qué botones va a añadir
									foreach(explode(".",$vv) as $btnBuild){
										$btn=explode(":",$btnBuild);
										if(@$btn[1]!=""){
											$dataBtn=explode("|",$btn[1]);
											$rows.=$this->createButton($btn[0],$dataBtn);
										}else{
											$rows.=$this->createButton($btn[0]);
										}
									}
								}else{
									$rows.=$vv;
								}
								$rows.='</td>';
							}else{
								// si es un array entonces se usan sus variables para dar formato a la celda td
								//$rows.='<td>ERROR: este elemento es un array, checar dataset.</td>';
								$rows.='<td id="'.$vv["id"].'" class="'.$vv["class"].'" data-field="'.$vv["colname"].'" >'.$vv["value"].'</td>';
							}
						}
						$rows.="</tr>";
					}else{
						// si no es array entonces es un solo renglón
						$rows.='<th>'.$v.'</th>';
					}
				}
				$tag='<tbody>'.$rows.'</tbody>';
			break;
		}
		return $tag;
	}
	
	// función para obtener la tabla
	public function writeDataTable($arr,$write=false){
		$id=@$arr["id"];
		$class="datatables display".@implode(' ',$arr["class"]);
		
		$table="<table id=\"$id\" class=\"$class\">";
		$hf=$this->colSet(@end($arr["data"]));
		$table.=$hf["thead"];
		$table.=$hf["tfoot"];
		$table.=$this->wrapTag(@$arr["data"],'body',@$arr["dataSet"]);
		$table.="</table>";
		
		if($write){
			echo $table;
		}else{
			return $table;
		}
	}
	public function writeDataTableAjax($arr,$write=false){
		$id=@$arr["id"];
		$class="datatables display".@implode(' ',$arr["class"]);
		
		$table="<table id=\"$id\" class=\"$class\" width=\"100%\">";
		$hf=$this->colSet(@end($arr["data"]));
		$table.=$hf["thead"];
		$table.=$hf["tfoot"];
		$table.=$this->wrapTag(@$arr["data"],'body',@$arr["dataSet"]);
		$table.="</table><script>
$(document).ready(function(e) {
	datables('$id');
});
</script>";
		
		if($write){
			echo $table;
		}else{
			return $table;
		}
	}
	public function writeTable($arr,$write=false){
		$id=@$arr["id"];
		$class="display ".@implode(' ',$arr["class"]);
		
		$table="<table id=\"$id\" class=\"$class\">";
		$hf=$this->colSet(@end($arr["data"]));
		$table.=$hf["thead"];
		$table.=$hf["tfoot"];
		$table.=$this->wrapTag(@$arr["data"],'body',@$arr["dataSet"]);
		$table.="</table>";
		
		if($write){
			echo $table;
		}else{
			return $table;
		}
	}
	public function writeTableAjax($arr,$write=false){
		$id=@$arr["id"];
		$class="display ".@implode(' ',$arr["class"]);
		
		$table="<table id=\"$id\" class=\"$class\">";
		$hf=$this->colSet(@end($arr["data"]));
		$table.=$hf["thead"];
		$table.=$hf["tfoot"];
		$table.=$this->wrapTag(@$arr["data"],'body',@$arr["dataSet"]);
		$table.="</table>";
		
		if($write){
			echo $table;
		}else{
			return $table;
		}
	}
	public function writeDataTableArray($arr,$write=false){
		$id=@$arr["id"];
		$class="arrDataTable display".@implode(' ',$arr["class"]);
		
		$cols=$arr["data"]["cols"];
		$values=$arr["data"]["values"];
		
		$table=array("cols"=>$cols,"values"=>$values);
		
		if($write){
			echo $table;
		}else{
			return $table;
		}
	}
}
?>