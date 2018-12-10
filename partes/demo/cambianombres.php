<script>
formFn["cambiarNombresPDFs"]=function(r){
	if(r.archivo!=""){
		url="/descargas/cambiarNombres/"+r.archivo;
		window.location.href=url;
	}
};
</script>
<div class="container">
	<div class="row">
    	<form id="cambiarNombresPDFs" class="inflow-media" method="post" action="<?php echo SCRIPT_URL; ?>s_rh.php">
            <input type="hidden" name="ctrl" value="cambiarNombresPDFs" />
            <div class="form-group col-md-6">
                <label for="f">Adjunta aquí el archivo</label><input type="file" name="f" multiple>
            </div>
            <div class="form-group col-md-6">
                <input class="btn btn-success" type="submit" value="cambiar" />
            </div>
        </form>
    </div>
</div>