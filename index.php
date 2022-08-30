<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// logged in?
session_start();

$m=true;

if (isset($_SESSION['log'])){
    $id=$_SESSION['log'];
    $log = true;
    $menu = array(
        array('Nuevo link','nl'),
        array('Perfil','up'),
	array('Cerrar sesión','ss')
    );
} else {
    $log = false;
    $menu = array(
        array('Iniciar sesión','is'),
        array('Crear cuenta','cc')
    );
}


include('dbconfig.php');
$conn = new mysqli($srv, $usr, $pwd, $dbn);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// menu de categorias acá
//    PENDIENTE
// hasta acá

if (isset($_GET['f'])){
    $f = cleanget('f');
    switch($f){
	case 'cc':
	    if ($log){$contenido=logged();break;}
	    $contenido[] = 'Crear cuenta';
	    $contenido[] = '
<div class="columns">
  <div class="column">
aca va una imagen bonita.
  </div>

  <div class="column">
<form action="./?f=nc" method="post">
<div class="field">
  <label class="label">Nombre de usuario</label>
  <div class="control">
    <input name="nombre" class="input" type="text" placeholder="Para identificarte dentro del sistema">
  </div>
</div>
<div class="field">
  <label class="label">Correo electrónico (opcional)</label>
  <div class="control">
    <input name="mail" class="input" type="email" placeholder="Para que recuperes tu cuenta, si olvidas tu contraseña">
  </div>
</div>
<div class="field">
  <label class="label">Contraseña</label>
  <div class="control">
    <input name="clave" class="input" type="password" placeholder="Para saber que eres tu">
  </div>
</div>
<div class="field">
  <div class="control">
    <button class="button is-link">Crear cuenta</button>
  </div>
</div>

</form>
</div>
</div>
	    ';
	    break;

	case 'nc':
	    $m=false;
	    if ($log){$contenido=logged();break;}
	    $contenido[] = "Creando cuenta";
	    $nombre = cleanpost('nombre');
	    $mail =   cleanpost('mail');
	    $clave = sha1(cleanpost('clave'));
	    //	    $clave=cleanpost('clave');

	    $wmail = $mail ? "OR mail = '$mail'" : false;
	    $n = "SELECT id FROM Users WHERE nombre = '$nombre' $wmail;";
	    $result = $conn->query($n);
	    if ($result->num_rows == 0) {
		$qmail = $mail ? ',mail':false;
		$vmail = $mail ? ",'$mail'":false;
		
		$c = "INSERT INTO Users (nombre,clave $qmail) VALUES ('$nombre','$clave' $qmail);";
		$resultm=$conn->query($c);
		if (!empty($result)){
		    $contenido[] ='Cuenta creada.';
		} else {
		    $contenido[] = 'error';
		}

	    } else {
		$contenido[] = 'No se puede crear la cuenta, intenta utilizando otros datos.';
	    }
	    

	    break;


	case 'is':
	    if ($log){$contenido=logged();break;}
	    $contenido[] = 'Iniciar sesión';
	    $contenido[] = '
<div class="columns">
<div class="column">
<form action="./?f=si" method="post">
<div class="field">
  <label class="label">Nombre de usuario</label>
  <div class="control">
    <input name="nombre" class="input" type="text" placeholder="nombre de usuario o correo electrónico">
  </div>
</div>
<div class="field">
  <label class="label">Contraseña</label>
  <div class="control">
    <input name="clave" class="input" type="password" placeholder="****">
  </div>
</div>
<div class="field">
  <div class="control">
    <button class="button is-link">Iniciar sesión</button>
  </div>
</div>
</form>
</div>
<div class="column">
	    acá va una imagen bonita
	    </div>
	    </div>	    ';
	    break;

	case 'si':
	    $m=false;
	    $contenido[] = 'Iniciando sesión';
	    $nombre=cleanpost('nombre');
	    $clave=cleanpost('clave');
	    $clave = $clave ? sha1($clave) : false; 
	    $n = "SELECT clave, id FROM Users WHERE nombre = '$nombre' OR mail = '$nombre';";
	    $result = $conn->query($n);
	    $laclave = $result->fetch_row();
	    if ($result->num_rows != 0) {	    
		if ($laclave[0] == $clave){
		    $_SESSION["log"]=$laclave[1];
		    $contenido[]='sesión iniciada correctamente';
		} else {
	    	    $contenido[]='contraseña incorrecta';
		}		
	    } else {
		$contenido[]='nombre de usuario no encontrado';
	    }
	    
	    break;

	case 'ss':
	    $m=false;
	    unset($_SESSION['log']);	    
	    $contenido[] = 'Sesión cerrada';
	    $contenido[] = '<p>te has desconectado.</p>

<div class="buttons"><a href=".">
  <button class="button is-primary is-light">Volver al inicio</button></a>
</div>
	    ';
	    break;

	case 'up':
	    if (!$log){$contenido=nologged();break;}
	    $id = isset($_GET['id']) ? intval(cleanget('id')) : $id;
	    $n = "SELECT * FROM Userinfo WHERE id = '$id';";	    
	    $perfilq = $conn->query($n);
	    $perfil = $perfilq->fetch_row();
	    //	    print_r($perfil);
	    
	    $contenido[] = 'imagen bonita';
	    //	    $mail = $perfil[3] ? ' ('.$perfil[3].')' : false; 
	    $contenido[] = '
<div class="card">
  <div class="card-content">
    <div class="columns">
      <div class="column has-background-grey-lighter  is-one-quarter ">
        <div class="media">
          <div class="media-content">
            <p class="title is-4">'.$perfil[1].@$mail.'</p>
            <p class="subtitle is-6">'.$perfil[4].'</p>
          </div>
        </div>
      </div>
      <div class="column">
	    '.$perfil[2].'
      </div>
    </div>
<div class="content">

    </div>
  </div>
</div>

cont';
	    break;

	    
	case 'nl':




	    
	    if (!$log){$contenido=nologged();break;}
	    $cats ='';
	    $contenido[] = 'Nuevo link';
	    $sqlc = "SELECT id, nombre FROM Categories;";
	    $resultc = $conn->query($sqlc);

	    $colcol=0;
	    if ($resultc->num_rows > 0) {
		$cats .= '<div style="max-height: 200px; overflow-y:scroll; overflow-x:hidden;">';
		while($rowc = $resultc->fetch_assoc()) {
		    $color = (is_int($colcol/2)) ? 'has-background-grey-lighter ': false;
		    $colcol++;
		    $cats .='<div class="columns">';
		    $cats .= '<div class="column '.$color.' is-one-quarter">'.$rowc['nombre'].'</div>';
		    $sqls = "SELECT id, nombre FROM Subcategories WHERE categId='".$rowc['id']."';";
		    $results = $conn->query($sqls);
		    $cats .= '<div class="column">';
		    if ($results->num_rows > 0) {
			while($rows = $results->fetch_assoc()) {
			    $cats .= '<div class="columns '.$color.'">';
			    $cats .= '<div class="column is-one-quarter">'.$rows['nombre'].'</div>';
			    $sqlt = "SELECT id, nombre FROM Topics WHERE subcatId='".$rows['id']."';";
			    $resultt = $conn->query($sqlt);
			    $cats .= '<div class="column">';
			    if ($results->num_rows > 0) {
				$cats .= '<ul>';
				while($rowt = $resultt->fetch_assoc()) {
				    $cats .= '
<li><label class="radio"><input type="radio" name="topic" value="'.$rowt['id'].'"> '.$rowt['nombre'].'</label></li>';
				}
				$cats .= '</ul></div>';
			    } else {
				$cats .= 'no hay topicos';
			    }
			    $cats .= '</div>';
			}

			$cats .= '</div>';

		    } else {
			$cats .= 'no hay subcats';
		    }
		    $cats .= '</div>';

		}

		$cats .= '</div>';
	    } else {
		$cats .= 'no hay cats';
	    }





	    
	    
	    $contenido[] = '
<div class="columns is-centered">
  <div class="column is-half ">
<div class="field">
  <label class="label">URL</label>
  <div class="control">
    <input class="input" type="text" placeholder="Text input">
  </div>

</div>

<div class="field">
  <label class="label">Descripción</label>
  <div class="control">
    <input class="input" type="text" placeholder="Text input">
  </div>
</div>

<div class="field">
  <label class="label">Tema</label>
	    '.$cats.'
</div>
  </div>

</div>

	    ';
	    break;

    }
} else {
    $contenido[]='aaa';
    $contenido[]='eee';
    
}





