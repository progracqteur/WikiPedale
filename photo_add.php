<?php require "vars_and_fcts.php"; ?>
<html>
<head>
	<title>Ajouter une photo - GRACQ Mons</title>
	<script src="jquery-1.6.1.min.js" type="text/javascript"></script>
	<script src="map.js" type="text/javascript"></script>
	<?php
	if(isset($_POST['action']) && $_POST['action'] == 'ajouter')
      	{ ?>
		<script language="javascript"> <!--
			$(document).ready(function(){
				rafraichir_photo_opener(photo_height_details);
				}); // -->
		</script>
		<?php
		} ?> 		
</head>
<body>
	<?php
	if(isset($_GET['id']) && is_numeric($_GET['id']))
  	{
    	$next_id = photo_jpg_next_id();

    if(isset($_POST['action']) && $_POST['action'] == 'ajouter')
      	{
			if (is_uploaded_file($_FILES['image']['tmp_name']))
	  		{
	    		$emplacement_image = $chemin_images.$_GET['id'].'_'.($next_id).'.jpg';
	    		$emplacement_miniature = $chemin_images.$_GET['id'].'_'.($next_id).'M.jpg';

	    		$extension = strchr($_FILES['image']['name'], '.');

	    		$image_sauvegardee = False;
	    		if($extension == '.JPG' || $extension == '.jpeg' || $extension == '.jpg') {
					$image_sauvegardee  = move_uploaded_file($_FILES['image']['tmp_name'],$emplacement_image);
	      		}
	    		elseif($extension == '.PNG' || $extension = '.png') {
					$image_sauvegardee = conversion_png_to_jpg($_FILES['image']['tmp_name'], $emplacement_image);
	      		}
	    
	    		if($image_sauvegardee) {	
					if(creation_miniature_jpg($emplacement_image, $emplacement_miniature))
		  				$next_id ++;
					else
		  				print '<h1>Erreur :</h1><span style="color:red">Miniature non cr&eacute;e. Si le probl&egrave;me persiste, veuillez le signaler.</span>';
	      		}
	    		else
	      			print '<h1>Erreur :</h1><span style="color:red">Image non sauvegard&eacture;e. Si le probl&egrave;me persiste, veuillez le signaler.</span>';
	  		}
			else
	  		{
	    		print '<h1>Erreur :</h1><span style="color:red">Le serveur n\'a pu t&eacute;l&eacute;charger votre image. Si le probl&egrave;me persiste, veuillez le signaler.</span>';
	  		}
      	}
		?>
		<h2>Ajouter une photo : </h2>
		<ul>
	    	<li>Seules les images 'png' et 'jpeg' sont accept&eacute;.</li>
	    	<li>Si la taille de l'image est importante, l'ajout de l'image peut prendre du temps.</li>
			<li>Une fois l'ajout de photos termin&eacute;, <button onclick="window.close()">fermez la page</button>.</li>
	    </ul>
	    <form enctype="multipart/form-data" action="photo_add.php?id=<?php print $_GET['id']; ?>" method="post">
	    	<input type="file" name="image">
	    	<input type="hidden" name="action" value="ajouter">
	     	<input type="submit" value="Ajouter">

		<h2>Photos actuelles du point noir <?php print $_GET['id']; ?>  :</h2>
		<?php
    	if($next_id == 0)
      		print 'Pas de photos pour ce point noir.';
    	else {
			for ($i = 0; $i < $next_id; $i ++) {
	    		print ' <a href="'.$chemin_images.$_GET['id'].'_'.($i).'.jpg'.'"><image height="100" src="'.$chemin_images.$_GET['id'].'_'.($i).'M.jpg'.'"></image></a>';
	  		}
			print '<br />(cliquez sur une image pour l\'agrandir)';
      	}
 	?>
   <?php
  }
else {
  print '<h1>Erreur :</h1><span style="color:red">Pas de point noir s&eacute;lectionn&eacute;.</span>';
}
?>
</body>
</html>