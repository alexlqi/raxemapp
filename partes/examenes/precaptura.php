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
if($permiso!==true){
	echo basename(__FILE__, '.php').$permiso;return;}
?>
<script>
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
	echo $formas->formCall("pre");
?>
</div>
<div class="tabs body-wrap">
    <ul>
    	<li><a href="#tabs-1">Pre Captura</a></li>
    </ul>
    <div id="tabs-1">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Registro de exámenes a interpretar <small class="refresh-table btn btn-info"><span class="glyphicon glyphicon-refresh"></span></small></h2>
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
                        <button class="btn btn-primary fullw" onclick="listar('lPre');">Buscar</button>
                    </div>
                </div>
                <div id="lPre" class="tabla col-md-12" data-tabla="listaPre" data-form="preForm" data-titulo="paciente"></div>
            </div>
        </div>
    </div>
</div>