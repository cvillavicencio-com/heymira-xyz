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
	    $estasen='<div>Bienvenido forastero</div>';
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
	    $estasen='Sintonizándonos...';
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
	    $estasen='<div><i>producat lux</i></div>';
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
	    $estasen='<div><i>profanes</i></div>';
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
	    $estasen='<div><i>sǝuɐɟoɹd</i></div>';	    
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


	    $linkstopics= '';
	    $total=0;
	    $lt = "SELECT * FROM Usertopics WHERE userid='$id';";
	    $ltq= $conn->query($lt);
	    if ($ltq->num_rows > 0) {
		while($rowlt = $ltq->fetch_assoc()) {
		    $total += intval($rowlt['total']);
		    $usertopics[]= array($rowlt['topicid'], $rowlt['topic'], $rowlt['total']);
		}
		$linkstopics.='';
		foreach ($usertopics as &$ut){
		    $percent = intval($ut[2]*100/$total);
		    $linkstopics .= '<a href="?top='.$ut[0].'&u='.$id.'">'.$ut[1].' ('.$ut[2].')</a><progress class="progress is-small" value="'.$percent.'" max="100">20%</progress><br>';
		}
	    }

	    $contenido[] = '
<div class="card">
  <div class="card-content">
    <div class="columns">
      <div class="column has-background-grey-lighter  is-one-quarter ">
        <div class="media">
          <div class="media-content">
            <p class="title is-4">'.$perfil[1].@$mail.'</p>
            <p class="subtitle is-6">'.$perfil[4].'</p>
            <p class="subtitle is-6"><a href="?u='.$id.'">Ver links ('.$total.')</a></p>
          </div>
        </div>
      </div>
      <div class="column">
	    '.$perfil[2].'
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-content">
	    '.$linkstopics.'
  </div>
