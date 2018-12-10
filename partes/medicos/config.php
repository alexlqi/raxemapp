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
$(document).ready(function(e) {
    //generarTablas(toggleChk);
	generarTablas();
});
</script>
<div id="formularios" style="display:none;">
<?php
	echo $formas->formCall("medicos");
?>
</div>
<div class="tabs body-wrap">
    <ul>
    	<li><a href="#tabs-1">Médicos</a></li>
    </ul>
    <div id="tabs-1">
    	<div class="container-fluid">
            <div class="row">
            	<h2>Listado de Médicos</h2>
                <div id="lMedicos" class="tabla col-md-12" data-tabla="listaMedicos" data-form="medicosForm" data-titulo="medicos"></div>
            </div>
        </div>
    </div>
</div>