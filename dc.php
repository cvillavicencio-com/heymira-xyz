<?php
// Comments
// id 	texto 	estado 	autorId 	linkId fecha

// Escribe comentario en bd

$com = nl2br(cleanpost('com'));
$lid = intval(cleanpost('lid'));
if ($com && $lid){
    $ds = "INSERT INTO Comments (texto, autorId, linkId) VALUES ('$com','$id','$lid')";
    $dq = $conn->query($ds) or die(mysqli_error());


    $contenido[] = 'Comentario recibido';
    $contenido[] =imgredirect('logo.png','?l='.$lid,'Comentario recibido exitosamente.');

} else {
    $contenido = array('Error',imgredirect('logo.png','?l='.$lid,'El mensaje no se pudo guardar.'));

}

?>
