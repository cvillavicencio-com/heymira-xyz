<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// logged in?
session_start();
$iniciodelsitio = floatval(microtime());
include('dbconfig.php');
$tema=1;
$m = true;

if (isset($_SESSION['log']) || isset($_COOKIE['log'])){
    $tokenIn = isset($_SESSION['log']) ? $_SESSION['log']: $_COOKIE['log'];
    // consigue id desde db
    $tokenS = "SELECT id, setfav, tema, nombre FROM Users WHERE token = '$tokenIn';";
    $tokenQ = $conn->query($tokenS) or die(mysqli_error());
    $tokenL = $tokenQ->fetch_row();

    if (!empty($tokenL)){
    $id = $tokenL[0];
    $setfav = $tokenL[1];
    $tema = $tokenL[2];
    

    //    if (!$id){echo '- sistema caído ;-)'; exit();}

    
    $log = true;
    $menu = array(
        array('<span class="icon-energy"></span> Nuevo link','nl'),
        array('<span class="icon-magnifier"></span> Buscar','bu'),
        array('<span class="icon-user"></span> '.$tokenL[3],'up'),
        array('<span class="icon-logout"></span>','cs'),
    );
    } else {
        $log = false;
    $menu = array(
        array('<span class="icon-login"></span> Iniciar sesión','is'),
        array('<span class="icon-key"></span> Crear cuenta','cc'),
    );

    }
} else {
    $log = false;
    $menu = array(
        array('<span class="icon-login"></span> Iniciar sesión','is'),
        array('<span class="icon-key"></span> Crear cuenta','cc'),
    );
}




if (isset($_GET['f'])){
    $f = cleanget('f');
    include('f.php');
} elseif (isset($_GET['l'])){
    include('vl.php');    
} else {
    include('ll.php');
}
?>

<!DOCTYPE html>
<html class="cbody-<?php echo $tema; ?>">
    <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>HeyMira!</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/themes.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">    
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
	<link rel="icon" type="image/x-icon" href="favicon.ico">


    </head>
    <body >
	<nav class="has-background-primary navbar"  role="navigation" aria-label="main navigation">
	    <div class="navbar-brand">
		<a class="navbar-item" href=".">
		    <img src="logo.gif" width="112" height="28">
		</a>
		<div class="buttons">
		    <?php
		    if ($m){
			foreach($menu as &$boton){
			    echo '
	      <a href="?f='.$boton[1].'" class="button is-small is-rounded">
		  <strong>'.$boton[0].'</strong>
              </a>';
			}
		    } else {
			echo'<a href="." class="button">
		  <strong>Volver al inicio</strong>
              </a>
			';
		    }
		    ?>
		    <?php
		    //echo @$estasen;  ?>
		</div>

	    </div>
	    

	</nav>
	
	<section class="section">
	    <div class="container ccont">
		<h1 class="title">
		    <a name="title"></a>
		    <?php
		    echo $contenido[0];
		    ?>
		</h1>

		<?php
		echo $contenido[1];
		?>

	    </div>
	</section>

	<footer>


		<p>
		    <strong>Heymira</strong> es <a href="info" target="_blank">software libre</a>.<br>

		    <?php

		    $findelsitio = floatval(microtime());
		    $tiempodesitio = $findelsitio - $iniciodelsitio;
echo 'Tiempo de carga: ' . substr($tiempodesitio,0,6);
		    ?>
		</p>

	</footer><br>
    </body>
    <script type="text/javascript" src="js/script.js" async></script>

</html>








<?php


$conn->close();
                         
function nologged() {
    return array('error','no estás conectado');
}
function logged() {
    return array('error','debes estar desconectado');
}

function cleanpost($a) {
    if (is_array($_POST[$a])) {
	$r=array();
	foreach ($_POST[$a] as &$b){
	    $r[] = htmlspecialchars($b, ENT_QUOTES);
	}
    } elseif (!is_array($_POST[$a])) {
        $r = isset($_POST[$a]) ? htmlspecialchars($_POST[$a], ENT_QUOTES) : false;
    } else {
	$r = false;
    }    
    return $r;
}

function cleanget($a){    
    $r = isset($_GET[$a]) ? htmlspecialchars($_GET[$a], ENT_QUOTES) : false;
    return $r;
}

function imgredirect($src,$loc,$txt,$alert=FALSE){
    $alert = $alert ? 'alert(\''.$txt.'\');' : false;
    return '<img src="'.$src.'" onload="'.$alert.'setTimeout((window.location.replace(\''.$loc.'\')),5000);"><p>'.$txt.'</p>';
}
// fin funciones


?>


