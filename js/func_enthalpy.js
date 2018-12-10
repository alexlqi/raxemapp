//Objeto con las funciones enthalpy
var enthalpy={
	ajax:function(url,datos,tipo,callback){
		return $.ajax({
			url:url,
			cache:false,
			type:tipo,
			data:datos,
			success: function(r){
				if(typeof callback != "undefined"){
					hideLoading();
					callback(r);
				}else{
					hideLoading();
					console.log(r);
				}
			},
			error: function(r,e,et){
				hideLoading();
				switch(r.status){
					case 302:
						location.reload();
					break;
					default:
						notificacion({content:'Ocurrio un error.'});
					break;
				}
			},
		});
	},

	ajaxMedia:function(url,datos,callback){
		return  $.ajax({
			url:url,
			cache:false,
			type:"POST",
			data:datos,
			contentType: false,
    		processData: false,
			success: function(r){
				if(typeof callback != "undefined"){
					hideLoading();
					callback(r);
				}else{
					hideLoading();
					console.log(r);
				}
			},
			error: function(r,e,et){
				hideLoading();
				switch(r.status){
					case 302:
						location.reload();
					break;
					default:
						notificacion({content:'Ocurrio un error.'});
					break;
				}
			},
		});
	},
	
	json2option:function(d,mtx){
		//mtx={value:'',name:''};
		opt = ['<option disabled selected value="0">Elige empresa</option>'];
		$.each(d,function(i,v) {
			//console.log("mtx (length): " + arr.length + "; " + v[mtx.value] + " - " + v[mtx.nombre]);
			opt.push('<option value="' + v[mtx.value] + '">' + v[mtx.nombre] + '</option>');
		});
		return opt.join("");
	},
	
	cotejarArrId:function(d,campo){
		var mtxId=[];
		$.each(d,function(i,v){
			mtxId[v[campo]]=i;
		});
		return mtxId;
	},
	
	rellenarCampos:function(d,puntero){
		var puntero = (typeof puntero != "undefined" || puntero !="") ? puntero : document ;
		var nodo;
		if(typeof d == 'object'){
			$.each(d,function(i,v){
				nodeClass=$("."+i);
				nodeName=$('[name="'+i+'"]');
				//$(puntero).find(i).val(v);
				if(nodeClass.length>0){
					nodo=nodeClass;
				}else if(nodeName.length>0){
					nodo=nodeName;
				}
				switch(nodo.prop("tagName")){
					case 'SELECT':
						nodo.val(v);
					break;
					case 'INPUT':
						switch(nodo.attr("type")){
							case 'text':
								nodo.val(v);
							break;
							case 'radio':
								if(typeof v =="string"){
									if($("."+i+primeraLetraMayuscula(v)).length>0){
										$("."+i+primeraLetraMayuscula(v)).prop("checked",true);
									}
								}else if (typeof v == "object"){
									console.log(v);
									//enthalpy.rellenarCampos(v,puntero);
								}
								if(v!="no"){
									$('[name="'+i+'"][value="si"]').val(v);
									$("."+i+"Text").click();
									$("."+i+"Text").val(v);
								}else{
									$('[name="'+i+'"][value="'+v+'"]').prop("checked",true);
								}
							break;
						}
					break;
					case 'TEXTAREA':
						nodo.html(v);
					break;
				}
			});
		}else{
			$(puntero).get(0).reset();
			console.log('rellenarCampos: El parámetro está vacío');
		}
	},
	
	//json to array
	json2array:function(json){
		arr = $.map(json, function(el) { return el; });
		return arr;
	}
}

