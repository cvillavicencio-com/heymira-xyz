<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// logged in?
session_start();
$iniciodelsitio = floatval(microtime());
$m=true;

if (isset($_SESSION['log'])){
    $id=$_SESSION['log'];
    $log = true;
    $menu = array(
        array('<span class="icon-energy"></span> Nuevo link','nl'),
        array('<span class="icon-user"></span> Perfil','up'),
	array('<span class="icon-logout"></span> Cerrar sesión','cs')
    );
} else {
    $log = false;
    $menu = array(
        array('<span class="icon-login"></span> Iniciar sesión','is'),
        array('<span class="icon-key"></span> Crear cuenta','cc'),
    );
}


include('dbconfig.php');
$conn = new mysqli($srv, $usr, $pwd, $dbn);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HeyMira!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico"> 
    <script type="text/javascript" src="js/script.js" defer></script>
  </head>
  <body>
      <nav class="has-background-primary navbar"  role="navigation" aria-label="main navigation">
	  <div class="navbar-brand">
	      <a class="navbar-item" href="http://localhost/heymira">
		  <img src="logo.gif" width="112" height="28">
	      </a>
	      <div class="buttons">
		  <?php
		  if ($m){
		      foreach($menu as &$boton){
			  echo '
	      <a href="?f='.$boton[1].'" class="button">
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
      
      <section class="section ">
	  <div class="container ">
	<h1 class="title">
	    <?php
	    echo $contenido[0];
	    ?>
	</h1>

	    <?php
	    echo $contenido[1];
	    ?>

    </div>
      </section>

      <footer class="footer">
  <div class="content has-text-centered">
    <p>
	<strong>Heymira</strong>.
	<a href="https://github.com/cvillavicencio-com/heymira-xyz" target="_blank"><span class="icon-social-github"></span></a>.
	is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.<br>
	<?php

	$findelsitio = floatval(microtime());
	$tiempodesitio = $findelsitio-$iniciodelsitio;
	echo 'Tiempo de carga: '.$tiempodesitio;
	?>
    </p>
  </div>
      </footer>
  </body>
</html>



<hr>




<?php

function nologged(){
    return array('error','no estás conectado');
}
function logged(){
    return array('error','debes estar desconectado');
}

function cleanpost($a){
    $r = isset($_POST[$a]) ? htmlspecialchars($_POST[$a]) : false;
    return $r;
}
function cleanget($a){    
    $r = isset($_GET[$a]) ? htmlspecialchars($_GET[$a]) : false;
    return $r;
    
}
// fin funciones


?>


