<?php
$contenido[] = 'Iniciando sesión';
$nombre=cleanpost('nombre');
$clave=cleanpost('clave');
@$mantener=intval(cleanpost('mantener'));

$clave = $clave ? sha1($clave) : false;
// en query hay que consultar 'token' (VARCHAR(200)), para que el dato que se guarda en $_session['log'] o $_cookie['log'] no sea sha1($clave)
$n = "SELECT clave, id, token FROM Users WHERE nombre = '$nombre' OR mail = '$nombre';";
$result = $conn->query($n);
$laclave = $result->fetch_row();
if ($result->num_rows != 0) {	    
    if ($laclave[0] == $clave){
        // crea token
        $tokenA= array('c','l','a','n','A','i','f','o');
        shuffle($tokenA);
        $token = rand(1000,9999).(rand(100,999)*4).$tokenA[rand(0,7)].(rand(10,99)*2).$tokenA[rand(0,7)].(rand(0,9)*6).rand(1000,9999);
        // lo guarda
	$tokS = "UPDATE Users SET token = '$token' WHERE nombre = '$nombre' OR mail = '$nombre';";
	$tokQ = $conn->query($tokS);
	
        
	/*
	   UPDATE table_name
	   SET column1 = value1, column2 = value2, ...
	   WHERE condition;
	 */


        
        // distingue si sesión es temporal o permanente        
        if (isset($mantener) && @$mantener==1){            
            setcookie("log", $token, time()+360000); //sesión dura abierta 600 horas, equivalentes a 25 días. ;; 360 0006 0025
        } else {
            $_SESSION["log"]=$token;
        }

        $contenido[]=imgredirect('css/ojo.gif','.','Sesión iniciada correctamente');
    } else {
        $contenido[]=imgredirect('css/ojo.gif','?f=is','Contraseña equivocada');
        $contenido[]='contraseña incorrecta';
    }
} else {
    $contenido[]=imgredirect('css/ojo.gif','?f=is','Usuario no se encuentra registrado');
}

?>
