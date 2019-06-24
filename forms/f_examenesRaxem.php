<?php
$sql="select
	concat(e.nombre,'-',s.nombre) as k,
	concat(e.nombre,'-',s.nombre) as v
from sucursales s
inner join empresas e on e.idEmpresa=s.idSucursal
;";
$clienteOpt=$modelo->query2opt($sql,array("k","v"));
$optCatalogo=$modelo->query2opt("select * from catalogos where activo=1;",array("clave","valor"),array("tree"=>"cgs","selected"=>0));
$columna=$modelo->query2arr("select categoria,grupo,subgrupo from catalogos where grupo='COLUMNA' and activo=1 group by subgrupo order by orden ASC;");
$torax=$modelo->query2arr("select categoria,grupo,subgrupo from catalogos where grupo='TORAX' and activo=1 group by subgrupo;");
$tagNames=array(
	'e'=>'ESCOLIOSIS',
	'r'=>'ROTACION',
	'bp'=>'BASCULAMIENTO PELVICO',
	'els'=>'EJE LUMBOSACRO',
	'be'=>'BALANCE ESPINAL',
	'eg3'=>'EJE GRAVEDAD L3',
	'cv'=>'CANTIDAD VERTEBRAS',
	'cvNivel'=>'cvNivel',
	'uls'=>'UNION LUMBOSACRA',
	'ce'=>'CIERRE ESPINOSAS',
	'a'=>'ARTROSIS',
	'eiv'=>'ESPACIOS IV',
	'eivNivel'=>'eivNivel',
	'cuv'=>'CUERPOS VERTEBRALES',
	'l'=>'LIGAMENTOS',
	'tb'=>'TEJIDOS BLANDOS',
	'h'=>'HALLAZGOS',
	'conclusion'=>'CONCLUSION',
	'comentario'=>'COMENTARIO',
	'resultado'=>'RESULTADO',
	'ht'=>'HALLAZGOS TORAX',
	'riescol'=>'RIESGO COLUMNA',
    'riestor'=>'RIESGO TORAX',
    'concradcol'=>'CONCLUSION RADIOLOGICA DE COLUMNA',
    'concradtor'=>'CONCLUSION RADIOLOGICA DE TORAX',
);

$colNivel=array(
	'eiv','cv',
);
$colOtro=array(
	'h','ht','ce','cuv','cv','l','tb','h','conclusion','conclusiont','comentario','resultado',
    'riescol','riestor',
);
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
formFn["preForm"]=function(r){
	cerrarDialog();
	listar("lPre");
}
formFn["columnaToraxForm"]=function(r){
	cerrarDialog();
	listar("lColumnaTorax");
}
formFn["columnaForm"]=function(r){
	cerrarDialog();
	listar("lColumna");
}
formFn["toraxForm"]=function(r){
	cerrarDialog();
	listar("lTorax");
}
formFn["otrosForm"]=function(r){
	cerrarDialog();
	listar("lOtrosExam");
}
$(document).on("keyup","form",function(e){
	switch(e.keyCode){
		case 37:
		break;
		case 39:
		break;
		default:
			//console.log(e.keyCode);
		break;
	}
});
</script>
<style>

</style>
<form id="preForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addPre" />
    <input type="hidden" name="idResultado" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <input class="form-control" type="text" name="cliente" class="requerido"/>
        <select name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>Proyección:</label>
        <select name="proyeccion" class="requerido form-control">
            <option value="AP Y LAT COLUMNA LUMBAR">AP Y LAT COLUMNA LUMBAR</option>
            <option value="AP Y LAT COLUMNA LUMBAR/TORAX">AP Y LAT COLUMNA LUMBAR/PA DE TORAX</option>
            <option value="PA DE TORAX">PA DE TORAX</option>
            <option value="OTRO">OTRO</option>
        </select>
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" class="fechaN requerido form-control" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <?php //<input type="text" name="empresa" class="requerido form-control" /> ?>
        <select name="empresa"><?php echo $empresasOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>TIPO DE EXAMEN:</label>
        <select name="tipoexamen" class="form-control"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FOLIO:</label>
        <input type="text" name="folio" class="form-control" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="nombre" class="form-control" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico" type="text" name="edad" class="form-control" />
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success form-control" value="Guardar" />
    </div>
