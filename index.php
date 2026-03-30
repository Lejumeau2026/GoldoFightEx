<?php
require_once('./Classes/DBGoldoFight.php');
session_start();

if (isset($_GET['disconnect'])) {
	session_unset();
}

$DB = new GoldoFightDB();
$UserAccount = null;
$UserScores = null;

$Activities = Activity::getAllActivities($DB, false);
$NbActivityPending = 0;
$NbActivityActive = 0;
$NbActivityClosed = 0;
$NbActivity = 0;
if ($Activities != null)
{
	foreach ($Activities as $act) {
		if ($act->IdActivity != 1)
		{
			switch($act->Statu)
			{
				case ActStatus::$Pending:$NbActivityPending ++; break;
				case ActStatus::$Active:$NbActivityActive ++; break;
				case ActStatus::$Closed:$NbActivityClosed ++; break;
			}
		}
	}
}

$NbActivity = $NbActivityActive + $NbActivityClosed + $NbActivityPending;

/*
$ActivitiesBestScore = array();
foreach ($Activities as $act) {
	$scliste = Scores::getBestActivityScore($DB, $act->IdActivity);
	if ($scliste != null) {
		foreach ($scliste as $sc) {
			array_push($ActivitiesBestScore, $sc);
		}
	}
}
*/

// Read all best score
$ActivitiesBestScore = Scores::getAllBestActivityScore($DB);

if (isset($_SESSION['IdAccount'])) {
	$IdAccount = $_SESSION['IdAccount'];
	$UserAccount = Account::getAccount($DB, $IdAccount);
	$UserScores = Scores::GetUserBetterScoreList($DB, $IdAccount);
	$analytic = Scores::GetUserAnalyticScoreList($DB, $IdAccount);
}

$DB->close();

$error = $_GET['error'] ?? '';
$info = $_GET['info'] ?? '';