?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello Bulma!</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script type="text/javascript" src="js/script.js" defer></script>
  </head>
  <body>
      <nav class="has-background-primary navbar" role="navigation" aria-label="main navigation">
	  <div class="navbar-brand">
	      <a class="navbar-item" href="https://bulma.io">
		  <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
	      </a>

	      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
		  <span aria-hidden="true"></span>
		  <span aria-hidden="true"></span>
		  <span aria-hidden="true"></span>
	      </a>
	  </div>

	  <div id="navbarBasicExample" class="navbar-menu">
	      <div class="navbar-start">
		  <a class="navbar-item">
		      Home
		  </a>

		  <a class="navbar-item">
		      Documentation
		  </a>

		  <div class="navbar-item has-dropdown is-hoverable">
		      <a class="navbar-link">
			  More
		      </a>

		      <div class="navbar-dropdown">
			  <a class="navbar-item">
			      About
			  </a>
			  <a class="navbar-item">
			      Jobs
			  </a>
			  <a class="navbar-item">
			      Contact
			  </a>
			  <hr class="navbar-divider">
			  <a class="navbar-item">
			      Report an issue
			  </a>
		      </div>
		  </div>
	      </div>

	      <div class="navbar-end">
		  <div class="navbar-item">
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
		      </div>
		  </div>
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


