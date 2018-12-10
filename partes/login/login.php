<?php
// $params viene desde el loadInclude en la clase view
//siempre usar @ para evitar que surjan errores por no encontrar el offset en la variable $params
$login = "scripts/s_login.php?redir=".@$params["redir"];
?>
<script type="text/javascript">
$(document).ready(function(e) {
    //alerta("titulo","descripcion");
	//notificacion({content:'mensaje'});
	
});
</script>
<style>
.form-group{
	text-align:center;
}
form{
	background-color:rgba(255,255,255,0.85);
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
}
</style>
<div class="completo vertical-align loginbg">
    <div class="container">
    	<div class="row">
            <form role="form centradoh" class="col-xs-12 col-md-4 col-md-offset-4" action="<?php echo $login; ?>" method="post">
                <input type="hidden" name="ctrl" value="login" />
                <div class="form-group">
                    <h2 align="center"><strong>Raxem</strong> Login</h2>
                </div>
                <div class="form-group">
                    <label>Usuario:</label>
                    <input type="text" name="user" />
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="pass" />
                </div>
                <div class="form-group">
                    <input class="boton-1 btn btn-default waves-effect waves-light" type="submit" value="Ingresar" />
                </div>
            </form>
		</div>
    </div>
</div>