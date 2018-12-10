<?php 
$rootPath=str_replace("//","/",$_SERVER["DOCUMENT_ROOT"]."/");
$root=str_replace("//","/",$_SERVER["DOCUMENT_ROOT"]."/");
## includes para todas las partes del sistema
if($_SERVER["SERVER_NAME"]==="localhost" || preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $_SERVER["HTTP_HOST"])){
	@define("ROOT",$_SERVER["HTTP_HOST"]."/");
	@define("ROOT_FOLDER","/");
	@define("ROOT_PATH",$root);
}else{
	@define("ROOT",$_SERVER["HTTP_HOST"]."/");
	@define("ROOT_FOLDER","/");
	@define("ROOT_PATH",$rootPath);
}
@define("ROOT_URL","//".$_SERVER["HTTP_HOST"]."/");
@define("FORMS_PATH",ROOT_PATH."forms/");
@define("PARTES_PATH",ROOT_PATH."partes/");
@define("CLASS_PATH", ROOT_PATH."includes/");
@define("SCRIPT_PATH", ROOT_PATH."scripts/");
@define("FUNC_PATH", ROOT_PATH."scripts/funciones.php");
@define("FUNC_FILE", ROOT_PATH."scripts/funciones.php");
@define("FUNC_DIR", ROOT_PATH."scripts/");
@define("CFG_PATH", CLASS_PATH."config.php");
@define("PARTES_URL","//".ROOT."partes/");
@define("SCRIPT_URL", "//".ROOT."scripts/");
//var_dump(get_defined_vars());
?>