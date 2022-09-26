<?php
$link = intval(cleanpost('link'));
$marca = cleanpost('marca'); //array con comentarios marcados

// verificación
$es="SELECT * FROM Comments WHERE linkId = '$link';";
$eq=$conn->query($es);
if ($eq->num_rows > 0) {
    while($el = $eq->fetch_assoc()) {
        if (in_array($el['id'],$marca)){
            $nuevoestado = ($el['estado'] == 1) ? '2':false;
            $nuevoestado = ($el['estado'] == 2) ? '1': $nuevoestado;
            $ns="UPDATE Comments SET estado = '$nuevoestado' WHERE id ='".$el['id']."' AND linkId = '$link';";
            $nq=$conn->query($ns) or die(mysqli_error());
        }
    }
}

$contenido=array('Operación exitosa','Ha sido cambiado el estado de los comentarios'.imgredirect('logo.png','?l='.$link,'redirigiendo al link'));   

    
?>
