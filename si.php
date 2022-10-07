<?php
$contenido[] = 'Iniciando sesión';
$nombre=cleanpost('nombre');
$clave=cleanpost('clave');
@$mantener=intval(cleanpost('mantener'));

$clave = $clave ? sha1($clave) : false;
// en query hay que consultar 'token' (VARCHAR(200)), para que el dato que se guarda en $_session['log'] o $_cookie['log'] no sea sha1($clave)
$n = "SELECT clave, id FROM Users WHERE nombre = '$nombre' OR mail = '$nombre';";
$result = $conn->query($n);
$laclave = $result->fetch_row();
if ($result->num_rows != 0) {	    
    if ($laclave[0] == $clave){
        if (isset($mantener) && @$mantener==1){
            setcookie("log", $laclave[1], time()+360000); //sesión dura abierta 600 horas, equivalentes a 25 días.
        } else {
            $_SESSION["log"]=$laclave[1];
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