</div>

	    ';
	    $estasen='<div>Es '.$perfil[1].'</div>';

	    break;

	    
	case 'nl':
	    $formedit='al';
	    $linkid=cleanget('link');
	    $actionform='Agregar';
	    if ($linkid) { // if está editando link q exista previamente.
		$ls="SELECT * FROM Linksinfo WHERE id='$linkid';";
		$lq = $conn->query($ls);
		$ll = $lq->fetch_row();
		// id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat
		// 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14
		if (!empty($ll[0])) {
		    $actionform='Editar';
		    $formedit='ul';
		    $formidfield='<input type="hidden" name="idediting" value="'.$linkid.'">';
		    echo 'editarás '.$ll['1'];
		    $titulo   = ' value="'.$ll['1'].'" ';
		    $info     = $ll['2'];
		    $url      = ' value="'.$ll['3'].'" ';
		    $urlextra = ' value="'.$ll['4'].'" ';
		    $topicid  = $ll['9'];
		    $tags = '';
		    $ts="SELECT * FROM Tagslinks WHERE linkId='$linkid';";
		    $tq=$conn->query($ts);
		    if ($tq->num_rows > 0) {
			$tags .= ' value="';
			while($tl = $tq->fetch_assoc()) {
			    $tags .=$tl['tag'].',';
			}
			$tags.='" ';
		    }
		}

	    }
	    if (!$log){$contenido=nologged();break;}
	    $cats ='';
	    $estasen='<div>Se preciso</div>';
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

		    $combocat[] = array($rowc['id'],$rowc['nombre']);
		    
		    $cats .='<div class="columns is-mobile">';
		    $cats .='<div id="cat-'.$rowc['id'].'" class="column '.$color.' is-one-fifth"><p style="position:sticky; top:0px;">'.$rowc['nombre'].'</p></div>';
		    $sqls = "SELECT id, nombre FROM Subcategories WHERE categId='".$rowc['id']."';";
		    $results = $conn->query($sqls);
		    $cats .= '<div class="column">';
		    if ($results->num_rows > 0) {
			while($rows = $results->fetch_assoc()) {
			    $colorsub = (is_int($colcolsub/2)) ? 'has-background-grey-lighter ': false;
			    $colcolsub++;
			    $cats .= '<div class="columns is-1 is-mobile '.$colorsub.'">';
			    $cats .= '<div class="column '.$colorsub.'is-one-fifth"><p style="position:sticky; top:0px;">'.$rows['nombre'].'</p></div>  <div class="is-divider-vertical" data-content="OR"></div>';
			    $sqlt = "SELECT id, nombre FROM Topics WHERE subcatId='".$rows['id']."';";
			    $resultt = $conn->query($sqlt);
			    $cats .= '<div class="column is-mobile">';
			    if ($results->num_rows > 0) {
				$cats .= '<ul>';

				while($rowt = $resultt->fetch_assoc()) {
				    $elnombre=$rowt['nombre'];
				    if (substr($elnombre,-12) == '[Unassigned]'){
					$unassig = ' disabled';
				    } else {
					$unassig = '';
				    }				    
				    $selctd = ($rowt['id'] == @$topicid) ? 'checked' : false;

				    $cats .= '
	       <li><label class="radio"><input type="radio" name="topic" value="'.$rowt['id'].'" '. $selctd.' '.$unassig.'> '.$rowt['nombre'].'</label></li>';
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
	    $combo='
<div class="field">
  <p class="control has-icons-left">
    <span class="select is-small">
      <select onchange="jump();" id="combocats">';
	    foreach ($combocat as &$comcat){
		$combo .= '<option value="'.$comcat[0].'">'.$comcat[1] . '</option> - ';
	    }

	    $combo .= '
      </select>
    </span>
    <span class="icon is-small is-left">
<span class="is-red icon-eyeglass"></span>
    </span>
  </p>
</div>
	    ';
	    $cats = $combo . $cats;
	    
	    $contenido[] = '
<form action="./?f='.$formedit.'" method="POST">'.@$formidfield.'
<div class="columns is-centered">
  <div class="column is-half">
    <div class="field">
      <label class="label">Título</label>
      <div class="control">
        <input name="titulo" class="input" type="text"'.@$titulo.'placeholder="De qué trata este link">
      </div>
    </div>

    <div class="field">
      <label class="label">Información sobre el link</label>
      <div class="control">
        <textarea class="textarea" name="info" placeholder="Por qué compartes este link">'.@$info.'</textarea>
      </div>
    </div>
    <div class="field">
      <label class="label">URL</label>
      <div class="control">
        <input name="url" class="input" type="text" placeholder="Dirección del link"'.@$url.'>
      </div>
    </div>
    <div class="field">
      <label class="label">Tema</label>
	'.$cats.'
    </div>

    <div class="field">
      <label class="label is-small">Tags (separados por comas)</label>
      <div class="control">
        <input name="tags" class="input is-small" type="text" placeholder="tags, etiquetas, etc" '.@$tags.'>
      </div>
    </div>

    <div class="columns is-mobile">
      <div class="column">
        <div class="field">
          <label class="label is-small">URL extra (opcional)</label>
          <div class="control">
            <input name="urlextra" class="input  is-small" type="text"'.@$urlextra.' placeholder="Respaldo o link relacionado al mismo tema">
          </div>
        </div>
      </div>
      <div class="column">
       <div class="control">
         <button type="submit" class="button is-primary is-large">'.$actionform.' link</button>
       </div>
      </div>
    </div>



  </div>
</div>
	 ';
	    break;

	case 'al': //agregar link
	    $estasen='<div>Tu aporte está siendo gravado</div>';
	    $m=false;
	    if (!$log){$contenido=nologged();break;}
	    $contenido[]='Agregando link';
	    // recibe datos:
	    $titulo   = cleanpost('titulo');
	    $info     = cleanpost('info');
	    $url      = cleanpost('url');
	    $tags     = cleanpost('tags');
	    $tags0    = explode(',',$tags);
	    $urlextra = cleanpost('urlextra');
	    $topicid  = intval(cleanpost('topic'));

	    // query opcionales: urlextra
	    $urlextraq = $urlextra ? array(', urlextra',", '$urlextra'") : array(false,false);
	    
	    // verificar si link ya existe
	    $al = "SELECT id FROM Links WHERE url = '$url';";
	    $result= $conn->query($al);
	    if ($result->num_rows == 0){ // link no existe. bien
		$ahora = date('Y-m-d H:i:s');
		$l = "INSERT INTO Links (titulo, info, url, topicId, creado, autorId {$urlextraq[0]}) VALUES ('$titulo','$info','$url','$topicid', '$ahora', '$id' {$urlextraq[1]});";
		$resultl=$conn->query($l) or die(mysqli_error($conn));
		if (!empty($resultl)){
		    if ($tags){
			$linkid = $conn->insert_id;
			$tq='';
			foreach($tags0 as &$tag){
			    $tag = (substr($tag,0,1) == ' ') ? substr($tag,1) : $tag;
			    $tq = "INSERT INTO Tagslinks (tag,linkid) VALUES ('$tag','$linkid');";
			    $resultag=$conn->query($tq) or die(mysqli_error($conn));
			}
		    }
		    $contenido[] ='Link creado.';
		} else {
		    $contenido[] = 'error guardando link';
		}
	    } else {
		$contenido[]='El link no se agregó porque ya estaba anteriormente.';
	    }
	    
	    break;

	case 'ul': // update link
	    $estasen='<div>Tu aporte está siendo gravado</div>';
	    $m=false;
	    if (!$log){$contenido=nologged();break;}
	    $contenido[]='Editando link';
	    // recibe datos:
	    $titulo    = cleanpost('titulo');
	    $info      = cleanpost('info');
	    $url       = cleanpost('url');
	    $tags      = cleanpost('tags');
	    $tags0     = explode(',',$tags);
	    $urlextra  = cleanpost('urlextra');
	    $topicid   = intval(cleanpost('topic'));
	    $idediting = intval(cleanpost('idediting'));

	    // query opcionales: urlextra
	    $urlextraq = $urlextra ? ",urlextra='$urlextra'":false;


	    /*
	       UPDATE table_name
	       SET column1 = value1, column2 = value2, ...
	       WHERE condition;
	     */

	    // confirmar si usuario es autor, para poder editar.
	    $ls="SELECT usrid FROM Linksinfo WHERE id='$idediting';";
	    $lq = $conn->query($ls);
	    $ll = $lq->fetch_row();
	    // id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat
	    // 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14

	    if ($ll[0] == $id){
		$us = "UPDATE Links SET titulo='$titulo', info='$info',url='$url',topicId='$topicid' $urlextraq WHERE id='$idediting';";
		$uq = $conn->query($us) or die(mysqli_error());

		// DELETE FROM table_name WHERE condition;
		$ds = "DELETE FROM Tagslinks WHERE linkId='$idediting';";
		$dq = $conn->query($ds) or die(mysqli_error());

		if ($tags){
		    $linkid = $conn->insert_id;
		    $tq='';
		    foreach($tags0 as &$tag){
			$tag = (substr($tag,0,1) == ' ') ? substr($tag,1) : $tag;
			$tq = "INSERT INTO Tagslinks (tag,linkid) VALUES ('$tag','$idediting');";
			$resultag=$conn->query($tq) or die(mysqli_error($conn));
		    }
		}

		$contenido[]='El link ha sido editado exitosamente.';
	    } else {
		$contenido[]='No puedes editar este link.';
	    }
	    break;

	default:
	    $estasen='<div>Buscad y encontrareis, pero no acá</div>';
	    $contenido[]='Qué raro...';
	    $contenido[]='no deberías estar leyendo esto. Igual, no hay nada en esta parte :-) <!-- y de verdad que no hay nada... -->';
	    // y en efecto no hay nada :-)
	    break;
    }
} elseif (isset($_GET['l'])){   
    // VISTA DE UN SOLO LINK
    // ver link
    $l = cleanget('l');
    if (is_int(intval($l))){  // ver link
	//	echo 'link int';

	$ls="SELECT * FROM Linksinfo WHERE id='$l';";
	$lq = $conn->query($ls);
	$ll = $lq->fetch_row();
	// id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat
	// 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14
	//Editar Edita
	$contenido[] = $ll['1'];

	$editlink = ($ll['7'] == $id) ? '<a href="?f=nl&link='.$ll['0'].'">Editar</a>' : false;

	$vistalink='
<div class="columns">
  <div class="column">
    <div class="box"><b><center>Resumen del contenido</center></b><br>'.$ll['2'].' 
    </div>
  </div>
  <div class="column is-one-third">
    <div class="box">
	<b>'.$ll['1'].'</b><br>
	'.$ll['3'].'<hr>Creado por<br>
	<span class="autor">&nbsp; '.$ll['8'].'</span><br><br>

	    Taxonomia:
      <br><span class="categ">
        <a href="?cat='.$ll['13'].'">'.$ll['14'].'</a><br>
        <a href="?sub='.$ll['11'].'">'.$ll['12'].'</a><br>
        <a href="?top='.$ll['9'].'">'.$ll['10'].'</a><br>
      </span><br><br>

            Tags: [ESTO NO ESTÁ PROGRAMADO AUN :p]
      <br><span class="categ">
        <a href="?cat='.$ll['12'].'">'.$ll['13'].'</a><br>
        <a href="?sub='.$ll['10'].'">'.$ll['11'].'</a><br>
        <a href="?top='.$ll['8'].'">'.$ll['9'].'</a><br>
      </span><br>'.$editlink.'<br>




    </div>
  </div>

</div>

</div>';

	$contenido[] = $vistalink;
	








	
    } else { // usos.
	echo 'link no int';

	switch($l){
	    default:
		echo '';
	}
    }
    

    
    $contenido[]='aaa';
    $contenido[]='eee';
    
} else {    
    $u=intval(cleanget('u'));
    $cat=intval(cleanget('cat'));
    $sub=intval(cleanget('sub'));
    $top=intval(cleanget('top'));
    $pag=intval(cleanget('pag'));
    $pag = !$pag ? 1 :$pag; 

    //    $opcion = $u ? array("usuario="," WHERE usrid='$u' ") : false;

    $opciontitulo='';
    $opcionquery='';
    $filtro='';

    if ( $u || $cat || $sub || $top ) {

	$opcionquery=' WHERE ';
	if ($u){
	    $quser="SELECT nombre FROM Users WHERE id = '$u';";
	    $ruser=$conn->query($quser)->fetch_row();
	    $filtro.=' del usuario '.$ruser['0'];
	    $opcionquery .=" usrid='$u' ";
	}
	if ( $u && ($cat || $sub || $top) ){
	    $opcionquery .=' AND ';
	}
	if ( $cat ){
	    $qcat="SELECT nombre FROM Categories WHERE id = '$cat'";
	    $rcat=$conn->query($qcat)->fetch_row();
	    $filtro.=' en categoría '.$rcat['0'];
	    $opcionquery .="  catid = '$cat' ";

	} elseif ( $sub ){
	    $qsub="SELECT nombre FROM Subcategories WHERE id = '$sub'";
	    $rsub=$conn->query($qsub)->fetch_row();
	    $filtro.=' en subcategoría '.$rsub['0'];
	    $opcionquery .="  subcatid = '$sub' ";

	} elseif ( $top ){
	    $qtop="SELECT nombre FROM Topics WHERE id = '$top'";
	    $rtop=$conn->query($qtop)->fetch_row();
	    $filtro.=' en tópico '.$rtop['0'];
	    $opcionquery .="  topicid = '$top' ";
	}
	
    }

    $contenido[]=' ';
    $estasen='<div class="">Viendo links'.$filtro.'</div>';
    
    //listado de links

    $linksporpag=10;
    $limit = $pag ? "LIMIT $linksporpag OFFSET ".($linksporpag * ($pag-1)) : 'LIMIT '.$linksporpag;
    $sql = "SELECT * FROM Linksinfo $opcionquery ORDER BY creado DESC $limit;";
    //        echo $sql;
    $result = $conn->query($sql);


    $listalinks=' <div class="columns">';

    if ($result->num_rows > 0) {
	$columncount=1;
	$listaok=true;
	while($row = $result->fetch_assoc()) {
	    $listalinks .= '
<div class="column">
<span class="link"><a href="?l='.$row['id'].'"><span class="icon-bubble"></span></a> <a target="_blank" href="'.$row['url'].'">'.$row['titulo'].'</a></span><br>
<span class="icon-user"></span>  <span class="autor"><a href="?f=up&id='.$row['usrid'].'">'.$row['user'].'</a></span> <br>
<span class="icon-book-open"></span>
<span class="categ">
<a href="?cat='.$row['catid'].'">'.$row['cat'].'</a> /
<a href="?sub='.$row['subcatid'].'">'.$row['subcat'].'</a> /
<a href="?top='.$row['topicid'].'">'.$row['topic'].'</a></span>

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

    $pags = "SELECT COUNT(*) FROM Linksinfo $opcionquery ORDER BY creado DESC";
    $pagq = $conn->query($pags);
    $pagl = $pagq->fetch_row();

    //    echo $pagl[0]; // <-- este es el total de links q existen :-D
    //paginación

    $totalpags=($pagl[0]/$linksporpag);
    $paginat=array(''.'');
    for ($i=0; $i <= $totalpags; $i++) {
	$o = $i+1;
	$current = ($o == $pag) ? array(' is-current','aria-current="page"') : array('',''); 
	$paginat[0] .= '<li><a href="?pag='.$o.'" class="pagination-link'.$current[0].'" aria-label="Goto page 2" '.$current[1].'>'.$o.'</a></li>'.PHP_EOL;	
    }

    $ant = ($pag > 1) ? array(($pag-1),'',($pag-1)) : array('#','is-disabled','No puedes avanzar más');
    $sig = ($pag < $totalpags) ? array(($pag+1),'',($pag+1)) : array('#','is-disabled','No puedes retroceder más');
    $listalinks .= '
<nav class="pagination is-small" role="navigation" aria-label="pagination">
  <a href="?pag='.$ant[0].'" class="pagination-previous '.$ant[1].'" title="'.$ant[2].'">Más reciente</a>
  <a href="?pag='.$sig[0].'" class="pagination-next '.$sig[1].'" title="'.$sig[2].'">Más antiguo</a>
  <ul class="pagination-list">'.$paginat[0].    ' </ul>
</nav>
  ';

    $contenido[]=$listalinks;
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
		  echo @$estasen;  ?>
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


