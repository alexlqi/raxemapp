<?php @session_start();
@include_once("header.config.php");
@include_once("../includes/inc.config.php");
?>
<script>
var timing=300; //miliseconds
$(document).ready(function(e) {
    $(".logoff").click(function(e) {
        $.ajax({
			url:'<?php echo ROOT_FOLDER; ?>scripts/s_login.php',
			cache:false,
			type:'POST',
			data:{
				ctrl:'logoff'
			},
			success: function(r){
				console.log(r);
				location.reload();
			}
		});
    });
	$(".glyphicon-home").click(function(e) {
        window.location="<?php echo ROOT_FOLDER; ?>index.php";
    });
	$(".nav-nivel-1").click(function(e) {
		if($(this).hasClass('logoff')){return false;}
		_lista=$(this).parent().find(".nav-nivel-2");	
        if(!_lista.is(":visible")){
			_lista.slideDown(timing);
		}else{
			_lista.slideUp(timing);
		}
    });
    $(".nav-nivel-2 li").click(function(e){
    	//para acceder a la liga
    	location.href=$(this).find("span").attr("data-href");
    });
    // para el nav icon clicks
	$(".nav-bars").click(function(e) {
        $(this).toggleClass("nav-icon-clicked");
		if($(this).hasClass("nav-icon-clicked")){
			//se abre
			$(".navcol").animate({'margin-left':0,},timing);
		}else{
			//se cierra
			$(".navcol").animate({'margin-left':-300,},timing);
		}
    });
    $(document).click(function(e) { 
	    if(!$(e.target).closest('.navColBtn').length && $(e.target).closest('.navcol').length==0) {
	        if($('.navcol').is(":visible")) {
	            $(".navcol").animate({'margin-left':-300,},timing);
	            if($('.nav-bars').hasClass("nav-icon-clicked")){
	            	$('.nav-bars').toggleClass("nav-icon-clicked");
	            }
	        }
	    }        
	});
});
</script>
<style>
.navBarTop{
	margin-right: 0;
	margin-left: 0;
	height:100%;
}
.navBarBtns{
	text-align:center;
	display: flex
	align-items: center;
	height:3em;
}
.navBarBtns div{
	cursor:pointer;
}
.logoff{
	cursor:pointer;
}
header{
	z-index:9999;
	position:absolute;
	top:0;
	width:100%;
	height:50px;
}
.navcol{
	top:0;
	left:0;
	position:fixed;
	width:300px;
	margin-left:-300px;
	height:100%;
	z-index:999;
	overflow-y:auto;
}
.navColBtn{
	z-index:99999;
	float:left;
}
.nav-icon{
	position:relative;
}
.nav-user-title{
	z-index:99999;
	position:relative;
}
.glyphicon-home{
	font-size:100%;
}
.nav-niveles{
	padding-top:50px;
}
.nav-niveles *{
	cursor:pointer;
	padding-left: 0;
}
.nav-nivel-1{
	padding: 5px 10px;
	word-break:normal;
	font-size:1.5em;
}
.nav-nivel-2 ul{
	-webkit-padding-start: 0;
	list-style-type:none;
	margin:0;
}
.nav-nivel-2 li{
	margin:0;
	padding:5px 5px 5px 30px;
	font-size:1.1em;
}
.nav-nivel-2 a{
	color:#000;
	text-decoration: none;
}
.titulo{
	font-size: 1.5em;
}
</style>
<?php if(@$_SESSION["idpanda"]!=""){ ?>
<div class="navcol paleta-1-sec shadow-3">
	<!--/ Aquí va todo el sistema, usar controles de acordeon de jquery /-->
    <div class="nav-niveles">
    <?php
    if(count($params->navBuild(ROOT_FOLDER))>0){ ?>
		<?php foreach($params->navBuild(ROOT_FOLDER) as $bloque){
			if($params->auth($bloque["permiso"])!==true){continue;}
		?>    
	    <div class="nav-wrap">
    		<div class="nav-nivel-1 hvr-glow"><?php echo $bloque["seccion"]; ?></div>
	        <div class="nav-nivel-2 paleta-1-terc" style="display:none;">
	        	<ul>
	        		<?php
	        		foreach ($bloque["modulo"] as $submodulo) {
	        			if($params->auth($submodulo[2])!==true){continue;}
	        		?>
	            		<li class="hvr-glow">
	            			<span data-href="<?php echo $submodulo[0]; ?>"><?php echo $submodulo[1]; ?></span>
	            		</li>
	                <?php } ?>
	            </ul>
	        </div>
	    </div>
	    <?php } ?>
	<?php } ?>
        <div class="nav-nivel-1 hvr-glow logoff">Cerrar sesión</div>
    </div>
</div>
<?php } ?>
<header class="container-flow paleta-1-pri">
	<!--/ Aquí van accesos directos o cosas generales /-->
	<div class="row navBarTop fullh" style="font-size: 2rem;">
        	<div class="col-xs-6 vertical-center fullh">
            	<?php if(@$_SESSION["pandaname"]!=""){ ?>
	            <div class="navColBtn nav-icon nav-bars"><div></div></div>
	            <div class="navColBtn" style="margin-left: 10px;"><?php echo APP_NAME; ?></div>
                <?php } ?>
        	</div>
            <div class="col-xs-6 vertical-center fullh"><div align="right" style="width:100%;"><?php echo @$_SESSION["pandaname"]; ?></div></div>
        <?php if(count($arriba)>0){ # los botones de arriba que serán a lo mucho 4?>
        	<div class="navBarBtns col-xs-12 paleta-1-pri">
        <?php $pans=round((12/(count($arriba)+1)),0);
				foreach($arriba as $botones){ ?>
	            	<div class="navBtn col-xs-<?php echo $pans; ?> paleta-1-acc"><a href="<?php echo $botones[0]; ?>"><?php echo $botones[1]; ?></a></div>
	    		<?php } ?>
	            <div class="navBtn col-xs-<?php echo $pans; ?> pull-right logoff paleta-1-acc">Cerrar sesión</div>
	        </div>
	    <?php } ?>
        </div>
    </div>
</header>