?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<title>GoldoFight accueil</title>
	<script src=" https://code.jquery.com/jquery-3.6.0.min.js "></script>
	<link rel="stylesheet" href="./Css/GoldoFightAccueil.css">
	<link rel="stylesheet" href="./Css/GoldoFightGame.css">

	<script>
		$(document).ready(function() {

			$('#IdCreateAccount').click(function() {
				$("#IdDivCreate").toggle(500);
				$("#IdDivConnect").hide(500);
				$("#IdDivLost").hide(500);
			});

			$('#IdConnectPlay').click(function() {
				$("#IdDivCreate").hide(500);
				$("#IdDivConnect").toggle(500);
				$("#IdDivLost").hide(500);

			});

			$('#IdLostPassword').click(function() {
				$("#IdDivCreate").hide(500);
				$("#IdDivConnect").hide(500);
				$("#IdDivLost").toggle(500);
			});


			$('#IdPlay').click(function() {
				window.location.href = 'GoldoFight.php';
			});

			$('#IdDeconnect').click(function() {
				window.location.href = 'index.php?disconnect';
			});

			$('#idBtnInscrire').click(function() {

				if (!CheckUserInfoCreateAccount()) return false;

				$("#CreateAccountForm").submit();
			});

			function CheckUserInfoCreateAccount() {
				const errorErray = [];

				if (!CheckRegex("Nom")) errorErray.push("le nom de famille");
				if (!CheckRegex("Prenom")) errorErray.push("le prénom");
				if (!CheckRegex("Email")) errorErray.push("l'adresse email");
				if (!CheckRegex("Pseudo")) errorErray.push("le pseudo");
				if (!CheckRegex("Password")) errorErray.push("le format du mot de passe");
				if (!CheckRegex("PasswordCheck")) errorErray.push("La format de la vérification");
				if (!CheckPassword()) errorErray.push("Le mot de passe et sa vérification n'ont pas la même valeur");

				if (errorErray.length > 0) $('.GuiError').html("Vérifiez : <br><ul>" + errorErray.join(" <br> ") + "</ul>");
				else $('.GuiError').html("");

				return (errorErray.length == 0);
			}

			function CheckPassword() {
				return ($('#Password').val() == $('#PasswordCheck').val());
			}

			function GetRegExPattern(name) {
				var pattern = '';
				switch (name) {
					case 'Nom':
					case 'Prenom':
						pattern = /^[A-Z][A-Za-z\é\è\ê\-]{1,49}$/;
						break;
					case 'Pseudo':
						pattern = /^[a-zA-Z0-9]{1,20}$/;
						break;

					case 'Email':
					case 'LoginNewPassword':
						pattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,4}$/;
						break;
					case 'Password':
					case 'PasswordCheck':
						// pattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,20}$/;
						pattern = /^(?!.*\s)(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;
						break;

				}
				return pattern;
			}

			function CheckRegex(FieldName) {
				var pattern = GetRegExPattern(FieldName);
				if (pattern == '') return true;

				const reg = new RegExp(pattern);
				var idField = '#' + FieldName;
				var res = reg.exec($(idField).val())
				$(idField).css("background-color", (res == null) ? "#FFAAAA" : "#FFFFFF");
				return (res != null);
			}

			$(".GuiCreateAc").on("input", function() {
				if (!CheckRegex($(this).attr("name"))) {
					$('.GuiError').html($(this).attr("name") + ' : Format non valide');
				} else {
					$('.GuiError').html("");
				}
			});

			$(".GuiCreateAc").on("focus", function() {
				$(this).css('box-shadow', '0px 0px 20px #F00');
			});

			$(".GuiCreateAc").on("focusout", function() {
				$(this).css('box-shadow', 'none');
			});

			$('#idBtnConnect').click(function() {

				if (!CheckUserInfoCnx()) return false;

				$("#ConnexionForm").submit();
			});

			function CheckUserInfoCnx() {
				const errorErray = [];

				if (IsUserInfoEmpty("Login")) errorErray.push("Votre login");
				if (IsUserInfoEmpty("PasswordCnx")) errorErray.push("Le mot de passe");

				if (errorErray.length > 0) $('.GuiErrorCnx').html("Vérifiez : <br><ul>" + errorErray.join(" <br> ") + "</ul>");
				else $('.GuiErrorCnx').html("");

				return (errorErray.length == 0);
			}



			$('#idBtnNewPassword').click(function() {
				
				if (!CheckRegex("LoginNewPassword"))
				{
					$('.GuiErrorLost').html("Le format de votre email");
					return false;
				}

				$("#LostPasswordForm").submit();
			});





			function IsUserInfoEmpty(FieldName) {
				var idField = '#' + FieldName;
				var isEmpty = ($(idField).val() == "");
				$(idField).css("background-color", (isEmpty) ? "#FFAAAA" : "#FFFFFF");
				return isEmpty;
			}

			$(".GuiCnx").on("input", function() {
				if (IsUserInfoEmpty($(this).attr("name"))) {
					$('.GuiErrorCnx').html($(this).attr("name") + ' : non valide');
				} else {
					$('.GuiErrorCnx').html("");
				}
			});

			$(".GuiCnx").on("focus", function() {
				$(this).css('box-shadow', '0px 0px 20px #F00');
			});

			$(".GuiCnx").on("focusout", function() {
				$(this).css('box-shadow', 'none');
			});


			$('#IdTestAjax').click(function() {
				$.post("ZZ_ProcessTestAjax.php", {
						AjaxValue: 50
					},
					function(data) {
						alert(data.a + " " + data.b + " " + data.c);
					});


				// $.post( "ajax/test.html", function( data ) {
				// $( ".result" ).html( data );
				// });

			});


		});
	</script>
</head>
<body>