//insertar la alertas y notificaciones
$(document).ready(function(e) {
	$.each($(".requerido"),function(i,v){
		if( $(this).parent().find("label").length > 0 ){
			$("<span style='color:#FF0000'>*</span>").appendTo($(this).parent().find("label"));
		}else{
			$(this).addClass("col-md-11");
			$(this).css("width","91.666666667%");
			$("<span class='col-md-1' style='color:#FF0000'>*</span>").insertAfter($(this));
		}
	});
	
	if(!$(".alerta_wrap").length){
		$('<style>.alerta_wrap{cursor:pointer;top:0;left:0;background-color:rgba(0,0,0,0.4);height:100%;width:100%;position:fixed;z-index:9999;display:table;}.alerta_cell{display:table-cell;height:100%;width:100%;vertical-align:middle;}.alerta_elem{min-height:10%;min-width:17.786%;max-height:40%;max-width:80%;display:inline-block;position:relative;background-color:#FFF;border:2px solid #03F;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);}.alerta_header{padding:1%;background-color:#09F;color:#FFF;font-size:1.5em;font-weight:bold;height:20%;}.alerta_content{padding:1%;height:80%;font-size:1.1em;color:#444;word-wrap:break-word;}</style><div class="alerta_wrap"><div class="alerta_cell" align="center"><div class="alerta_elem"><h2 class="alerta_header"></h2><p class="alerta_content"></p></div></div></div>').appendTo("body");
		$(".alerta_wrap").hide();
		$(".alerta_wrap").click(function(e) {
			$(this).fadeToggle('fast');
			$("html").removeClass("flowOculto");
		});
	}
	
	if(!$(".notificacion_wrap").length){
		//$('<style>.notificacion_content{cursor:pointer;font-size:1.1em;display:block;background-color:#093;color:#FFF;margin-bottom:1%;padding:2% 5%;}</style><div class="notificacion_wrap"></div>').appendTo("body");
		$('<div class="notificacion_wrap"></div>').appendTo("body");
		$(".notificacion_wrap").hide();
	}
	setInterval(function(){
		if($(".notificacion_wrap").children().length == 0){
			$(".notificacion_wrap").hide();
		}
	},1000);

	if(!$("#loading").length){ $('<div id="loading" class="shadow-1" style="display:none;"><table><tr><td><img src="/css/loading1.gif" height="50" width="50"></td><td>cargando</td></tr></table></div>').appendTo("body");}
}); /*termina el document ready para alertas y notificaciones*/
function alerta(header,content){
	$(".alerta_header").html(header);
	$(".alerta_content").html(content);
	$("html").addClass("flowOculto");
	if(typeof callback != 'undefined'){
		$(".alerta_wrap").fadeIn('slow');
	}else{
		$(".alerta_wrap").fadeIn('slow');
	}
}
function showLoading(){
	$("#loading").fadeIn('slow');
}
function hideLoading(){
	$("#loading").fadeOut('slow');
}
function notificacion(configs){
	var notifid="notif_"+Date.now();
	var cfg={
		'tipo':'info', // (success|info|warning|danger)
		'content':'Texto de la notificación',
		'dismiss':3000,
	}
	if(typeof configs=='object'){
		$.each(configs,function(i,v){
			cfg[i]=v;
		});
	}
	//$('<p class="notificacion_content '+notifid+'" style="display:none;">'+cfg.content+'</p>').appendTo(".notificacion_wrap");
	switch(cfg.tipo){
		case 'success':
			$('<div class="notificacion_content '+notifid+'" style="display:none;"><div class="alert alert-success"><strong>¡Bien!, </strong>'+cfg.content+'</div></div>').appendTo(".notificacion_wrap");
		break
		case 'warning':
			$('<div class="notificacion_content '+notifid+'" style="display:none;"><div class="alert alert-warning"><strong>¡Cuidado!, </strong>'+cfg.content+'</div></div>').appendTo(".notificacion_wrap");
		break
		case 'danger':
			$('<div class="notificacion_content '+notifid+'" style="display:none;"><div class="alert alert-danger"><strong>¡Error!, </strong>'+cfg.content+'</div></div>').appendTo(".notificacion_wrap");
		break	
		case 'info':
		default:
			$('<div class="notificacion_content '+notifid+'" style="display:none;"><div class="alert alert-warning"><strong>Aviso:</strong>'+cfg.content+'</div></div>').appendTo(".notificacion_wrap");
		break;
	}
	$('.notificacion_wrap').show();
	var dismissTO;
	$("."+notifid).fadeIn('normal',function(){
		dismissTO=setTimeout(function(){
			$("."+notifid).fadeOut('slow',function(){
				$("."+notifid).remove();
			});
		},cfg.dismiss);
	});
	$("."+notifid).click(function(e) {
		_notif=$(this);
		_notif.fadeOut('fast',function(){
			_notif.remove();
		});
	});
}
function primeraLetraMayuscula(string) {
	if(typeof string == "string"){
		return string.charAt(0).toUpperCase() + string.slice(1);
	}else{
		return string;
	}
}

//funciones que solo funcionan con jQuery para añadirlas
if(typeof $ != "undefined"){
	//NOTA: se debe regresar this para que elem tenga el elemento usado
	jQuery.fn.extend({
		conEnter:function(callback){
			this.keyup(function(e){
				if(e.keyCode==13){
					if(typeof callback != "undefined"){callback($(this));}
				}
			});
			return this;
		},
		serializeFiles:function() {
		    var obj = $(this);
		    var formData = new FormData();
		    $.each($(obj).find("input[type='file']"), function(i, tag) {
		        $.each($(tag)[0].files, function(ii, file) {
		            formData.append(tag.name+"["+ii+"]", file);
		        });
		    });
		    var params = $(obj).serializeArray();
		    $.each(params, function (i, val) {
		        formData.append(val.name, val.value);
		    });
		    //console.log(formData.toString());
		    return formData;
		},
		serializeMax:function() {
		    var obj = $(this);
		    var params = $(obj).serializeArray();
		    var formData = Array();
		    $.each(params, function (i, val) {
		        formData[i]=val.name+'='+val.value;
		    });
		    //console.log(formData.toString());
		    return formData.join('&');
		},
		serializeObject : function(){
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
		},
	});
}