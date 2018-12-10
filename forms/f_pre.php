<?php
$sql="select
	concat(e.nombre,'-',s.nombre) as k,
	concat(e.nombre,'-',s.nombre) as v
from sucursales s
inner join empresas e on e.idEmpresa=s.idSucursal
;";
$clienteOpt=$modelo->query2opt($sql,array("k","v"));
$optCatalogo=$modelo->query2opt("select * from catalogos;",array("clave","valor"),array("tree"=>"cgs"));
$columna=$modelo->query2arr("select categoria,grupo,subgrupo from catalogos where grupo='COLUMNA' group by subgrupo order by orden ASC;");
$torax=$modelo->query2arr("select categoria,grupo,subgrupo from catalogos where grupo='TORAX' group by subgrupo;");
$tagNames=array(
	'e'=>'ESCOLIOSIS',
	'r'=>'ROTACION',
	'bp'=>'BASCULAMIENTO PELVICO',
	'els'=>'EJE LUMBOSACRO',
	'be'=>'BALANCE ESPINAL',
	'eg3'=>'EJE GRAVEDAD L3',
	'cv'=>'CANTIDAD VERTEBRAS',
	'uls'=>'UNION LUMBOSACRA',
	'ce'=>'CIERRE ESPINOSAS',
	'a'=>'ARTROSIS',
	'eiv'=>'ESPACIOS IV',
	'cuv'=>'CUERPOS VERTEBRALES',
	'l'=>'LIGAMENTOS',
	'tb'=>'TEJIDOS BLANDOS',
	'h'=>'HALLAZGOS',
	'conclusion'=>'CONCLUSION',
	'comentario'=>'COMENTARIO',
	'resultado'=>'RESULTADO',
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
</script>
<form id="preForm" role="form" method="post" class="inflow col-md-12" action="<?php echo SCRIPT_URL; ?>s_examenes.php">
	<input type="hidden" name="ctrl" value="addPre" />
    <input type="hidden" name="idResultado" />
    <div class="form-group">
    	<label>CLIENTE:</label>
        <?php //<input type="text" name="cliente" class="requerido form-control"/> ?>
        <select class="requerido" name="cliente"><?php echo $clienteOpt["data"]; ?></select>
    </div>
    <div class="form-group">
    	<label>PROYECCIÓN:</label>
        <select name="proyeccion" class="requerido">
            <option value="AP Y LAT COLUMNA LUMBAR">AP Y LAT COLUMNA LUMBAR</option>
            <option value="AP Y LAT COLUMNA LUMBAR/TORAX">AP Y LAT COLUMNA LUMBAR/TORAX</option>
            <option value="PA DE TORAX">PA DE TORAX</option>
            <option value="OTRO">OTRO</option>
        </select>
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
    <div class="form-group text-right">
    	<input type="submit" class="btn btn-success" value="Guardar" />
    </div>
</form>