<?php

// your data, here
$srv = 'localhost';
$usr = 'user';
$pwd = 'password';
$dbn = 'int-matric';

$conn = new mysqli($srv, $usr, $pwd, $dbn);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