</form>
<form id="columnaForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addColumna" />
    <input type="hidden" name="idResultado" />
    <input type="hidden" name="proyeccion" value="AP Y LAT COLUMNA LUMBAR" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <?php //<input type="text" name="cliente" class="requerido form-control"/> ?>
        <select name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" class="fechaN requerido form-control" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <input type="text" name="empresa" class="requerido form-control" />
    </div>
    <div class="form-group">
    	<label>TIPO DE EXAMEN:</label>
        <select name="tipoexamen" class="form-control"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FOLIO:</label>
        <input type="text" name="folio" class="form-control" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="nombre" class="form-control" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico form-control" type="text" name="edad" />
    </div>
    <div class="row">
    <?php
    foreach($columna["data"] as $r){
		$campo=array_search($r["subgrupo"],$tagNames);
	?>
    	<?php if(in_array($campo,$colOtro)){
			$func=$campo."Otro";
		?>
        <script type="text/javascript">
			$(document).ready(function(e) {
                $(".<?php echo $func; ?>").val($(".<?php echo $func; ?>Select").find('option:selected').val());
            });
			function <?php echo $func; ?>(e){
				_e=$(e);
				opt=_e.find('option:selected').val();
				if(opt=="OTRO" || opt=="OTROS"){
					$('.<?php echo $func; ?>').attr("type","text");
				}else{
					$('.<?php echo $func; ?>').attr("type","hidden").val(opt);
				}
			}
		</script>
			<?php if(in_array($campo,$colNivel)){?>
                <div class="col-xs-12 col-md-6">
                    <table class="fullw">
                        <tr>
                            <td class="form-group" width="80%">
                                <label><?php echo $r["subgrupo"]; ?>:</label>
                                <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                                <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                            </td>
                            <td class="form-group" width="20%">
                                <label>Nivel:</label>
                                <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
			<?php }else{ ?>
                <div class="form-group col-xs-12 col-md-6">
                    <label><?php echo $r["subgrupo"]; ?>:</label>
                    <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                    <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                </div>
            <?php } ?>
    	<?php }elseif(in_array($campo,$colNivel)){?>
        <div class="col-xs-12 col-md-6">
            <table class="fullw">
            	<tr>
                	<td class="form-group" width="80%">
                    	<label><?php echo $r["subgrupo"]; ?>:</label>
                    	<select class="form-control" name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                	</td>
                	<td class="form-group" width="20%">
                    	<label>Nivel:</label>
	                    <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                	</td>
                </tr>
            </table>
        </div>
        <?php }else{ ?>
    	<div class="form-group col-xs-12 col-md-6">
            <label><?php echo $r["subgrupo"]; ?>:</label>
            <select class="form-control" name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
        </div>
        <?php } ?>
	<?php } ?>
    </div>
    <div class="form-group col-xs-12 col-md-6">
        <label>Estatus:</label>
        <select name="estado" class="form-control">
        	<option value="0">Precaptura</option>
        	<option value="1">Pendiente</option>
        	<option value="2" selected="selected">Completo</option>
		</select>
    </div>
    <div class="form-group col-xs-12">
    	<label>Comentarios:</label>
        <textarea class="form-control reseteable" name="comentario"></textarea>
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success form-control" value="Guardar" />
    </div>
