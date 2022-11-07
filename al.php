<?php
$contenido[]='Agregando link';
// recibe datos:
$titulo   = cleanpost('titulo');
$info     = nl2br(cleanpost('info'));
$url      = cleanpost('url');
$tags     = cleanpost('tags');
$tags0    = explode(',',$tags);
$urlextra = cleanpost('urlextra');
$topicid  = intval(cleanpost('topic'));
$catset   = intval(cleanpost('catset'));
$tokensal   = @cleanpost('tokensal');

// query opcionales: urlextra
$urlextraq = $urlextra ? array(', urlextra',", '$urlextra'") : array(false,false);

// verificar si link ya existe
$al = "SELECT id FROM Links WHERE url = '$url';";
$result= $conn->query($al);
if ($result->num_rows == 0){ // link no existe. bien

    // ahora tomar id del último link, para crear el siguiente
    $gs = "SELECT MAX(id) FROM Links;";
    $gq = $conn->query($gs);
    $gl = $gq->fetch_row();
    $idmax=$gl[0];
    
    $ahora = date('Y-m-d H:i:s');
    $l = "INSERT INTO Links (titulo, info, url, topicId, catsetId, creado, autorId {$urlextraq[0]}) VALUES ('$titulo','$info','$url','$topicid', '$catset','$ahora', '$id' {$urlextraq[1]});";
    $resultl=$conn->query($l) or die(mysqli_error($conn));
    if (!empty($resultl)){

        // inicio de subida exitosa
        if ($tags){
            $linkid = $conn->insert_id;
            $tq='';
            foreach($tags0 as &$tag){
                $tag = (substr($tag,0,1) == ' ') ? substr($tag,1) : $tag;
                $tq = "INSERT INTO Tagslinks (tag,linkid) VALUES ('$tag','$linkid');";
                $resultag=$conn->query($tq) or die(mysqli_error($conn));
            }
        }

        // Cuantos Links ha publicado el usuario?
        $clS = "SELECT COUNT(id) FROM Linksinfo WHERE usrid='$id';";
        $clQ = $conn->query($clS);
        $clL = $clQ->fetch_row();
        $totallinks = $clL[0];

        // UPGRADE DE TIPO DE USUARIO SEGÚN CANTIDAD DE LINKS
        include('permisos.php');
        foreach ($permisos as &$p){
            if ($totallinks == $p[0]){
                $perS = "UPDATE Users SET rol = '{$p[1]}' WHERE id = '$id';";
                $perQ = $conn->query($perS);	
            }
        }
        // FIN UPGRADE

       

        // tokens :-D

        $mtS = "SELECT * FROM Tokens WHERE usrid='$id';";
        $mtQ = $conn->query($mtS);
        $mtL = $mtQ->fetch_row();


        $titulonot = str_replace('"',"'",$titulo); // para evitar problemas con el sh que publica.
        $notif = "He publicado el link '$titulonot' en https://heymira.xyz/?l=".($idmax+1);

        // inicio mastodon
        $mstin = $mtL[2];
        $mstin = substr($mstin,-1) == '/' ? substr($mstin,0,-1) : $mstin;
        $msttk = $mtL[3];

        $tokensal = !$tokensal ? array('') : $tokensal;
        if (!empty($mstin) && !empty($msttk) && @in_array("1",$tokensal)){
            // tooteo acá.
            exec('./toot.sh '.$mstin.' '.$msttk.' "'.$notif.'"');
        }
        // fin mastodon
        
        // inicio twitter
        // fin twitter
        
        // inicio tg
        $tg_id = $mtL[6];
        if (!empty($tg_id) && @in_array("3",$tokensal)){
            // msg tg acá.
            $com = './tg.sh '.$tg_id.' '.$tgtok.' "'.$notif.'"';
        }
        // fin tg

        
        // fin tokens


        

        $contenido[] = imgredirect('css/ojo.gif','?l='.($idmax+1),'Link guardado exitosamente.');
        // fin de subida exitosa
    } else {
        $contenido[] = 'error guardando link';
    }
} else {
    $contenido[]='El link no se agregó porque ya estaba anteriormente.';
}

?>
