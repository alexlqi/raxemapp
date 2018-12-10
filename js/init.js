// JavaScript Document
var datatables={};
var accionesTabla={};
var callbackFn, formFn={}, autocompletes={};
var ajaxCalls=[];
var requeridos=[];
var wWidth,wHeight,maxW,maxH,dialogW,dialogH,maxDiagW,DiagW; //variables para dialogs,etc.
var widthDiagForm='Auto';
var showCols={};
var TOinit={
	"ulTabWrap":0,
};
var tabActual='tabs-1';
var botonesDataTables= [
//	'copy', 'csv', 'excel', 'pdf', 'print', 'selectAll', 'selectNone'
	'excel','selectAll', 'selectNone'
];
$(document).on('focus click',"input", function(e){
	if($(this).hasClass("fechaN")){
		$(this).datepicker({
			dateFormat:'yy-mm-dd',
		});
	}else if($(this).hasClass("numerico")){
		$(this).numeric();
	}
});
$(document).on("click",".botonTabla",function(e){
	$("[type=\"reset\"]").click();
	var boton=$(this);
	var tablaAttr=$("#"+boton.data("id")).data();
	accionesTabla[boton.data("id")]=$(this).data("action");
	switch($(this).data("action")){
		case 'add':
			abrirDialog("#"+tablaAttr.form,"Agegar "+tablaAttr.titulo);
		break;
		case 'pdf':
			ids=Array();
			if(typeof datatables[tablaAttr.tabla].row('.selected').data() == "object"){
				sRows=datatables[tablaAttr.tabla].rows('.selected').data();
				for(i=0;i<sRows.length;i++){
					ids.push(sRows[i][0]);
				}
				//console.log(ids);
				if(ids.length>1){
					alert("decargar varios activado");
					/*$.ajax({
						url:'//app.raxem.com.mx/scripts/s_descarga_multiple.php',
						cache:false,
						type:'POST',
						data:{
							ids:ids,
						},
						success: function(r){
							
						}
					});//*/
					forma=$('<form target="_blank" method="post" action="//app.raxem.com.mx/scripts/s_descarga_multiple.php" style="display:none;"></form>');
					$.each(ids,function(i,v){
						forma.append('<input name="ids[]" value="'+v+'" />');
					});
					forma.appendTo(document.body).submit();
				}else{
					id=datatables[tablaAttr.tabla].row('.selected').data()[0];
					ctrl=boton.data("ctrl");
					fallback=function(r){
						if(!r.err){
							notificacion({tipo:'success',content:"PDF generado correctamente"});
							downloadFile('pdfResultados/'+r.download);
						}else{
							notificacion({tipo:'danger',content:r.msg});
						}
					};
					pdfGral({'tabla':tablaAttr.tabla,'ctrl':ctrl,'id':id},tablaAttr.form,fallback);
				}
			}
		break;
		case 'edit':
			if(typeof datatables[tablaAttr.tabla].row('.selected').data() == "object"){
				fallbacks={
					"onOpen":function(e,ui){
						editarGral(boton.data("ctrl"),datatables[tablaAttr.tabla].row('.selected').data()[0],$(e.target));
					},
					"onClose":function(e,ui){
						$(e.target);
					},
				}
				abrirDialog("#"+tablaAttr.form,"Editar "+tablaAttr.titulo,fallbacks);
			}
		break;
		case 'delete':
			if(typeof datatables[tablaAttr.tabla].row('.selected').data() == "object"){
				id=datatables[tablaAttr.tabla].row('.selected').data()[0];
				var ok=function(){
					deleteGral(id,tablaAttr.form,function(r){
						if(!r.err){
							notificacion({content:"Registro eliminado correctamente"});
							listar(boton.data("id"));
						}else{
							notificacion({content:r.msg});
						}
					});
				}
				if(id!=""){
					confirmar("¿Eliminar registro?",ok,null);
				}
			}
		break;
	}
});
$(document).on( "submit keydown", "form", function(e){
	if((e.type=='keydown' && e.keyCode==13) || e.type=='submit'){
		submitForm(e,this);
	}
	if((e.type=='keydown' && e.keyCode==13) && ($(this).hasClass("inflow") || $(this).hasClass("inflow-media"))){
		return false;
	}
});
$(document).ready(function(e) {
	checkSizes();
	
	//para actualizar las tablas
	$(".refresh-table").click(function(e) {
        listar($(this).parent().parent().find("div.tabla").attr("id"));
    });
	// para poner los tabs
	$(".tabs").tabs({
		activate: function( event, ui ) {
			//console.log(event,ui);
			document.title=ui.newTab[0].innerText;
			//console.log(tabActual);
			tabActual=ui.newPanel[0].id;
			//console.log(tabActual);
		}
	});
	
	//para los autocompletes
	$(".autocompletar").keyup(function(e) {
		//autoid es la referencia
		buscador=$(this);
        autoid=buscador.attr("data-autocomplete");
		chars=buscador.val().length;
		//cuando los caracteres de a busqueda sea >= a los de la configuración entonces hace el autocomplete
		if( chars >= (autocompletes[autoid].minLength - 1)){
			buscador.autocomplete(autocompletes[autoid]);
		}
    });
	
	//numérico
	$(".numerico").numeric();
	
	//data tables
    datables();
	//botones de tabla
	$(".botonTabla").click(function(e) {
		$("[type=\"reset\"]").click();
		var boton=$(this);
		var tablaAttr=$("#"+boton.data("id")).data();
		switch($(this).data("action")){
			case 'add':
				abrirDialog("#"+tablaAttr.form,"Agegar "+tablaAttr.titulo);
			break;
			case 'edit':
				if(typeof datatables[tablaAttr.tabla].row('.selected').data() == "object"){
					fallbacks={
						"onOpen":function(e,ui){
							editarGral(boton.data("ctrl"),datatables[tablaAttr.tabla].row('.selected').data()[0],$(e.target));
						},
						"onClose":function(e,ui){
							$(e.target);
						},
					}
					abrirDialog("#"+tablaAttr.form,"Editar "+tablaAttr.titulo,fallbacks);
				}
			break;
			case 'delete':
				if(typeof datatables[tablaAttr.tabla].row('.selected').data() == "object"){
					id=datatables[tablaAttr.tabla].row('.selected').data()[0];
					var ok=function(){
						deleteGral(id,tablaAttr.form,function(r){
							if(!r.err){
								notificacion({content:"Registro eliminado correctamente"});
								listar(boton.data("id"));
							}else{
								notificacion({content:r.msg});
							}
						});
					}
					if(id!=""){
						confirmar("¿Eliminar registro?",ok,null);
					}
				}
			break;
		}
    });
	
	//clic a los rows de datatables
	$("td").click(function(e) {
        _tr=$(this).closest("tr");
		tabla=_tr.attr("data-tabla");
		field=$(this).attr("data-field");
		idRow=_tr.attr("data-row");
		
		//console.log({_tr,tabla,field,idRow});
		//alert("modificar tabla="+tabla+" campo="+field+" id="+idRow);
    });
	
	//para la sección de los formularios
	$("input[type=text],input[type=password],textarea,select").addClass("form-control");
	
	$("[type=\"reset\"]").click(function(e) {
        $(this).closest("form").find("[type=\"hidden\"]").not('[name="ctrl"]').val('');
    });
	
	//prevenir el procesamiento de los formularios
	$("form").submit(function(e) {
		forma=$(e);
		script=forma.attr("action");
		metodo=forma.attr("method");
		
		//validar el formulario aquí
		requeridos=[]; // se resetea el contador de requeridos
		$.each(forma.find(".requerido"),function(i,v){
			//si un requerido no fue llenado entonces se añade al array de requeridos
			if($(this).val()==""){requeridos.push($(this));}
		});
		if(requeridos.length>0){
			alerta("Advertencia","Campos requeridos no fueron llenados, favor de revisar.");
			return false;
		}
		
		//para sacar el callbackFn
		fid=$(this).attr("id");
		callbackFn=(typeof fid!= 'undefined' && fid!="")? formFn[fid] : function(){} ;
		
		if(forma.hasClass("inflow")){
			//se usa como ajax
			e.preventDefault(); e.stopImmediatePropagation(); e.stopPropagation();
			showLoading();
			data=forma.serializeMax();
			ajaxCalls.push(
				enthalpy.ajax(
					script,
					data,
					metodo,
					callbackFn
				)
			);
		}else if(forma.hasClass("inflow-media")){
			//se usa como ajax pero sin procesar, se pasan los datos completos
			e.preventDefault(); e.stopImmediatePropagation(); e.stopPropagation();
			showLoading();
			data=forma.serializeFiles();
			ajaxCalls.push(
				enthalpy.ajaxMedia(
					script,
					data,
					callbackFn
				)
			);
		}else if(forma.hasClass("outflow")){
			//formulario normal ir al action script
		}
    });//*/

    $(".dropzone");

    //para los date picker fechas
    $("input.fecha").datepicker({
    	dateFormat:'yy-mm-dd',
    });
});

