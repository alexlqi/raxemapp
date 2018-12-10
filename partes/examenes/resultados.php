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
<script>
showCols["listaColumnaTorax"]=showCols["listaColumna"]=showCols["listaTorax"]=showCols["listaOtrosExam"]=[0,1,2,3,4,5,6,7,8];
$(document).ready(function(e) {
    //generarTablas(toggleChk);
	generarTablas();
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
<div class="tabs body-wrap">
    <ul class="ulTabWrap">
    	<li><a href="#tabs-1">Resultados</a></li>
    </ul>
    <div id="tabs-1">
        <h2>Resultados <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
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
        <div id="lResultados" class="tabla col-md-12" data-tabla="listaColumna" data-form="columnaForm" data-titulo="exámenes"></div>
    </div>
</div>