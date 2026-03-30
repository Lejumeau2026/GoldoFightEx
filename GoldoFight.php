<?php
require_once('./Classes/DBGoldoFight.php');
session_start();

$account = null;
$activities = null;
$DB = new GoldoFightDB();
if (isset($_SESSION['IdAccount'])) {
	$account = Account::getAccount($DB, $_SESSION['IdAccount']);
	$activities = Activity::getAllActivities($DB, false);
}
$DB->close();


?>
<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script> -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


	<script type="text/javascript" src="./Js/ChainedList.js"></script>
	<script type="text/javascript" src="./Js/QuadScape.js"></script>
	<script type="text/javascript" src="./Js/Collision.js"></script>
	<script type="text/javascript" src="./Js/QuadTree.js"></script>
	<script type="text/javascript" src="./Js/LoadManager.js"></script>
	<script type="text/javascript" src="./Js/GoldoEntities.js"></script>
	<script type="text/javascript" src="./Js/Other.js"></script>
	<script type="text/javascript" src="./Js/GoldoShoot.js"></script>
	<link rel="stylesheet" href="./Css/GoldoFightGame.css">

</head>

<body id="body">

	<canvas id="Canvas" width="1024" height="1024">
		<!-- <canvas id="Canvas"> -->
		Your browser does not support the HTML5 canvas tag.
	</canvas>


	<div id="idMsgboxLoading" class="DivModal">
		<div class="BoxLoading Zoom">
		</div>
	</div>

	<div id="idMsgboxStartGame" class="DivModal">
		<div id="IdStartGame" class="BoxStartGame Zoom" style="text-align:center">
			<div>
					<?php
					if ($activities != null)
					{
						echo ("<h3 style=\"color:#CCC\">Sélectionnez une activité</h3>");
						echo ("<select class=\"ActivityDropDown\" name=\"PlayActivity\" id=\"PlayActivity\">");
							foreach ($activities as $activity) {
								if ($activity->Statu == ActStatus::$Active)
								{
									echo "<option value=\"$activity->IdActivity\">$activity->Nom</option>";
								}
							}
						echo ("</select>");
					}
					?>
				<button id="StartBtn" class="OptionButton" style="font-size:24px">Jouer</button>
				<div style="text-align:center;color:#FFFFFF">
				<INPUT id="idNoSoundTrack" type="checkbox" name="NoSoundTrack" checked>&nbsp;Jouer avec la musique
				<br>
				<INPUT id="idPlayIntro" type="checkbox" name="PlayIntro" checked>&nbsp;Jouer avec l'intro
				</div>
				<hr>
				<?php if ($account != null) { ?>
					<div><button id="idScoresListeBtn" class="OptionButton">Vos scores</button></div>
				<?php } ?>
				<div><button id="idEndGameScoreBtn" class="OptionButton">Le dernier score</button></div>
			</div>
			
			<div><button id="StartTraining" class="OptionButton">Entrainement</button></div>
			<hr>
			<div><button id="idEntityListBtn" class="OptionButton">Liste des entit&eacute;s</button></div>
			<div><button id="idGameInfoBtn" class="OptionButton">Informations</button></div>
			<div><button id="idCreditBtn" class="OptionButton">Cr&eacute;dits</button></div>
			<div style="margin-left:auto;margin-right:auto;width:200px">

			</div>
			<hr>
			<div><button id="idHomePage" class="OptionButton">Page d'accueil</button></div>
			<?php if ($account != null) { ?>
			<div><button id="IdDeconnect" class="OptionButton">Me déconnecter</button></div>
			<?php } ?>
				<hr>
				<div class="DisplaySetting">
					<a href="https://www.youtube.com/watch?v=Dw29khzB3eY" target="_blank" style="color:#FF6666">
						<img src="Images/LogoYoutube.png" class="DisplaySettingPuceInfo" /><b>A propos des paramètres d'affichage</b>
					</a>
				</div>
			
		</div>
	</div>

	<div id="IdEndGameScore" class="DivModal noSelect">
		<div class="BoxGameScore Zoom">
			<button id="idEndGameScoreExitBtn">Retour</button>
			<div>Votre Score</div>
			<div id="iTextScore"></div>
			<div id="iTextTour"></div>
			<div id="iTextClick"></div>
			<div id="iTextTemps"></div>
			<img class="ImgDukeStandUp" src="Images/ActarusDebout.png">
			<img class="ImgGoldoStandUp" src="Images/GoldoDebout.png">
		</div>
		<input id="hidden_Start_Intro" type="hidden" value="0">
	</div>

	<?php

	// Accéder à la score liste seulement si on est connecté
	if ($account != null) {
		include 'GoldoFightGameScoreList.php';
	}

	// Code html game info
	include 'GoldoFightGameInfo.html';

	// Code html game info
	include 'GoldoFightGameCredit.html';

	// Code html sur la liste des entités
	include 'GoldoFightGameEntityList.html';

	// Captcha for score recording
	include 'Captcha.html';

	?>



</body>

</html>