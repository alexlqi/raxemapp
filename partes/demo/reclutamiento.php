<?php
@include_once "inc.config.php";
$dsnModelo=$dsnPmRHVacantes;
@include(CLASS_PATH."class.modelo.php");
@include(CLASS_PATH."class.table.php");
@include_once(FUNC_DIR."tablas.php");
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
</script>
<div class="tabs">
    <ul>
    	<li><a href="#tabs-2">Solicitudes</a></li>
        <li><a href="#tabs-1">Vacantes</a></li>
    </ul>
    <div id="tabs-2"><?php @include("solicitudes.inc.php"); ?></div>
    <div id="tabs-1"><?php @include("vacantes.inc.php"); ?></div>
</div>