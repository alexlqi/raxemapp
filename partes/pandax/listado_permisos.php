<?php @session_start();
#@include_once "inc.config.php";
# si es llamado por ajax entonces cargar los defined vars
$idSusc=@$_SESSION["idSuscripcion"];
$pandas=$modelo->query2opt("select * from pandas where idSuscripcion=$idSusc;",array('idpanda','panda'));
?>
<script type="text/javascript">
$(document).ready(function(e) {
	$(".usuarios").change(function(e) {
        $.ajax({
			url:'<?php echo urlAjaxPartes("d_permisos.php"); ?>',
			type:'POST',
			cache:false,
			data:{
				p:$(this).find("option:selected").val(),
				idSusc:$(".idSusc").val(),
			},
			success: function(r){
				//console.log(r);
				$(".permisos").html(r);
			},
		});
    });
});
</script>
<div class="row">
	<form id="permisos1" role="form">
    	<input type="hidden" class="idSusc" name="idSuscripcion" value="<?php echo $_SESSION["idSuscripcion"]; ?>" />
	    <h2>Activar/Descativar permisos</h2>
		<div class="col-xs-6">
		    <div class="form-group">
		    	<select class="usuarios">
		        	<option disabled selected>Elige un usuario</option>
		        	<?php echo $pandas["data"]; ?>
		        </select>
		    </div>
		</div>
	    <div class="form-group permisos col-xs-12"></div>
	</form>
</div>