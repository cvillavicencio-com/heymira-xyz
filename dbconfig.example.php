<?php
// db aNd config
// your data, here
$srv = 'localhost';
$usr = 'user';
$pwd = 'password';
$dbn = 'database';

$conn = new mysqli($srv, $usr, $pwd, $dbn);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//config
if ($_GET['f'] == 'al'){
    // telegrambot token.
    $tgtok= '[TOKEN]';
}
?>
