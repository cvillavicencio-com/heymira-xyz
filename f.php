<?php
switch($f){
case 'cc': if ($log){$contenido=logged();break;} include('cc.php'); break;

case 'nc': $m=false; if ($log){$contenido=logged();break;} include('nc.php'); break;

case 'is': if ($log){$contenido=logged();break;} include('is.php'); break;

case 'si': $m=false; include('si.php'); break;

case 'cs': $m=false; if (!$log){$contenido=nologged();break;} include('cs.php'); break;

case 'up': if (!$log){$contenido=nologged();break;} include('up.php'); break;

case 'ep': if (!$log){$contenido=nologged();break;} include('ep.php'); break;

case 'ap': if (!$log){$contenido=nologged();break;} include('ap.php'); break;

case 'nl': if (!$log){$contenido=nologged();break;} include('nl.php'); break;

case 'al': $m=false; if (!$log){$contenido=nologged();break;} include('al.php'); break;

case 'ul': $m=false; if (!$log){$contenido=nologged();break;} include('ul.php'); break;

case 'dc': $m=false; if (!$log){$contenido=nologged();break;} include('dc.php'); break;

case 'ec': $m=false; if (!$log){$contenido=nologged();break;} include('ec.php'); break;

case 'bu': $m=false; if (!$log){$contenido=logged();break;} include('bu.php'); break;

default:
    $estasen='<div>Buscad y encontrareis, pero no acá</div>';
    $contenido[]='Qué raro...';
    $contenido[]='no deberías estar leyendo esto. Igual, no hay nada en esta parte :-) <!-- y de verdad que no hay nada... -->';
    // y en efecto no hay nada :-)
    break;
}

?>
