<?php //clase para manipulación de la base de datos para los formularios

class formas {
	private $objetos;
	public $forma=array();
	
	public function __construct($arr=array()){
		$this->objetos=(!empty($arr)) ? $arr : array();
	}
	public function formCall($id,$output='r',$params=NULL){
		$modelo=(isset($this->objetos["modelo"]))? $this->objetos["modelo"] : NULL;
		$permisos=(isset($this->objetos["permisos"]))? $this->objetos["permisos"] : NULL;
		$tablas=(isset($this->objetos["tablas"]))? $this->objetos["tablas"] : NULL;
		ob_start();
		include(FORMS_PATH."f_{$id}.php");
		switch($output){
			case 'r': # return
				return ob_get_clean();
			break;
			case 'p': # print
				echo ob_get_clean();
			break;
		}
	}
	public function formulario($forma=array()){
		/*
		$forma=array(
			"idform"=>"",
			"classform"=>"",
			"action"=>"",
			"method"=>"",
			"elems"=>array(
				array(
					"label"=>"titulo",
					"control"=>array(
						"tag"=>"", //input,select,textarea
						"dataSrc"=>"sql para cargar",
						"attrs"=>array(
							"data-name"=>"valor",
							"type"=>"",
							"class"=>"",
							"value"=>"",
							"placeholder"=>"",
						),
					),
				),
				array(
					"label"=>"titulo",
					"control"=>array(
						"tag"=>"", //input,select,textarea
						"dataSrc"=>"sql para cargar",
						"attrs"=>array(
							"data-name"=>"valor",
							"type"=>"",
							"class"=>"",
							"value"=>"",
							"placeholder"=>"",
						),
					),
				),
			),
		);
		*/
		$forma=(!empty($forma))?$forma:$this->forma;
		if(!empty($forma)){
			$form='<div class="row">';
			$form.='<form role="form" id="'.@$forma["idform"].'" class="'.@$forma["classform"].'" action="'.@$forma["action"].'" method="'.@$forma["idform"].'">';
			if(@!empty($forma["elems"])){				
				foreach($forma["elems"] as $elems){
					$ctrlTag="";
					$form.='<div class="form-group">';
					if(@$elems["label"]!="") $form.='<label>'.@$elems["label"].'</label>';
					if(@!empty($elems["control"])){
						$control=$elems["control"];
						switch($control["tag"]){
							case 'input':
								$ctrlTag.='<input '; //añade el type
								if(!empty($control["attrs"])){
									foreach($control["attrs"] as $c=>$v){
										$ctrlTag.="$c=\"$v\" ";
									}
								}
								$ctrlTag.=' />';
							break;
						}
					}
					$form.=$ctrlTag;
					$form.='</div>';
				}
			}
			$form.='</form>';
			$form.='</div>';
			//se empieza a procesar el formulario
		}
		return $form;
	}
}
?>