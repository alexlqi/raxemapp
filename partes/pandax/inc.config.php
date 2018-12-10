<?php 
## includes para todas las partes del sistema
if(!@$configLoaded){
	if(@$_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest"){
		# si fue solicitado via ajax
		@define("ROOT",$_SERVER["HTTP_HOST"]."/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
		@define("ROOT_FOLDER","/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
		@define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"].explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
		@define("PARTES_PATH",ROOT_PATH."partes/");
		@define("CLASS_PATH", ROOT_PATH."includes/");
		@define("SCRIPT_PATH", ROOT_PATH."scripts/");
		@define("FUNC_PATH", ROOT_PATH."scripts/funciones.php");
		@define("FUNC_FILE", ROOT_PATH."scripts/funciones.php");
		@define("FUNC_DIR", ROOT_PATH."scripts/");
		@define("CFG_PATH", CLASS_PATH."config.php");
		@define("PARTES_URL","//".ROOT."partes/");
		@define("SCRIPT_URL", "//".ROOT."scripts/");

		if(@$configLoaded==false) include CFG_PATH;
		if(@$funcLoaded==false) include FUNC_PATH;
	}else{
		if(@$configLoaded==false) include CFG_PATH;
		//if(@$funcLoaded==false)include FUNC_PATH;
	}
};
?>