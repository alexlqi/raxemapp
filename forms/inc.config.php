<?php 
## includes para todas las partes del sistema
if(!@$configLoaded){
	if(@$_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest"){
		# si fue solicitado via ajax
		$root=str_replace("//","/",$_SERVER["DOCUMENT_ROOT"]."/");
		# si fue solicitado via ajax
		@define("ROOT",$_SERVER["HTTP_HOST"]."/");
		# root folder y root path deben cambiar. seran leidos de server[document_root], configurado desde el virtualhost
		/*
		@define("ROOT_FOLDER","/".explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
		@define("ROOT_PATH",$root.explode("/", trim($_SERVER["SCRIPT_NAME"],"/"))[0]."/");
		//*/
		@define("ROOT_FOLDER","/");
		@define("ROOT_PATH",$root);
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