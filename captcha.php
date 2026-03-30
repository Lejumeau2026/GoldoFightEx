<?php
session_start();

if (!isset($_SESSION['IdAccount'])) return;


// $_SESSION['Captcha'] = mt_rand(10000,99999);
// $img = imagecreate(80,34);

$_SESSION['Captcha'] = mt_rand(10000,99999);
$img = imagecreate(100,34);

$font = 'Fonts/LucidaSansRegular.ttf';
$bg = imagecolorallocate($img,0,0,0);
$textcolor = imagecolorallocate($img, 255,255,255);

imagettftext($img,24,0,0,30,$textcolor,$font,$_SESSION['Captcha']);

$captchafilename = "./Captcha/captcha".$_SESSION['IdAccount'].".jpg";
// Save the image as 'captcha.jpg'
imagejpeg($img, $captchafilename);
imagedestroy($img);

// echo "Bonjour à tous bande de cons ".$_SESSION['Captcha']." !!!!".
// "<br>".
// "<img src=\"captcha.jpg?".Date('U')."\" />";

echo "<img src=\"$captchafilename?".Date('U')."\" />";

?>

