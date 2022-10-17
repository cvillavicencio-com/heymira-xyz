<?php
// vl VER LINK : muestra link que recibe por get. incluye ver link, lectura de permisos de usuario (para distinguir si puede editar contenido o no) y comentarios
// ver link
$l = cleanget('l');
if (is_int(intval($l))){  // ver link
    //	echo 'link int';

    $ls="SELECT * FROM Linksinfo WHERE id='$l';";

    $lq = $conn->query($ls);
    $ll = $lq->fetch_row()
    ;
    // id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat
    // 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14
    //Editar Edita
    $contenido[] = $ll['1'];




    // permisos del usuario
    $escribircoment = true; // esto por mientras
    $editlink = '';
    if ($log){
	$rolS= "SELECT rolId, Roles.accion FROM Userinfo INNER JOIN Roles ON Roles.id = Userinfo.rolId WHERE Userinfo.id = '$id'";
	$rolQ = $conn->query($rolS);
	$rolL = $rolQ->fetch_row();
	$permisos = explode(',',$rolL[1]);

	if ($ll['7'] == $id && in_array('ep',$permisos)){
	    $editlink = '<a href="?f=nl&link='.$ll['0'].'"><button class="button is-warning">Editar</button></a>';
	    $escribircoment = true; // temporalmente no habilitado
	} elseif (in_array('el',$permisos)){
	    $editlink = '<a href="?f=nl&link='.$ll['0'].'"><button class="button is-danger">Editar el link de<br>'.$ll[8].'</button></a>';	    
	}
    }
    // fin permisos






    

    $ts="SELECT * FROM Tagslinks WHERE linkId='{$ll[0]}';";
    $tq=$conn->query($ts);
    if ($tq->num_rows > 0) {
	$tags = 'Tags:<br><span class="tags">·&nbsp;';
	while($tl = $tq->fetch_assoc()) {
	    $tags .='<a href="?tag='.$tl['tag'].'">'.$tl['tag'].'</a>&nbsp;·&nbsp;';
	}
	$tags .='</span>';
    } else {
	$tags='';
    }

    // VER COMENTARIOS
    if ($log){

	$noautor = ($ll[7] != $id) ? 'AND estado = 1':false;
	$vs = "SELECT Comments.id AS 'comid', texto, estado, autorId,linkId, fecha , Users.nombre AS 'user', Users.id as 'usrid' FROM Comments INNER JOIN Users ON Comments.autorId = Users.id WHERE linkId='$l' $noautor;";
	$vq = $conn->query($vs);

	$coms ='
<div class="columns is-centered">
  <div class="column ">
	';
	if ($vq->num_rows > 0) { // sí hay comentarios :-D
	    $marca = ($ll[7] == $id) ? true:false;

	    $coms .= $marca ? '<form action="?f=ec" method="POST">' : '';
	    $coms .= '
    <label class="label is-centered">Comentarios </label>
	    ';
	    while($vl = $vq->fetch_assoc()) {
		$avatar = (file_exists('avatars/'.$vl['autorId'].'-'.strlen($vl['user']).'.png')) ? $vl['autorId'].'-'.strlen($vl['user']) : 'default';
		$visible = (!$noautor && $vl['estado'] != '1') ? ' comnv':false;
		$marcar = $marca ? '<span class="tags"><input name="marca[]" value="'.$vl['comid'].'" type="checkbox">&nbsp;Marcar mensaje</span>' : '';

		$coms .= '
    <div class="card '.$visible.'">
      <div class="card-content">
        <div class="columns">
          <div class="column is-one-third">
            <div class="media">
              <div class="media-left">
                <figure class="image is-48x48">
                  <img src="avatars/'.$avatar.'.png" alt="'.$vl['user'].'">
                </figure>
              </div>
              <div class="media-content">
                <p class="title is-6"><a href="?f=up&id='.$vl['usrid'].'">'.$vl['user'].'</a><br>
                <span class="autor">'.$vl['fecha'].'</span></p>
              </div>
            </div>
          </div>
          <div class="column">
            <div class="content">'.$vl['texto'].'</div>
          </div>
	    '.$marcar.'
        </div>
      </div>
    </div>
<br>
	    ';

	    }
	    $coms .= $marca ? ' <br> <input type="hidden" value="'.$ll[0].'" name="link"><div class="field">
      <div class="control">
        <button class="button is-warning">Cambiar estado (visible/invisible)<br>de mensajes marcados</button>
     </div><br>
   </div>
</form>


<div class="is-hidden-tablet linkbtn" style="right:10px; bottom: 20px; position: fixed !important;">

 <div class="linkbtnn">
  <a href="."><span class="icon-list"></span></a>

 </div>&nbsp;

 <div class="linkbtnn">
  <a href="#comms"><span class="icon-bubble"></span></a>
 </div>&nbsp;

 <div class="linkbtnn">
  <a href="#title"><span class="icon-arrow-up"></span></a>
 </div>
</div>



	    ' : '';

	} else {
	    $coms .= '<p>No hay comentarios</p>';
	}
	$coms .= '
  </div>
	';
	// FORM NUEVO COMENTARIO
	$formulario = '
  <div class="column is-one-third">
    <div class="box">
      <form action="?f=dc" method="POST">
      <div class="field">
        <label class="label">Dejar un comentario</label><a name="comms"></a>
      <div class="control">
        <textarea name="com" class="textarea" placeholder="Textarea"></textarea>
      </div>
    </div>
    <input type="hidden" name="lid" value="'.$l.'">
    <div class="field">
      <div class="control">
        <button class="button is-link">Enviar</button>
     </div><br>
   </div>
 </div>
</div>';

	// VISTA DE COMENTARIOS

	$comentarios = '<hr>'.$coms;
	$comentarios .= $escribircoment ? $formulario : '';
    } else {
	$comentarios = '<div class="box">Inicia sesión o crea una cuenta para dejar un comentario</div>';
    }

    $vistalink = '
<div class="columns">
  <div class="column">
    <div class="box">
      <b><center>Resumen del contenido</center></b><br>'.$ll['2'].' 
    </div>
  </div>
  <div class="column is-one-third">
    <div class="box">
	  <b>'.$ll['1'].'</b><br>
          <a rel="noreferrer noopener nofollow" href="'.$ll['3'].'">'.$ll['3'].' <span class="icon-link"></span></a>';
    if (!empty($ll['4'])) {
	$vistalink .= '
          <br><br>
	  <span class="autor">URL extra:
          <a rel="noreferrer noopener nofollow" href="'.$ll['4'].'" target="_blank">'.$ll['4'].'</a>
          </span><br><br>
	  ';
    }
    
    $vistalink .= '
<hr>Creado por<br>

	  <span class="autor">&nbsp; '.$ll['8'].'</span><br>
Creación/modificación<br>
	  <span class="autor">&nbsp; '.$ll['5'].'</span><br>

<br><br>

	    Taxonomia:
      <br><span class="categ">
        <a href="?cat='.$ll['13'].'">'.$ll['14'].'</a><br>
         <span class="icon-arrow-right"></span> <a href="?sub='.$ll['11'].'">'.$ll['12'].'</a><br>
        <span class="icon-arrow-right"></span><span class="icon-arrow-right"></span> <a href="?top='.$ll['9'].'">'.$ll['10'].'</a><br>
      </span><br><br>

        '.$tags.'<br>'.$editlink.'<br>
    </div>
  </div>
</div>
	';

    $contenido[] = $vistalink .$comentarios;
    
} else { // usos.
    echo 'link no int';

    switch($l){
	default:
	    echo '';
    }
}



$contenido[]='aaa';
$contenido[]='eee';

?>