<div class="GuiMenuDiv">
	<h1 class="GoldoFightTitle">Goldo Fight</h1>
	<div style="text-align: center;width:450px;margin:0px auto 0px auto;">
		<?php
		if ($UserAccount != null) { ?>
			<h3 style="text-align: center">Bienvenue <?php echo $UserAccount->Nom . " " . $UserAccount->Prenom; ?></h3>
			<button id="IdPlay" class="ButtonMenu">Charger le jeu</button>
			<button id="IdDeconnect" class="ButtonMenu">Me déconnecter</button>

		<?php } else { ?>
			<button id="IdCreateAccount" class="ButtonMenu">Créer un compte</button>
			<div id="IdDivCreate">
				<form id="CreateAccountForm" action="ProcessCreateAccount.php" method="post">

					<div class="GuiUserInfo">
						<h3 style="color:white;text-align: center;">Création de compte</h3>
						<p style="margin:0px;color:#AAAAFF">Nom & Prénom : Une majuscule, longueur [1,50]</p>
						<div>
							<label class="GuiLabel" for="Nom">Votre nom</label>
							<input class="GuiInput GuiCreateAc" type="text" id="Nom" name="Nom" placeholder="" required>
						</div>
						<div>
							<label class="GuiLabel" for="Prenom">Votre prénom</label>
							<input class="GuiInput GuiCreateAc" type="text" id="Prenom" name="Prenom" placeholder="" required>
						</div>
						<div>
							<label class="GuiLabel" for="Email">Votre e-mail</label>
							<input class="GuiInput GuiCreateAc" type="text" id="Email" name="Email" placeholder="" required>
						</div>
						<p style="margin:5px 0px 0px 0px;color:#AAAAFF">Pseudo : longueur [1,20], pas de caractère spécial</p>
						<div>
							<label class="GuiLabel" for="Pseudo">Votre pseudo</label>
							<input class="GuiInput GuiCreateAc" type="text" id="Pseudo" name="Pseudo" placeholder="" required>
						</div>
						<hr>

						<p style="margin:0px;color:#AAAAFF">Une majuscule, un chiffre, longueur [8,20]</p>
						<div>
							<label class="GuiLabel" for="Password">Mot de passe</label>
							<input class="GuiInput GuiCreateAc" type="password" id="Password" name="Password" placeholder="" required>
						</div>
						<div>
							<label class="GuiLabel" for="PasswordCheck">Vérification</label>
							<input class="GuiInput GuiCreateAc" type="password" id="PasswordCheck" name="PasswordCheck" placeholder="" required>
						</div>

						<hr>
						<div style="margin-top:20px; display: flex; align-items: center;  justify-content: center;">
							<button id="idBtnInscrire" class="BtnAction">Créer le compte</button>
						</div>
						<div class="GuiError"></div>
						<div class="msge"></div><br>
						<div class="resultat"></div>
					</div>
				</form>
			</div>


			<button id="IdConnectPlay" class="ButtonMenu">Se connecter</button>
			<div id="IdDivConnect">
				<form id="ConnexionForm" action="ProcessConnexion.php" method="post">

					<div class="GuiUserInfo">
						<h3 style="color:white;text-align: center;">Connexion</h3>
						<hr>
						<div style="text-align:center;color:#FFFFFF"><INPUT id="IdCheckPlay" type="checkbox" name="IdCheckPlay" checked>Se connecter et jouer</div>
						<hr>
						<div>
							<label class="GuiLabel" for="Login">Email ou Pseudo</label>
							<input class="GuiInput GuiCnx" type="text" id="Login" name="Login" placeholder="" required>
						</div>
						<div>
							<label class="GuiLabel" for="PasswordCnx">Mot de passe</label>
							<input class="GuiInput GuiCnx" type="password" id="PasswordCnx" name="PasswordCnx" placeholder="" required>
						</div>

						<hr>
						<div style="margin-top:20px; display: flex; align-items: center;  justify-content: center;">
							<button id="idBtnConnect" class="BtnAction">Connecter</button>
						</div>
						<div class="GuiErrorCnx"></div>
					</div>
				</form>
			</div>
			<button id="IdLostPassword" class="ButtonMenu">Mot de passe perdu</button>
			<div id="IdDivLost">

					<div class="GuiUserInfo">
						<form id="LostPasswordForm" action="ProcessLostPassword.php" method="post">
							<h3 style="color:white;text-align: center;">Nouveau mot de passe</h3>

							<div>
								<label class="GuiLabel" for="Login">Email</label>
								<input class="GuiInput GuiCnx" type="text" id="LoginNewPassword" name="LoginNewPassword" placeholder="" required>
							</div>
						</form>
						<hr>
						<div style="margin-top:20px; display: flex; align-items: center;  justify-content: center;">
							<button id="idBtnNewPassword" class="BtnAction">Réinitialiser</button>
						</div>
						<div class="GuiErrorLost"></div>
					</div>

			</div>


			<hr style="width:90%">
			<button id="IdPlay" class="ButtonMenu">Jouer sans connection</button>
			<!-- <button id="IdTestAjax" class="ButtonMenu">TestAjax</button> -->

		<?php } ?>

		
		<div class="GuiMsgDiv">
		<?php
		if ($error != "") {
			echo "<p style=\"font-size:24px;color:#FAA\"><b>$error</b></p>";
		}
		if ($info != "") {
			echo "<p style=\"font-size:24px;color:#AFA\"><b>$info</b></p>";
		}
		?>
		</div>

		<img src="./Images/ActarusFin.png" style="text-align:center;height:500px;" />
	</div>




</div>

