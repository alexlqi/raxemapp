<?php session_start();
if(substr($_SERVER["REQUEST_URI"],-1)!=="/"){
	header("location: {$_SERVER["REQUEST_URI"]}/");
}
$mimes=array(
	"application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>".docx",
	"application/vnd.openxmlformats-officedocument"=>".docx",
	"application/pdf"=>".pdf",
	"application/msword"=>".doc",
	"application/zip"=>".zip",
	"image/jpeg"=>".jpg",
	"application/octet-stream"=>".zip",
	"application/x-zip-compressed"=>".zip",
	"application/vnd.openxmlformats-officedocument.presentationml.presentation"=>".pptx",
);
if(@$_SESSION["idpanda"]!=""){
	@include_once("includes/config.php");
	#aquí se escribe el switch para descargar
	switch(@$_GET["getParam"][0]){
		case 'solicitudes':
			$dsnModelo=$dsnPmRHVacantes;
			@include_once("includes/class.modelo.php");
			$id=@$_GET["getParam"][2];
			
			switch(@$_GET["getParam"][1]){
				case 'CV':
					$data=$modelo->query2arr("select adjuntoCV,mimeCV from {$_GET["getParam"][0]} WHERE idSolicitud={$id}");
					if(!$data["err"]){
						if($data["data"][0]["mimeCV"]==""){
							echo "<script>alert('no existe el archivo');window.close();</script>";
							break;
						}
						header('Content-Type: application/octet-stream');
						header("Content-Transfer-Encoding: Binary");
						$ext=@$mimes[$data["data"][0]["mimeCV"]];
						header("Content-disposition: attachment; filename=\"solicitud_{$id}{$ext}"); 
						echo base64_decode($data["data"][0]["adjuntoCV"]);
					}else{
						echo "<script>alert('Ocurrió un error');window.close();</script>";
						break;
					}
				break;
				case 'P':
					$data=$modelo->query2arr("select adjuntoP,mimeP from {$_GET["getParam"][0]} WHERE idSolicitud={$id}");
					if(!$data["err"]){
						if($data["data"][0]["mimeP"]==""){
							echo "<script>alert('no existe el archivo');window.close();</script>";
							break;
						}
						$tmpfname = tempnam(sys_get_temp_dir(), 'd');
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						file_put_contents($tmpfname, base64_decode($data["data"][0]["adjuntoP"]));
						$mime=finfo_file($finfo,$tmpfname);
						//header('Content-Type: application/octet-stream');
						//header("Content-Transfer-Encoding: Binary");
						$ext=@$mimes[$mime];
						if($ext==""){echo $mime; break;}
						header("Content-disposition: attachment; filename=\"solicitud_{$id}{$ext}"); 
						echo base64_decode($data["data"][0]["adjuntoP"]);
					}else{
						echo "<script>alert('Ocurrió un error');window.close();</script>";
						break;
					}
				break;
			}
		break;
		case 'cambiarNombres':
			$file=base64_decode($_GET["getParam"][1]);
			$filename=basename($file);
			//header('Content-Type: application/octet-stream');
			//header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"{$filename}"); 
			echo file_get_contents($file);
		break;
		case 'pdfResultados':
			$metadata=explode("@",base64_decode($_GET["getParam"][1]));
			$file=$metadata[0];
			$filename=$metadata[1];
			header('Content-Type: application/pdf');
			header("Content-disposition: {$metadata[2]}; filename=\"{$filename}"); 
			header("Content-Transfer-Encoding: Binary");
			header("Accept-ranges: bytes"); 
			$data=file_get_contents($file);
			echo $data;
		break;
		case 'variosPdf':
			$metadata=explode("@",base64_decode($_GET["getParam"][1]));
			$file=$metadata[0];
			$filename=$metadata[1];
			header('Content-Type: application/pdf');
			header("Content-disposition: attachment; filename=\"{$filename}"); 
			header("Content-Transfer-Encoding: Binary");
			header("Accept-ranges: bytes"); 
			$data=file_get_contents($file);
			echo $data;
			@unlink($file);
		break;
	}
}else{
	echo "no tiene permisos para descargar <a href='/'>Inicie sesión aquí</a>";
}

?>