<?php
$idSusc=$_SESSION["idSuscripcion"];
@include_once "inc.config.php";
$dsnModelo=$dsnPandaRW;
@include(CLASS_PATH."class.modelo.php");
$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
$vistaPerm=array(
	"sec"=>$modelo->query2opt("select * from permisos where idSuscripcion=$idSusc and tipo='sec';",array("permiso","nombre")),
	"sub"=>$modelo->query2opt("select * from permisos where idSuscripcion=$idSusc and tipo='sub'",array("permiso","nombre")),
	"pes"=>$modelo->query2opt("select * from permisos where idSuscripcion=$idSusc and tipo='tab'",array("permiso","nombre")),
);
?>
<script type="text/javascript"><?php include("administracion.js"); ?></script>
<script type="text/javascript">
$(document).ready(function(e) {
});
function modForm(e,selector){
	$(selector).html('');
	$("#"+$(e).find(":selected").val()+"Elem").clone().appendTo(selector);
	fluidDialog();
}
</script>
<div id="formularios" style="width:0;height:0;display:none;">
    <div id="secElem">
    </div>
    <div id="subElem">
        <div id="seccion" class="form-group">
            <label>Sección</label>
            <?php if(!$vistaPerm["sec"]["err"]){ ?>
            <select name="sec"><option disabled="disabled" selected="selected">---Elige---</option><?php echo $vistaPerm["sec"]["data"]; ?></select>
            <?php } ?>
        </div>
    </div>
    <div id="tabElem">
        <div id="seccion" class="form-group">
            <label>Sección</label>
            <?php if(!$vistaPerm["sec"]["err"]){ ?>
            <select name="sec"><option disabled="disabled" selected="selected">---Elige---</option><?php echo $vistaPerm["sec"]["data"]; ?></select>
            <?php } ?>
        </div>
        <div id="subseccion" class="form-group">
            <label>Subsección</label>
            <?php if(!$vistaPerm["sub"]["err"]){ ?>
            <select name="sub"><option disabled="disabled" selected="selected">---Elige---</option><?php echo $vistaPerm["sub"]["data"]; ?></select>
            <?php } ?>
        </div>
    </div>
    <form id="permisos" role="form" class="inflow col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_permisos.php" onsubmit="submitForm(this);">
        <input type="hidden" name="ctrl" value="ns" />
        <input type="hidden" name="idPermiso" value="0" />
        <input type="hidden" class="idSusc" name="idSuscripcion" value="<?php echo $_SESSION["idSuscripcion"]; ?>" />
        <div class="form-group">
            <label>Tipo Permiso</label>
            <select name="tipo" onchange="modForm(this,'.permElem');">
                <option disabled="disabled" selected="selected">Elige un tipo</option>
                <option disabled="disabled">---Vista---</option>
                <option value="sec">Sección</option>
                <option value="sub">Modulo</option>
                <option value="tab">Pestaña</option>
                <option disabled="disabled">---Modelo---</option>
                <option value="tabla">Tabla</option>
            </select>
        </div>
        <div id="clave" class="form-group">
            <label>Clave del Permiso</label>
            <input class="autocompletar" data-autocomplete="autocomplete1" type="text" name="permiso" placeholder="Clave del permiso" />
        </div>
        <div class="permElem"></div>
        <div class="form-group">
            <label>Nombre del permiso</label>
            <input class="autocompletar" data-autocomplete="autocomplete1" type="text" name="nombre" placeholder="Nombre del permiso" />
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea class="autocomplete-descripcion" name="descripcion"></textarea>
        </div>
        <div class="form-group">
            <input class="btn btn-default waves-effect waves-light" type="submit" value="guardar" />
        </div>
    </form>
</div>
<div class="container fullh">
<div class="tabs">
    <ul>
    	<li><a href="#tabs-2">Permisos</a></li>
        <li><a href="#tabs-3">Activar/Desactivar</a></li>
    </ul>
	<div id="tabs-2">
	<?php if($_SESSION["idSuscripcion"]==0){ ?>
    	<div class="row">
            <div class="col-md-12">
                <h2>Suscripciones</h2>
                <select class="idSuscripcion">
                    <option disabled="disabled" selected="selected">---Elige una suscripción---</option>
                    <?php echo $modelo->query2opt("SELECT idSuscripcion, nombre FROM suscripciones;",array('idSuscripcion', 'nombre'))["data"]; ?>
                </select>
            </div>
        </div>
    <?php } ?>
         <div class="row">
            <h2>Listado de Permisos</h2>
            <div id="lPermisosAjax" class="tabla col-md-12" data-tabla="listaPermisos" data-form="permisos" data-titulo="Permiso"></div>
        </div>
    </div>
    <div id="tabs-3">
    	<div class="row">
            <div class="listado_permiso col-md-12">
                <?php include_once("listado_permisos.php");?>
            </div>
        </div>
    </div>
</div>