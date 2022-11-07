<?php
$contenido[] = 'Actualizando datos';
$res='';
// actualiza datos
// cambiaron los datos?
$es="SELECT * FROM Users WHERE id='$id'";
$eq=$conn->query($es);
$el=$eq->fetch_row();
//id nombre clave info mail tema setfav utypeId
//0  1      2     3    4    5    6

$info = substr(cleanpost('info'),0,1000);
if ($info != $el['3']){ // info cambió ;-)
    $is="UPDATE Users SET info='$info' WHERE id='$id';";
    $iq=$conn->query($is) or die(mysqli_error());		
    $res .= 'Información de perfil actualizada<br>';
}

$tema = @cleanpost('tema');
if ($tema != $el['5']){
    $is="UPDATE Users SET tema='$tema' WHERE id='$id';";
    $iq=$conn->query($is) or die(mysqli_error());		
    $res .= 'Tema actualizado<br>';    
}

$setfav = @cleanpost('setfav');
if ($setfav != $el['6']){
    $is="UPDATE Users SET setfav='$setfav' WHERE id='$id';";
    $iq=$conn->query($is) or die(mysqli_error());		
    $res .= 'Set de categorías por defecto actualizada<br>';    
}

$tkS = "SELECT * FROM Tokens WHERE usrId = '$id';";
$tkQ = $conn->query($tkS);
$tkL = $tkQ->fetch_row();

$mstin = @cleanpost('mstin');
$msttk = @cleanpost('msttk');
$twitr = @cleanpost('twitr');
$tg_id = @cleanpost('tg_id');

if (!empty($tkL)){
    if ($mstin != $tkL['2']){
	$is="UPDATE Tokens SET mstin='$mstin' WHERE usrId='$id';";
	$iq=$conn->query($is) or die(mysqli_error());		
	$res .= 'Instancia de mastodon actualizada<br>';    
    }

    if ($msttk != $tkL['3']){
	$is="UPDATE Tokens SET msttk='$msttk' WHERE usrId='$id';";
	$iq=$conn->query($is) or die(mysqli_error());		
	$res .= 'Token de mastodon actualizado<br>';    
    }

    if ($twitr != $tkL['4']){
	$is="UPDATE Tokens SET twitr='$twitr' WHERE usrId='$id';";
	$iq=$conn->query($is) or die(mysqli_error());		
	$res .= 'Token de twitter actualizado<br>';    
    }

    if ($tg_id != $tkL['5']){
	$is="UPDATE Tokens SET tg_id='$tg_id' WHERE usrId='$id';";
	$iq=$conn->query($is) or die(mysqli_error());		
	$res .= 'Instancia de mastodon actualizada<br>';    
    }
} else {
	$is="INSERT INTO Tokens (usrId) VALUES ('$id')";
	$iq=$conn->query($is);
    echo '<script>alert("cuenta actualizada, vuelve a ingresar info para notificar fuera.");</script>';    
}

if (@cleanpost('conf1') == 1 && @cleanpost('conf2') == 2){
    // falta verificar q contraseña anterior sea correcta
    $clave = sha1(cleanpost('clavesi'));
    $cs="UPDATE Users SET clave='$clave' WHERE id='$id';";
    $cq=$conn->query($cs) or die(mysqli_error());
    $res .= 'Clave actualizada<br>';		
}



// sube imagen (si hay) name=avatar -- código de: https://www.jose-aguilar.com/blog/upload-de-imagenes-con-php/
//Recogemos el archivo enviado por el formulario
$archivo = $_FILES['avatar']['name'];
//Si el archivo contiene algo y es diferente de vacio
if (isset($archivo) && $archivo != "") {
    //Obtenemos algunos datos necesarios sobre el archivo
    $tipo = $_FILES['avatar']['type'];
    $tamano = $_FILES['avatar']['size'];
    $temp = $_FILES['avatar']['tmp_name'];
    //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
    if (!((strpos($tipo, "gif") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 200000))) {
        $res .= 'Avatar no fue actualizado. Se permiten archivos .gif, .jpg, .png. y de 200 kb como máximo.<br>';
    } else {
        //Si la imagen es correcta en tamaño y tipo
        //Se intenta subir al servidor
        $avatarfile=explode('.',$archivo);
        $archivok=$id.'-'.strlen($el[1]).'.png';
        if (move_uploaded_file($temp, 'avatars/'.substr($temp,4))) {
	    //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
	    chmod('avatars/'.substr($temp,4), 0777);
	    exec('convert -resize 100 avatars/'.substr($temp,4).' avatars/'.$archivok.'; rm avatars/'.substr($temp,4));
	    //Mostramos el mensaje de que se ha subido co éxito
	    $res .= 'Avatar modificado exitosamente.<br>';
	    //Mostramos la imagen subida
	    //			echo '<p><img src="avatars/'.$archivok.'"></p>';
        }
        else {
	    //Si no se ha podido subir la imagen, mostramos un mensaje de error
	    $res .= '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
        }
    }
}






// notifica
$contenido[] = $res.imgredirect('css/ojo.gif','?f=up','Información actualizada exitosamente.');
?>
