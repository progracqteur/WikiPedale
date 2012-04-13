<?php 

$db_host = "localhost";
$db_login = "root";
$db_pass = "root";
$db_name = "orangead";
$db_table_pn = "gracq_points_noirs";

$taille_miniature = 210000; // la taille des miniatures associee aux points noirs
$chemin_images = 'photo/'; // le repertoire ou se trouve les photos et les miniatures

/* String functions */

function secure($string)
	{
	  $string = mysql_real_escape_string($string);
	  $string = addcslashes($string, '%_');
	  return $string;
	}

function unescape($string)
{
  $search = array("\\x00", "\\n", "\\r", "\\\x1a");
  $replace = array("\x00","\n", "\r", "\x1a");

  $retString = str_replace($search, $replace, $string);
  $search = array("\'", '\\'.'"');

  $replace = array(  "'", '"',);
  $retString = str_replace($search, $replace, $retString);
  
  $search = array("\\\\");
  $replace = array( "\\");

  $retString = str_replace($search, $replace, $retString);
  
  return $retString;
}

/* PHOTO FUNCTIONS */

function photo_jpg_next_id() // trouve quel est le dernier id utilise + 1 pour les photos de ce point noir
{
  global $chemin_images;

  $id = 0;
  while(file_exists($chemin_images.$_GET['id'].'_'.$id.'.jpg'))
    $id ++;
  return $id;
}


function creation_miniature_jpg($emplacement_source, $emplacement_miniature) // doit etre en jpg de base
{
  global $taille_miniature;
  $emplacement_size = filesize($emplacement_source);
  
  if ($emplacement_size <= $taille_miniature)
    $reduction = 1;
  else
    $reduction = $taille_miniature / filesize($emplacement_source) ;

  $source = imagecreatefromjpeg($emplacement_source);
  $largeur_source = imagesx($source);
  $hauteur_source = imagesy($source);
  $largeur_destination = floor(imagesx($source) * $reduction);
  $hauteur_destination = floor(imagesy($source) * $reduction);
						
  $destination = imagecreatetruecolor($largeur_destination, $hauteur_destination);
  imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);

  return imagejpeg($destination, $emplacement_miniature);
}

function conversion_png_to_jpg($emplacement_source, $emplacement_destination)
{
  ini_set('memory_limit', '-1');
  $source = imagecreatefrompng($emplacement_source);

  return imagejpeg($source, $emplacement_destination);
}
?>