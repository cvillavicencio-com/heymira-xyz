<?php
$u=intval(cleanget('u'));
$cat =intval(cleanget('cat'));
$sub=intval(cleanget('sub'));
$top=intval(cleanget('top'));
$pag=intval(cleanget('pag'));
$tag=cleanget('tag');
$bus=cleanget('buscar');
$pag = !$pag ? 1 : $pag; 

//    $opcion = $u ? array("usuario="," WHERE usrid='$u' ") : false;
$opciontitulo='';
$opcionquery='';
$filtro='';

$ppag ='';
$linksporpag=5;
$limit = $pag ? "LIMIT $linksporpag OFFSET ".($linksporpag * ($pag-1)) : 'LIMIT '.$linksporpag;

if ( $u || $cat || $sub || $top ) {

	$opcionquery=' WHERE ';
	if ($u){
	    $quser="SELECT nombre FROM Users WHERE id = '$u';";
	    $ruser=$conn->query($quser)->fetch_row();
	    $filtro.=' del usuario '.$ruser['0'];
	    $opcionquery .=" usrid='$u' ";
        $ppag.='&u='.$u;
	}
	if ( $u && ($cat || $sub || $top) ){
	    $opcionquery .=' AND ';
	}
	if ( $cat ){
	    $qcat="SELECT nombre FROM Categories WHERE id = '$cat'";
	    $rcat=$conn->query($qcat)->fetch_row();
	    $filtro.=' en categoría <i>'.$rcat['0'].'</i>';
	    $opcionquery .="  catid = '$cat' ";
        $ppag.='&cat='.$cat;

	} elseif ( $sub ){
	    $qsub="SELECT nombre FROM Subcategories WHERE id = '$sub'";
	    $rsub=$conn->query($qsub)->fetch_row();
	    $filtro.=' en subcategoría <i>'.$rsub['0'].'</i>';
	    $opcionquery .="  subcatid = '$sub' ";
        $ppag.='&sub='.$sub;

	} elseif ( $top ){
	    $qtop="SELECT nombre FROM Topics WHERE id = '$top'";
	    $rtop=$conn->query($qtop)->fetch_row();
	    $filtro.=' en tópico <i>'.$rtop['0'].'</i>';
	    $opcionquery .="  topicid = '$top' ";
        $ppag.='&top='.$top;
	}


} elseif ($bus) {
    $opcionquery.=" WHERE info LIKE '%$bus%' OR titulo LIKE '%$bus%' ";
    $filtro =" en búsqueda de <i>$bus</i>";
    $ppag .='&buscar='.$bus;


}


$sql = "SELECT * FROM Linksinfo $opcionquery ORDER BY id DESC $limit;";

if($tag) {
    $sql = "SELECT * FROM Tagslinks INNER JOIN Linksinfo ON Tagslinks.linkId = Linksinfo.id WHERE Tagslinks.tag = '$tag';";
    $filtro =" con el tag <i>$tag</i>";
    $ppag .= "&tag=$tag";

}


$contenido[]='Viendo links'.$filtro.'';
    
//listado de links

$result = $conn->query($sql) or die(mysqli_error());




// ver tags


$listalinks=' <div class="columns">';

if ($result->num_rows > 0) {
	$columncount=1;
	$listaok=true;
	while($row = $result->fetch_assoc()) {
	    $listalinks .= '
<div class="column is-one-third">
<div class="clink">
<div class="columns">
<div clasS="column">
<span class="link"><a href="?l='.$row['id'].'"><span class="icon-bubble"></span></a> <a target="_blank" href="'.$row['url'].'">'.$row['titulo'].'</a></span><br>
<span class="icon-user"></span>  <span class="autor"><a href="?f=up&id='.$row['usrid'].'">'.$row['user'].'</a></span>
</div>
<div class="column">

<span class="categ">
        <a href="?cat='.$row['catid'].'">'.$row['cat'].'</a><br>
         <span class="icon-arrow-right"></span> <a href="?sub='.$row['subcatid'].'">'.$row['subcat'].'</a><br>
        <span class="icon-arrow-right"></span><span class="icon-arrow-right"></span> <a href="?top='.$row['topicid'].'">'.$row['topic'].'</a><br>
 

      </span>


<div class="is-hidden-tablet linkbtn">
 <div class="linkbtnn">
   <a href="?l='.$row['id'].'"><span class="icon-bubble"></span></a>
 </div>&nbsp;
 <div class="linkbtnn">
  <a target="_blank" href="'.$row['url'].'"><span class="icon-link"></span></a>
 </div>

</div> 

</div>
</div>
</div>
</div>
';
	    if (is_int($columncount/3)){  // cantidad de columnas, acá :D
            $listalinks .='</div> <div class="columns">';
            $listaok=false;
	    } else {
            $listaok=true;
	    }
	    $columncount++;
	    
	    /*
          SELECT Links.id, Links.info, Links.url, Links.urlextra, Links.creado,
          Users.id AS 'usrid', Users.nombre AS 'user',
          Topics.id AS 'topicid', Topics.nombre AS 'topic',
          Subcategories.id AS 'subcatid', Subcategories.nombre AS 'subcat',
          Categories.id AS 'catid', Categories.nombre As 'cat'
        */ 

	    
	    //    $listalinks .= "  <div class=\"box\">" . $row["url"]. " - " . $row["info"]. " - " . $row["creado"]. "</div>";
	}
}
$listalinks.='</div>';

$pags = explode('DESC',$sql);
$pags = $pags[0].'DESC';

$pagq = $conn->query($pags);
$pagt = $pagq->num_rows;

// $opquery = explode('DESC',$opcionquery);
// $opquery = $opquery[0];
// $paqs = "SELECT COUNT(*) FROM Linksinfo $opquery";
// echo "paqs: $paqs";
// $paql = $pagq->fetch_row();
//    echo $pagl[0]; // <-- este es el total de links q existen :-D
//paginación

//$totalpags = ($paql[0] % $linksporpag) ? ($paql[0] / $linksporpag) : (($paql[0] + ($paql[0] % $linksporpag)*10) / $linksporpag);
$totalpags=($pagt / $linksporpag);


$paginat=array(''.'');
for ($i=0; $i <= $totalpags; $i++) {
	$o = $i+1;
	$current = ($o == $pag) ? array(' is-current','aria-current="page"') : array('',''); 
	$paginat[0] .= '<li><a href="?pag='.$o.'" class="pagination-link'.$current[0].'" '.$current[1].'>'.$o.'</a></li>'.PHP_EOL;	
}

$ant = ($pag > 1) ? array(($pag-1),'',($pag-1)) : array('#','is-disabled','No puedes avanzar más');
$sig = ($pag < $totalpags) ? array(($pag+1),'',($pag+1)) : array('#','is-disabled','No puedes retroceder más');
$listalinks .= '
<nav class="pagination is-small" role="navigation" aria-label="pagination">
  <a href="?pag='.$ant[0].$ppag.'" class="pagination-previous '.$ant[1].'" title="'.$ant[2].'">Más reciente</a>
  <a href="?pag='.$sig[0].$ppag.'" class="pagination-next '.$sig[1].'" title="'.$sig[2].'">Más antiguo</a>
  <ul class="pagination-list">'.$paginat[0].    ' </ul>
</nav>
  ';

$contenido[]=$listalinks;

?>
    
  
