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

// formato: url;;topicid
// IMPORTANTE!!! CREAR $topicid que se llama más adelantEEE
echo 'rssbot! :D';

$sitios = file('./rss.list');
foreach ($sitios as &$sitio){
	if (substr($sitio,0,1) == '#'){
		break;
	}

	$sitio= explode(";;",$sitio);
	$url = $sitio[0];	
	$topicid= $sitio[1];

	$rss_feed = simplexml_load_file($url);
  if (!empty($rss_feed)) {
    $i = 0;
    foreach ($rss_feed->channel->item as $feed_item) {
      if ($i >= 10)
        break;

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
        echo 'ok <br>';
      } else {
        echo '<br>link ya existe <br>';
      }
      // asigna 
      $i ++;
    }

  }
}
?>
