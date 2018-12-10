<?php
@include_once "inc.config.php";
$dsnModelo=$dsnPandaRW;
@include(CLASS_PATH."class.modelo.php");
@include(CLASS_PATH."class.table.php");
@include(FUNC_DIR."tablas.php");
$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
$tabla=new tables;
?>	
<script type="text/javascript"><?php include("administracion.js"); ?></script>
<div id="formularios" style="display:none;">
    <form id="pandaxUsers" role="form" class="inflow col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_usuarios.php">
        <h2>Crear usuario</h2>
        <input type="hidden" name="ctrl" value="nu" />
        <input type="hidden" class="idSusc" name="idpanda" />
        <input type="hidden" class="idSusc" name="idSuscripcion" value="<?php echo $_SESSION["idSuscripcion"]; ?>" />
        <div class="form-group">
            <label>Usuario</label>
            <input class="" type="text" name="panda" placeholder="Nombre de usuario" />
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input class="" type="text" name="pandita" />
        </div>
        <div class="form-group">
            <label>Tipo Usuario</label>
            <select name="tipoUser">
                <option value="3">Usuario</option>
                <option value="1">Super Usuario</option>
                <option value="2">Administrador</option>
                <option value="4">Invitado</option>
                <option value="5">Permisos de ejecución</option>
            </select>
        </div>
        <div class="form-group">
            <label>Nombre</label>
            <input class="" type="text" name="nombre" />
        </div>
        <div class="form-group">
            <input class="btn btn-default waves-effect waves-light" type="submit" value="guardar" />
        </div>
    </form>
</div>
<div class="container body-container">
	<div class="row">
    	<?php if($_SESSION["idSuscripcion"]==0){ ?>
    	<div class="col-md-12">
	        <h2>Suscripciones</h2>
        	<select class="idSuscripcion">
            	<option disabled="disabled" selected="selected">---Elige una suscripción---</option>
				<?php echo $modelo->query2opt("SELECT idSuscripcion, nombre FROM suscripciones;",array('idSuscripcion', 'nombre'))["data"]; ?>
            </select>
        </div>
        <?php } ?>
        <div class="col-md-12">
        	<h2>Listado de usuarios</h2>
            <div id="luAjax" class="tabla row" data-tabla="usuarios" data-form="pandaxUsers" data-titulo="usuario"></div>
        </div>
    </div>
</div>