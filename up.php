<?php
$idup = isset($_GET['id']) ? intval(cleanget('id')) : $id;
$us = "SELECT * FROM Userinfo WHERE id = '$idup';";	    
$uq = $conn->query($us);
$ul = $uq->fetch_row();
//	    print_r($perfil);
	    
$contenido[] = 'Perfil de '.$ul['1'];
//	    $mail = $ul[3] ? ' ('.$ul[3].')' : false;


$linkstopics= '';
$total=0;
$lt = "SELECT * FROM Usertopics WHERE userid='$idup';";
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

$editperfil = ($idup == @$id) ? '<p><a href="?f=ep"><button class="button is-warning">Editar perfil</button></a></p>' : false;

// $idup.'-'.strlen($ul[1])

$avatar = (file_exists('avatars/'.$idup.'-'.strlen($ul[1]).'.png')) ? $idup.'-'.strlen($ul[1]) : 'default';
$contenido[] = '
<div class="card">
  <div class="card-content">
    <div class="columns">
      <div class="column has-background-grey-lighter  is-one-quarter ">
        <div class="media">
          <div class="media-content">
            <img src="avatars/'.$avatar.'.png">
            <p class="title is-4">'.$ul[1].@$mail.'</p>
            <p class="subtitle is-6">'.$ul[4].'</p>
            <p class="subtitle is-6"><a href="?u='.$id.'">Ver links ('.$total.')</a></p>'.$editperfil.'
          </div>
        </div>
      </div>
      <div class="column">
	    '.$ul[2].'
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

?>
