<?php

$formedit='al';
$linkid=cleanget('link');
$actionform='Agregar nuevo';
$error=false;
$ls="SELECT * FROM Linksinfo WHERE id='$linkid';";
$lq = $conn->query($ls);
$ll = $lq->fetch_row();
// id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat catset
// 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14  15

if ($linkid) { // if está editando link q exista previamente.
    if (!empty($ll[0]) && $ll[7] == $id) {
        $actionform='Editar';
        $formedit='ul';
        $formidfield='<input type="hidden" name="idediting" value="'.$linkid.'">';
        $titulo   = ' value="'.$ll['1'].'" ';
        $info     = str_replace('<br />','',$ll['2']);
        $url      = ' value="'.$ll['3'].'" ';
        $urlextra = ' value="'.$ll['4'].'" ';
        $topicid  = $ll['9'];
        $catset   = cleanget('catset') ? intval(cleanget('catset')) : $ll['15'];
        $tags = '';
        $ts="SELECT * FROM Tagslinks WHERE linkId='$linkid';";
        $tq=$conn->query($ts);
        if ($tq->num_rows > 0) {
            while($tl = $tq->fetch_assoc()) {
                $tags .=$tl['tag'].', ';
            }
            $tags=' value="'.substr($tags,0,-2).'" ';
        }
    } else {
				$contenido = array('Error',imgredirect('css/ojo.gif','.','No puedes editar este link.'));
				$error = true;
    }
} else {
    $csS = "SELECT setfav FROM Users WHERE id='$id';";
    $csQ = $conn->query($csS);
    $csL = $csQ->fetch_row();
    $catset = cleanget('catset') ? intval(cleanget('catset')) : $csL[0];
}

$contenido[] = $actionform.' link';
$salida = '';

