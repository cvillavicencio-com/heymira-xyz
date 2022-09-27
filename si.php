<?php
	    $contenido[] = 'Iniciando sesión';
	    $nombre=cleanpost('nombre');
	    $clave=cleanpost('clave');
	    $clave = $clave ? sha1($clave) : false; 
	    $n = "SELECT clave, id FROM Users WHERE nombre = '$nombre' OR mail = '$nombre';";
	    $result = $conn->query($n);
	    $laclave = $result->fetch_row();
	    if ($result->num_rows != 0) {	    
		if ($laclave[0] == $clave){
		    $_SESSION["log"]=$laclave[1];
            $contenido[]=imgredirect('logo.png','.','Sesión iniciada correctamente');
		} else {
            $contenido[]=imgredirect('logo.png','?f=is','Contraseña equivocada');
	    	    $contenido[]='contraseña incorrecta';
		}		
	    } else {
            $contenido[]=imgredirect('logo.png','?f=is','Usuario no se encuentra registrado');
	    }

?>
