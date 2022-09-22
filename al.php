<?php
	    $contenido[]='Agregando link';
	    // recibe datos:
	    $titulo   = cleanpost('titulo');
	    $info     = cleanpost('info');
	    $url      = cleanpost('url');
	    $tags     = cleanpost('tags');
	    $tags0    = explode(',',$tags);
	    $urlextra = cleanpost('urlextra');
	    $topicid  = intval(cleanpost('topic'));
	    $catset  = intval(cleanpost('catset'));

	    // query opcionales: urlextra
	    $urlextraq = $urlextra ? array(', urlextra',", '$urlextra'") : array(false,false);
	    
	    // verificar si link ya existe
	    $al = "SELECT id FROM Links WHERE url = '$url';";
	    $result= $conn->query($al);
	    if ($result->num_rows == 0){ // link no existe. bien
		$ahora = date('Y-m-d H:i:s');
		$l = "INSERT INTO Links (titulo, info, url, topicId, catsetId, creado, autorId {$urlextraq[0]}) VALUES ('$titulo','$info','$url','$topicid', '$catset','$ahora', '$id' {$urlextraq[1]});";
		$resultl=$conn->query($l) or die(mysqli_error($conn));
		if (!empty($resultl)){
		    if ($tags){
			$linkid = $conn->insert_id;
			$tq='';
			foreach($tags0 as &$tag){
			    $tag = (substr($tag,0,1) == ' ') ? substr($tag,1) : $tag;
			    $tq = "INSERT INTO Tagslinks (tag,linkid) VALUES ('$tag','$linkid');";
			    $resultag=$conn->query($tq) or die(mysqli_error($conn));
			}
		    }
		    $contenido[] ='Link creado.';
		} else {
		    $contenido[] = 'error guardando link';
		}
	    } else {
		$contenido[]='El link no se agregó porque ya estaba anteriormente.';
	    }

?>
