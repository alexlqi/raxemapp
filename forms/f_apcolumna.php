<?php
$optCatalogo=$modelo->query2opt("select * from catalogos;",array("clave","valor"),array("tree"=>"s"));
$catalogo=$modelo->query2arr("select grupo,subgrupo from catalogos;");
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
formFn["columnaForm"]=function(r){
	cerrarDialog();
	listar("lExamenesAjax");
}
</script>
<form id="columnaForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addColumna" />
    <input type="hidden" name="idResultado" />
    <input type="hidden" name="proyeccion" value="AP Y LAT COLUMNA LUMBAR" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <input type="text" name="cliente" />
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <input type="text" name="empresa" />
    </div>
    <div class="form-group">
    	<label>TIPO DE EXAMEN:</label>
        <select name="tipo"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
    </div>
    <div class="form-group">
    	<label>ESCOLIOSIS:</label>
        <select name="tipo"><?php echo $optCatalogo["data"]["EXAMENES"]["COLUMNA"]["ESCOLIOSIS"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FOLIO:</label>
        <input type="text" name="folio" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="folio" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico" type="text" name="folio" />
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
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>
<form id="toraxForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addTorax" />
    <input type="hidden" name="idResultado" />
    <div class="form-group">
    	<label>TIPO:</label>
        <select name="idConcepto"><?php echo $optCatalogo["TIPORX"]; ?></select>
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
<form id="otrosForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addOtros" />
    <input type="hidden" name="idResultado" />
    <div class="form-group">
    	<label>TIPO:</label>
        <select name="idConcepto"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
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