<div class="GuiInfoDiv">
	<div style="text-align:center;">
		<img class="GoldoBuste" src="./Images/GoldoBuste.png" />
	</div>
	<?php if ($UserScores != null) { ?>
			<div class="DivBarre" onclick="$('#idScorePanel').toggle(250)">
				<h2>
					<img class="HeadGoldo3DImg" src="./Images/HeadGoldo3D.png" />
					Vos scores
				</h2>
			</div>
			<div id="idScorePanel" class="DivScore">
				<table class="TableScore">
				<caption>Vos meilleurs scores</caption>
				<tr>
					<th>Activit&#233;</th>
					<th>Score</th>
					<th>Date</th>
					<th>Tour</th>
					<th>Nb Click</th>
					<th>Durée</th>
				</tr>
				<?php
					foreach ($UserScores as $sco) {
						echo "<tr><td>" . $sco->ActivityName . "</td>" .
							"<td>" . $sco->Score . "</td>" .
							"<td>" . $sco->CreateDate . "</td>" .
							"<td>" . $sco->Tour . "</td>" .
							"<td>" . $sco->Nbclick . "</td>" .
							"<td>" . Scores::FormatGameTime($sco->Minutes) . "</td></tr>";
					}
				?>
				</table>

				<table class="TableScore" style="margin-top:10px">
				<caption>Statistiques</caption>
				<tr>
					<th>Activit&#233;</th>
					<th>Nb Parties</th>
					<th>Min Score</th>
					<th>Max Score</th>
					<th>Durée Min</th>
					<th>Durée Max</th>
				</tr>
				<?php
					foreach ($analytic as $sco) {
						echo "<tr><td>" . $sco->ActivityName . "</td>" .
							"<td>" . $sco->NbScore . "</td>" .
							"<td>" . $sco->MinScore . "</td>" .
							"<td>" . $sco->MaxScore . "</td>" .
							"<td>" . Scores::FormatGameTime($sco->MinDuration) . "</td>" .
							"<td>" . Scores::FormatGameTime($sco->MaxDuration) . "</td></tr>";
					}
				?>
				</table>
			</div>
	<?php } ?>

		<div class="DivBarre" onclick="$('#idBestScorePanel').toggle(250)">
			<h2>
				<img class="HeadGoldo3DImg" src="./Images/HeadGoldo3D.png" />
				Les meilleurs scores
			</h2>
		</div>
		<div id="idBestScorePanel" class="DivScore">
			<table class="TableScore">
			<caption>Meilleurs scores aux activit&#233;s</caption>
				<tr>
					<th>Activit&#233;</th>
					<th>Nom</th>
					<th>Prenom</th>
					<th>Pseudo</th>
					<th>Score</th>
				</tr>
			<?php
				foreach ($ActivitiesBestScore as $sco) {
					echo "<tr><td>" . $sco->ActivityName . "</td>" .
						"<td>" . $sco->Nom . "</td>" .
						"<td>" . $sco->Prenom . "</td>" .
						"<td>" . $sco->Pseudo . "</td>" .
						"<td>" . $sco->Score . "</td></tr>";
				}
			?>
			</table>
		</div>

		<div class="DivBarre" onclick="$('#idInfoDiv').toggle(250)">
			<h2>
				<img class="HeadGoldo3DImg" src="./Images/HeadGoldo3D.png" />
				Informations
			</h2>
		</div>
		<div id="idInfoDiv" class="DivScore" style="padding:5px;font-size:20px;color:#DDD">
			<h3 style="text-align:center">Bonjour,</h3>
			
						
			<p>Bienvenu sur le site du jeu Goldo Fight.</p>
			<p>
			Ce jeu est un Shoot'em up sur le th&egrave;me de Goldorak.

			<br><br><b>A propos du jeu :</b>
				<li>Le jeu est gratuit</li>
				<li>C'est un jeu web sur Mac ou Pc</li>
				<li>Se joue au clavier et &agrave; la souris.</li>
				<li style="color:#F55"><b>Ne se joue pas sur Smart Phone</b></li>
				<li>Cr&eacute;ez un compte pour enregistrer vos scores</li>
			</p>
			<hr style="width:50%;margin:50px auto 50px auto">
			<h3 style="text-align:center">A propos de la license Goldorak</h3>
			
			<b>A propos du nom du jeu: </b>
			<br>Le nom ne reprend pas compl&egrave;tement celui de Goldorak juste au cas o&#249; ... h&#233; h&#233; 😉
			
			<br><br><b><span style="color:#F55">En terme de deniers,</span></b>
			pour des raisons &eacute;videntes, <span style="color:#F55"><b>je ne touche absolument rien</b></span>
			et ce jeu est totalement gratuit.
			<ul style="margin-left:20px">
			<li>Il faut quand m&ecirc;me savoir que le jeu me revient &agrave; une mensualit&eacute; de <b>4 Euros</b> par mois chez un h&eacute;bergeur.</li>
			<br>4 euros par mois ce n&#39;est pas la d&#232;che mais il ne faut pas oublier que ce jeu a &eacute;t&eacute; d&eacute;veloppé gentiment pour la communaut&eacute;.
			<br>Initialement l'h&eacute;bergement ne me coutait absolument rien car le service de page perso chez Orange &eacute;tait gratuit,
			mais suite &agrave; leur d&eacute;cision de fermer d&eacute;finitivement le service, j'ai d&ucirc; le transf&eacute;rer ailleur.			
			</ul>

			
		</div>		


		<?php if ($Activities != null && count($Activities) > 1) { ?>
		<div class="DivBarre" onclick="$('#idActivityDiv').toggle(250)">
			<h2>
				<img class="HeadGoldo3DImg" src="./Images/HeadGoldo3D.png" />
				Liste des activit&eacute;s
			</h2>
		</div>
		<div id="idActivityDiv" style="text-align:center" class="DivScore">
		<?php
			if ($NbActivityPending > 0)
			{
				echo "<h2>Liste des futures activités.</h2>";
				foreach ($Activities as $act) {
					if ($act->IdActivity > 1 && $act->Statu == ActStatus::$Pending)
					{
					
						$StartDate = new DateTime ($act->StartDate);
						$EndDate = new DateTime ($act->EndDate);
						$fileDesc = "./Activities/$act->Name.php";

						?>

						<div class="ActivityBarreDiv"
						onclick="$('#<?php echo $act->Name; ?>').toggle(250)">
							<h2><?php echo $act->Nom." - du ".$StartDate->format("d/m/Y")." au ".$EndDate->format("d/m/Y"); ?></h2>
						</div>
						
						<?php
							echo "<div class=\"ActivityInfoDiv\" id=\"$act->Name\" style=\"display: none;\">";
							if (file_exists($fileDesc)) include ($fileDesc);
							else echo "<h3>".$act->Info."</h3>";
							echo "</div>";
					}
				}
			}

			if ($NbActivityActive > 0)
			{
				if ($NbActivityPending > 0)
					echo "<hr style='margin:30px auto 10px auto;width:50%'>";

				echo "<h2>Liste des activités encours.</h2>";
				foreach ($Activities as $act) {
					if ($act->IdActivity > 1 && $act->Statu == ActStatus::$Active)
					{
					
						$StartDate = new DateTime ($act->StartDate);
						$EndDate = new DateTime ($act->EndDate);
						$fileDesc = "./Activities/$act->Name.php";

						?>

						<div class="ActivityBarreDiv"
						onclick="$('#<?php echo $act->Name; ?>').toggle(250)">
							<h2><?php echo $act->Nom." - du ".$StartDate->format("d/m/Y")." au ".$EndDate->format("d/m/Y"); ?></h2>
						</div>
						
						<?php
						if (file_exists($fileDesc)) {
							echo "<div class=\"ActivityInfoDiv\" id=\"$act->Name\" style=\"display: none;\">";
							include ($fileDesc);
							echo "</div>";
						}
					}
				}
			}


			if ($NbActivityClosed > 0)
			{
				if ($NbActivityPending > 0 || $NbActivityActive > 0)
					echo "<hr style='margin:30px auto 10px auto;width:50%'>";
				echo "<h2>Liste des activités fermées.</h2>";
				foreach ($Activities as $act) {
					if ($act->IdActivity > 1 && $act->Statu == ActStatus::$Closed)
					{
						$StartDate = new DateTime ($act->StartDate);
						$EndDate = new DateTime ($act->EndDate);
						$fileDesc = "./Activities/$act->Name.php";

						?>

						<div class="ActivityBarreDiv"
						onclick="$('#<?php echo $act->Name; ?>').toggle(250)">
							<h2><?php echo $act->Nom." - du ".$StartDate->format("d/m/Y")." au ".$EndDate->format("d/m/Y"); ?></h2>
						</div>
						
						<?php
						if (file_exists($fileDesc)) {
							echo "<div class=\"ActivityInfoDiv\" id=\"$act->Name\" style=\"display: none;\">";
							include ($fileDesc);
							echo "</div>";
						}
					}
				}
			}


		?>
		</div>		
		<?php } ?>



		
		<div class="DivBarre" onclick="$('#idScreenshotDiv').toggle(250)">
			<h2>
				<img class="HeadGoldo3DImg" src="./Images/HeadGoldo3D.png" />
				Quelques images du jeu
			</h2>
		</div>
		<div id="idScreenshotDiv" style="text-align:center" class="DivScore">
			<img class="ScreenshotImg" src="./Screenshot/ScreenShot01.jpg" />
			<img class="ScreenshotImg" src="./Screenshot/ScreenShot02.jpg" />
			<img class="ScreenshotImg" src="./Screenshot/ScreenShot03.jpg" />
		</div>		
		
</div>

</body>

</html>