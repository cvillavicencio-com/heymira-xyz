<?php
$contenido[] = 'Cierre de sesión';
if (isset($_SESSION['log'])) {
    unset($_SESSION['log']);
    $txto = 'La sesión temporal se ha cerrado exitosamente.';
} elseif ($_COOKIE['log']) {
    setcookie("log", 'null', time()); //

    $txto = 'La sesión permanente se ha cerrado exitosamente.';
} else {
    $txto = 'No hay sesión para cerrar.';
}

$contenido[] = imgredirect('css/ojo.gif','.',$txto);

?>