</form>
<form id="columnaToraxForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addColumnaTorax" />
    <input type="hidden" name="idResultado" />
    <input type="hidden" name="proyeccion" value="AP Y LAT COLUMNA LUMBAR/TORAX" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <?php //<input type="text" name="cliente" class="requerido form-control"/> ?>
        <select name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" class="fechaN requerido" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <input type="text" name="empresa" class="requerido" />
    </div>
    <div class="form-group">
    	<label>TIPO DE EXAMEN:</label>
        <select name="tipoexamen"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FOLIO:</label>
        <input type="text" name="folio" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="nombre" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico" type="text" name="edad" />
    </div>
    <div class="row">
    <?php
    foreach($columna["data"] as $r){
		$campo=array_search($r["subgrupo"],$tagNames);
	?>
    	<?php if(in_array($campo,$colOtro)){
			$func=$campo."Otro";
		?>
        <script type="text/javascript">
			$(document).ready(function(e) {
                $(".<?php echo $func; ?>").val($(".<?php echo $func; ?>Select").find('option:selected').val());
            });
			function <?php echo $func; ?>(e){
				_e=$(e);
				opt=_e.find('option:selected').val();
				if(opt=="OTRO" || opt=="OTROS"){
					$('.<?php echo $func; ?>').attr("type","text");
				}else{
					$('.<?php echo $func; ?>').attr("type","hidden").val(opt);
				}
			}
		</script>
			<?php if(in_array($campo,$colNivel)){?>
                <div class="col-xs-12 col-md-6">
                    <table class="fullw">
                        <tr>
                            <td class="form-group" width="80%">
                                <label><?php echo $r["subgrupo"]; ?> (Col):</label>
                                <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                                <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                            </td>
                            <td class="form-group" width="20%">
                                <label>Nivel:</label>
                                <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
			<?php }else{ ?>
                <div class="form-group col-xs-12 col-md-6">
                    <label><?php echo $r["subgrupo"]; ?> (Col):</label>
                    <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                    <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                </div>
            <?php } ?>
    	<?php }elseif(in_array($campo,$colNivel)){?>
        <div class="col-xs-12 col-md-6">
            <table class="fullw">
            	<tr>
                	<td class="form-group" width="80%">
                    	<label><?php echo $r["subgrupo"]; ?> (Col):</label>
                    	<select class="form-control" name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                	</td>
                	<td class="form-group" width="20%">
                    	<label>Nivel:</label>
	                    <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                	</td>
                </tr>
            </table>
        </div>
        <?php }else{ ?>
    	<div class="form-group col-xs-12 col-md-6">
            <label><?php echo $r["subgrupo"]; ?> (Col):</label>
            <select name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
        </div>
        <?php } ?>
	<?php } ?>
    </div>
    <div class="row">
    <?php
    foreach($torax["data"] as $r){
		$campo=array_search($r["subgrupo"],$tagNames);
	?>
    	<?php if(in_array($campo,$colOtro)){
			$func=$campo."Otro";
		?>
        <script type="text/javascript">
			$(document).ready(function(e) {
                $(".<?php echo $func; ?>").val($(".<?php echo $func; ?>Select").find('option:selected').val());
            });
			function <?php echo $func; ?>(e){
				_e=$(e);
				opt=_e.find('option:selected').val();
				if(opt=="OTRO" || opt=="OTROS"){
					$('.<?php echo $func; ?>').attr("type","text");
				}else{
					$('.<?php echo $func; ?>').attr("type","hidden").val(opt);
				}
			}
		</script>
			<?php if(in_array($campo,$colNivel)){?>
                <div class="col-xs-12 col-md-6">
                    <table class="fullw">
                        <tr>
                            <td class="form-group" width="80%">
                                <label><?php echo $r["subgrupo"]; ?>:</label>
                                <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                                <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                            </td>
                            <td class="form-group" width="20%">
                                <label>Nivel:</label>
                                <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
			<?php }else{ ?>
                <div class="form-group col-xs-12 col-md-6">
                    <label><?php echo $r["subgrupo"]; ?>:</label>
                    <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                    <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                </div>
            <?php } ?>
    	<?php }elseif(in_array($campo,$colNivel)){?>
        <div class="col-xs-12 col-md-6">
            <table class="fullw">
            	<tr>
                	<td class="form-group" width="80%">
                    	<label><?php echo $r["subgrupo"]; ?>:</label>
                    	<select class="form-control" name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                	</td>
                	<td class="form-group" width="20%">
                    	<label>Nivel:</label>
	                    <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                	</td>
                </tr>
            </table>
        </div>
        <?php }else{ ?>
    	<div class="form-group col-xs-12 col-md-6">
            <label><?php echo $r["subgrupo"]; ?>:</label>
            <select name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
        </div>
        <?php } ?>
	<?php } ?>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-md-6">
            <label>Estatus:</label>
            <select name="estado">
                <option value="0">Precaptura</option>
                <option value="1">Pendiente</option>
                <option value="2" selected="selected">Completo</option>
            </select>
        </div>
    </div>
    <div class="form-group col-xs-12">
    	<label>Comentarios:</label>
        <textarea class="form-control reseteable" name="comentario"></textarea>
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>
<form id="toraxForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addTorax" />
    <input type="hidden" name="idResultado" />
    <input type="hidden" name="proyeccion" value="PA DE TORAX" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <?php //<input type="text" name="cliente" class="requerido form-control"/> ?>
        <select name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" class="fechaN requerido" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <input type="text" name="empresa" class="requerido" />
    </div>
    <div class="form-group">
    	<label>TIPO DE EXAMEN:</label>
        <select name="tipoexamen"><?php echo $optCatalogo["data"]["EXAMENES"]["FORM"]["TIPORX"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FOLIO:</label>
        <input type="text" name="folio" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="nombre" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico" type="text" name="edad" />
    </div>
    <div class="row">
    <?php
    foreach($torax["data"] as $r){
		$campo=array_search($r["subgrupo"],$tagNames);
	?>
    	<?php if(in_array($campo,$colOtro)){
			$func=$campo."Otro";
		?>
        <script type="text/javascript">
			$(document).ready(function(e) {
                $(".<?php echo $func; ?>").val($(".<?php echo $func; ?>Select").find('option:selected').val());
            });
			function <?php echo $func; ?>(e){
				_e=$(e);
				opt=_e.find('option:selected').val();
				if(opt=="OTRO" || opt=="OTROS"){
					$('.<?php echo $func; ?>').attr("type","text");
				}else{
					$('.<?php echo $func; ?>').attr("type","hidden").val(opt);
				}
			}
		</script>
			<?php if(in_array($campo,$colNivel)){?>
                <div class="col-xs-12 col-md-6">
                    <table class="fullw">
                        <tr>
                            <td class="form-group" width="80%">
                                <label><?php echo $r["subgrupo"]; ?>:</label>
                                <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                                <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                            </td>
                            <td class="form-group" width="20%">
                                <label>Nivel:</label>
                                <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
			<?php }else{ ?>
                <div class="form-group col-xs-12 col-md-6">
                    <label><?php echo $r["subgrupo"]; ?>:</label>
                    <select class="form-control <?php echo $func; ?>Select" onchange="<?php echo $func; ?>(this);"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                    <input class="form-control <?php echo $func; ?>" type="hidden" name="<?php echo $campo; ?>" placeholder="Especifique..." />
                </div>
            <?php } ?>
    	<?php }elseif(in_array($campo,$colNivel)){?>
        <div class="col-xs-12 col-md-6">
            <table class="fullw">
            	<tr>
                	<td class="form-group" width="80%">
                    	<label><?php echo $r["subgrupo"]; ?>:</label>
                    	<select class="form-control" name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
                	</td>
                	<td class="form-group" width="20%">
                    	<label>Nivel:</label>
	                    <input class="form-control reseteable" type="text" name="<?php echo $campo."Nivel"; ?>" />
                	</td>
                </tr>
            </table>
        </div>
        <?php }else{ ?>
    	<div class="form-group col-xs-12 col-md-6">
            <label><?php echo $r["subgrupo"]; ?>:</label>
            <select name="<?php echo $campo; ?>"><?php echo $optCatalogo["data"][$r["categoria"]][$r["grupo"]][$r["subgrupo"]]; ?></select>
        </div>
        <?php } ?>
	<?php } ?>
    </div>
    <div class="form-group col-xs-12">
    	<label>Comentarios:</label>
        <textarea class="form-control reseteable" name="comentario"></textarea>
    </div>
    <div class="form-group col-xs-12 col-md-6">
        <label>Estatus:</label>
        <select name="estado">
        	<option value="0">Precaptura</option>
        	<option value="1">Pendiente</option>
        	<option value="2" selected="selected">Completo</option>
		</select>
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>
<form id="otrosForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addOtros" />
    <input type="hidden" name="idResultado" />
    <div class="form-group">
    	<label>PROYECCION:</label>
        <input type="text" name="proyeccion" value="OTRO" />
    </div>
	<div class="form-group">
    	<label>CLIENTE:</label>
        <?php //<input type="text" name="cliente" class="requerido form-control"/> ?>
        <select name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>FECHA:</label>
        <input type="text" name="fecha" class="fechaN requerido" />
    </div>
    <div class="form-group">
    	<label>EMPRESA:</label>
        <input type="text" name="empresa" class="requerido" />
    </div>
    <div class="form-group">
    	<label>MOTIVO DE EXAMEN:</label>
        <input type="text" name="tipoexamen" />
    </div>
    <div class="form-group col-md-8">
    	<label>NOMBRE:</label>
        <input type="text" name="nombre" />
    </div>
    <div class="form-group col-md-4">
    	<label>EDAD:</label>
        <input class="numerico" type="text" name="edad" />
    </div>
    <div class="form-group">
    	<label>Hallazgos:</label>
        <textarea class="form-control reseteable" name="h"></textarea>
    </div>
    <div class="form-group">
    	<label>Comentarios:</label>
        <textarea class="form-control reseteable" name="comentario"></textarea>
    </div>
    <div class="form-group col-xs-12 col-md-6">
        <label>Estatus:</label>
        <select name="estado">
        	<option value="0">Precaptura</option>
        	<option value="1">Pendiente</option>
        	<option value="2" selected="selected">Completo</option>
		</select>
    </div>
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>