<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('dbconfig.php');


$sitios = array(
    'https://nftnow.com/rss',
    'https://publicdomainreview.org/rss.xml',
    'https://news.ycombinator.com/rss',
);

// extrae rss desde links que están en rss.list
// formato: url;;topicid por línea.

echo '<h1>rssbot! :D</h1>';
$t = 'ejecución rssbot - '.date('l jS \of F Y h:i:s A').PHP_EOL;

$sitios = file('./rss.list');
foreach ($sitios as &$sitio){

	$sitio= explode(";;",$sitio);
	$url = $sitio[0];	
	$topicid= $sitio[1];
    
	$rss_feed = simplexml_load_file($url);
    echo '<h2>'.$url.'</h2>';
    if (!empty($rss_feed)) {
        $i = 0;
        foreach ($rss_feed->channel->item as $feed_item) {
            if ($i >= 10){
                break;
            }
            $url = $feed_item->link;
            $title = $feed_item->title;
            $desc = $feed_item->description;
            $desc = $desc = '' ? '.': substr($desc,0,999);
            $desc = htmlspecialchars($desc,ENT_QUOTES);
            $ahora = date('Y-m-d H:i:s');

            // evalua si link ya está puesto
            $leS = "SELECT * FROM Linksinfo WHERE url = '$url'";
			//  echo 'evalexist: '.$leS.'<br>';

			$leQ = $conn->query($leS);
            $leL = $leQ->fetch_row();
            if (empty($leL[0])){
                $t .= '+ '.substr($title,0,30).' - '.substr($url,0,25).PHP_EOL;
            
                // obtiene catset
                $obcS = "
SELECT Catsets.id 
FROM Topics 
JOIN Subcategories ON Topics.subcatId = Subcategories.id 
JOIN Categories ON Subcategories.categId = Categories.id 
JOIN Catsets ON Categories.catsetId = Catsets.id
WHERE Topics.id = '$topicid';
				";
                $obcQ = $conn->query($obcS);
                $obcL = $obcQ->fetch_row();    
                $catsetid = $obcL[0];
                // sube link
                $q = "
INSERT INTO Links (titulo, info, url, topicId, catsetId, creado, autorId) VALUES ('$title','$desc','$url','$topicid', '$catsetid','$ahora', '13');";
                //        echo '<hr>'.$q.'<hr>';
                $l = $conn->query($q);
                echo 'link <b>'.substr($title,0,35).'</b> fue agregado<br>';
            } else {
                echo 'link <b>'.substr($title,0,35).'</b> ya existe <br>';
            }

            
            $i ++;
        }

    }
}

$tt = file_get_contents('rssread.log');

file_put_contents('rssread.log',$t.$tt);

?>
