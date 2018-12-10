<?php session_start();
header('Content-type: application/json');
@include("../includes/config.php");
$dsnModelo=$dsnPmCompras;
@include("../includes/class.modelo.php");
$r=$no_permite=array("err"=>true,"msg"=>"No tiene permisos de ejecución.");

$cfgProveedores=array(
	"articulos"=>array(
		"DNM090610RW2"=>array(
			"codigo"=>"noIdentificacion",
			"nombre"=>"descripcion",
			"unidad"=>"unidad",
			"cantidad"=>"cantidad",
		),
		"CHI990710I32"=>array(
			"codigo"=>"generar",
			"nombre"=>"descripcion",
			"unidad"=>"unidad",
			"cantidad"=>"cantidad",
		),
	),
);

switch(@$_POST["seccion"]){
	case 'inventario':
		switch(@$_POST["ctrl"]){
			case 'altaFactura':
				@include("funcionesXml.php");
				$batch=array();
				$arcnum=0;
				foreach($_FILES["factura"]["tmp_name"] as $f){
					$arbol=array();
					$xmlStr=file_get_contents($f);
					//leer el documento
					$xmlDoc = new DOMDocument();
					$xmlDoc->loadXml($xmlStr);
					$xm = $xmlDoc->documentElement;
					//funcion de xmlRecursivo
					$tree=array();
					$tipoXml="factura";
					$arbol=xmlRecursivo($xm,$xm->nodeName);
					//ponerlo en batch
					$batch[$arcnum]["filename"]=$f;
					$batch[$arcnum]["data"]=$arbol;
					$xmlDoc=NULL;
					$arcnum++;
				}
				foreach($batch as $factura){
					## obtener el proveedor ["cfdi:Emisor"] 
					$insertProveedor=array();
					$insertProveedor[]=array(
						"rfc"=>$factura["data"]["cfdi:Emisor"]["rfc"],
						"razon"=>$factura["data"]["cfdi:Emisor"]["nombre"],
					);
					# insertamos el proveedor y sacamos su id de proveedor
					$modelo->array2insert("proveedores",$insertProveedor,"","",2);
					# buscamos al proveedor
					$idProveedor=$modelo->query2arr("select idProveedor from proveedores where rfc='{$factura["data"]["cfdi:Emisor"]["rfc"]}';");
					$idProveedor=$idProveedor["data"][0]["idProveedor"];
					
					## obtenemos la factura
					$insertFactura=array();
					$insertFactura[]=array(
						"uuid"=>@$factura["data"]["tfd:TimbreFiscalDigital"]["UUID"],
						"serie"=>@$factura["data"]["cfdi:Comprobante"]["serie"],
						"folio"=>@$factura["data"]["cfdi:Comprobante"]["folio"],
						"fecha"=>@$factura["data"]["tfd:TimbreFiscalDigital"]["FechaTimbrado"],
					);
					# insertamos la factura
					$modelo->array2insert("facturas",$insertFactura,"","",2);
					# buscamos la factura
					$idFactura=$modelo->query2arr("select idFactura from facturas where uuid='{$factura["data"]["tfd:TimbreFiscalDigital"]["UUID"]}';");
					$idFactura=$idFactura["data"][0]["idFactura"];
					
					## obtener los conceptos ["cfdi:Conceptos"] 
					$insertArticulos=array();
					$insertInventario=array();
					$insertConceptos=array();
					$contador[$idProveedor]=1;
					$conceptos=array();
					foreach($factura["data"]["cfdi:Concepto"] as $id=>$concepto){
						foreach($cfgProveedores["articulos"][$factura["data"]["cfdi:Emisor"]["rfc"]] as $colPM=>$colProv){
							if($colProv=="generar"){
								# buscar si existe nombre
								$codigo=$modelo->query2array("select codigo from articulos where idProveedor={$idProveedor} and nombre='{$concepto[$cfgProveedores["articulos"][$factura["data"]["cfdi:Emisor"]["rfc"]]["nombre"]]}';");
								if(!$codigo["err"]){
									#si existe entonces 
									$conceptos[$id][$colPM]=@$codigo["data"][0]["codigo"];
								}else{
									# si no existe entonces se genera un consecutivo
									$codigo=$modelo->query2array("select count(*)+{$contador[$idProveedor]} as consecutivo from articulos where idProveedor={$idProveedor};");
									$conceptos[$id][$colPM]=$codigo["data"][0]["consecutivo"];
									$contador[$idProveedor]++;
								}
							}else{
								$conceptos[$id][$colPM]=@$concepto[$colProv];
							}
						}
					}
					
					# insertamos los articulos
					foreach($conceptos as $id=>$dConcepto){
						$insertArticulos[$id]["idProveedor"]=$idProveedor;
						$insertArticulos[$id]["codigo"]=$dConcepto["codigo"];
						$insertArticulos[$id]["nombre"]=$dConcepto["nombre"];
						$insertArticulos[$id]["unidad"]=$dConcepto["unidad"];
					}
					$tmp=$modelo->array2insert("articulos",$insertArticulos,""," ON DUPLICATE KEY UPDATE idProveedor=idProveedor ",2);
					# buscamos los articulos y los metemos al array de inventario
					
					foreach($conceptos as $id=>$dConcepto){
						$insertInventario[$id]["idProveedor"]=$idProveedor;
						$insertInventario[$id]["idFactura"]=$idFactura;
						$idArticulo=$modelo->query2arr("select idArticulo from articulos where idProveedor={$idProveedor} and codigo='{$dConcepto["codigo"]}';");
						$insertInventario[$id]["idArticulo"]=@$idArticulo["data"][0]["idArticulo"];
						$insertInventario[$id]["cantidad"]=$dConcepto["cantidad"];
					}
					$modelo->array2insert("inventario_factura",$insertInventario,""," ON DUPLICATE KEY UPDATE cantidad=cantidad ");
				}
				//$r=$modelo->array2insert("solicitudes",$insertData);
			break;
		}
	break;
}

echo json_encode($r);
?>