<?php
$image = imagecreate(122, 200);

$purple = imagecolorallocate($image, 160, 32, 240);
$green = imagecolorallocate($image, 102, 205, 0x00);
$red      = imagecolorallocate($image, 0xFF, 0x00, 0x00);
$orange = imagecolorallocate($image, 255, 160, 0);
$white    = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($image, 0, 0, 0);
$dimgray = imagecolorallocate($image, 105, 105, 105);
$brown = imagecolorallocate($image,238,59,59);
$select = imagecolorallocate($image,255,52,179);

$colors = array('g' =>  $green, 'r' =>  $red, 'o' => $orange, 'w' => $white, 'b' => $black, 'd' => $dimgray, 'b' => $brown, 's' => $select);

$bg_color = $black;
if(isset($_GET["bg"])) {
	$bg_color = $colors[$_GET["bg"]];
}

$points = array(10, 90, 60, 200, 110, 90);
	imagefilledellipse ($image, 60, 60, 120, 120, $bg_color);	
	ImagefilledPolygon ($image, $points, 3, $bg_color);

if(isset($_GET["c3"])) {
	imagefilledarc($image, 60, 60, 100, 100, 0, 120, $colors[$_GET["c3"]], IMG_ARC_PIE);
	imagefilledarc($image, 60, 60, 100, 100, 120, 240 , $colors[$_GET["c1"]], IMG_ARC_PIE);
	imagefilledarc($image, 60, 60, 100, 100, 240, 360 , $colors[$_GET["c2"]], IMG_ARC_PIE);

	ImageLine ($image, 60, 60, 30, 10, $bg_color);
	ImageLine ($image, 60, 60, 120, 60, $bg_color);
	ImageLine ($image, 60, 60, 30, 110, $bg_color);
}
elseif(isset($_GET["c2"])) {
	imagefilledarc($image, 60, 60, 100, 100, 90, 270 , $colors[$_GET["c1"]], IMG_ARC_PIE);
	imagefilledarc($image, 60, 60, 100, 100, 270, 90 , $colors[$_GET["c2"]], IMG_ARC_PIE);

	ImageLine ($image, 60, 5, 60, 115, $bg_color);
}
else {
	imagefilledarc($image, 60, 60, 100, 100, 0, 360 , $colors[$_GET["c1"]], IMG_ARC_PIE);
}

imagecolortransparent($image, $purple);


// flush image
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>