$(window).resize(function(e){
	checkSizes();
	fluidDialog();
	redrawTables();
});

// catch dialog if opened within a viewport smaller than the dialog width
$(document).on("dialogopen", ".ui-dialog", function (event, ui) {
    fluidDialog();
});

function redrawTables(){
	$.each(datatables,function(i,table){
		table.columns.adjust().draw();
	});
}

function checkSizes(){
	widthDiagForm=window.innerWidth;
	clearTimeout(TOinit.ulTabWrap);
	TOinit.ulTabWrap=setTimeout(function(){
		ulTabWrap=$(".ulTabWrap");
		minWidthTab=0;
		if(ulTabWrap.length>0){
			$.each(ulTabWrap.find("li"),function(i,v){
				//console.log($(this).width());
				minWidthTab=minWidthTab+$(this).width()+10;
			});
			//console.log(minWidthTab);
			ulTabWrap.css("min-width",minWidthTab+"px");
		}
	},50);
	wWidth=window.innerWidth;
	wHeight=window.innerHeight;
	ratio=wWidth/wHeight;
	if(wWidth<=640){ // handheld
		maxW= ( (ratio>1)? wHeight*ratio : wWidth ) / 1.05;
		maxH= ( (ratio>1)? wHeight : wWidth*ratio ) / 1.05;
		dialogW= ( (ratio>1)? wHeight*ratio : wWidth ) / 1.10;
		dialogH= ( (ratio>1)? wHeight : wWidth*ratio ) / 1.10;
		DiagW=0.7;
		maxDiagW=0.8;
	}else{
		maxW= ( (ratio>1)? wHeight*ratio : wWidth ) / 2;
		maxH= ( (ratio>1)? wHeight : wWidth*ratio ) / 2;
		dialogW= ( (ratio>1)? wHeight*ratio : wWidth ) / 3;
		dialogH= ( (ratio>1)? wHeight : wWidth*ratio ) / 3;
		DiagW=0.5;
		maxDiagW=0.6;
	}
}

