<?php
session_start();

//header('Content-type: text/json');
//header('Content-type: application/json');

require "vars_and_fcts.php";

$array_ret;

if (! isset($_SESSION['can_edit']) || $_SESSION['can_edit'] != "vas y mon coco") {
	$array_ret = array("ok" => false, "id_erreur" => 1, "message_erreur" => "vous n'êtes pas connecté");
}
elseif (empty($_POST['id']) ||  ! is_numeric($_POST['id']) || empty($_POST['description']) || empty($_POST['couleur']))
  {	
	$array_ret = array("ok" => false, "id_erreur" => 2, "message_erreur" => "pas de d'id pour le point");
  }
else {
	$id = $_POST['id'];
	$lieu = $_POST['lieu'];
	$description = $_POST['description'];
	$couleur = $_POST['couleur'];
	$conn_db = mysql_connect($db_host, $db_login, $db_pass);
    
	if (!$conn_db) {
		$array_ret = array("ok" => false, "id_erreur" => 3, "message_erreur" => "Unable to connect to DB:" . mysql_error());
		}
	elseif (!mysql_select_db($db_name)) {
		$array_ret = array("ok" => false, "id_erreur" => 4, "message_erreur" => "Unable to select mydbname: " . mysql_error());
      }
	else {
		mysql_query('SET CHARACTER SET utf8');
		$id = secure($id);
      	$lieu = secure($lieu);
      	$description = secure($description);
      	$couleur = secure($couleur);

      	$sql = "UPDATE $db_table_pn SET lieu='$lieu', description='$description', couleur='$couleur' WHERE id = '$id'";
      	$edit_ok = mysql_query($sql);

      	if ($edit_ok)
			$array_ret = array("ok" => true);
      	else
			$array_ret = array("ok" => false, "id_erreur" => 5, "message_erreur" => "erreur sql");
      	mysql_close($conn_db);
  	}
}
print json_encode($array_ret);
 ?>
