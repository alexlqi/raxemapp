<table class="header">
    <tr>
    	<td style="width:10%;"><img src="<?php echo __DIR__."/images/raxem_logo_sm.jpg"; ?>" height="70" width="70" /></td>
        <td align="center" style="width:80%;"><h3>RAXEM</h3><h4>Radiología Empresaial Mexicana</h4><h5>Damos valor agregado a la interpretación de tus radiografías</h5></td>
        <td style="width:10%;">Fecha: 2017-09-27</td>
    </tr>
</table>
<?php tabla(array("folio","cliente","tipoexamen","nombre","edad"),array("class"=>"body hw","formato"=>"v"),$tableData);; ?> 
<p class="display-5 textbox">
DESPUES DE VALORAR EN TOTALIDAD LAS ESTRUCTURAS DE <b>AP Y LAT COLUMNA LUMBAR/TORAX</b>; RESCATAMOS LOS SIGUIENTES HALLAZGOS DE IMPORTANCIA CLINICA.
</p>
<?php tabla(array("h","comentario"),array("class"=>"body fw","formato"=>"textbox"),$tableData);; ?>
<div style="font-style:italic;"><font color="#990000">NOTA:</font> No hay estudio que sustituya un adecuado interrogatorio y una completa exploracion clinica.</div>
<footer style="position:fixed; bottom:0; width:100%;">
	<div style="margin:10px auto; width:35%;" align="center">
        <img src="<?php echo __DIR__."/images/"; ?>59c2dd325932b.jpg" height="50" /><br />
        <div style="display:block;width:100%;margin:10px 0 0; padding:0; border-top:0.1pt solid #000;" align="center"><b>Médico Prueba</b></div>
        <span style="width:100%;">Cedula Profesional: 123456789 <?php echo isLandscape(__DIR__."/images/{$data["medSign"]}"); ?></span>
    </div>
	<div style="border-top:0.1pt solid #000;font-size:7pt;text-align:center;">Raxem - Radilogia Empresarial Mexicana - Datos de la empresa - Telefono de la empresa - info@raxem.com.mx</div>
</footer>