function colorSelect(e){
	elem=$(e);
	$.each(elem,function(i,v){
		clase=$(this).find(":selected").data("class");
		if(typeof clase == "string" && clase!=""){$(this).attr("class","form-control "+clase);}
	});
}
function loadElem(url,pointer,elem){
	$.ajax({
		url:rootUrl+url,
		cache:false,
		type:'POST',
		success: function(r){
			if(typeof elem == "string"){
				$(pointer).html($(r).find(elem).html());
			}else{
				$(pointer).html(r);
			}
		}
	});
}
function abrirDialog(form,titulo,fallback){
	widthDiagForm=window.innerWidth;
	$("body").addClass("bloquear");
	if($("#dialog-form").length==0){$('<div id="dialog-form"></div>').appendTo("body");}
	$("#dialog-form").html('');
	$(form).clone().appendTo("#dialog-form",function(e){
		$(form).fadeIn(fast);
	});
	var onOpen=onClose=null;
	if(typeof fallback == "object"){
		onOpen=(typeof fallback.onOpen == "function") ? fallback.onOpen : null;
		onClose=(typeof fallback.onClose == "function") ? fallback.onOpen : null;
	}
	onClose=function(){$("body").removeClass("bloquear");};
	$("#dialog-form").dialog({
		title:titulo,
		width: widthDiagForm*DiagW, // overcomes width:'auto' and maxWidth bug
		minWidth: 350,
		maxWidth: widthDiagForm*maxDiagW,
		height: 'auto',
		modal: true,
		fluid: true, //new option
		resizable: false,
		open: onOpen,
		close: onClose,
	});
}
function confirmar(titulo,ok,cancel){
	if($("#dialog-confirm").length==0){$('<div id="dialog-confirm" style="width:200px;"><p></p></div>').appendTo("body");}
	$( "#dialog-confirm" ).dialog({
		resizable: false,
		width: 'auto', // overcomes width:'auto' and maxWidth bug
		minWidth: 250,
		maxWidth: 600,
		height: 'auto',
		modal: true,
		fluid: true, //new option
		title:titulo,
		buttons: {
			"Aceptar": function() {
				$( this ).dialog( "close" );
				if(typeof ok == "function"){
					ok();
				}
			},
			"Cancelar": function() {
		  		$( this ).dialog( "close" );
				if(typeof cancel == "function"){
					cancel();
				}
			}
		}
    });
}

