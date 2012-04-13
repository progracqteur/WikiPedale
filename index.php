<html>
<head>
	<title>&Eacute;dition des points noirs - GRACQ Mons</title>
	<script src="OpenLayers.js"  type="text/javascript"></script>
	<script src="jquery-1.6.1.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="jquery.form.js"></script>
	<script src="map.js" type="text/javascript"></script>
	<script language="javascript"> <!--
		$(document).ready(function(){
			<?php
			if (isset($_GET['id']) && is_numeric($_GET['id']))
				print 'marker_onload_select = '.$_GET['id'].';';
			?>
			map_with_action('db_points_noirs.php',details_point_noir);
		}); // -->
	</script>
	  <style>
	li.white
	{
	list-style-image:url('../img/marker_white_legend.png');
	}

	li.red
	{
	  list-style-image:url('../img/marker_red_legend.png');
	}

	li.yellow
	{
	  list-style-image:url('../img/marker_yellow_legend.png');
	}

	li.green
	{
	  list-style-image:url('../img/marker_green_legend.png');
	}
	  </style>
	<link rel="stylesheet" type="text/css" href='formulaire.css'></link>
</head>
<body>
	<div style="width:50%; height:100%; float:left;" id="map"></div>
	<div style="width:50%; height:100%; float:left; overflow:auto;">
		<div id="div_details_point_noir" style="margin-left:15px;display:none;"> 
			<h1>D&eacute;tails du point noir <span id="span_id"></span></h1>	
			<strong>Lieu</strong> : <span id="f_lieu"></span><br />
			<strong>Description</strong> : <span id="f_description"></span><br />
			<strong>Photo</strong>
            (<a id="link_add_photo" href="#">ajouter une photo</a>) :
			<span id="span_photo"></span>
      	</div>
		<br />
	    <br />
		<div style="margin-left:15px;">
			<a href="signaler.php" target="_blank"><h1>Signalez nous un autre point noir!</h1></a>
	     	<h1>Informations</h1>
			Un point noir est quelque chose qui pose probl&egrave;me &agrave; la pratique id&egrave;ale du v&eacute;lo. Cet outil sert &agrave; signaler les points noirs v&eacute;lo.
			L'outil est propos&eacute; par la locale de Mons du <a href="http://www.gracq.be/" target="_blank">GRACQ</a>. Le <a href="http://www.gracq.be/" target="_blank">GRACQ</a> est une association sans but lucratif et sans appartenance politique qui a pour objectif principal la promotion du v&eacute;lo comme moyen de d&eacute;placement.
			<br />
			<br />
			Pour lire les details des points noirs, veuillez cliquer dessus.
	      	<br/>
	      	</br>
	      	<b>L&eacute;gende : </b><br/>
	      	<ul>
				<li class="white">Point noir sugg&eacute;r&eacute; par un visiteur.</li>
				<li class="red">Point noir reconnu par le GRACQ et non r&eacute;solu.</li>
				<li class="yellow">Point noir reconnu par le GRACQ et en cours de r&eacute;solution.</li>
				<li class="green">Point noir reconnu par le GRACQ et r&eacute;solu.</li>
	      	</ul>
	      	<br />
	      	<b>Plus l'outil sera connu, plus de points noirs seront	      signal&eacute;s et plus nous pourrons am&eacute;liorer la pratique du v&eacute;lo dans le grand Mons. Donc, n'h&eacute;sitez surtout pas &agrave; faire conna&icirc;tre l'outil. Merci.</b>
			<br />
			<br />
	      	Si vous &ecirc;tes int&eacute;ress&eacute; par l'outil, n'h&eacute;sitez pas &agrave; <a href="mailto:marc.ducobu@gmail.com">nous contacter</a>.
	    </div>
    </div>
</body>
</html>
