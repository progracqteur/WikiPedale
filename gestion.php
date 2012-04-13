<?php
session_start();
?>
<html>
<head>
	<title>&Eacute;dition des points noirs - GRACQ Mons</title>
	<script src="OpenLayers.js"  type="text/javascript"></script>
	<script src="jquery-1.6.1.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="jquery.form.js"></script>
	<script src="map.js" type="text/javascript"></script>
	<script language="javascript"> <!--
		$(document).ready(function(){
			map_with_action('db_points_noirs.php?all_info=1',edition_points_noirs);
		}); // -->
	</script>
	<link rel="stylesheet" type="text/css" href='formulaire.css'></link>
</head>
<body>
<?php
if(isset($_SESSION['can_edit']) && $_SESSION['can_edit'] == "vas y mon coco")
{ ?>
	<div style="width:50%; height:100%; float:left;" id="map"></div>
	<div style="width:50%; height:100%; float:left; overflow:auto;">
    	<div id="div_accueil" style="margin-left:15px;">
			<center><h1>Cliquez sur un point noir pour l'&eacute;diter</h1></center>
      	</div>
   		<div id="div_edition" style="display:none;margin-left:15px;">
			<center><h1>Point noir <span id="span_id"></span></h1></center>
			<strong><a id="link_delete" href="#">Supprimer le point noir</a><strong><br />
			<br />
			<strong>Contact</strong> : <span id="span_nomprenom"></span><br />
			<strong>Mail</strong> : <span id="span_email"></span><br />
			<br />
			
			<form method="post" action="#" name="f" id="formulaire_edition">
	  			<input type ="hidden" value="" id="f_id" name="id" />
				<p class="p_form">
	  				<label for="f_lieu" class="label_aligne"><strong>Lieu</strong> : </label> 
	  				<input name="lieu"  id="f_lieu" size=42 />
				</p>
				<p class="p_form">
	 				<label for="f_description" class="label_aligne"><strong>Description</strong> :</label>
	  				<textarea name="description" id="f_description" cols=35 rows=5></textarea>
				</p>
				<p class="p_form">
	  				<label for="c" class="label_aligne"><strong>Couleur</strong> :</label>
	  				<select name="couleur" id="c">
	    				<option id="white" value="white" >Blanc</option>
	    				<option id="red" value="red">Rouge</option>
	    				<option id="yellow" value="yellow">Jaune</option>
	    				<option id="green" value="green">Vert</option>
	  				</select>
				</p>
				<p class="p_form" id="message_erreur" style="display:none">
					<span style="margin-left:100px; background-color:red;">Veuillez vous <a href="connexion.php" target="_blank">connecter</a> pour enregistrer la modification.</span>
				</p>
				<p class="p_form">
					<span style="padding-left:100px;"><input type="submit" value="&Eacute;diter"></span>
				</p>
			</form>
			
			<strong>Photo</strong>
            (<a id="link_add_photo" href="#">ajouter une photo</a>) :
			<span id="span_photo"></span>
      	</div>

    </div>
<?php  }
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
