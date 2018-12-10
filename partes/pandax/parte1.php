<?php @session_start();
//configuración de clases
$pandadb='r';
$area="parte1";
include("includes/class.permisos.php");
$permisos->area($area);
include("includes/class.table.php");
?>
<div class="container">
	<div class="row">
        <div class="col-lg-12"><?php //$tablas->writeTable($infoArr,true); ?></div>
    </div>
</div>