if (!$error){

    // salida al internet exterior


		if (!$linkid){
    $vertokS = "SELECT mstin, msttk, twitr, tg_id FROM Tokens WHERE usrid='$id';"; // VERifica TOKens 
    $vertokQ = $conn->query($vertokS);
    $vertokL = $vertokQ->fetch_row();
    $hay = 0;
    $salida .= '   <div class="field">
      <label class="label"><br>Notificar al exterior</label>
    ';
    if (!empty($vertokL[0])){
				$hay++;
				$salida .='
      <input type="checkbox" name="tokensal[]" value="1"> Tootear nuevo link<br>
				';
    }

    if (!empty($vertokL[2])){
				$hay++;
				$salida .='
      <input type="checkbox" name="tokensal[]" value="2"> Twittear nuevo link<br>
				';
    }

    if (!empty($vertokL[3])){
				$hay++;
				$salida .='
      <input type="checkbox" name="tokensal[]" value="3"> Compartir nuevo link en telegram<br>
				';
    }
    if ($hay < 3){
				$salida .= '<br>Configura las notificaciones a otros servicios en <a href="?f=ep">Editar perfil</a>.';
    }
				}
    
    $salida .= '</div>';
    // fin salida al internet exterior



    // inicio categorias    
    $cats ='';
    $sqlc = "SELECT id, nombre FROM Categories WHERE catsetId = '$catset';";
    $resultc = $conn->query($sqlc);

    $colcol=0;
    $colcolsub=0;
    if ($resultc->num_rows > 0) {
				$cats .= '<div style="max-height: 200px; width:100%; overflow-y:scroll; overflow-x:hidden;">';
				while($rowc = $resultc->fetch_assoc()) {
            $color = (is_int($colcol/2)) ? 'has-background-grey-light ': false;
            $colcol++;

            $combocat[] = array($rowc['id'],$rowc['nombre']);
						
            $cats .='';
            $cats .='<div class="cscat"><p id="cat-'.$rowc['id'].'" style="background-color:#444; color:white; z-index:100; position:sticky; top:0px;">'.$rowc['nombre'].'</p>';
            $sqls = "SELECT id, nombre FROM Subcategories WHERE categId='".$rowc['id']."';";
            $results = $conn->query($sqls);
            $cats .= '<div class="column">';
            if ($results->num_rows > 0) {
								while($rows = $results->fetch_assoc()) {
                    $colorsub = (is_int($colcolsub/2)) ? 'has-background-grey-lighter ': false;
                    $colcolsub++;
                    $cats .= '<div class="scat block">';
                    $cats .= '<div class="columns is-1 is-mobile '.$colorsub.'">';
                    $cats .= '<div class="column '.$colorsub.'is-one-third"><p style="position:sticky; top:2em;">'.$rows['nombre'].'</div>';
                    $sqlt = "SELECT id, nombre FROM Topics WHERE subcatId='".$rows['id']."';";
                    $resultt = $conn->query($sqlt);
                    $cats .= '<div class="column is-mobile">';
                    if ($results->num_rows > 0) {
												$cats .= '<ul">';
												while($rowt = $resultt->fetch_assoc()) {
                            $elnombre=$rowt['nombre'];
                            if (substr($elnombre,-12) == '[Unassigned]'){
																$unassig = ' disabled';
                            } else {
																$unassig = '';
                            }
                            $selctd = ($rowt['id'] == @$topicid) ? 'checked' : false;



                            $cats .= '
	    <li class="top"><label class="radio"><input type="radio" name="topic" value="'.$rowt['id'].'" '. $selctd.' '.$unassig.' required> '.$rowt['nombre'].'</label></li>';
												}
												$cats .= '</ul></div></div>';
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

				$cats .= '';
    } else {
				$cats .= 'no hay cats';
    }

    $comboset='
<div class="columns">
	    <div class="column">
        <label class="label is-small">Set de categorías</label>
	    <p class="control has-icons-left">
	    <span class="select is-small">
	    <select id="catset" name="catset" onchange="cargasetcat();">';

    $ss='SELECT * FROM Catsets;';
    $sq=$conn->query($ss);
    if ($sq->num_rows > 0){
				while ($sl = $sq->fetch_assoc()){
            $slectd = ($sl['id'] == @$catset) ? 'selected' : false;
            $comboset .= '<option value="'.$sl['id'].'" '.$slectd.'>'.$sl['nombre'] . '</option> - ';		    
				}
    }
    $comboset .= '
	    </select>
	    </span>
	    <span class="icon is-small is-left">
	    <span class="is-red icon-eyeglass"></span>
	    </span>
	    </p>
	    </div>

    ';




    $combo='
	    <div class="column">
      <label class="label is-small">ir directo a categoría</label>
	    <p class="control has-icons-left">
	    <span class="select is-small">
	    <select  onchange="jump();" id="combocats">';
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
</div>
    ';
    $buscarapida ='

<script>

</script>
<div class="field"><input class="input is-small" onkeyup="buscarap()" style="width:100%" id="br"></div>';
    $cats = '<div id="cats">'.$cats.'</div>'.$buscarapida;

    $contenido[] = '
<form action="?f='.$formedit.'" method="POST">'.@$formidfield.'

<div class="columns">
  <div class="column">
'.$comboset.$combo.'
    <div class="field">
      <label class="label"><big>Título</big></label>
      <input name="titulo" class="input" type="text"'.@$titulo.'placeholder="De qué trata este link" required>
    </div>
    <div class="field">
      <label class="label">Información sobre el link</label>
        <textarea class="textarea" rows="10" name="info" placeholder="Por qué compartes este link" required>'.@$info.'</textarea>
    </div>

    <div class="field">
      <label class="label">URL</label>
      <input name="url" class="input" type="text" onblur="checkURL(this)" placeholder="Dirección del link"'.@$url.' required>
    </div>
    <div class="field">
      <label class="label is-small">URL extra (opcional, acepta otros protocolos)</label>
      <input name="urlextra" class="input  is-small" type="text"'.@$urlextra.' placeholder="Respaldo o link relacionado al mismo tema">
    </div>
  </div>


  <div class="column">
      <div class="field">
      <label class="label">Categorización</label>
      '.$cats.'
    </div>
    <div class="field">
      <label class="label is-small"><br>Tags (separados por comas)</label>
      <input name="tags" class="input is-small" type="text" placeholder="tags, etiquetas, etc" '.@$tags.'>
    </div><br>
     <div class="control">
       <button type="submit" class="button is-primary is-large">'.$actionform.' link</button>
     </div>
       '.$salida.'
  </div>
</div>

</form>

       ';

}
?>
