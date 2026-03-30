<?php
require_once('./Classes/DBGoldoFight.php');
session_start();

$errMsg = null;
if (isset($_GET["error"])) $errMsg = $_GET["error"];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<link rel="stylesheet" href="./Css/GoldoFightAccueil.css">
	
	<style>
		body {
			background-color: #111111;
			margin: 0 !important;
			padding: 0 !important;
			overflow: hidden;
			background-image: url("./Images/CoverError.jpg");
			background-size: cover;
			background-repeat: no-repeat;
			font-family: Comic Sans MS;
		}
		
		hr {
		  border-top: 1px solid rgba(255, 255, 255, .6);
		  border-bottom: 1px solid rgba(100, 100, 100, .6);
		}
		
		.ErrorDiv {
			position: absolute;
			width: 90%;
			height: 500px;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);  
			background-color: rgba(0, 0, 0, 0.3);
			box-shadow: 5px 5px 15px #000;
			border-radius: 5px;
			color: #FAA;
			overflow: hidden;
			text-align:center;
			
		}		
		
		.ReturnBtn {
			padding: 10px;
			border: 1px outset buttonborder;
			border-radius: 5px;
			color: buttontext;
			background-color: buttonface;
			text-decoration: none;
		}		
	</style>

</head>
<body>

<div class="ErrorDiv">

<h1 style="vertical-align:middle;" >Une error est survenue</h1>
<hr>
<h2 style="vertical-align:middle;" ><?php echo ($errMsg);?></h2>

<hr>

<form id="ChangePassword" action="index.php" method="post"><button class="ReturnBtn" type="submit">Retour à la page d'accueil</button></form>

</div>



</body>

</html>













