<?php
// MIT License - Copyright (c) 2023 Heymira.xyz

// crea código
// la forma de crear el código de invitación es arbitraria. Podría ser un rand(0,99), pero sugiero que sea algo más entretenido y caótico, para ayudar al azar a ser más incontrolable.

$letras = array('a', 'b', 'd', 'e', 'f', 'g', 'h', 'j',
                'm', 'n', 'q', 'r', 't', 'y', 'A', 'B',
                'D', 'E', 'F', 'G', 'H', 'J', 'M', 'N',
                'Q', 'R', 'T', 'Y');

$ncode = $letras[rand(0,(count($letras)-1))] .
       substr(microtime(),-5) . $letras[rand(0,(count($letras)-1))] .
       rand (1111,9999) .
       $letras[substr(microtime(),-1,1)].rand(111,999);  // creación de código de inv.

// salida debe llamarse $ncode.

?>
