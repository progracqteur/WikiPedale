<?php 
session_start();
$list_users = array("gracq" => "vivelevelo");
?>
<html>
  <head>
    <title>Connexion - GRACQ Mons</title>
  </head>
  <body>
<?php 
$afficher_formulaire = true;

if(isset($_POST['utilisateur']) && isset($_POST['mdp'])) 
  //traitement d'une demande de connection (formulaire rempli)
  {
    $name = $_POST['utilisateur'];
    if(array_key_exists($name, $list_users) && ($list_users[$name] == $_POST['mdp']))
      { 
		$_SESSION['can_edit'] = "vas y mon coco";
		print "<h1>Vous &ecirc;tes connect&eacute;</h1><br />";
		?>
		<script language="javascript"> <!--
		<?php 
		if(isset($_GET['next'])) { // retour à la page de gestion.php ?>
			window.location = "gestion.php"
		<?php }
		else { // suppression du message d'erreur et fermeture de la fenêtre de connection ?> 
			window.opener.document.getElementById("message_erreur").style.display = "none"; 
			setTimeout("window.close();",2000);
		<?php } ?>
		// -->
		</script>
		<?php
		$afficher_formulaire = false;
      }
    else
      { 
	print '<h1><font color="red">Erreur</font></h1>';
      }
  }

if($afficher_formulaire)
  { ?>
    <h1>Connexion</h1>
    <form action="" method="post">
    	Utilisateur : <input type="text" name="utilisateur"><br />
    	Mot de Passe :<input type="password" name="mdp"><br />
    	<input type="submit" value="Connexion">
	</form>
  <?php  }
?>
  </body>
</html>


