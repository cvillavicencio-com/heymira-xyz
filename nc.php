<?php
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
		    $contenido[] =imgredirect('css/ojo.gif','?f=is','Cuenta creada, bienvenido.');


		} else {
		    $contenido[] = 'error';
		}

	    } else {
		$contenido[] = 'No se puede crear la cuenta, intenta utilizando otros datos.';
	    }

?>
