<?php
$usuarios=$permisos->getPandas();
$usuariosOpt=$modelo->array2opt($usuarios["data"],array("idpanda","nombre"));
?>
<script>
formFn["medicosForm"]=function(r){
	cerrarDialog();
	listar("lMedicos");
}
</script>
<form id="medicosForm" role="form" method="post" class="inflow-media col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	  <input type="hidden" name="ctrl" value="addMed" />
	  <input type="hidden" name="idMedico" />
	<div class="form-group">
   	  <label>Nombre Completo:</label>
        <input type="text" name="nombre" class="requerido"/>
    </div>
    <div class="form-group">
    	<label>Cédula Profesional:</label>
        <input type="text" name="cedula" class="requerido numerico"/>
    </div>
    <div class="form-group">
    	<label>Firma:</label>
        <input type="file" name="firma" class="requerido" />
    </div>
    <div class="form-group">
    	<label>Usuario en sistema:</label>
        <select name="idUsuario" class="requerido">
        	<option value="0">--Elige un usuario--</option>
            <?php echo $usuariosOpt; ?>
        </select>
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>