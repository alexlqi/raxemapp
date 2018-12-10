<?php
$optConceptos=$modelo->query2opt("select * from conceptos;",array("idConcepto","nombre"));
$optEmpleados=$modelo->query2opt("select c.*,concat(p.nombre,' ',p.paterno,' ',p.materno) as nombrec from contratos c inner join personas p on p.idPersona=c.idPersona order by nombrec;",array("idContrato","nombrec"));
$optClientes=$modelo->query2opt("select CLIENTE_ID,NOMBRE from c_clientes order by NOMBRE;",array("CLIENTE_ID","NOMBRE"));
$optSemanas="";
for($i=1;$i<=52;$i++){
	$semana=str_pad($i,2,"0",STR_PAD_LEFT);
	$optSemanas.="<option value=\"{$semana}\">Semana {$semana}</option>";
}
$optDias="";
$arrDias=array(1=>"Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo");
for($i=1;$i<=7;$i++){
	$optDias.="<option value=\"{$i}\">{$arrDias["$i"]}</option>";
}
?>
<script>
formFn["tarifasForm"]=function(r){
	cerrarDialog();
	listar("lTarifasAjax");
}
</script>
<form id="tarifasForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_rh.php">
	<input type="hidden" name="ctrl" value="addTarifas" />
    <input type="hidden" name="idTarifa" />
    <div class="form-group">
    	<label>Concepto:</label>
        <select name="idConcepto"><?php echo $optConceptos["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>Empleado:</label>
        <select name="idContrato"><option value="0">Todos</option><?php echo $optEmpleados["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>Cliente:</label>
        <select name="idCliente"><?php echo $optClientes["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>Fecha:</label>
        <input type="text" class="fechaN" name="fecha" />
    </div>
    <div class="form-group">
    	<label>Semana:</label>
        <select name="semana"><option value="T">Todos</option><?php echo $optSemanas; ?></select>
    </div>
    <div class="form-group">
    	<label>Día:</label>
        <select name="dia"><option value="T">Todos</option><?php echo $optDias; ?></select>
    </div>
    <div class="form-group">
    	<label>Monto:</label>
        <input type="text" class="numerico" name="monto" />
    </div>
    <?php if(@$params){?>
        <input type="hidden" name="estatus" value="0" />
    <?php }else{ ?>
    <div class="form-group">
    	<label>Estatus:</label>
        <select name="estatus"><option value="1">Activo</option><option value="0">Inactivo</option></select>
    </div>
    <?php } ?>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>