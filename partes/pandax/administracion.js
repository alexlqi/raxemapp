//funciones ajax de formularios
$(document).ready(function(e) {
	generarTablas();
    $(".idSuscripcion").change(function(e) {
		opt=$(this).find("option:selected").val();
		$(".idSusc").val(opt); // llena todos los input.idSusc:hidden
		
		//ajax accionado para usuarios
        pruebaData={
			ctrl:"lu", //listar usuarios
			idSuscripcion:opt,
		};
		pruebaFallback=function(r){
			console.log(r);
		};
		ajaxUsuarios(pruebaData,pruebaFallback);
		
		//ajax para permisos
		ajaxPermisos({ctrl:'lu',idSusc:opt},function(r){$(".usuarios").html(r.data);});
    });
});

formFn["pandaxUsers"]=function(r){
	listar("luAjax");
}

//funciones para formularios
formFn['permisos2']=function(r){
	notificacion({content:r.msg});
	$.ajax({
		url:'/partes/seccion1/listado_permisos.php',
		success: function(r){
			// en este caso r es la respuesta HTML y se inserta en una parte del codigo
			$(".listado_permiso").html(r);
		}
	});
}
formFn['permisos']=function(r){
	notificacion({content:r.msg});
	if(!r.err){
		cerrarDialog();
		listar("lPermisosAjax");
	}
}
formFn['pandaxServicio']=function(r){
    notificacion({content:r.msg});
    $("#pandaxServicio").get(0).reset();
}
formFn['activaServicio']=function(r){
    notificacion({content:r.msg});
    $("#activaServicio").get(0).reset();
}

//funciones de autocompletado
autocompletes["autocomplete1"]={
	source: "scripts/s_autocomplete.php?ctrl=p",
	minLength: 0,
	select: function( event, ui ) {
		$(".autocompletar").val(ui.item.label);
		$(".autocomplete-descripcion").text(ui.item.descripcion);
	}
}

//funciones generales
function ajaxUsuarios(data,fallback){
	//regresa el html del listado de usuarios junto con su respectivo codigo para datatables
	$.ajax({
		url:scriptPath+'s_usuarios.php',
		type:'POST',
		cache:false,
		data:data,
		success: function(r){
			fallback(r);
		}
	});
}

function ajaxPermisos(data,fallback){
	//regresa el html del listado de usuarios junto con su respectivo codigo para datatables
	$.ajax({
		url:scriptPath+'s_permisos.php',
		type:'POST',
		cache:false,
		data:data,
		success: function(r){
			fallback(r);
		}
	});
}