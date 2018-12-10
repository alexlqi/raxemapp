<?php
@include_once "inc.config.php";
$dsnModelo=$dsnPmRH;
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
	echo $formas->formCall("personas");
	echo $formas->formCall("contratos");
?>
</div>
<div class="tabs">
    <ul>
    	<li><a href="#tabs-1">Personas</a></li>
        <li><a href="#tabs-2">Contratos</a></li>
    </ul>
    <div id="tabs-1">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Personas</h2>
                <div id="lPersonasAjax" class="tabla col-md-12" data-tabla="listaPersonas" data-form="personas" data-titulo="persona"></div>
            </div>
        </div>
    </div>
    <div id="tabs-2">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Contratos</h2>
                <div id="lContratosAjax" class="tabla col-md-12" data-tabla="listaContratos" data-form="contratos" data-titulo="contrato"></div>
            </div>
        </div>
	</div>
</div>