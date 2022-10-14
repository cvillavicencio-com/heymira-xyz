<?php


$tokS = "UPDATE Users SET token = NULL WHERE id = '$id';";

$tokQ = $conn->query($tokS);

if (isset($_SESSION['log'])) {
    unset($_SESSION['log']);
    $txto = 'La sesión temporal se ha cerrado exitosamente.';
} elseif ($_COOKIE['log']) {
    setcookie("log", 'null', time()); //
    $txto = 'La sesión permanente se ha cerrado exitosamente.';
} else {
    $txto = 'No hay sesión para cerrar.';
}

$contenido[] = 'Cierre de sesión';
$contenido[] = imgredirect('css/ojo.gif','.',$txto);

?>
