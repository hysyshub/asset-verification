<?php 

session_start();

$text = rand(100000,999990);

$_SESSION["vercode"] = $text; 

$height = 25; 

$width = 70; 

 

$image_p = imagecreate($width, $height); 

$black = imagecolorallocate($image_p, 69, 77, 243); 

$white = imagecolorallocate($image_p, 255, 255, 255); 

$font_size = 16; 

 

imagestring($image_p, $font_size, 5, 5, $text, $white); 

imagejpeg($image_p, null, 100); 

?>
