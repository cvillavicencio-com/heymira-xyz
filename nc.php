<?php
// cc CREAR CUENTA.
$invitacionesporusuario = 4;
$contenido[] = "Creando cuenta";
$nombre = cleanpost('nombre');
$mail =   cleanpost('mail');
$cinv =   cleanpost('cinv');
$clave = sha1(cleanpost('clave'));
//	    $clave=cleanpost('clave');

$registrable = false;
// ** código de invitación **
// confirma que exista

if ($cinv){
    $iS = "SELECT * FROM Refers WHERE code = '$cinv';"; // busca código
    $iQ = $conn->query($iS) or die(mysqli_error());

    

    if ($iQ){ // el código existe
	$iL = $iQ->fetch_row();
	// id code ownerId userId
	// 0  1    2       3



	if ($iL[3] == ''){ // el código no ha sido usado
	    $registrable = true;
	    // referidor
	    $idref = $iL[0];
	    $code  = $iL[1];
	    $refer = $iL[2];

	} else {
	    $err .= 'código de invitación ya fue utilizado.';
	}

    } else {
	$err .= 'código de invitación no es válido.';
    }
} else {
    $err .= 'no se ingresió código de invitación.';
}
// fin código de invitación

if ($registrable){ // código ok
    $wmail = $mail ? "OR mail = '$mail'" : false;
    $n = "SELECT id FROM Users WHERE nombre = '$nombre' $wmail;";  // verifica si existe nombre o email, si se ingresa.
    $result = $conn->query($n);

    if ($result->num_rows == 0) {  // no existe nombre o mail registrado
	$qmail = $mail ? ', mail':false;
	$vmail = $mail ? ",'$mail'":false;
	
	$c = "INSERT INTO Users (nombre, clave, refer$qmail) VALUES ('$nombre','$clave','$cinv' $qmail);";
	$resultm=$conn->query($c)or die(mysqli_error());
	

	if ($resultm){ // se creó el usuario
	    $dS = "SELECT id FROM Users WHERE nombre = '$nombre';";
	    $dQ = $conn->query($dS);
	    $dL = $dQ->fetch_row();
	    $nu = $dL[0]; // se obtiene id de usuario nuevo

	    $e = "UPDATE Refers SET userId = '$nu' WHERE id = '$idref';";
	    $resultn = $conn->query($e);

	    if ($resultn){ // se registra usuario con código de invitacion

		$invitaciones = array();
		// se generan nuevos códigos de invitación para usuario nuevo
		for ($i = 1 ; $i <= $invitacionesporusuario ; $i++){
		    $letras = array('a', 'b', 'd', 'e', 'f', 'g', 'h', 'j', 'm', 'n', 'q', 'r', 't', 'y', 'A', 'B', 'D', 'E', 'F', 'G', 'H', 'J', 'M', 'N', 'Q', 'R', 'T', 'Y');
		    $ncode = $letras[rand(0,(count($letras)-1))]. substr(microtime(),-5) . $letras[rand(0,(count($letras)-1))] . rand (1111,9999) . $letras[substr(microtime(),-1,1)].rand(111,999);  // creación de código de inv.
		    $invitaciones[] = "INSERT INTO Refers (code, ownerId) VALUES ('$ncode','$nu') ;"; // creación de query que crea nueva invitación
		}
		// ejecución de querys :D
		foreach($invitaciones as &$inv){
		    $invok = $conn->query($inv) or die(mysqli_error());
		}


		$contenido[] = imgredirect('css/ojo.gif','?f=is','Cuenta creada, bienvenido.');
	    } else {
		$contenido[] = 'error';
	    }
	} else {
	    $contenido[] = 'no existe? D:';
	}

    } else {
	$contenido[] = 'No se puede crear la cuenta, intenta utilizando otros datos.';
    }
} else {
    $contenido[] = imgredirect('css/ojo.gif','?f=cc',$err,true);
}
?>
