<?php
//header('Content-type: text/json');
//header('Content-type: application/json');

require "vars_and_fcts.php";

if(isset($_GET['id']) && is_numeric($_GET['id']))
  {
    $all = array();
    $next_id = photo_jpg_next_id();

    for ($i = 0; $i < $next_id; $i ++)
      {
	$row = array("url_image" => ($chemin_images.$_GET['id'].'_'.($i).'.jpg'),
		     "url_miniature" => ($chemin_images.$_GET['id'].'_'.($i).'M.jpg'));
	$all[] = $row;
      }

    print json_encode($all);
  }
?>
