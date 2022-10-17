<?php
$idup = isset($_GET['id']) ? intval(cleanget('id')) : $id;

$cS = "SELECT id FROM Users WHERE id='$idup';";
$cQ = $conn->query($cS);
$cL = $cQ->fetch_row();

if (empty($cL[0])){
    $contenido = array('No',imgredirect('css/ojo.gif','.','Usuario no existe',true));
} else {
    $us = "SELECT DISTINCT *, Roles.info FROM Userinfo INNER JOIN Roles ON Roles.id = Userinfo.rolId WHERE Userinfo.id = '$idup';";
    $uq = $conn->query($us) or die(mysqli_error());
    $ul = $uq->fetch_row();

    $contenido[] = 'Perfil de '.$ul['1'];


    $optags = '<img src="css/constru.png"><br>';

    $iS = "SELECT * FROM Refers WHERE ownerId = '$id' AND userId = '' IS NULL;";
    $iQ = $conn->query($iS);
    if ($iQ->num_rows > 0 && $idup == @$id) {
	$optags .= '<hr><b>Invitaciones disponibles</b>';

	while($iL = $iQ->fetch_assoc()) {
	    $optags .= '<br>- <a href="https://heymira.xyz/inv='.$iL['code'].'">https://heymira.xyz/inv='.$iL['code'].'</a>';
            
	}
    } 





    $linkstopics= '';
    $total=0;
    $lt = "SELECT * FROM Usertopics WHERE userid='$idup';";
    $ltq= $conn->query($lt);
    $linkstopics .= '<div class="columns">';
    $linkstopics .= '<div class="column is-one-half">'.$optags.'</div>';
    $linkstopics .= '<div class="column is-one-half"><b>Participación</b><br>';
    if ($ltq->num_rows > 0) {
	while($rowlt = $ltq->fetch_assoc()) {
            $total += intval($rowlt['total']);
            $usertopics[]= array($rowlt['topicid'], $rowlt['topic'], $rowlt['total']);
	}


	$columncount=1;
	foreach ($usertopics as &$ut){
            $percent = intval($ut[2]*100/$total);
	    $getinfoS = "
SELECT DISTINCT Topics.id AS 'idtop', 
       Topics.nombre AS 'ntop', 
       Subcategories.id AS 'idsub',
       Subcategories.nombre AS 'nsub',
       Categories.id AS 'idcat',
       Categories.nombre AS 'ncat',
       Catsets.nombre AS 'nset'
FROM Topics 
INNER JOIN Subcategories ON Topics.subcatId = Subcategories.id
INNER JOIN Categories ON Subcategories.categId = Categories.id
INNER JOIN Catsets ON Categories.catsetId = Catsets.id

WHERE Topics.id = '{$ut[0]}'; ";

	    $getinfoQ = $conn->query($getinfoS);
	    $getinfoL = $getinfoQ->fetch_row();
	    $linkstopics .= '<b>'.$getinfoL[6].'</b> / '; 
	    $linkstopics .= '<a href="?cat='.$getinfoL[4].'&u='.$id.'">'.$getinfoL[5].'</a> / ';
            $linkstopics .= '<a href="?sub='.$getinfoL[2].'&u='.$id.'">'.$getinfoL[3].'</a> / ';
            $linkstopics .= '<a href="?top='.$ut[0].'&u='.$id.'">'.$ut[1].' ('.$ut[2].')</a><progress class="progress is-small" value="'.$percent.'" max="100">20%</progress><br>';


	    $columncount++;

	}
    }
    $linkstopics .= '</div>';
    $linkstopics .= '</div>';

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
<br>
	<div class="card">
	<div class="card-content">
	'.$linkstopics.'
	</div>
	</div>

	';
}
?>
