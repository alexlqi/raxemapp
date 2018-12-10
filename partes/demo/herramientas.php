<?php
#@include_once "inc.config.php";
#$dsnModelo=$dsnPmRHVacantes;
#@include(CLASS_PATH."class.modelo.php");
#@include(CLASS_PATH."class.table.php");
#@include_once(FUNC_DIR."tablas.php");
?>
<script>
$(document).ready(function(e){
});
formFn["partirNombres"]=function(r){
	if(!r.err){
		$("#resultPartir").html(r.tabla);
	}
}
</script>
<div class="tabs">
    <ul>
    	<li><a href="#tabs-1">Cambiar Nombre PDFs</a></li>
        <li><a href="#tabs-2">Partir Nombres</a></li>
    </ul>
    <div id="tabs-1"><?php @include("cambianombres.php"); ?></div>
    <div id="tabs-2">
    	<div class="container">
        	<form id="partirNombres" class="inflow col-md-4" role="form" method="post" action="<?php echo SCRIPT_URL; ?>s_rh.php">
            	<input type="hidden" name="ctrl" value="partirNombres" />
                <div class="form-group col-md-12">
                	<label>Escriba aquí los nombres a separar por renglón:</label>
                    <textarea name="nn"></textarea>
                </div>
                <div class="col-md-12">
                	<input type="submit" class="btn btn-success" />
                </div>
            </form>
            <div class="col-md-8">
            	<h2>Resultado:</h2>
            	<div id="resultPartir"></div>
            </div>
        </div>
    </div>
</div>