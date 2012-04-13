<?php 
//header('Content-type: text/json');
//header('Content-type: application/json');

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


if(isset($_GET['all_info']) && $_GET['all_info'] == 1)
	$label_row_db = array('id', 'lon', 'lat', 'description', 'nom_prenom', 'lieu', 	'email', 'couleur', 'date_resol');
else
	$label_row_db = array('id', 'lon', 'lat', 'description', 'lieu', 'couleur', 'date_resol');
	

mysql_query('SET CHARACTER SET utf8');

if(isset($_GET['id']) && is_numeric($_GET['id']))
  {
    $sql = "SELECT ".(join(",", $label_row_db))." FROM $db_table_pn WHERE type='points_noirs' AND id='".$_GET['id']."'";
  }
else
  {
    $sql = "SELECT ".(join(",", $label_row_db))." FROM $db_table_pn WHERE type='points_noirs'";
  }

$result = mysql_query($sql);

$to_print = "var all_reports = new Array();";
$to_print .= "var all_reports = [";

$all = array();

while($row = mysql_fetch_assoc($result)){
  $row['description'] = unescape($row['description']);
  $row['lieu'] = unescape($row['lieu']);
  $all[] = $row;
}

print json_encode($all);
?>