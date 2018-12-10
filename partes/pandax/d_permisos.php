<?php @session_start();
//variables de configuración para as clases
include "../inc.config.php"; ## se añade solo para los XML

$dsnModelo=$dsnPandaRW;
include(CLASS_PATH."class.modelo.php");
include(CLASS_PATH."class.permisos.php");

//comprobación pposterior a las clases
if(@$_POST["p"]!=""){
	$panda=$_POST["p"];
	$idSusc=$_POST["idSusc"];
}else{
	exit;
}

$permisos=$modelo->query2array("call sp_getPermAuth($panda,$idSusc);");	

$permArr=array();
if(!$permisos["err"]){
	foreach($permisos["data"] as $d){
		// usuario -> idperm -> datos permiso
		$permArr[$d["idPanda"]][$d["idPermiso"]]=$d;
		$user=$d["panda"];
	}
}
//vardump($permArr);
$permOutline=array();
foreach($permArr as $idPanda=>$p){
	foreach($p as $idPerm=>$pp){
		if(!in_array($pp["tipo"],array("sec","sub","tab"))){continue;}
		$permOutline[$idPerm]["idPermiso"]=$pp["idPermiso"];
		$permOutline[$idPerm]["permiso"]=$pp["permiso"];
		$permOutline[$idPerm]["nombre"]=$pp["nombre"];
		$permOutline[$idPerm]["descripcion"]=$pp["descripcion"];
		$permOutline[$idPerm]["estado"]=$pp["estado"];
		$permOutline[$idPerm]["tipo"]=$pp["tipo"];
		$permOutline[$idPerm]["sec"]=$pp["seccion"];
		$permOutline[$idPerm]["sub"]=$pp["modulo"];
		$permOutline[$idPerm]["tab"]=$pp["tab"];
	}
}
function null2blank(&$v,$k){
	$v=(is_null($v)) ? '' : $v ;
}
function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}

array_walk_recursive($permOutline,'null2blank');
$permTmp=array();
foreach($permOutline as $d){
	if($d["tipo"]=="sec"){
		$permTmp[$d["sec"]][$d["idPermiso"]]=$d;
	}elseif($d["tipo"]=="sub"){
		$permTmp[$d["sec"]][$d["permiso"]][$d["idPermiso"]]=$d;
	}elseif($d["tipo"]=="tab"){
		$permTmp[$d["sec"]][$d["sub"]][$d["permiso"]][$d["idPermiso"]]=$d;
	}
}
//vardump($permTmp);
function makeOutline(&$arr,&$r){
	foreach($arr as $k=>$d){
		if(!isset($d["idPermiso"])){
			makeOutline($d,$r);
		}else{
			$r[$k]=$d;
		}
	}
}
$outline=array();
makeOutline($permTmp,$outline);
//configs
$chkArr=array(' ',' checked="checked" ');
?>

<script type="text/javascript">
var scriptPath='<?php echo SCRIPT_URL; ?>';
$(document).ready(function(e) {
	$(".authChecks").accordion({
		header:'h4',
		active:false,
		collapsible:true,
		heightStyle: "content",
	});
	$(".permisoVista").change(function(e) {
		e.stopPropagation();
		e.stopImmediatePropagation();
		perm=$(this);
		user=$(".usuarios").find("option:selected").val();
		actual=!perm.is(":checked");
        data={ctrl:'m',tipo:'vista',idPanda:user,idPerm:perm.val(),st:(perm.is(":checked"))?1:0};
		$.ajax({
			url:scriptPath+'s_permisos.php',
			type:'POST',
			cache:false,
			data:data,
			success: function(r){
				notificacion({content:r.msg});
				if(!r.err){
					//si no hubo error
				}else{
					//si hubo error
					perm.prop("checked",actual);
				}
			}
		});
    });
	$(".permisoTabla").change(function(e) {
		e.stopPropagation();
		e.stopImmediatePropagation();
		perm=$(this);
		user=$(".usuarios").find("option:selected").val();
		actual=!perm.is(":checked");
        data={ctrl:'m',tipo:'tabla',p:perm.data('p'),idPanda:user,idPerm:perm.val(),st:(perm.is(":checked"))?1:0};
		$.ajax({
			url:scriptPath+'s_permisos.php',
			type:'POST',
			cache:false,
			data:data,
			success: function(r){
				notificacion({content:r.msg});
				if(!r.err){
					//si no hubo error
				}else{
					//si hubo error
					perm.prop("checked",actual);
				}
			}
		});
    });
});
</script>
<style>
.authChecks{
	padding-right: 5px;
	padding-left: 5px;
}
.secRow{
	padding-left:0;
}
.subRow{
	padding-left:3%;
}
.tabRow{
	padding-left:6%;
}
</style>
<div class="row">
	<h3>Usuario: <?php echo $user; ?></h3>
</div>
<div class="row">
    <div class="form-group col-md-6">
    	<h2>Permisos para Secciones</h2>
        <?php 
        $chkbox=0;
        foreach($outline as $idPerm=>$dPerm){
        ?>
            <div class="checkbox <?php echo "{$dPerm["tipo"]}Row"; ?>">
                <input id="chkbox_<?php echo $chkbox; ?>" type="checkbox" class="permisoVista" value="<?php echo @$idPerm; ?>" <?php echo @$chkArr[$dPerm["estado"]]; ?> />
                <label for="chkbox_<?php echo $chkbox; ?>"><abbr title="<?php echo @$dPerm["descripcion"] ?>"><?php echo @$dPerm["nombre"]; ?></abbr></label>
            </div>
        <?php $chkbox++; 
		} ?>
    </div>
    <div class="form-group col-md-6">
	    <h2>Permisos para Tablas</h2>
        <div class="table-responsive">
            <table class="table">
            <tr>
            	<th>Tabla</th>
                <th>Ver</th>
                <th>Insertar</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
            <?php 
            $chkbox=0;
            $chkGroup=array();
            foreach($permArr as $idpanda=>$perm){ ?>
                <?php foreach($perm as $idperm=>$dPerm){
                    if(!in_array($dPerm["tipo"],array("tabla"))){continue;}
                ?>
                    <abbr title="<?php echo @$dPerm["descripcion"] ?>"><tr>
                        <td>
                            <span><?php echo $dPerm["nombre"]; ?></span>
                        </td>
                        <td>
                            <input id="chkbox_<?php echo $chkbox; ?>" type="checkbox" class="permisoTabla" data-p="ver" value="<?php echo @$idperm; ?>" <?php echo @$chkArr[$dPerm["ver"]]; ?> />
                        </td>
                        <td>
                            <input id="chkbox_<?php echo $chkbox; ?>" type="checkbox" class="permisoTabla" data-p="insertar" value="<?php echo @$idperm; ?>" <?php echo @$chkArr[$dPerm["insertar"]]; ?> />
                        </td>
                        <td>
                            <input id="chkbox_<?php echo $chkbox; ?>" type="checkbox" class="permisoTabla" data-p="editar" value="<?php echo @$idperm; ?>" <?php echo @$chkArr[$dPerm["editar"]]; ?> />
                        </td>
                        <td>
                            <input id="chkbox_<?php echo $chkbox; ?>" type="checkbox" class="permisoTabla" data-p="eliminar" value="<?php echo @$idperm; ?>" <?php echo @$chkArr[$dPerm["eliminar"]]; ?> />
                        </td>
                    </tr></abbr>
                <?php //$chkGroup[$dPerm["tipo"]][]=trim(ob_get_clean()); 
                $chkbox++; } ?>
            <?php } ?>
            </table>
        </div>
    </div>
</div>