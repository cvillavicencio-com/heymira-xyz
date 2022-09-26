<?php
$contenido[] = 'Actualizando datos';
$res='';
// actualiza datos
// cambiaron los datos?
$es="SELECT * FROM Users WHERE id='$id'";
$eq=$conn->query($es);
$el=$eq->fetch_row();
//id nombre clave info mail utypeId
//0  1      2     3    4    5

$info = nl2br(substr(cleanpost('info'),0,1000));
if ($info != $el['3']){ // info cambió ;-)
    $is="UPDATE Users SET info='$info' WHERE id='$id';";
    $iq=$conn->query($is) or die(mysqli_error());		
    $res .= 'Información actualizada<br>';
}

if (@cleanpost('conf1') == 1 && @cleanpost('conf2') == 2){
    $clave = sha1(cleanpost('clave'));
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
$contenido[] = $res;
?>
