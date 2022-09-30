<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include('dbconfig.php');

/* Este archivo crea un json con la siguiente estructura:

+ heymira
\- catset
\-- categoria
\--- subcategoria
\---- topico


La idea es reemplazar el selector de tópicos actual por algo más dinámico y cómodo usando angular.


*/

$catset=1;

$sql0 = "SELECT id, nombre FROM Catsets;";
$res0 = $conn->query($sql0);
$out0 = "";
while($r0 = $res0->fetch_assoc()) {
    if ($out0 != "") {$out0 .= ",".PHP_EOL;}

    $out0 .=                                      '  {"id":"'  . $r0["id"] . '",'.PHP_EOL;
    $out0 .=                                      '   "nombre":"'. $r0["nombre"].'",'.PHP_EOL;
    
    $out0 .=                                      '   "categorias":['.PHP_EOL;    
    $sql1 = "SELECT id, nombre FROM Categories WHERE catsetId='{$r0["id"]}';";
    $res1 = $conn->query($sql1);
    $out1 = "";
    while($r1 = $res1->fetch_assoc()) {
        if ($out1 != "") {$out1 .= ",".PHP_EOL;}
        $out1 .=                                  '    {"id":"'  . $r1["id"] . '",'.PHP_EOL;
        $out1 .=                                  '     "nombre":"'. $r1["nombre"].'",'.PHP_EOL;
        $out1 .=                                  '     "subcategorias":['.PHP_EOL;
        $sql2 = "SELECT id, nombre FROM Subcategories WHERE categId='{$r1["id"]}';";
        $res2 = $conn->query($sql2);
        $out2 = "";
        while($r2 = $res2->fetch_assoc()) {
            if ($out2 != "") {$out2 .= ",".PHP_EOL;}
            $out2 .=                              '      {"id":"'  . $r2["id"] . '",'.PHP_EOL;
            $out2 .=                              '       "nombre":"'. $r2["nombre"].'",'.PHP_EOL;
            $out2 .=                              '       "topicos":['.PHP_EOL;
            $sql3 = "SELECT id, nombre FROM Topics WHERE subcatid='{$r2["id"]}';";
            $res3 = $conn->query($sql3);
            $out3 = "";
            while($r3 = $res3->fetch_assoc()) {
                if ($out3 != "") {$out3 .= ",".PHP_EOL;}
                $out3 .=                          '      {"id":"'  . $r3["id"] . '",'.PHP_EOL;
                $out3 .=                          '       "nombre":"'. $r3["nombre"].'"'.PHP_EOL;
                $out3 .=                          '      }'.PHP_EOL;

            }
            $out2 .= $out3.                       '     ]'.PHP_EOL;
            $out2 .=                              '    }'.PHP_EOL;
            
        }
        $out1 .= $out2.                           '     ]'.PHP_EOL;
        $out1 .=                                  '    }'.PHP_EOL;
    }
    $out0 .= $out1.                               '   ]'.PHP_EOL;
    $out0 .=                                      '  }';

}

$outp ='{"heymira":['.PHP_EOL.$out0.PHP_EOL.' ]'.PHP_EOL.'}';
$conn->close();

echo($outp);

exit();
?>
