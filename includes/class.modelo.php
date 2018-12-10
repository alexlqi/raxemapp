<?php 
@include_once("config.php");

/*
en el modelo se incluye el archivo de config.php referente a la conexión con la BD
así al cargarse la clase con include, configura los datos de conexión de manera rápida
*/
//función de conexión con la BD de operación del sistema
function connectModelo($dsn){
	return $bd=new PDO($dsn[0],$dsn[1],$dsn[2],$dsn[3]);
}

class modelo{
	//zona de las variables
	protected $pdo=NULL;
	private $logfile="log/conn.log";
	private $resp=array("err"=>false,"data"=>array(),"sql"=>"","msg"=>"");
	
	public function __construct($pdo){
		$this->pdo=$pdo;
		$this->pdo->query("SET NAMES utf8;");
	} //termina clase de construcción
	
	public function array2opt($arr,$id,$optCfg=array()){
		$r="";
		//$r="<option value=\"E\">Elige</option>";
		if(is_array($arr)){
			foreach($arr as $i=>$v){
				# se colocará el atributo desde el key optCfg y se tomará el valor dado por el valor del key en optCfg
				$attrs="";
				if(!empty($optCfg)){
					$attrArr=array();
					foreach($optCfg as $attr=>$col){
						if(isset($v[$col])){
							$attrArr[]="{$attr}=\"$val\"";
						}
					}
					$attrs=implode(" ",$attrArr);
				}
				$r.='<option value="'.$v[$id[0]].'" '.$attrs.' >'.$v[$id[1]].'</option>';
			}
		}else{
			$r=false;
		}
		return $r;
	}
	
	//funcion de query to option
	public function query2opt($sql,$ident=array(),$optConfig=array()){
		$r=$this->resp;
		if(count($ident)>0){
			try{
				$res=$this->pdo->query($sql);
				if(@$optConfig["tree"]!=""){
					if($res->rowCount()>0){
						$rows=$res->fetchAll(PDO::FETCH_ASSOC);
						switch($optConfig["tree"]){
							case 'cgs':
								foreach($rows as $rowId=>$row){
									if($row["grupo"]==NULL and $row["subgrupo"]==NULL){
										$r["data"][$row["categoria"]][$rowId]=$this->array2opt(array($row),$ident,$optConfig);
									}else if($row["subgrupo"]==NULL){
										$r["data"][$row["categoria"]][$row["grupo"]][$rowId]=$this->array2opt(array($row),$ident,$optConfig);
									}else{
										if(!isset($r["data"][$row["categoria"]][$row["grupo"]][$row["subgrupo"]])){$r["data"][$row["categoria"]][$row["grupo"]][$row["subgrupo"]]=$this->array2opt(array($row),$ident,$optConfig);}
										$r["data"][$row["categoria"]][$row["grupo"]][$row["subgrupo"]].=$this->array2opt(array($row),$ident,$optConfig);
									}
								}
							break;
							case 's':
								foreach($rows as $rowId=>$row){
									$r[$row["subgrupo"]][$rowId]=$this->array2opt(array($row),$ident,$optConfig);
								}
							break;
						}
					}
				}else{
					$r["data"] = $res->rowCount()>0 ? $this->array2opt($res->fetchAll(PDO::FETCH_ASSOC),$ident,$optConfig) : '<option selected="selected" disabled="disabled" value="">No hay elementos</option>';
				}
			}
			catch(PDOException $e)
			{
				$r["err"]=true;
				$r["sql"]=$sql;
				$r["msg"]=$e->getMessage();
			}
		}else{
			$r["err"]=true;
			$r["msg"]="No ha pasado la matriz para cotejar la infoprmación para el par de option value";
		}
		
		return $r;
	}
	
