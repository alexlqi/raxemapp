<?php
function respuestaHtml($ctrl,$data){
	include("respuestas/{$ctrl}_{$data["fase"]}.php");
	return $html;
}
?>