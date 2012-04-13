<html>
<head>
<title>Ajout d un nouveau point noir - GRACQ Mons</title>
</head>
<body>
<center>
<?php 
require "vars_and_fcts.php";

$lon = $_POST['lon'];
$lat = $_POST['lat'];
$description = $_POST['description'];
$email = $_POST['email'];
$lieu = $_POST['lieu'];
$nomprenom = $_POST['nomprenom'];

if (empty($lon) || empty($lat) || empty($description) || empty($email))
  {
    ?>
    Veuillez au minimum indiquer o&ugrave; se trouve le point noir, une description,
      et un email. Merci.<br />
      <br />
      <a href="signaler.php">Retour au formulaire.</a>
    <?php
  }
else
  {
      $conn_db = mysql_connect($db_host, $db_login, $db_pass);

      if (!$conn_db) {
	echo "Unable to connect to DB: " . mysql_error();
	exit;
      }

      if (!mysql_select_db($db_name)) {
	echo "Unable to select mydbname: " . mysql_error();
	exit;
      }

      $lon = secure($lon);
      $lat = secure($lat);
      $description = secure($description);
      $email = secure($email);
      $lieu = secure($lieu);
      $nomprenom = secure($nomprenom);

      $sql = "INSERT INTO $db_table_pn (lon, lat, description, email, lieu, nom_prenom) VALUES ($lon,$lat,'$description','$email','$lieu','$nomprenom')";
      $insert_ok = mysql_query($sql);

      if ($insert_ok)
	print "Merci de votre collaboration.<br />";
      else
	print "<red>PBM sql. Contactez le webmaster.</red><br />";
      mysql_close($conn_db);
      ?>
      <br />
      <a href="signaler.php">Ajouter un nouveau point noir.</a><br />
      <a href="index.php">Voir tous les points noirs.</a>
      <?php
  }
 ?>
</center>
</body>
</html>
