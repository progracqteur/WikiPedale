<html>
  <head>
  <title>Ajout d un nouveau point noir - GRACQ Mons</title>
  <script src="OpenLayers.js"  type="text/javascript"></script>
  <script src="./jquery-1.6.1.min.js" type="text/javascript"></script>
  <script src="./map.js" type="text/javascript"></script>
  <style type="text/css">
   label {
   display:block;
   width:100px;
   float:left;
   }
   </style>
    <script language="javascript">
    <!--
	$(document).ready(function(){
	map_and_pointer();
	});
	// -->
  </script>
  </head>
  <body>
	<div style="width:50%; height:100%; float:left;" id="map"></div>
	<div style="width:45%; height:100%; float:left;padding-left:3%">
   		<h1>Signaler un point noir.</h1>
   		<strong>Pour des petits travaux et des travaux d'entretien, vous pouvez contacter directement la ville via le num&eacute;ro gratuit 0800 92 329 ou via <a href="mailto:travaux.vert@ville.mons.be">travaux.vert@ville.mons.be</a> (<a href="http://www.mons.be/default.aspx?GUID={BF4FA10D-9842-11DA-9742-0002A58CB319}&LNG=FRA" target="_blank">mons.be</a>)</strong>.
		<br />
		<br />
		Cliquez sur la carte pour indiquer la position du point noir et remplissez
   		le formulaire suivant.<br /><br />
  		<form method="post" action="saving_report.php" name="f">
   			<input  type="hidden" name="lon" readonly="true" <?php if(isset($_POST['lon'])) { print 'value="'.$_POST['lon'].'"'; } ?> />
  			<input   type="hidden" name="lat" readonly="true" <?php if(isset($_POST['lat'])) { print 'value="'.$_POST['lat'].'"'; } ?> />
   			<label for="l">Lieu : </label> <input name="lieu" <?php if(isset($_POST['lieu'])) { print 'value="'.$_POST['lieu'].'"'; } ?> id="n" size=42 /><br />
   			<label for="d">Description:</label> <textarea name="description" id="d" cols=35 rows=5><?php if(isset($_POST['description'])) { print $_POST['description']; } ?></textarea><br />
   			<br />
   			<label for="n">Nom, prénom: </label> <input name="nomprenom" <?php if(isset($_POST['nomprenom'])) { print 'value="'.$_POST['nomprenom'].'"'; } ?> id="n" size=42 /><br />
   			<br />
   			<label for="e">Email:</label> <input name="email" <?php if(isset($_POST['email'])) { print 'value="'.$_POST['email'].'"'; } ?> id="e"  size=42 /><br />
   			<br />
   			<div style="padding-left:100px"><input type="submit" value="soumettre"></div>
    	</form>
   		<br />
   		<b>Aide :</b>
   		<ul>
   			<li>Un point noir est quelque chose qui pose probl&egrave;me &agrave; la pratique id&eacute;ale du v&eacute;lo ;  ça peut &ecirc;tre une bordure non abaiss&eacute;e, une piste cyclable mal entretenue, un feu mal synchronis&eacute;, ...
   			<li>Pour nous signaler un point noir, indiquez sur la carte o&ugrave; se trouve ce point noir, remplissez le formulaire et cliquez sur "soumettre". Merci.
   		</ul>	
		<br />
		<a href="index.php">Retour à la liste des points noirs.</a>										
	</div>
  </body>
</html>
