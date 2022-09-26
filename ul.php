<?php
	    $contenido[]='Editando link';
	    // recibe datos:
	    $titulo    = cleanpost('titulo');
        $info      = nl2br(cleanpost('info'));
	    $url       = cleanpost('url');
	    $tags      = cleanpost('tags');
	    $tags0     = explode(',',$tags);
	    $urlextra  = cleanpost('urlextra');
	    $topicid   = intval(cleanpost('topic'));
	    $catset   = intval(cleanpost('catset'));
	    $idediting = intval(cleanpost('idediting'));

	    // query opcionales: urlextra
	    $urlextraq = $urlextra ? ",urlextra='$urlextra'":false;


	    /*
	       UPDATE table_name
	       SET column1 = value1, column2 = value2, ...
	       WHERE condition;
	     */

	    // confirmar si usuario es autor, para poder editar.
	    $ls="SELECT usrid FROM Linksinfo WHERE id='$idediting';";
	    $lq = $conn->query($ls);
	    $ll = $lq->fetch_row();
	    // id titulo info url urlextra creado stateid usrid user topicid topic subcatid subcat catid cat
	    // 0  1      2    3   4        5      6       7     8    9       10    11       12     13    14

	    if ($ll[0] == $id){
		$us = "UPDATE Links SET titulo='$titulo', info='$info',url='$url',topicId='$topicid', creado = creado, catsetId='$catset' $urlextraq WHERE id='$idediting';";
		$uq = $conn->query($us) or die(mysqli_error());

		// DELETE FROM table_name WHERE condition;
		$ds = "DELETE FROM Tagslinks WHERE linkId='$idediting';";
		$dq = $conn->query($ds) or die(mysqli_error());

		if ($tags){
		    $linkid = $conn->insert_id;
		    $tq='';
		    foreach($tags0 as &$tag){
			$tag = (substr($tag,0,1) == ' ') ? substr($tag,1) : $tag;
			$tq = "INSERT INTO Tagslinks (tag,linkid) VALUES ('$tag','$idediting');";
			$resultag=$conn->query($tq) or die(mysqli_error($conn));
		    }
		}

		$contenido[]=imgredirect('logo.png','?l='.$idediting,'El link ha sido editado exitosamente.');
	    } else {
		$contenido[]='No puedes editar este link.';
	    }


?>
