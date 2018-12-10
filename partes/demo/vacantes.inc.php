<?php
//$acceso=explode(".",basename(__FILE__, '.php'));
$acceso="reclutamiento";
$permiso=$params->auth($acceso);
if($permiso!==true){echo $permiso;return;}
?>
<div class="container">
	<div class="tablas col-md-3">
		<div class="tablaWrap col-md-12 shadow-1">
        	<h4>Solicitudes por estado</h4>
        </div>
    </div>
	<div class="tablas col-md-9">
        <div class="tablaWrap">
	        <h4>Solicitudes</h4>
            <form class="filtros col-md-10" data-t="lsAjax">
            	<div class="row">
                    <div class="form-group col-md-4">
                    	<label for="cedulaProfesional">Cedula Profesional</label>
                        <select name="f1">
	                        <option value="T">Todos</option>
                            <option value="numerico">Numérico</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                    	<label for="estado">Estado</label>
                        <select name="f2">
							<option value="T">Todos</option>
                            <option value="Aguascalientes">Aguascalientes</option>
                            <option value="Baja California">Baja California</option>
                            <option value="Baja California Sur">Baja California Sur</option>
                            <option value="Campeche">Campeche</option>
                            <option value="Coahuila">Coahuila</option>
                            <option value="Colima">Colima</option>
                            <option value="Chiapas">Chiapas</option>
                            <option value="Chihuahua">Chihuahua</option>
                            <option value="Distrito Federal">Distrito Federal (CDMX)</option>
                            <option value="Durango">Durango</option>
                            <option value="Guanajuato">Guanajuato</option>
                            <option value="Guerrero">Guerrero</option>
                            <option value="Hidalgo">Hidalgo</option>
                            <option value="Jalisco">Jalisco</option>
                            <option value="México">México</option>
                            <option value="Michoacán">Michoacán</option>
                            <option value="Morelos">Morelos</option>
                            <option value="Nayarit">Nayarit</option>
                            <option value="Nuevo León">Nuevo León</option>
                            <option value="Oaxaca">Oaxaca</option>
                            <option value="Puebla">Puebla</option>
                            <option value="Querétaro">Querétaro</option>
                            <option value="Quintana Roo">Quintana Roo</option>
                            <option value="San Luis Potosí">San Luis Potosí</option>
                            <option value="Sinaloa">Sinaloa</option>
                            <option value="Sonora">Sonora</option>
                            <option value="Tabasco">Tabasco</option>
                            <option value="Tamaulipas">Tamaulipas</option>
                            <option value="Tlaxcala">Tlaxcala</option>
                            <option value="Veracruz">Veracruz</option>
                            <option value="Yucatán">Yucatán</option>
                            <option value="Zacatecas">Zacatecas</option>
                        </select>
                    </div>
                </div>
            </form>
            <div class="col-md-2"><span class="btn btn-success actualizar" data-t="lsAjax">Actualizar</span></div>
        </div>
    </div>
</div>
