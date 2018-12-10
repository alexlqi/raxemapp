<?php
@include_once "inc.config.php";
$dsnModelo=$dsnPmRH;
@include(CLASS_PATH."class.modelo.php");
$sql="select * from v_vacantes;";
$vacantes=$modelo->query2arr($sql);
?>
<script type="text/javascript">
	var TO={
		"curp":0
	}
	
	formFn['sorianaPrevio']=function(r){
		notificacion({content:r.msg});
		if(r.err){
			return false;
		}
		if(r.html!="" && !r.err){
			$(".container").fadeOut('fast',function(){
				$("#sorianaPrevio").remove();
				$(".container").append(r.html).fadeIn();
			});
		}
	};
	formFn['sorianaCompleto']=function(r){
		notificacion({content:r.msg});
		if(r.err){
			return false;
		}
		if(r.html!="" && !r.err){
			$(".container").fadeOut('fast',function(){
				$("#sorianaPrevio").remove();
				$(".container").append(r.html).fadeIn();
			});
		}
	};
	$(document).ready(function(e) {
		$('#sorianaPrevio [name="curp"]').keyup(function(e) {
			clearTimeout(TO["curp"]);
			tCurp=$(this);
			TO["curp"]=setTimeout(function(){
				$.ajax({
					url:scriptPath+"s_vacantes.php",
					cache:false,
					type:'POST',
					data:{
						ctrl:"bSolicitud",
						vacante:$('#sorianaPrevio [name="idVacante"]').val(),
						curp:tCurp.val(),
					},
					success: function(r){
						if(r.url!=""){
							if(confirm("Ya tiene una solicitud previa, ¿desea completarla?")){
								window.location.href=r.url;
							};
						}
					}
				});
			},3000);
		});
	});
</script>
<div class="container">
<?php if(@$_GET["vacante"]==""){?>
	<h1>Listado de Vacantes</h1>
    <?php
		$vacantesArr=array();
		if(!empty($vacantes["data"])){
			foreach($vacantes["data"] as $v){
				# se generan los grupos de las vacantes y se ñaden sus vacantes
				$vacantesArr[$v["idProyecto"]]["proyecto"]=$v["proyecto"];
				$vacantesArr[$v["idProyecto"]]["vacantes"][$v["idVacante"]]["puesto"]=$v["puesto"];
				$vacantesArr[$v["idProyecto"]]["vacantes"][$v["idVacante"]]["url"]="/public/vacantes/".$v["clave"]."/".$v["idVacante"]."-".str_replace(" ","_",iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $v["puesto"]))."/";
			}
			foreach($vacantesArr as $idProyecto => $proyecto){
				echo "<h2>{$proyecto["proyecto"]}</h2>";
				echo "<ol>";
				foreach($proyecto["vacantes"] as $idVacante=>$vacante){
					echo "<li><a href=\"{$vacante["url"]}\">{$vacante["puesto"]}</a></li>";
				}
				echo "</ol>";
			}
		}
    ?>
