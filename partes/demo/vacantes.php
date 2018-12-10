<?php
@include_once "inc.config.php";
$dsnModelo=$dsnPmRHVacantes;
@include(CLASS_PATH."class.modelo.php");
@include(CLASS_PATH."class.table.php");
@include_once(FUNC_DIR."tablas.php");

$permiso=$params->auth(basename(__FILE__, '.php'));
if($permiso!==true){echo $permiso;return;}
?>

<script>
$(document).ready(function(e) {
	generarTablas();
	$(".actualizar").click(function(e) {
        listar($(this).data("t"));
    });
});
$.fn.serializeObject = function() {
	var self = this,
	json = {},
	push_counters = {},
	patterns = {
		"validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
		"key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
		"push":     /^$/,
		"fixed":    /^\d+$/,
		"named":    /^[a-zA-Z0-9_]+$/
	};


	this.build = function(base, key, value){
		base[key] = value;
		return base;
	};

	this.push_counter = function(key){
		if(push_counters[key] === undefined){
			push_counters[key] = 0;
		}
		return push_counters[key]++;
	};

	$.each($(this).serializeArray(), function(){

		// skip invalid keys
		if(!patterns.validate.test(this.name)){
			return;
		}

		var k,
			keys = this.name.match(patterns.key),
			merge = this.value,
			reverse_key = this.name;

		while((k = keys.pop()) !== undefined){

			// adjust reverse_key
			reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

			// push
			if(k.match(patterns.push)){
				merge = self.build([], self.push_counter(reverse_key), merge);
			}

			// fixed
			else if(k.match(patterns.fixed)){
				merge = self.build([], k, merge);
			}

			// named
			else if(k.match(patterns.named)){
				merge = self.build({}, k, merge);
			}
		}

		json = $.extend(true, json, merge);
	});
	return json;
};
function listar(t){
	$.ajax({
		url:scriptPath+"s_tablas.php",
		type:'POST',
		cache:false,
		data:{
			ctrl:t,
			data:{
				id:$("#"+t).data("tabla"),
				data:$("#"+t).parent().find("form.filtros").serializeObject(),
			},
		},
		success: function(r){
			$("#"+t).html(r.tabla);
		},
	});
}
</script>
<div class="container">
	<div class="tablas col-md-3">
		<div class="tablaWrap col-md-12 shadow-1">
        	<h4>Solicitudes por estado</h4>
            <div id="lSolPorEstAjax" class="tabla row" data-tabla="listaSolicitudesPorEstado"></div>
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
        <div id="lsAjax" class="tabla row" data-tabla="listaSolicitudes"></div>
    </div>
</div>