function fluidDialog() {
    var $visible = $(".ui-dialog:visible");
    // each open dialog
    $visible.each(function () {
        var $this = $(this);
        var dialog = $this.find(".ui-dialog-content").data("ui-dialog");
        // if fluid option == true
        if (dialog.options.fluid) {
            var wWidth = $(window).width();
            // check window width against dialog width
            if (wWidth < (parseInt(dialog.options.maxWidth) + 50))  {
                // keep dialog from filling entire screen
                $this.css("max-width", "90%");
            } else {
                // fix maxWidth bug
                $this.css("max-width", dialog.options.maxWidth + "px");
            }
            //reposition dialog
            dialog.option("position", dialog.options.position);
        }
    });

}

function datables(tag){
	if(typeof tag == "string" && tag!=""){
		_this=$("#"+tag);
		if(_this.id!=""){
			datatables[tag]=$(_this).DataTable({
				select: true,
				dom: 'lBfrtip',
				responsive: true,
				buttons: botonesDataTables,
				language: {
					buttons: {
						selectAll: "Seleccionar Todos",
						selectNone: "Quitar Selección"
					}
				},
				"lengthMenu": [ 50, 100 ],
				"createdRow": function( row, data, dataIndex ) {
					switch(data[1]){
						case 'Precaptura':
							$(row).addClass('alert-danger');
						break;
						case 'Pendiente':
							$(row).addClass('alert-warning');
						break;
						case 'Completado':
							$(row).addClass('alert-success');
						break;
						default:
						break;
					}
				},
			});
			if(typeof showCols[tag] == 'object'){
				datatables[tag].columns( ['*'] ).visible( false, false );
				datatables[tag].columns( showCols[tag] ).visible( true, true );
				datatables[tag].columns.adjust().draw( false ); // adjust column sizing and redraw
			}
		}
	}else{
		$.each($(".datatables"),function(i,v){
			if(this.id!=""){
				datatables[this.id]=$(this).DataTable({
					select: true,
					dom: 'lBfrtip',
					responsive: true,
					buttons: botonesDataTables,
					language: {
						buttons: {
							selectAll: "Seleccionar Todos",
							selectNone: "Quitar Selección"
						}
					},
					"lengthMenu": [ 50, 100 ],
					"createdRow": function( row, data, dataIndex ) {
						switch(data[1]){
							case 'Precaptura':
								$(row).addClass('alert-danger');
							break;
							case 'Pendiente':
								$(row).addClass('alert-warning');
							break;
							case 'Completo':
								$(row).addClass('alert-success');
							break;
							default:
							break;
						}
					},
				});
			}
			if(typeof showCols[tag] == 'object'){
				datatables[tag].columns( ['*'] ).visible( false, false );
				datatables[tag].columns( showCols[tag] ).visible( true, true );
				datatables[tag].columns.adjust().draw( false ); // adjust column sizing and redraw
			}
		});
	}
}

