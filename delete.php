<?php
session_start();
require "vars_and_fcts.php";
?>
<html>
<head>
	<title>&Eacute;dition des points noirs - GRACQ Mons</title>
</head>
<body>
<?php
if(isset($_SESSION['can_edit']) && $_SESSION['can_edit'] == "vas y mon coco")
{
  if (isset($_GET['id']) && is_numeric($_GET['id']))
    {
      $conn_db = mysql_connect($db_host, $db_login, $db_pass);

      if (!$conn_db) {
	echo "<font color='red'>Unable to connect to DB: " . mysql_error() . "</font>";
	exit;
      }

      if (!mysql_select_db($db_name)) {
	echo "<font color='red'>Unable to select mydbname: " . mysql_error() . "</font>";
	exit;
      }

      $id = $_GET['id'];
      $sql = "UPDATE $db_table_pn SET type='points_noirs_supp'  WHERE id='$id'";
      $delete_ok = mysql_query($sql);

      if ($delete_ok)
	print "<font color='green'>Point noir supprimé!</font> <a href='gestion.php'>Retour.</a>";
      else
	print "<font color='red'>PBM sql. Contactez le webmaster.</font>";
      mysql_close($conn_db);
    }
  else
    {
      print "<font color='red'>PBM lien non-valide. Contactez le webmaster.</font>";
    }
}
else {
	?>
	<h1>Il faut &ecirc;tre connect&eacute;.</h1>
	<h1>Connexion</h1>
    <form action="connexion.php?next=gestion" method="post">
    	Utilisateur : <input type="text" name="utilisateur"><br />
      	Mot de Passe :<input type="password" name="mdp"><br />
      	<input type="submit" value="Connexion">
	</form>
	<?php
}
?>
</body>
</html>
