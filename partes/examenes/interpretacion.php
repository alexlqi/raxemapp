<?php
@include_once "inc.config.php";
$dsnModelo=$dsnExamenes;
@include(CLASS_PATH."class.modelo.php");
@include(CLASS_PATH."class.table.php");
@include_once(FUNC_DIR."tablas.php");
@include(CLASS_PATH."class.forms.php");

//vardump(get_defined_constants());

$formas=new formas(array("modelo"=>$modelo,"permisos"=>$params));

//var_dump($zonasOpt,$clientesOpt,$personasOpt);

$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
?>
<style>
.container-fluid{
	width:calc(100% - 100px);
	margin: 0 50px;
}
.botoneraSide{
	position:fixed;
	z-index:1000;
	top:50%;
	left:0;
	width:50px;
}
.botonSide{
	float:left;
}
</style>
<script>
var botoneraOrigen=false;
var botoneraH=34;
showCols["listaColumnaTorax"]=showCols["listaColumna"]=showCols["listaTorax"]=showCols["listaOtrosExam"]=[0,1,2,3,4,5,6,7,8];
$(document).ready(function(e) {
    //generarTablas(toggleChk);
	generarTablas();
	tiBtnOrig=setInterval(function(){
		if($(document).find("#lExamenes .botonera").length>0){
			//console.log($(document).find("#lExamenes .botonera"));
			_b=$(document).find("#lColumna .botonera");
			botoneraOrigen=_b.offset().top;
			botoneraH=_b.height();
			clearInterval(tiBtnOrig);
		}else{
			//console.log("no se ha encontrado botonera");
		}
	},500);
	
	//botoneraSide
	$(".botonSide").click(function(e) {
		action=$(this).data("action");
        $("#"+tabActual).find("[data-action='" + action + "']").click();
    });
});
$(document).on("click",".datatables",function(e){
	console.log(e);
	//$(".botonera").get(0).clone().appendTo("body").css({"positon":"fixed",top:100,left:50});;
});

function editar(elem){
	e=$(elem);
	d=e.data();
	$.ajax({
		url:scriptPath+'s_rh.php',
		cache:'false',
		type:'POST',
		data:{
			ctrl:d.ctrl,
			id:d.id,
		},
		success: function(r){
			$.each(r.data,function(i,v){
				$("#"+d.form).find('[name="'+i+'"]').val(v);
			})
		}
	});
}
</script>
<div id="formularios" style="display:none;">
<?php
	echo $formas->formCall("examenesRaxem");
?>
</div>
<div class="botoneraSide">
    <button class="btn btn-success botonSide" data-action="add" ><span class="glyphicon glyphicon-plus"></span></button>
    <button class="btn btn-info botonSide" data-action="edit" ><span class="glyphicon glyphicon-pencil"></span></button>
    <button class="btn btn-danger botonSide" data-action="del" ><span class="glyphicon glyphicon-remove"></span></button>
    <button class="btn btn-warning botonSide" data-action="pdf" ><span class="glyphicon glyphicon-save-file"></span></button>
</div>
<div class="tabs body-wrap">
    <ul class="ulTabWrap">
    	<li><a href="#tabs-1">Columna</a></li>
        <li><a href="#tabs-4">Columna y Tórax</a></li>
        <li><a href="#tabs-2">Tórax</a></li>
        <li><a href="#tabs-3">Otros</a></li>
    </ul>
    <div id="tabs-1">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Exámenes de Columna <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
                <div class="row" style="margin:5px auto;min-height:1vh;">
                    <form role="form" class="filtros col-md-11 alert-info" onsubmit="return false;">
                        <div class="col-md-4">
                        	<h3 class="text-center"><label>Buscar por fecha</label></h3>
                            <table class="fullw">
                            	<tr>
                                    <td>
                                   		<div class="form-group">
                                          <span>Desde</span>
                                          <input name="desde" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <span>Hasta</span>
                                            <input name="hasta" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <div class="col-md-1">
                        <button class="btn btn-primary fullw" onclick="listar('lColumna');">Buscar</button>
                    </div>
                </div>
                <div id="lColumna" class="tabla col-md-12" data-tabla="listaColumna" data-form="columnaForm" data-titulo="exámenes"></div>
            </div>
        </div>
    </div>
    <div id="tabs-4">
        <div class="container-fluid">
            <div class="row">
                <h2>Listado de Exámenes de Columna y Torax <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
                <div class="row" style="margin:5px auto;min-height:1vh;">
                    <form role="form" class="filtros col-md-11 alert-info" onsubmit="return false;">
                        <div class="col-md-4">
                        	<h3 class="text-center"><label>Buscar por fecha</label></h3>
                            <table class="fullw">
                            	<tr>
                                    <td>
                                   		<div class="form-group">
                                          <span>Desde</span>
                                          <input name="desde" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <span>Hasta</span>
                                            <input name="hasta" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <div class="col-md-1">
                        <button class="btn btn-primary fullw" onclick="listar('lColumnaTorax');">Buscar</button>
                    </div>
                </div>
                <div id="lColumnaTorax" class="tabla col-md-12" data-tabla="listaColumnaTorax" data-form="columnaToraxForm" data-titulo="exámenes"></div>
            </div>
        </div>
    </div>
    <div id="tabs-2">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Exámenes de Tórax <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
                <div class="row" style="margin:5px auto;min-height:1vh;">
                    <form role="form" class="filtros col-md-11 alert-info" onsubmit="return false;">
                        <div class="col-md-4">
                        	<h3 class="text-center"><label>Buscar por fecha</label></h3>
                            <table class="fullw">
                            	<tr>
                                    <td>
                                   		<div class="form-group">
                                          <span>Desde</span>
                                          <input name="desde" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <span>Hasta</span>
                                            <input name="hasta" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <div class="col-md-1">
                        <button class="btn btn-primary fullw" onclick="listar('lTorax');">Buscar</button>
                    </div>
                </div>
                <div id="lTorax" class="tabla col-md-12" data-tabla="listaTorax" data-form="toraxForm" data-titulo="exámenes"></div>
            </div>
        </div>
	</div>
    <div id="tabs-3">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Otros Exámenes <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
                <div class="row" style="margin:5px auto;min-height:1vh;">
                    <form role="form" class="filtros col-md-11 alert-info" onsubmit="return false;">
                        <div class="col-md-4">
                        	<h3 class="text-center"><label>Buscar por fecha</label></h3>
                            <table class="fullw">
                            	<tr>
                                    <td>
                                   		<div class="form-group">
                                          <span>Desde</span>
                                          <input name="desde" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <span>Hasta</span>
                                            <input name="hasta" type="text" class="form-control fecha" value="<?php echo date("Y-m-d"); ?>" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <div class="col-md-1">
                        <button class="btn btn-primary fullw" onclick="listar('lOtrosExam');">Buscar</button>
                    </div>
                </div>
                <div id="lOtrosExam" class="tabla col-md-12" data-tabla="listaOtrosExam" data-form="otrosForm" data-titulo="exámenes"></div>
            </div>
        </div>
	</div>
</div>