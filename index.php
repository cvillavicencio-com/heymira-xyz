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
        array('<span class="icon-energy"></span>','nl'),
        array('<span class="icon-user"></span>','up'),
	array('<span class="icon-logout"></span>','ss')
    );
} else {
    $log = false;
    $menu = array(
        array('<span class="icon-login"></span>','is'),
        array('<span class="icon-key"></span>','cc'),
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
		    $contenido[]='<img src="logo.png" onload="window.location.replace(\'/heymira\');"><p>sesión iniciada correctamente</p>';
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
	    $contenido[] = '<p onload="window.location.replace(\'/.\');">te has desconectado.</p>
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
	    $mail = $perfil[3] ? ' ('.$perfil[3].')' : false;



	    
	    $contenido[] = '
<div class="card">
  <div class="card-content">
    <div class="columns">
      <div class="column has-background-grey-lighter  is-one-quarter ">
        <div class="media">
          <div class="media-content">
            <p class="title is-4">'.$perfil[1].@$mail.'</p>
            <p class="subtitle is-6">'.$perfil[4].'</p>
            <p class="subtitle is-6"><a href="?u='.$id.'">Ver links</a></p>
          </div>
        </div>
      </div>
      <div class="column">
	    '.$perfil[2].'
      </div>
    </div>
  </div>
</div>


	    ';
	    break;

	    
	case 'nl':
	    if (!$log){$contenido=nologged();break;}
	    $cats ='';
	    $contenido[] = 'Nuevo link';
	    $sqlc = "SELECT id, nombre FROM Categories;";
	    $resultc = $conn->query($sqlc);

	    $colcol=0;
	    $colcolsub=0;
	    if ($resultc->num_rows > 0) {
		$cats .= '<div style="max-height: 200px; overflow-y:scroll; overflow-x:hidden;">';
		while($rowc = $resultc->fetch_assoc()) {
		    $color = (is_int($colcol/2)) ? 'has-background-grey-light ': false;
		    $colcol++;
		    $cats .='<div class="columns is-mobile">';
		    $cats .= '<div class="column '.$color.' is-one-quarter"><p style="position:sticky; top:0px;">'.$rowc['nombre'].'</p></div>';
		    $sqls = "SELECT id, nombre FROM Subcategories WHERE categId='".$rowc['id']."';";
		    $results = $conn->query($sqls);
		    $cats .= '<div class="column">';
		    if ($results->num_rows > 0) {
			while($rows = $results->fetch_assoc()) {
			    $colorsub = (is_int($colcolsub/2)) ? 'has-background-grey-lighter ': false;
			    $colcolsub++;
			    $cats .= '<div class="columns is-mobile '.$colorsub.'">';
			    $cats .= '<div class="column '.$colorsub.'is-one-quarter"><p style="position:sticky; top:0px;">'.$rows['nombre'].'</p></div>';
			    $sqlt = "SELECT id, nombre FROM Topics WHERE subcatId='".$rows['id']."';";
			    $resultt = $conn->query($sqlt);
			    $cats .= '<div class="column is-mobile">';
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
<form action="./?f=al" method="POST">
<div class="columns is-centered">
  <div class="column is-half">
    <div class="field">
      <label class="label">Descripción</label>
      <div class="control">
        <input name="info" class="input" type="text" placeholder="De qué trata este link">
      </div>
    </div>
    <div class="field">
      <label class="label">URL</label>
      <div class="control">
        <input name="url" class="input" type="text" placeholder="Dirección del link">
      </div>
    </div>
    <div class="field">
      <label class="label">Tema</label>
	    '.$cats.'
    </div>

    <div class="columns is-mobile">
      <div class="column">
        <div class="field">
          <label class="label is-small">URL extra (opcional)</label>
          <div class="control">
            <input name="urlextra" class="input  is-small" type="text" placeholder="Respaldo o link relacionado al mismo tema">
          </div>
        </div>
      </div>
      <div class="column">
       <div class="control">
         <button type="submit" class="button is-primary is-large">Agregar link</button>
       </div>
      </div>
    </div>



  </div>
</div>
	    ';
	    break;

	case 'al':
	    $m=false;
	    if (!$log){$contenido=nologged();break;}
	    $contenido[]='Agregando link';
	    // recibe datos:
	    $info=cleanpost('info');
	    $url=cleanpost('url');
	    $urlextra=cleanpost('urlextra');
	    $topicid=intval(cleanpost('topic'));

	    // query opcionales: urlextra
	    $urlextraq = $urlextra ? array(', urlextra',", '$urlextra'") : array(false,false);
	    
	    // verificar si link ya existe
	    $al = "SELECT id FROM Links WHERE url = '$url';";
	    $result= $conn->query($al);
	    if ($result->num_rows == 0){ // link no existe. bien
		$ahora = date('Y-m-d H:i:s');
		$l = "INSERT INTO Links (info, url, topicId, creado, autorId {$urlextraq[0]}) VALUES ('$info','$url','$topicid', '$ahora', '$id' {$urlextraq[1]});";
		$resultl=$conn->query($l) or die(mysqli_error($conn));
		if (!empty($resultl)){
		    $contenido[] ='Link creado.';
		} else {
		    $contenido[] = 'error';
		}
	    } else {
		$contenido[]='El link no se agregó porque ya estaba anteriormente.';
	    }
	    
	    break;
	default:
	    $contenido[]='aaa?';
	    $contenido[]='eee?';
	    break;
    }
} elseif (isset($_GET['l'])){

    // VISTA DE UN SOLO LINK
    // ver link
    $l = cleanget('l');
    switch($l){
	default:
	    echo '';
    }

    
    $contenido[]='aaa';
    $contenido[]='eee';
    
} else {    
    $u=intval(cleanget('u'));
    $cat=intval(cleanget('cat'));
    //    $opcion = $u ? array("usuario="," WHERE usrid='$u' ") : false;

    $opciontitulo='';
    $opcionquery='';
    $filtro='';

    if ($u || $cat) {
	$opcionquery=' WHERE ';
	if ($u){
	    $quser="SELECT nombre FROM Users WHERE id = '$u';";
	    $ruser=$conn->query($quser)->fetch_row();
	    $filtro.=' del usuario '.$ruser['0'];
	    $opcionquery .=" usrid='$u' ";
	}
	if ($u && $cat){
	    $opcionquery .=' AND ';
	}
	if ($cat){
	    $qcat="SELECT nombre FROM Categories WHERE id = '$cat'";
	    $rcat=$conn->query($qcat)->fetch_row();
	    $filtro.=' dentro de la categoría '.$rcat['0'];
	    $opcionquery .="  catid = '$cat' ";
	    //	$cat =$rcat;
	    
	}
    }
    
    
    $contenido[]=' ';
    $listalinks='
<div class="box is-half">Viendo links'.$filtro.'</div>
<div class="columns">';
    
    //listado de links

    $sql = "SELECT * FROM Linksinfo $opcionquery ORDER BY creado DESC;";
    echo $sql;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
	$columncount=1;
	$listaok=true;
	while($row = $result->fetch_assoc()) {
	    $listalinks .= '
<div class="column">
<span class="link"><a target="_blank" href="'.$row['url'].'">'.$row['info'].'</a></span><br>
<span class="autor"><span class="icon-arrow-up-circle"></span> <a href="?f=up&id='.$row['usrid'].'">'.$row['user'].'</a></span> - 
<span class="categ"><a href="#">'.$row['cat'].'</a> / <a href="#">'.$row['subcat'].'</a> / <a href="#">'.$row['topic'].'</a></span>
</div>
';
	    if (is_int($columncount/2)){
		$listalinks .='</div> <div class="columns">';
		$listaok=false;
	    } else {
		$listaok=true;
	    }
	    $columncount++;
	    
	    /*
	       SELECT Links.id, Links.info, Links.url, Links.urlextra, Links.creado,
	       Users.id AS 'usrid', Users.nombre AS 'user',
	       Topics.id AS 'topicid', Topics.nombre AS 'topic',
	       Subcategories.id AS 'subcatid', Subcategories.nombre AS 'subcat',
	       Categories.id AS 'catid', Categories.nombre As 'cat'
	     */ 

	    
	    //    $listalinks .= "  <div class=\"box\">" . $row["url"]. " - " . $row["info"]. " - " . $row["creado"]. "</div>";
	}
    }
    $listalinks.='</div>';

    $contenido[]=$listalinks;
}





?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello Bulma!</title>
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
      is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
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


