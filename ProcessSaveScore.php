<?php
require_once('./Classes/DBGoldoFight.php');
session_start();

if (!isset($_SESSION['IdAccount']) || !isset($_POST['Score'])) return;

if (!isset($_SESSION['IdAccount']) || !isset($_POST['Score']) || !isset($_POST['Captcha'])) return;

// Delete the capcha file generated before if exists
$captchafilename = "./Captcha/captcha".$_SESSION['IdAccount'].".jpg";
if (file_exists($captchafilename) == true)
{
	unlink($captchafilename);    
}

if ($_SESSION['Captcha'] == $_POST['Captcha'])
{
	$IdAccount = $_SESSION['IdAccount'];
	$IdActivity = $_POST['Activity'];
	$Score = $_POST['Score'];
	$Nbclick = $_POST['Nbclick'];
	$Tour = $_POST['Tour'];
	$Minutes = $_POST['Minutes'];

	$DB = new GoldoFightDB();
	Scores::saveScore($DB, $IdAccount, $IdActivity, $Score, $Nbclick, $Tour, $Minutes);
	$DB->close();
	if (file_exists($captchafilename) == true)
	{
		unlink($captchafilename);    
	}
} else echo "-1";