<?php }else{?>
	<?php
	$vacante=explode("-",@$_GET["vacante"]);
	$idVacante=$vacante[0];
	$proyecto=@$_GET["proyecto"];
    switch($proyecto){
		case 's':
		if(@$_GET["solicitud"]==""){
	?>
        	<form id="sorianaPrevio" role="form" class="inflow-media col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_vacantes.php">
            	<h2>Favor de llenar los siguientes campos</h2>
	            <input type="hidden" name="idVacante" value="<?php echo $idVacante; ?>" />
                <input type="hidden" name="proyecto" value="<?php echo $proyecto; ?>" />
                <input type="hidden" name="fase" value="1" />
                <div class="row">
	                <div class="form-group col-md-4"><label>CURP:</label><input class="requerido" type="text" name="curp" /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6"><label>Nombre Completo:</label><input class="requerido" type="text" name="nombreC" /></div>
                    <div class="form-group col-md-3"><label>Email:</label><input class="" type="text" name="email" /></div>
                    <div class="form-group col-md-3"><label>Telefono de Contacto:</label><input class="numerico requerido" type="text" name="telefono" /></div>
                </div>
                <div class="row">
                	<div class="form-group col-md-2"><label>Estado:</label>
                        <select name="estado">
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
	                <div class="form-group col-md-2"><label>Ciudad:</label><input type="text" name="ciudad" /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4"><label>Cédula Profesional</label><br>
                    	<div class="radio-inline"><label><input type="radio" class="cedulaProfesionalSi " name="cedulaProfesional" value="si"><input type="text" class="numerico" onclick="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true;" onKeyUp="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true; $('.cedulaProfesionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalNo" name="cedulaProfesional" value="no" checked="checked">No</label></div>
                    </div>
                    <div class="form-group col-md-4"><label>Cédula Provisional</label><br>
                    	<div class="radio-inline"><label><input type="radio" class="cedulaProvisionalSi" name="cedulaProvisional" value="si"><input type="text" class="numerico" onclick="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true;" onKeyUp="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true; $('.cedulaProvisionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalNo" name="cedulaProvisional" value="no" checked="checked">No</label></div>
                    </div>
                </div>
                <div class="row">
                	<div class="form-group col-md-4"><label>¿Antecedentes Penales?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="no" checked="checked">No</label></div>
                    </div>
                    <div class="form-group col-md-4"><label>¿Antecedentes Laborales?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="no" checked="checked">No</label></div>
                    </div>
                    <div class="form-group col-md-4"><label>Adjunta tu Curriculum Vitae aquí</label><input type="file" name="adjuntoCV" /></div>
                </div>
                <div class="row">
                	<div align="right">
                    	<div class="form-group col-md-12"><input class="btn btn-success" type="submit" /></div>
                    </div>
                </div>
            </form>
            <?php }else{ ?>
            <?php
				$datosPrevios=$modelo->query2arr("select curp,json from solicitudes where idSolicitud={$_GET["solicitud"]};");
				$jsonPrevio=@$datosPrevios["data"][0]["json"];
				$curp=@$datosPrevios["data"][0]["curp"];
				if($jsonPrevio==""){break;}
				$jsonPrevio=json_decode(base64_decode($jsonPrevio),true);
				unset($jsonPrevio["idVacante"],$jsonPrevio["proyecto"],$jsonPrevio["fase"]);
				$jsonPrevio["curp"]=$curp;
				$jsonJS=json_encode($jsonPrevio,JSON_UNESCAPED_UNICODE);
            ?>
            <script type="text/javascript">
				var jsonPrevio=<?php echo $jsonJS; ?>;
				$(document).ready(function(e) {
					<?php if(@$_SESSION["idpanda"]==""){ ?>
					var curpPass=prompt("Para continuar teclee su CURP");
					if(!curpPass){
						$("#sorianaCompleto").remove();
						$(".container").append("<h2 align=\"center\">Vuelva a intentarlo, actualice la pagina</h2>");
					}else if(curpPass.toUpperCase()===jsonPrevio.curp){
						$("#sorianaCompleto").show();
					}else{
						$("#sorianaCompleto").remove();
						$(".container").append("<h2 align=\"center\">Vuelva a intentarlo, actualice la pagina</h2>");
					}
					<?php } ?>
					enthalpy.rellenarCampos(jsonPrevio,"#sorianaCompleto");
					$(".requisitosBtn").click(function(e) {
						$(".requisitos").slideToggle("fast");
					});
				});
			</script>
            <style>
				.requisitos{
					display:none;
				}
				#sorianaCompleto{
					/*display:none;/**/
				}
			</style>
            <form id="sorianaCompleto" role="form" class="inflow-media col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_vacantes.php">
            <input type="hidden" name="idSolicitud" value="<?php echo $_GET["solicitud"]; ?>" />
            <input type="hidden" name="idVacante" value="<?php echo $idVacante; ?>" />
            <input type="hidden" name="proyecto" value="<?php echo $proyecto; ?>" />
            <input type="hidden" name="fase" value="2" />
            <div class="row">
                <h2>Datos de la Vacante</h2>
                <div class="form-group col-md-2"><label>¿Trabaja Actualmente?:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="trabajoActual" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="trabajoActual" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Sueldo más reciente:</label><input class="numerico" type="text" name="sueldoActual" /></div>
                <div class="col-md-8">
                	<div class="row">
                        <h4>Requisitos de papelería</h4>
                        <p>Para acelerar el proceso de contratación, por favor adjunta en un archivo zip lo siguiente:</p>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Adjunta tu papelería aquí</label><input type="file" name="adjuntoP" />
                        </div>
                        <div class="col-md-6">
	                        <span class="requisitosBtn btn btn-info">Click para mostrar/ocultar requisitos</span>
                        </div>
                    </div>
                    <div class="row">
                        <ul class="requisitos">
                            <li>Curriculum Vitae</li>
                            <li>Acta de Nacimiento</li>
                            <li>Acta de Matrimonio (opcional)</li>
                            <li>Identificación Oficial</li>
                            <li>Comprobante IMSS</li>
                            <li>CURP</li>
                            <li>Comprobante de domicilio</li>
                            <li>Comprobante de Estudios</li>
                            <li>Cursos, capacitaciones o diplomas</li>
                            <li>Estado de cuenta AFORE (opcional)</li>
                            <li>Carta de recomendación (2)</li>
                            <li>Aviso de retención</li>
                            <li>Carta de No antecedentes penales</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <h2>Datos Personales</h2>
                <div class="form-group col-md-3"><label>¿Alta IMSS vigente?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="no">No</label></div>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="no lo se">No sé</label></div>
                </div>
                <div class="form-group col-md-2"><label>NSS:</label><input class="numerico" type="text" name="nss" /></div>
                <div class="form-group col-md-3"><label>RFC:</label><input class="requerido" type="text" name="rfc" /></div>
                <div class="form-group col-md-4"><label>CURP:</label><input class="requerido" type="text" name="curp" /></div>
                <div class="form-group col-md-6"><label>Nombre Completo:</label><input type="text" name="nombreC" /></div>
                <div class="form-group col-md-2"><label>Edad:</label><input class="numerico" type="text" name="edad" /></div>
                <div class="form-group col-md-2"><label>Fecha de Nacimiento:</label><input class="fecha" type="text" name="fechaNac" /></div>
                <div class="form-group col-md-2"><label>Sexo:</label>
                    <select name="sexo">	
                        <option disabled selected>--elige una--</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="form-group col-md-6"><label>Lugar de nacimiento:</label><input type="text" name="lugarNac" /></div>
                <div class="form-group col-md-3"><label>Estado Civil:</label>
                    <select name="estadoCivil">
                        <option disabled selected>--elige una--</option>
                        <option value="Soltero">Soltero(a)</option>
                        <option value="Casado">Casado(a)</option>
                    </select>
                </div>
                <div class="form-group col-md-3"><label>Religión:</label><input type="text" name="religion" /></div>
                <div class="form-group col-md-2"><label>Credencial INE:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="elector" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="elector" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Licencia:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="licencia" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="licencia" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Tipo Licencia:</label>
                    <select name="tipoLicencia">
                        <option value="N">Ninguna</option>
                        <option value="A">Tipo A</option>
                        <option value="B">Tipo B</option>
                        <option value="C">Tipo C</option>
                        <option value="D">Tipo D</option>
                    </select>
                </div>
                <div class="form-group col-md-2"><label>Casa:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="casa" value="propia">Propia</label></div>
                    <div class="radio-inline"><label><input type="radio" name="casa" value="si">Renta</label></div>
                </div>
                <div class="form-group col-md-2"><label>INFONAVIT:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="infonavit" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="infonavit" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Medio de Transporte:</label><br>
                    <select name="transporte">
                        <option value="publico">Público</option>
                        <option value="propio">Auto Propio</option>
                    </select>
                </div>
                <div class="form-group col-md-2"><label>¿Con quien vive?</label><input type="text" name="viveCon" /></div>
                <div class="form-group col-md-2"><label>¿Dependientes?</label><input class="numerico" type="text" name="dependientes" /></div>
                <div class="form-group col-md-2"><label>UMF:</label><input class="numerico" type="text" name="umf" /></div>
                <div class="form-group col-md-2"><label>¿Antecedentes Penales?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>¿Antecedentes Laborales?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="no">No</label></div>
                </div>
            </div>
            <div class="row">
                <h3>Domicilio</h3>
                <div class="form-group col-md-6"><label>Calle:</label><input type="text" name="calle" /></div>
                <div class="form-group col-md-3"><label># Ext:</label><input type="text" name="exterior" /></div>
                <div class="form-group col-md-3"><label># Int:</label><input type="text" name="interior" /></div>
                <div class="form-group col-md-6"><label>Colonia:</label><input type="text" name="colonia" /></div>
                <div class="form-group col-md-2"><label>Ciudad:</label><input type="text" name="ciudad" /></div>
                <div class="form-group col-md-2"><label>Estado:</label>
                	<select name="estado">
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
                <div class="form-group col-md-2"><label>Codigo Postal:</label><input class="numerico" type="text" name="cp" /></div>
            </div>
            <div class="row">
                <h2>Datos de Contacto</h2>
                <div class="form-group col-md-4"><label>Email:</label><input class="" type="text" name="email" /></div>
                <div class="form-group col-md-4"><label>Telefono:</label><input class="numerico requerido" type="text" name="telefono" /></div>
                <div class="form-group col-md-4"><label>Celular:</label><input class="numerico" type="text" name="celular" /></div>
            </div>
            <div class="row">
                <h2>Escolaridad</h2>
                <div class="row">
                    <div class="col-md-2"><h4>Técnica</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="" type="text" name="tecnicaInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="" type="text" name="tecnicaPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="tecnicaCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="tecnicaCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><h4>Preparatoria</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="" type="text" name="prepaInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="" type="text" name="prepaPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="prepaCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="prepaCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><h4>Profesional</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="requerido" type="text" name="profInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="requerido" type="text" name="profPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="profCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="profCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2"><label>¿Titulado?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="titulado" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="titulado" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>Cedula Profesional</label><br>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalSi" name="cedulaProfesional" value="si"><input class="cedulaProfesionalText" type="text" onclick="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true;" onKeyUp="$('.cedulaProfesionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalNo" name="cedulaProfesional" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>Cedula Provisional</label><br>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalSi" name="cedulaProvisional" value="si"><input class="cedulaProvisionalText" type="text" onclick="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true;" onKeyUp="$('.cedulaProvisionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalNo" name="cedulaProvisional" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12"><label>Cursos:</label><textarea name="cursos"></textarea></div>
                    <div class="form-group col-md-6"><label>Estudios Actuales:</label><input class="" type="text" name="estudioActual" /></div>
                    <div class="form-group col-md-6"><label>Horario de Estudios Actuales:</label><input class="" type="text" name="horarioEstudio" /></div>
                </div>
            </div>
            <div class="row">
                <h2>Datos de Familiares</h2>
                <div class="form-group col-md-12"><label>Padre:</label><input type="text" name="papa" /></div>
                <div class="form-group col-md-12"><label>Madre:</label><input type="text" name="mama" /></div>
                <div class="form-group col-md-12"><label>Esposo(a):</label><input type="text" name="conyuge" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[0]" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[1]" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[2]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[0]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[1]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[2]" /></div>
            </div>
            <div class="row">
                <h2>Experiencia Laboral</h2>
                <div class="row">
                    <h3>Empleo Actual</h3>
                    <div class="form-group col-md-8"><label>Nombre de la empresa:</label><input type="text" name="empleos[actual][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Giro de la empresa:</label><input type="text" name="empleos[actual][giro]" /></div>
                    <div class="form-group col-md-2"><label>Sueldo:</label><input class="fecha" type="text" name="empleos[actual][sueldo]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Ingreso:</label><input class="fecha" type="text" name="empleos[actual][alta]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Baja:</label><input class="fecha" type="text" name="empleos[actual][baja]" /></div>
                    <div class="form-group col-md-4"><label>Jefe Inmediato:</label><input type="text" name="empleos[actual][jefe]" /></div>
                    <div class="form-group col-md-2"><label>Telefono:</label><input class="numerico" type="text" name="empleos[actual][telefono]" /></div>
                    <div class="form-group col-md-12"><label>Motivo de baja:</label><textarea name="empleos[actual][direccion]"></textarea></div>
                    <div class="form-group col-md-4"><label>Puesto:</label><input class="numerico" type="text" name="empleos[actual][puesto]" /></div>
                    <div class="form-group col-md-8"><label>Actividades Principales:</label><textarea name="empleos[actual][actividades]"></textarea></div>
                    <div class="form-group col-md-6"><label>Prestaciones:</label><textarea name="empleos[actual][prestaciones]"></textarea></div>
                    <div class="form-group col-md-6"><label>Motivo de baja:</label><textarea name="empleos[actual][motivo]"></textarea></div>
                </div>
                <div class="row">
                    <h3>Empleo Anterior</h3>
                    <div class="form-group col-md-8"><label>Nombre de la empresa:</label><input type="text" name="empleos[anterior][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Giro de la empresa:</label><input type="text" name="empleos[anterior][giro]" /></div>
                    <div class="form-group col-md-2"><label>Sueldo:</label><input class="fecha" type="text" name="empleos[anterior][sueldo]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Ingreso:</label><input class="fecha" type="text" name="empleos[anterior][alta]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Baja:</label><input class="fecha" type="text" name="empleos[anterior][baja]" /></div>
                    <div class="form-group col-md-4"><label>Jefe Inmediato:</label><input type="text" name="empleos[anterior][jefe]" /></div>
                    <div class="form-group col-md-2"><label>Telefono:</label><input class="numerico" type="text" name="empleos[anterior][telefono]" /></div>
                    <div class="form-group col-md-12"><label>Motivo de baja:</label><textarea name="empleos[anterior][direccion]"></textarea></div>
                    <div class="form-group col-md-4"><label>Puesto:</label><input class="numerico" type="text" name="empleos[anterior][puesto]" /></div>
                    <div class="form-group col-md-8"><label>Actividades Principales:</label><textarea name="empleos[anterior][actividades]"></textarea></div>
                    <div class="form-group col-md-6"><label>Prestaciones:</label><textarea name="empleos[anterior][prestaciones]"></textarea></div>
                    <div class="form-group col-md-6"><label>Motivo de baja:</label><textarea name="empleos[anterior][motivo]"></textarea></div>
                </div>
            </div>
            <div class="row">
                <h2>Referencias Personales</h2>
                <div class="row">
                    <div class="form-group col-md-8"><label>Nombre:</label><input type="text" name="referencias[0][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Telefono:</label><input type="text" name="empleos[0][telefono]" /></div>
                    <div class="form-group col-md-6"><label>Parentesco:</label><input type="text" name="empleos[0][parentesco]" /></div>
                    <div class="form-group col-md-6"><label>Tiempo de conocerlo:</label><input type="text" name="empleos[0][conociendose]" /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8"><label>Nombre:</label><input type="text" name="referencias[1][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Telefono:</label><input type="text" name="empleos[1][telefono]" /></div>
                    <div class="form-group col-md-6"><label>Parentesco:</label><input type="text" name="empleos[1][parentesco]" /></div>
                    <div class="form-group col-md-6"><label>Tiempo de conocerlo:</label><input type="text" name="empleos[1][conociendose]" /></div>
                </div>
            </div>
            <div class="row">
                <h2>Datos Generales</h2>
                <div class="row">
                    <div class="form-group col-md-3"><label>¿Cómo se enteró de la vacante?</label><input type="text" name="enteradoPor" /></div>
                    <div class="form-group col-md-3"><label>¿Trabajó antes en esta Empresa?</label><br>
                        <div class="radio-inline"><label><input class="trabajoAquiSi" type="radio" name="trabajoAqui" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="trabajoAquiNo" type="radio" name="trabajoAqui" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Disponibilidad de Horario?</label><br>
                        <div class="radio-inline"><label><input class="dispHorarioSi" type="radio" name="dispHorario" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="dispHorarioNo" type="radio" name="dispHorario" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Disponibilidad de Ingreso?</label><input type="text" name="dispIngreso" placeholder="ejemplo: 2 semanas, 1 mes, ..." /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12"><label>Conocimientos de computación:</label><textarea name="computacion"></textarea></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3"><label>Tatuajes</label><br>
                        <div class="radio-inline"><label><input class="tatuajesSi" type="radio" name="tatuajes" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="tatuajesNo" type="radio" name="tatuajes" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Padece alguna Enfermedad?</label><br>
                        <div class="radio-inline"><label><input class="enfermedadSi" type="radio" name="enfermedad[sino]" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="enfermedadNo" type="radio" name="enfermedad[sino]" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-6"><label>¿Cuál?</label><input class="cual" type="text" name="enfermedad[cual]" /></div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-1"><input class="btn btn-default" type="submit" /></div>
            </div>
            </form>
            <?php } ?>
        <?php break;
			case 's1':
			if(@$_GET["solicitud"]==""){
		?>
        	<form id="sorianaPrevio" role="form" class="inflow-media col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_vacantes.php">
            	<h2>Para aplicar a la vacante favor de llenar los siguientes campos</h2>
	            <input type="hidden" name="idVacante" value="<?php echo $idVacante; ?>" />
                <input type="hidden" name="proyecto" value="<?php echo $proyecto; ?>" />
                <input type="hidden" name="fase" value="1" />
                <div class="row">
	                <div class="form-group col-md-4"><label>CURP:</label><input class="requerido" type="text" name="curp" /></div>
                    <div class="form-group col-md-4"><label>Adjunta tu Curriculum Vitae aquí</label><input type="file" name="adjuntoCV" /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6"><label>Nombre Completo:</label><input class="requerido" type="text" name="nombreC" /></div>
                    <div class="form-group col-md-3"><label>Email:</label><input class="" type="text" name="email" /></div>
                    <div class="form-group col-md-3"><label>Telefono de Contacto:</label><input class="numerico requerido" type="text" name="telefono" /></div>
                </div>
                <div class="row">
                	<div class="form-group col-md-2"><label>Estado:</label>
                        <select name="estado">
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
	                <div class="form-group col-md-2"><label>Ciudad:</label><input type="text" name="ciudad" /></div>
                    <div class="form-group col-md-4"><label>¿Antecedentes Penales?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="no" checked="checked">No</label></div>
                    </div>
                    <div class="form-group col-md-4"><label>¿Antecedentes Laborales?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="no" checked="checked">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4"><label>Cédula Profesional</label><br>
                    	<div class="radio-inline"><label><input type="radio" class="cedulaProfesionalSi " name="cedulaProfesional" value="si"><input type="text" class="numerico" onclick="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true;" onKeyUp="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true; $('.cedulaProfesionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalNo" name="cedulaProfesional" value="no" checked="checked">No</label></div>
                    </div>
                    <div class="form-group col-md-4"><label>Cédula Provisional</label><br>
                    	<div class="radio-inline"><label><input type="radio" class="cedulaProvisionalSi" name="cedulaProvisional" value="si"><input type="text" class="numerico" onclick="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true;" onKeyUp="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true; $('.cedulaProvisionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalNo" name="cedulaProvisional" value="no" checked="checked">No</label></div>
                    </div>
                </div>
                <div class="row">
                	<div class="form-group col-md-6">
                    	<h4>Días que desea cubrir:</h4>
	                	<div class="checkbox-inline"><label><input type="checkbox" name="dias[]" value="L a V">Lunes a Viernes</label></div>
                        <div class="checkbox-inline"><label><input type="checkbox" name="dias[]" value="S">Sábados</label></div>
                        <div class="checkbox-inline"><label><input type="checkbox" name="dias[]" value="D">Domingos</label></div>
                    </div>
                    <div class="form-group col-md-6">
                    	<h4>Horarios que desea cubrir:</h4>
	                	<div class="checkbox-inline"><label><input type="checkbox" name="horarios[]" value="10 a 2">10:00am a 02:00pm</label></div>
                        <div class="checkbox-inline"><label><input type="checkbox" name="horarios[]" value="4 a 8">04:00pm a 08:00pm</label></div>
                    </div>
                </div>
                <div class="row">
                	<div align="right">
                    	<div class="form-group col-md-12"><input class="btn btn-success" type="submit" /></div>
                    </div>
                </div>
            </form>
            <?php }else{ ?>
            <?php
				$datosPrevios=$modelo->query2arr("select curp,json from solicitudes where idSolicitud={$_GET["solicitud"]};");
				$jsonPrevio=@$datosPrevios["data"][0]["json"];
				$curp=@$datosPrevios["data"][0]["curp"];
				if($jsonPrevio==""){break;}
				$jsonPrevio=json_decode(base64_decode($jsonPrevio),true);
				unset($jsonPrevio["idVacante"],$jsonPrevio["proyecto"],$jsonPrevio["fase"]);
				$jsonPrevio["curp"]=$curp;
				$jsonJS=json_encode($jsonPrevio,JSON_UNESCAPED_UNICODE);
            ?>
            <script type="text/javascript">
				var jsonPrevio=<?php echo $jsonJS; ?>;
				$(document).ready(function(e) {
					<?php if(@$_SESSION["idpanda"]==""){ ?>
					var curpPass=prompt("Para continuar teclee su CURP");
					if(!curpPass){
						$("#sorianaCompleto").remove();
						$(".container").append("<h2 align=\"center\">Vuelva a intentarlo, actualice la pagina</h2>");
					}else if(curpPass.toUpperCase()===jsonPrevio.curp){
						$("#sorianaCompleto").show();
					}else{
						$("#sorianaCompleto").remove();
						$(".container").append("<h2 align=\"center\">Vuelva a intentarlo, actualice la pagina</h2>");
					}
					<?php } ?>
					enthalpy.rellenarCampos(jsonPrevio,"#sorianaCompleto");
					$(".requisitosBtn").click(function(e) {
						$(".requisitos").slideToggle("fast");
					});
				});
			</script>
            <style>
				.requisitos{
					display:none;
				}
				#sorianaCompleto{
					/*display:none;/**/
				}
			</style>
            <form id="sorianaCompleto" role="form" class="inflow-media col-md-12" method="POST" action="<?php echo SCRIPT_URL; ?>s_vacantes.php">
            <input type="hidden" name="idSolicitud" value="<?php echo $_GET["solicitud"]; ?>" />
            <input type="hidden" name="idVacante" value="<?php echo $idVacante; ?>" />
            <input type="hidden" name="proyecto" value="<?php echo $proyecto; ?>" />
            <input type="hidden" name="fase" value="2" />
            <div class="row">
                <h2>Datos de la Vacante</h2>
                <div class="form-group col-md-2"><label>¿Trabaja Actualmente?:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="trabajoActual" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="trabajoActual" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Sueldo más reciente:</label><input class="numerico" type="text" name="sueldoActual" /></div>
                <div class="col-md-8">
                	<div class="row">
                        <h4>Requisitos de papelería</h4>
                        <p>Para acelerar el proceso de contratación, por favor adjunta en un archivo zip lo siguiente:</p>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Adjunta tu papelería aquí</label><input type="file" name="adjuntoP" />
                        </div>
                        <div class="col-md-6">
	                        <span class="requisitosBtn btn btn-info">Click para mostrar/ocultar requisitos</span>
                        </div>
                    </div>
                    <div class="row">
                        <ul class="requisitos">
                            <li>Curriculum Vitae</li>
                            <li>Acta de Nacimiento</li>
                            <li>Acta de Matrimonio (opcional)</li>
                            <li>Identificación Oficial</li>
                            <li>Comprobante IMSS</li>
                            <li>CURP</li>
                            <li>Comprobante de domicilio</li>
                            <li>Comprobante de Estudios</li>
                            <li>Cursos, capacitaciones o diplomas</li>
                            <li>Estado de cuenta AFORE (opcional)</li>
                            <li>Carta de recomendación (2)</li>
                            <li>Aviso de retención</li>
                            <li>Carta de No antecedentes penales</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <h2>Datos Personales</h2>
                <div class="form-group col-md-3"><label>¿Alta IMSS vigente?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="no">No</label></div>
                    <div class="radio-inline"><label><input type="radio" name="imssVigente" value="no lo se">No sé</label></div>
                </div>
                <div class="form-group col-md-2"><label>NSS:</label><input class="numerico" type="text" name="nss" /></div>
                <div class="form-group col-md-3"><label>RFC:</label><input class="requerido" type="text" name="rfc" /></div>
                <div class="form-group col-md-4"><label>CURP:</label><input class="requerido" type="text" name="curp" /></div>
                <div class="form-group col-md-6"><label>Nombre Completo:</label><input type="text" name="nombreC" /></div>
                <div class="form-group col-md-2"><label>Edad:</label><input class="numerico" type="text" name="edad" /></div>
                <div class="form-group col-md-2"><label>Fecha de Nacimiento:</label><input class="fecha" type="text" name="fechaNac" /></div>
                <div class="form-group col-md-2"><label>Sexo:</label>
                    <select name="sexo">	
                        <option disabled selected>--elige una--</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="form-group col-md-6"><label>Lugar de nacimiento:</label><input type="text" name="lugarNac" /></div>
                <div class="form-group col-md-3"><label>Estado Civil:</label>
                    <select name="estadoCivil">
                        <option disabled selected>--elige una--</option>
                        <option value="Soltero">Soltero(a)</option>
                        <option value="Casado">Casado(a)</option>
                    </select>
                </div>
                <div class="form-group col-md-3"><label>Religión:</label><input type="text" name="religion" /></div>
                <div class="form-group col-md-2"><label>Credencial INE:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="elector" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="elector" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Licencia:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="licencia" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="licencia" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Tipo Licencia:</label>
                    <select name="tipoLicencia">
                        <option value="N">Ninguna</option>
                        <option value="A">Tipo A</option>
                        <option value="B">Tipo B</option>
                        <option value="C">Tipo C</option>
                        <option value="D">Tipo D</option>
                    </select>
                </div>
                <div class="form-group col-md-2"><label>Casa:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="casa" value="propia">Propia</label></div>
                    <div class="radio-inline"><label><input type="radio" name="casa" value="si">Renta</label></div>
                </div>
                <div class="form-group col-md-2"><label>INFONAVIT:</label><br>
                    <div class="radio-inline"><label><input type="radio" name="infonavit" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="infonavit" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>Medio de Transporte:</label><br>
                    <select name="transporte">
                        <option value="publico">Público</option>
                        <option value="propio">Auto Propio</option>
                    </select>
                </div>
                <div class="form-group col-md-2"><label>¿Con quien vive?</label><input type="text" name="viveCon" /></div>
                <div class="form-group col-md-2"><label>¿Dependientes?</label><input class="numerico" type="text" name="dependientes" /></div>
                <div class="form-group col-md-2"><label>UMF:</label><input class="numerico" type="text" name="umf" /></div>
                <div class="form-group col-md-2"><label>¿Antecedentes Penales?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesPenales" value="no">No</label></div>
                </div>
                <div class="form-group col-md-2"><label>¿Antecedentes Laborales?</label><br>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="si">Sí</label></div>
                    <div class="radio-inline"><label><input type="radio" name="antecedentesLaborales" value="no">No</label></div>
                </div>
            </div>
            <div class="row">
                <h3>Domicilio</h3>
                <div class="form-group col-md-6"><label>Calle:</label><input type="text" name="calle" /></div>
                <div class="form-group col-md-3"><label># Ext:</label><input type="text" name="exterior" /></div>
                <div class="form-group col-md-3"><label># Int:</label><input type="text" name="interior" /></div>
                <div class="form-group col-md-6"><label>Colonia:</label><input type="text" name="colonia" /></div>
                <div class="form-group col-md-2"><label>Ciudad:</label><input type="text" name="ciudad" /></div>
                <div class="form-group col-md-2"><label>Estado:</label>
                	<select name="estado">
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
                <div class="form-group col-md-2"><label>Codigo Postal:</label><input class="numerico" type="text" name="cp" /></div>
            </div>
            <div class="row">
                <h2>Datos de Contacto</h2>
                <div class="form-group col-md-4"><label>Email:</label><input class="" type="text" name="email" /></div>
                <div class="form-group col-md-4"><label>Telefono:</label><input class="numerico requerido" type="text" name="telefono" /></div>
                <div class="form-group col-md-4"><label>Celular:</label><input class="numerico" type="text" name="celular" /></div>
            </div>
            <div class="row">
                <h2>Escolaridad</h2>
                <div class="row">
                    <div class="col-md-2"><h4>Técnica</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="" type="text" name="tecnicaInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="" type="text" name="tecnicaPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="tecnicaCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="tecnicaCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><h4>Preparatoria</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="" type="text" name="prepaInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="" type="text" name="prepaPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="prepaCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="prepaCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><h4>Profesional</h4></div>
                    <div class="form-group col-md-4 text-center"><input class="requerido" type="text" name="profInstitucion" placeholder="Institución" /></div>
                    <div class="form-group col-md-4"><input class="requerido" type="text" name="profPeriodo" placeholder="Fechas Inicio / Termino" /></div>
                    <div class="form-group col-md-2">
                        <label>¿Certificado?</label><br />
                        <div class="radio-inline"><label><input type="radio" name="profCert" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="profCert" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2"><label>¿Titulado?</label><br>
                        <div class="radio-inline"><label><input type="radio" name="titulado" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input type="radio" name="titulado" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>Cedula Profesional</label><br>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalSi" name="cedulaProfesional" value="si"><input class="cedulaProfesionalText" type="text" onclick="document.getElementsByClassName('cedulaProfesionalSi')[0].checked=true;" onKeyUp="$('.cedulaProfesionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProfesionalNo" name="cedulaProfesional" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>Cedula Provisional</label><br>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalSi" name="cedulaProvisional" value="si"><input class="cedulaProvisionalText" type="text" onclick="document.getElementsByClassName('cedulaProvisionalSi')[0].checked=true;" onKeyUp="$('.cedulaProvisionalSi').val(this.value);" /></label></div>
                        <div class="radio-inline"><label><input type="radio" class="cedulaProvisionalNo" name="cedulaProvisional" value="no">No</label></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12"><label>Cursos:</label><textarea name="cursos"></textarea></div>
                    <div class="form-group col-md-6"><label>Estudios Actuales:</label><input class="" type="text" name="estudioActual" /></div>
                    <div class="form-group col-md-6"><label>Horario de Estudios Actuales:</label><input class="" type="text" name="horarioEstudio" /></div>
                </div>
            </div>
            <div class="row">
                <h2>Datos de Familiares</h2>
                <div class="form-group col-md-12"><label>Padre:</label><input type="text" name="papa" /></div>
                <div class="form-group col-md-12"><label>Madre:</label><input type="text" name="mama" /></div>
                <div class="form-group col-md-12"><label>Esposo(a):</label><input type="text" name="conyuge" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[0]" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[1]" /></div>
                <div class="form-group col-md-12"><label>Hijo(a):</label><input type="text" name="hijo[2]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[0]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[1]" /></div>
                <div class="form-group col-md-12"><label>Hermano(a):</label><input type="text" name="hermano[2]" /></div>
            </div>
            <div class="row">
                <h2>Experiencia Laboral</h2>
                <div class="row">
                    <h3>Empleo Actual</h3>
                    <div class="form-group col-md-8"><label>Nombre de la empresa:</label><input type="text" name="empleos[actual][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Giro de la empresa:</label><input type="text" name="empleos[actual][giro]" /></div>
                    <div class="form-group col-md-2"><label>Sueldo:</label><input class="fecha" type="text" name="empleos[actual][sueldo]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Ingreso:</label><input class="fecha" type="text" name="empleos[actual][alta]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Baja:</label><input class="fecha" type="text" name="empleos[actual][baja]" /></div>
                    <div class="form-group col-md-4"><label>Jefe Inmediato:</label><input type="text" name="empleos[actual][jefe]" /></div>
                    <div class="form-group col-md-2"><label>Telefono:</label><input class="numerico" type="text" name="empleos[actual][telefono]" /></div>
                    <div class="form-group col-md-12"><label>Motivo de baja:</label><textarea name="empleos[actual][direccion]"></textarea></div>
                    <div class="form-group col-md-4"><label>Puesto:</label><input class="numerico" type="text" name="empleos[actual][puesto]" /></div>
                    <div class="form-group col-md-8"><label>Actividades Principales:</label><textarea name="empleos[actual][actividades]"></textarea></div>
                    <div class="form-group col-md-6"><label>Prestaciones:</label><textarea name="empleos[actual][prestaciones]"></textarea></div>
                    <div class="form-group col-md-6"><label>Motivo de baja:</label><textarea name="empleos[actual][motivo]"></textarea></div>
                </div>
                <div class="row">
                    <h3>Empleo Anterior</h3>
                    <div class="form-group col-md-8"><label>Nombre de la empresa:</label><input type="text" name="empleos[anterior][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Giro de la empresa:</label><input type="text" name="empleos[anterior][giro]" /></div>
                    <div class="form-group col-md-2"><label>Sueldo:</label><input class="fecha" type="text" name="empleos[anterior][sueldo]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Ingreso:</label><input class="fecha" type="text" name="empleos[anterior][alta]" /></div>
                    <div class="form-group col-md-2"><label>Fecha de Baja:</label><input class="fecha" type="text" name="empleos[anterior][baja]" /></div>
                    <div class="form-group col-md-4"><label>Jefe Inmediato:</label><input type="text" name="empleos[anterior][jefe]" /></div>
                    <div class="form-group col-md-2"><label>Telefono:</label><input class="numerico" type="text" name="empleos[anterior][telefono]" /></div>
                    <div class="form-group col-md-12"><label>Motivo de baja:</label><textarea name="empleos[anterior][direccion]"></textarea></div>
                    <div class="form-group col-md-4"><label>Puesto:</label><input class="numerico" type="text" name="empleos[anterior][puesto]" /></div>
                    <div class="form-group col-md-8"><label>Actividades Principales:</label><textarea name="empleos[anterior][actividades]"></textarea></div>
                    <div class="form-group col-md-6"><label>Prestaciones:</label><textarea name="empleos[anterior][prestaciones]"></textarea></div>
                    <div class="form-group col-md-6"><label>Motivo de baja:</label><textarea name="empleos[anterior][motivo]"></textarea></div>
                </div>
            </div>
            <div class="row">
                <h2>Referencias Personales</h2>
                <div class="row">
                    <div class="form-group col-md-8"><label>Nombre:</label><input type="text" name="referencias[0][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Telefono:</label><input type="text" name="empleos[0][telefono]" /></div>
                    <div class="form-group col-md-6"><label>Parentesco:</label><input type="text" name="empleos[0][parentesco]" /></div>
                    <div class="form-group col-md-6"><label>Tiempo de conocerlo:</label><input type="text" name="empleos[0][conociendose]" /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8"><label>Nombre:</label><input type="text" name="referencias[1][nombre]" /></div>
                    <div class="form-group col-md-4"><label>Telefono:</label><input type="text" name="empleos[1][telefono]" /></div>
                    <div class="form-group col-md-6"><label>Parentesco:</label><input type="text" name="empleos[1][parentesco]" /></div>
                    <div class="form-group col-md-6"><label>Tiempo de conocerlo:</label><input type="text" name="empleos[1][conociendose]" /></div>
                </div>
            </div>
            <div class="row">
                <h2>Datos Generales</h2>
                <div class="row">
                    <div class="form-group col-md-3"><label>¿Cómo se enteró de la vacante?</label><input type="text" name="enteradoPor" /></div>
                    <div class="form-group col-md-3"><label>¿Trabajó antes en esta Empresa?</label><br>
                        <div class="radio-inline"><label><input class="trabajoAquiSi" type="radio" name="trabajoAqui" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="trabajoAquiNo" type="radio" name="trabajoAqui" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Disponibilidad de Horario?</label><br>
                        <div class="radio-inline"><label><input class="dispHorarioSi" type="radio" name="dispHorario" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="dispHorarioNo" type="radio" name="dispHorario" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Disponibilidad de Ingreso?</label><input type="text" name="dispIngreso" placeholder="ejemplo: 2 semanas, 1 mes, ..." /></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12"><label>Conocimientos de computación:</label><textarea name="computacion"></textarea></div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3"><label>Tatuajes</label><br>
                        <div class="radio-inline"><label><input class="tatuajesSi" type="radio" name="tatuajes" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="tatuajesNo" type="radio" name="tatuajes" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-3"><label>¿Padece alguna Enfermedad?</label><br>
                        <div class="radio-inline"><label><input class="enfermedadSi" type="radio" name="enfermedad[sino]" value="si">Sí</label></div>
                        <div class="radio-inline"><label><input class="enfermedadNo" type="radio" name="enfermedad[sino]" value="no">No</label></div>
                    </div>
                    <div class="form-group col-md-6"><label>¿Cuál?</label><input class="cual" type="text" name="enfermedad[cual]" /></div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-1"><input class="btn btn-default" type="submit" /></div>
            </div>
            </form>
            <?php } ?>
        <?php break; ?>
    <?php } ?>
<?php } ?>
</div>