<?php

require "vars_and_fcts.php";

$conn_db = mysql_connect($db_host, $db_login, $db_pass);

if (!$conn_db) {
  print "Unable to connect to DB: " . mysql_error();
  exit;
}

if (!mysql_select_db($db_name)) {
  print "Unable to select mydbname: " . mysql_error();
  exit;
}



$label_row_db = array('id', 'lon', 'lat', 'description', 'nom_prenom', 'lieu', 	'email', 'couleur', 'date_resol');


$sql = "SELECT ".(join(",", $label_row_db))." FROM $db_table_pn WHERE type='points_noirs'";

$result = mysql_query($sql);

while($row = mysql_fetch_assoc($result)){
  $row['description'] = unescape($row['description']);
  $row['lieu'] = unescape($row['lieu']);

  print '<strong>Point noir '.$row['id'].' :</strong><br/>';
  print '<br/>';
  print 'lieu: '.$row['lieu'].' <br/>';
  print 'couleur: '.$row['couleur'].' <br/>';
  print 'description: '.$row['description'].' <br/>';
  print 'nom prénom: '.$row['nom_prenom'].' <br/>';
  print 'email: <a href="mailto:'.$row['email'].'">'.$row['email'].'</a> <br/>';
  $url = 'http://orangeade.be/osm/points_noirs/index.php?id='.$row['id'];
  print 'accès direct: <a href="'.$url.'">'.$url.'</a><br />';
  print '<br/>';
  print '<br/>';
  print '<br/>';
}
?>