El paradigma de utilizar la carpeta partes es para organizar de manera eficiente, por secciones las partes de cada módulo y así modificarlos fácilmente. El hecho de utilizar un framework o 1 función ajax en general permite la incorporación de código particularizado a cada fragmento de la vista. Así se puede hacer un arbol de la aplicación independiente por sección > módulo(s) > submódulo(s) > fragmento, y en cada uno de estos hacer uso de manera independiente de las clases y funciones de PHP y javascript disponibles en func_enthalpy.js e init.js y en las clases de php.

Así cada carpeta podría ser una sección que contiene todas las vistas de la misma.
En la carpeta "partes", estarán las generalidades del sistema, es decir, las vistas compartidas, como la navegación o un header.

## -partes y subpartes- ##
se recomienda hacer una división de cada parte de una parte, a esto se le llamará submodulo, cuando se necesite recargar vía ajax.
Se pueden usar dos maneras para la carga de las partes dependientes de la parte mayor:
1) utilizar AJAX para procesarlo en el servidor y traer el fragmento inferior hacia alguna parte del DOM.
	Esto implica utilizar una referencia a clases y otros archivos de manera absoluta.
	La ventaja de utilizar este método es que se puede repetir varias veces sin afectar la aplicación en sí y de manera fluida sin cambiar de pagina actual.
2) utilizar includes de PHP
	La ventaja de esto es el procesamiento rápdio de mucha información como en las datatables.
	La desventaja más grande es que se tiene que cargar de nuevo la página.

## -formularios- ##
Para los formularios se harán de dos maneras diferentes y en cualquiera de las dos (o más si llega a haber alguna otra) se evitará que los formularios con la clase inflow hagan la petición html y la realizarán vía ajax.
inflow 			-ajax plain text
inflow-media 	-ajax multipar encoded
outflow			-html request normal
				-sin clase no se procesará el formulario
lo mejor será escribir el codigo javascript por formulario ya que no se pude generalizar del todo debido a que genera diferentes procesamientos y diferentes respuestas. Lo que sí es que se pueden tener algunas funciones como enthalpy.ajax y la enthalpy.notificaciones para ser usadas para input y output generales

Para el uso generico de los formularios vía AJAX, se ha pensado en declarar una variable general llamada callbackFn y se modificará cada vez que alguien haga uso de un formulario y estará definida según sea el formulario.

## -Autocomplete- ##
Para el uso generico del autocomplete en el sdk framework se usará una variable global para capturar todos los autocompletes y así se configuren en cada sección.
Se usará el atributo data-autocomplete para referenciar el objeto del autocomplete.
El autocomplete se activiará con el evento keyup y buscará el data-autocomplete de cada elemento que lo dispare para checar minLength y, cuando sea >= cantidad de caracteres escritos en el campo, se activará.
autocompletes["autocomplete1"]={
	source: "scripts/s_autocomplete.php?ctrl=p",
	minLength: 2,
	select: function( event, ui ) {
		$(".autocompletar").val(ui.item.label);
		$(".autocomplete-descripcion").text(ui.item.descripcion);
	}
}