	//funcion de query a array
	public function query2arr($sql,$msg=''){
		try
		{
			$cons=$this->pdo->query($sql);
			if($cons->rowCount()>0){
				$r["data"]=$cons->fetchAll(PDO::FETCH_ASSOC);
				$r["err"]=false;
				$r["msg"]=$msg;
			}else{
				$r["data"]=array();
				$r["err"]=true;
				$r["msg"]="No existen registros.";
			}
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	
	//funcion para hacer un arbol desde la consilta de mysql
	public function query2tree($sql,$msg='',$tree=array()){
		$r["tree"]=array();
		try
		{
			$cons=$this->pdo->query($sql);
			if($cons->rowCount()>0){
				$d=$cons->fetchAll(PDO::FETCH_ASSOC);
				foreach($d as $rowId=>$row){
					$tmp=$row;
				}
				$r["err"]=false;
				$r["msg"]=$msg;
			}else{
				$r["err"]=true;
				$r["msg"]="No existen registros.";
			}
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	
	//funcion de query a array data table
	public function query2datatable($sql,$msg=''){
		try
		{
			$cons=$this->pdo->query($sql);
			if($cons->rowCount()>0){
				$info=$cons->fetchAll(PDO::FETCH_ASSOC);
				$colss=end($info);
				$colss=array_keys($colss);
				$cols=array();
				foreach($colss as $c){
					$cols[]["title"]=$c;
				}
				$values=array();
				foreach($info as $i=>$v){
					$values[$i]=array_values($v);
				}
				$r["data"]=array("cols"=>$cols,"values"=>$values);
				$r["err"]=false;
				$r["msg"]=$msg;
			}else{
				$r["data"]=array();
				$r["err"]=true;
				$r["msg"]="No existen registros.";
			}
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	public function update($sql,$msg=''){
		try
		{
			$this->pdo->beginTransaction();
			$cons=$this->pdo->exec($sql);
			$this->pdo->commit();
			$r["err"]=false;
			$r["msg"]="update correcto";
		}
		catch(PDOException $e)
		{
			$this->pdo->rollback();
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	public function insertSql($sql,$msg=''){
		try
		{
			$this->pdo->beginTransaction();
			$cons=$this->pdo->exec($sql);
			$this->pdo->commit();
			$r["err"]=false;
			$r["msg"]=($msg!="")? $msg : "Registro insertado correctamente.";
		}
		catch(PDOException $e)
		{
			$this->pdo->rollback();
			//$r["sql"]=$sql;
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		return $r;
	}
	public function updateSql($sql,$msg=''){
		try
		{
			$this->pdo->beginTransaction();
			$cons=$this->pdo->exec($sql);
			$this->pdo->commit();
			$r["err"]=false;
			$r["msg"]=($msg!="")? $msg : "Registro modificado correctamente.";
		}
		catch(PDOException $e)
		{
			$this->pdo->rollback();
			//$r["sql"]=$sql;
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		return $r;
	}
	public function array2insert($tabla,$arr=array(),$msg='', $onDuplicate='',$tipo=1){
		#convertir el array en sentencia insert into $tabla ()
		if(empty($arr)){
			$r["err"]=true;
			$r["msg"]="no hay datos para guardar";
			return $r;
		}else{
			# si es un solo row entonces se añade a otro array
			if(!is_array(reset($arr))){$arr=array($arr);}
			#sacar los nombres de campos
			$campos=end($arr);
			$cols=$campos=array_keys($campos);
			$campos=implode(",",$campos);
			
			$tmpDatos=array();
			$colData=array();
			$duplicate=array();
			foreach($arr as $pos=>$data){
				# para onDuplicate
				if(is_array($onDuplicate)){
					# revisa las columnas a modificar
					$d=array();
					foreach($onDuplicate as $colU){
						if(!isset($data[$colU])) {continue;}
						$valU=$data[$colU];
						$d[]="$colU='$valU'";
					}
					$duplicate[$pos]=' on duplicate key update '.implode(",",$d);
				}else{
					$duplicate[$pos]=$onDuplicate;
				}
				$tmpDatos["data"][$pos]=$data;
				$tmpDatos["concat"][$pos]="('".implode("','",$data)."')";
			}
			
			switch($tipo){
				case 1: # unica sentencia
					$datos=implode(",",$tmpDatos["concat"]);
					$duplicate=$duplicate[0];
					$sql="insert into $tabla ($campos) VALUES $datos $duplicate;";
					$sql=str_replace("''","'0'",$sql);
					try
					{
						$this->pdo->beginTransaction();
						$cons=$this->pdo->exec($sql);
						$this->pdo->commit();
						$r["err"]=false;
						$r["msg"]=($msg!="")? $msg : "Registro insertado correctamente.";
					}
					catch(PDOException $e)
					{
						$this->pdo->rollback();
						//$r["sql"]=$sql;
						$r["err"]=true;
						$r["msg"]="Error encontrado. ".$e->getMessage();
						//echo $e->getMessage();
					}
				break;
				case 2: # varias sentencias
					foreach($tmpDatos["concat"] as $pos=>$datos){
						$sql="insert into $tabla ($campos) VALUES $datos {$duplicate[$pos]};"; // revisar la manera de usar el onduplicate key, debe ser un acceso a un array
						$sql=str_replace("''","'0'",$sql);
						foreach($tmpDatos["data"][$pos] as $col=>$data){
							$sql=str_replace("%{$col}%","'".$data."'",$sql);
						}
						try
						{
							$this->pdo->beginTransaction();
							$cons=$this->pdo->exec($sql);
							$this->pdo->commit();
							$r["err"]=false;
							$r["msg"]=($msg!="")? $msg : "Registro insertado correctamente.";
						}
						catch(PDOException $e)
						{
							$this->pdo->rollback();
							//$r["sql"]=$sql;
							$r["err"]=true;
							$r["msg"]="Error encontrado. ".$e->getMessage();
							//echo $e->getMessage();
						}
					}
			}
		}
		
		return $r;
	}
	public function exec($sql){
		try
		{
			$this->pdo->beginTransaction();
			$cons=$this->pdo->exec($sql);
			$this->pdo->commit();
			$r["err"]=false;
		}
		catch(PDOException $e)
		{
			$this->pdo->rollback();
			$r["err"]=true;
			$r["msg"]="Error encontrado. ".$e->getMessage();
			//echo $e->getMessage();
		}
		
		return $r;
	}
	public function query2array($sql){
		$r=$this->resp;
		try
		{
			$res=$this->pdo->query($sql);
			$r["data"] = $res->rowCount()>0 ? $res->fetchAll(PDO::FETCH_ASSOC) : array();
			$r["err"] = $res->rowCount()>0 ? false : true;
			$r["msg"] = $res->rowCount()>0 ? '' : 'Conjunto vacio';
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["sql"]=$sql;
			$r["msg"]=$e->getMessage();
		}
		
		return $r;
	} #end query2array();

	/*	funcion de query a array con detalle para la clase de tablas
	* forma del array para las tablas con más información en el TD
	*	$tabla=array(
	*		'id'=>'datos1',
	*		'class'=>array('datatables',),
	*		'data'=>array( //row
	*			array( //columna
	*				'colname'=>'columna 1',
	*				'value'=>'Dato de columna1',
	*				'id'=>'Dato de columna1',
	*				'class'=>'Dato de columna1',
	*			),
	*		)
	*	);
	*/
	public function query2arrayDetalle($sql,$pivote=""){
		$r=$this->resp;
		try
		{
			$res=$this->pdo->query($sql);
			
			//para conseguir la tabla del select en caso de haberla
			if(preg_match("/.* (from|FROM) ([a-z_A-Z0-9]*) .*/",$sql,$from)){
				$tabla=$from[2];
			};
			
			if($pivote!=""){
				foreach($dataSet=$res->fetchAll(PDO::FETCH_ASSOC) as $row){
					if(!isset($row[$pivote])){$dataArr=$dataSet;break;}
					$rid=$row[$pivote];
					foreach($row as $col=>$val){
						$dataArr[$rid][$col]=array( //col
							'tabla'=>@$tabla,
							'colname'=>$col,
							'value'=>$val,
							'id'=>$rid."_".$col,
							'class'=>'modifCol',
						);					}
				}
			}else{
				$dataArr=$res->fetchAll(PDO::FETCH_ASSOC);
			}
			$r["data"] = $res->rowCount()>0 ? $dataArr : array();
			$r["err"] = $res->rowCount()>0 ? false : true;
			$r["msg"] = $res->rowCount()>0 ? '' : 'Conjunto vacio';
		}
		catch(PDOException $e)
		{
			$r["err"]=true;
			$r["sql"]=$sql;
			$r["msg"]=$e->getMessage();
		}
		return $r;
	} #end query2array();
}

//autoload
$autoModelo=(isset($autoModelo)) ? $autoModelo : true;
if(@$autoModelo==true and !empty($dsnModelo)){
	$modelo=new modelo(connectModelo($dsnModelo));
}

?>