function downloadFile(url){
	window.open('/descargas/'+url);
}

function accionTabla(accion,elem){
	e=$(elem);
	switch(accion){
		case 'download':
			url="/descargas/"+e.data("tabla")+"/"+e.data("tipo")+"/"+e.data("id")+"/";
			window.open(url);
		break;
		case 'open':
		break;
		case 'edit':
			url=e.data("tabla")+"/"+e.data("id")+"/";
			window.open(url);
		break;
		case 'edit2':
			$("tbody").on('click','tr',function(){
			});
		break;
		default:
			alert(elem);
		break;
	}
}
function pdfGral(data,form,fallback){
	showLoading();
	if(typeof form == "object"){
		f=form.find("form");
	}else{
		f=$("#"+form);
	}
	$.ajax({
		url:f.attr("action"),
		cache:'false',
		type:'POST',
		data:data,
		success: function(r){
			r=(typeof r=="object") ? r : $.parseJSON(r);
			if(typeof fallback == "function"){
				fallback(r);
			}
			hideLoading();
			//console.log(r);
		},
		error:function(r,e){
			hideLoading();
			notificacion({content:'Ocurrió un error',});
		}
	});
}
function editarGral(ctrl,id,form){
	if(typeof form == "object"){
		f=form.find("form");
	}else{
		f=$("#"+form);
	}
	$.ajax({
		url:f.attr("action"),
		cache:'false',
		type:'POST',
		data:{
			"ctrl":ctrl,
			"id":id,
		},
		success: function(r,a){
			r=(typeof r=="object") ? r : $.parseJSON(r);
			f[0].reset();
			$.each(f.find("select"),function(i,v){
				$(this).val($(this).find("option").first().val()).change();
				//console.log($(this).val($(this).find("option").first().val()),$(this).find("option").first().val());
			});
			console.log(f[0]);
			$.each(f.find("input.reseteable"),function(i,v){
				//console.log($(this));
				$(this).val('');
			});
			$.each(f.find("textarea.reseteable"),function(i,v){
				//console.log($(this));
				$(this).html('').text('');
			});//*/
			$.each(r.data,function(i,v){
				if(f.find('.'+i+'OtroSelect option[value="'+v+'"]').length){
					f.find('.'+i+'OtroSelect').val(v);
				}else{
					f.find('.'+i+'OtroSelect').val('OTRO').change();
				}
				f.find('[name="'+i+'"]').val(v);
				f.find('.'+i).val(v);
			})
		},
		error:function (xhr, ajaxOptions, thrownError){
			if(xhr.status==404) {
				location.reload();
			}
		}
	});
}
function deleteGral(id,form,fallback){
	f=$("#"+form);
	$.ajax({
		url:f.attr("action"),
		cache:'false',
		type:'POST',
		data:{
			"ctrl":"eliminar",
			"tabla":form,
			"id":id,
		},
		success: function(r){
			if(typeof fallback == "function"){
				fallback(r);
			}
		}
	});
}
function generarTablas(fallback){
	var t=[];
	$.each($(".tabla"),function(i,v){
		_tabla=$(this).attr("id");
		listar(_tabla,"#"+_tabla);
	});
}
function listarSelect(id,pointer,dataSet){
	console.log(id,pointer,dataSet);
	$.ajax({
		url:scriptPath+"s_tablas.php",
		type:'POST',
		cache:false,
		data:{
			ctrl:id,
			data:{
				id:dataSet.tabla,
				filtros:$(dataSet["formfiltros"]).serializeObject(),
			},
		},
		success: function(r){
			pointer=$(pointer);
			if(pointer.length > 0){
				pointer.html(r.tabla);
			}else{
				$("#"+id).html(r.tabla);
			}
		}
	});
}
function listarArrDataTable(t,cols,values){
	$("#"+t).html('');
	if(typeof datatables[t] != "undefined"){
		datatables[t].destroy();
		$("#"+t).empty();
	}
	loader('abrir',"#"+t);
	$.ajax({
		url:scriptPath+"s_tablas.php",
		type:'POST',
		cache:false,
		data:{
			ctrl:t,
			data:{
				id:$("#"+t).data("tabla"),
				filtros:$("#"+t).parent().parent().find("form.filtros").serializeObject(),
			},
		},
		success: function(r){
			datatables[t]=$("#"+t).DataTable({
				select: true,
				dom: 'lBfrtip',
				responsive: true,
				data:r.tabla.data.values,
				columns:r.tabla.data.cols,
				buttons: botonesDataTables,
				"lengthMenu": [ 50, 100 ],
				language: {
					buttons: {
						selectAll: "Seleccionar Todos",
						selectNone: "Quitar Selección"
					}
				}
			});
			loader('cerrar',"#"+t);
		}
	});
}
function listar(t,pointer){
	setTimeout(function(){
		$("#"+t).html('');
		loader('abrir',"#"+t);
		$.ajax({
			url:scriptPath+"s_tablas.php",
			type:'POST',
			cache:false,
			data:{
				ctrl:t,
				data:{
					id:$("#"+t).data("tabla"),
					filtros:$("#"+t).parent().find("form.filtros").serializeObject(),
				},
			},
			success: function(r){
				pointer=$(pointer);
				if(pointer.length > 0){
					pointer.html(r.tabla);
				}else{
					$("#"+t).html(r.tabla);
				}
				loader('cerrar',"#"+t);
			}
		});
	},0);
}
function toggleChk(){
	$(".toggleChk").bootstrapToggle();
	$('.toggleChk').change(function() {
		console.log($($(this)[0]).data());
    })
}
function submitForm(e,f){
	forma=$(f);
		
	script=forma.attr("action");
	metodo=forma.attr("method");
	
	//validar el formulario aquí
	requeridos=[]; // se resetea el contador de requeridos
	$.each(forma.find(".requerido"),function(i,v){
		//si un requerido no fue llenado entonces se añade al array de requeridos
		if($(this).val()==""){requeridos.push($(this));}
	});
	if(requeridos.length>0){
		console.log(requeridos.length);
		alerta("Advertencia","Campos requeridos no fueron llenados, favor de revisar.");
		return false;
	}
	
	//para sacar el callbackFn
	fid=forma.attr("id");
	callbackFn=(typeof fid!= 'undefined' && fid!="")? formFn[fid] : function(){} ;
	$("body").removeClass("bloquear");
	
	if(forma.hasClass("inflow")){
		//se usa como ajax
		e.preventDefault(); e.stopImmediatePropagation(); e.stopPropagation();
		showLoading();
		data=forma.serializeMax();
		ajaxCalls.push(
			enthalpy.ajax(
				script,
				data,
				metodo,
				callbackFn
			)
		);
	}else if(forma.hasClass("inflow-media")){
		//se usa como ajax pero sin procesar, se pasan los datos completos
		e.preventDefault(); e.stopImmediatePropagation(); e.stopPropagation();
		showLoading();
		data=forma.serializeFiles();
		ajaxCalls.push(
			enthalpy.ajaxMedia(
				script,
				data,
				callbackFn
			)
		);
	}else if(forma.hasClass("outflow")){
		//formulario normal ir al action script
	}
}
function cerrarDialog(){
	$("#dialog-form").html('').dialog('destroy');
	$("#dialog-confirm").html('').dialog('destroy');
}
function loader(ctrl,tag){
	switch(ctrl){
		case 'abrir':
			if($(tag).find(".cssloader").length==0){
				$("#cssloader").clone().appendTo(tag);
			}
		break;
		case 'cerrar':
			$(tag).find(".cssloader").remove();
		break;
	}
}