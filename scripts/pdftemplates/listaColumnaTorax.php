<table class="header">
    <tr>
    	<td style="width:10%;"><img src="<?php echo __DIR__."/images/raxem_logo_sm.jpg"; ?>" height="70" width="70" /></td>
        <td align="center" style="width:80%;"><h3>RAXEM</h3><h4>Radiología Empresarial Mexicana</h4><h5>Damos valor agregado a la interpretación de tus radiografías</h5></td>
        <td style="width:10%;">Fecha: {{%fecha}}</td>
    </tr>
</table>
{{@tabla(array("folio","empresa","cliente","tipoexamen","nombre","edad"),array("class"=>"body hw","formato"=>"v"),$tableData);}} 
<p class="display-5 textbox">
DESPUES DE VALORAR EN TOTALIDAD LAS ESTRUCTURAS DE <b>{{%proyeccion}}</b>; RESCATAMOS LOS SIGUIENTES HALLAZGOS DE IMPORTANCIA CLINICA.
</p>
<h4>Resultados de Columna</h4>
{{@tabla(array("e","r","bp","els"),array("class"=>"body fw"),$tableData);}}
{{@tabla(array("be","eg3","cv","uls"),array("class"=>"body fw"),$tableData);}}
{{@tabla(array("ce","a","eiv","cuv"),array("class"=>"body fw"),$tableData);}}
{{@tabla(array("l","tb"),array("class"=>"body hw"),$tableData);}}
{{@tabla(array("conclusion"),array("class"=>"body fw","formato"=>"textbox"),$tableData);}}
<h4>Resultados de Tórax</h4>
{{@tabla(array("ht","conclusiont"),array("class"=>"body fw","formato"=>"textbox"),$tableData);}}
{{@tabla(array("comentario"),array("class"=>"body fw","formato"=>"textbox"),$tableData);}}
<div style="font-style:italic;"><font color="#990000">NOTA:</font> No hay estudio que sustituya un adecuado interrogatorio y una completa exploracion clinica.</div>
<footer style="position:fixed; bottom:0; width:100%;">
	<div style="margin:10px auto; width:35%;" align="center">
        <img src="<?php echo __DIR__."/images/"; ?>{{%medSign}}" <?php echo (isLandscape(__DIR__."/images/{$data["medSign"]}"))? "height" : "width" ; ?>="50" /><br />
        <div style="display:block;width:100%;margin:10px 0 0; padding:0; border-top:0.1pt solid #000;" align="center"><b>{{%medName}}</b></div>
        <span style="width:100%;">Cedula Profesional: {{%medCed}}</span>
    </div>
	<div style="border-top:0.1pt solid #000;font-size:7pt;text-align:center;">Raxem - Radiologia Empresarial Mexicana - contacto@raxem.com.mx</div>
</footer>