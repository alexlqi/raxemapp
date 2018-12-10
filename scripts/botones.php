<?php
$glifos=array(
	"pdf"=>array(
		"tipo"=>"pdf",
		"cfg"=>array(
			"color"=>"warning",
			"glyph"=>"save-file",
			"texto"=>"Descargar PDF",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"pdf",	
			"ctrl"=>'result2pdf',
		),
	),
	"add"=>array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	"edit"=>array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>"lBusca",
		),
	),
	"delete"=>array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lsAjax"]=array(
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"ctrl"=>'lBuscaPermisos',
		),
		"eventSet"=>array(
			"onclick"=>"editarSolicitud(this);",
		),
	),
);
/*$btnArr["lServicioMedico"]=array(
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaConsulta',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);//*/
$editPre=$glifos["edit"];
$editPre["dataSet"]["ctrl"]="lBuscaResultado";
$btnArr["lPre"]=array(
	$glifos["add"],
	$editPre,
	$glifos["delete"],
);

$editColumna=$glifos["edit"];
$editColumna["dataSet"]["ctrl"]="lBuscaResultado";
$btnArr["lColumnaTorax"]=
$btnArr["lTorax"]=
$btnArr["lOtrosExam"]=
$btnArr["lColumna"]=array(
	$glifos["add"],
	$editColumna,
	$glifos["delete"],
	$glifos["pdf"],
);

$btnArr["lResultados"]=array(
	$glifos["pdf"],
);

$editMed=$glifos["edit"];
$editMed["dataSet"]["ctrl"]="lBuscaMed";
$btnArr["lMedicos"]=array(
	$glifos["add"],
	$editMed,
	$glifos["delete"],
);
$btnArr["lUsuariosAsginadosAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["luAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaUsuarios',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lIncidenciaEventualAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'buscaIncidenciaEventual',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lTiempoExtraAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'buscaTiempoExtra',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lSupervisoresAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaSupervisor',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lUsuariosAsginadosAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaUsuarioAsignado',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
		),
		"eventSet"=>array(
			"onclick"=>"quitarUsuario(this);",
		),
	),
);
$btnArr["lPermisosAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaPermisos',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lPersonasAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaPersona',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
$btnArr["lContratosAjax"]=array(
	array(
		"tipo"=>"add",
		"cfg"=>array(
			"color"=>"success",
			"glyph"=>"plus",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"add",
		),
	),
	array(
		"tipo"=>"edit",
		"cfg"=>array(
			"color"=>"info",
			"glyph"=>"pencil",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"edit",
			"ctrl"=>'lBuscaContrato',
		),
	),
	array(
		"tipo"=>"delete",
		"cfg"=>array(
			"color"=>"danger",
			"glyph"=>"remove",
		),
		"dataSet"=>array(
			"id"=>$t,
			"action"=>"delete",
		),
	),
);
?>