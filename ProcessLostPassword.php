<?php
require_once('./Classes/DBGoldoFight.php');
require_once('./Classes/GFError.php');
require_once('./Classes/MailUtil.php');
session_start();


// Il faut toujours un login de renseigné
if (!isset($_POST['LoginNewPassword']))
{
	GFError::FatalError("Il manque l'adresse mail du correspondant");
	return;
}

$DB = new GoldoFightDB();
$Login = $DB->escapeString($_POST["LoginNewPassword"]);

// Prendre le compte correspondant au login
$Account = Account::getAccountByLogin ($DB,$Login);
if ($Account == null)
{
	GFError::FatalError("Ce compte n'existe pas : $Login");
	return;
}


if ($Account->isActive() == false)
{
	GFError::FatalError("Ce compte n'a pas encore été activé : $Login");
	return;
}


// Nous sommes dans le cas ou l'on attend que le membre
// renseigne son secret après avoir reçu un mail
// Donc on génère un token et on lui envoie par mail
if (!isset($_POST['Secret']) && !isset($_POST['Password']))
{
	$Step = 1; 

	// Generer un secret
	$Secret = $DB->generateRandomString(10);

	// Enregistrer le secret
	if (Account::SetSecretToken ($DB,$Secret,$Account->IdAccount) == false)
	{
		GFError::FatalError("Réinitialisation du mot de passe");
		return;
	}

	// Envoyer le mail
	SendReinitPassword ($Account->Nom,$Account->Prenom,$Secret,$Account->Email,'');
	
}
// Sinon nous sommes dans le cas ou on réalise le changement
else
{
	$Step = 2;
	$Secret = $DB->escapeString($_POST["Secret"]);
	if (Account::IsSecretExists($DB,$Secret,$Account->IdAccount) == false)
	{
		GFError::FatalError("Impossible de réinitialiser le mot de passe");
		return;
	}
	
	$Password = $DB->escapeString($_POST["Password"]);
	if (Account::SetNewPassword ($DB,$Password,$Account->IdAccount) == false)
	{
		GFError::FatalError("Réinitialisation du mot de passe");
		return;
	}
}
$DB->close();
// $Step = 2;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<title>GoldoFight accueil</title>
	<script src=" https://code.jquery.com/jquery-3.6.0.min.js "></script>
	<link rel="stylesheet" href="./Css/GoldoFightAccueil.css">

	<script>
		$(document).ready(function() {
			
			
			<?php if ($Step == 1) { ?>
			$('#IdUpdate').click(function() {

				// alert ('BtnUpdate');
				const errorErray = [];
				if (!CheckRegex("Password")) errorErray.push("le format du mot de passe");

				if (errorErray.length > 0) $('.GuiError').html("Vérifiez : <br><ul>" + errorErray.join(" <br> ") + "</ul>");

				else $("#ChangePassword").submit();

			});


			function GetRegExPattern(name) {
				var pattern = '';
				switch (name) {

					case 'Password':
					case 'PasswordCheck':
						pattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,20}$/;
						break;

				}
				return pattern;
			}

			function CheckRegex(FieldName, pattern) {
				var pattern = GetRegExPattern(FieldName);
				if (pattern == '') return true;

				const reg = new RegExp(pattern);
				var idField = '#' + FieldName;
				var res = reg.exec($(idField).val())
				$(idField).css("background-color", (res == null) ? "#FFAAAA" : "#FFFFFF");
				return (res != null);
			}
			
			<?php } ?>			

			$("#idHomePage").click(function () {
				window.location.href = "index.php";
			});
			
		});
	</script>
</head>



<?php if ($Step == 1) { ?>




				<img class="GoldoBuste" src="./Images/GoldoBuste.png" />
				<div style="text-align:center;width:450px;margin:0px auto 0px auto;">
				<div class="GuiUserInfo">
				
				
				
				<h3 style="color:#FFFFFF">
					Un mail vous a été envoyé à l'adresse <?php echo $Account->Email; ?><br>
					Vous recevrez un code secret que vous devez renseigner ci-dessous.
				</h3>
				<hr>


				<form id="ChangePassword" action="ProcessLostPassword.php" method="post">
						<p style="margin:0px;color:#AAAAFF">Une majuscule, un chiffre, longueur [8,20]</p>
						<div>
							<label class="GuiLabel" for="Password">Mot de passe</label>
							<input class="GuiInput GuiCreateAc" type="password" id="Password" name="Password" placeholder="" required>
						</div>
						<div>
							<label class="GuiLabel" for="PasswordCheck">Secret</label>
							<input class="GuiInput GuiCreateAc" id="Secret" name="Secret" placeholder="" required>
						</div>
						
						
						<div>
							<label class="GuiLabel" for="LoginNewPassword">Email</label>
							<input class="GuiInput GuiCreateAc" type="text" id="LoginNewPassword" name="LoginNewPassword" value="<?php echo $Account->Email; ?>" placeholder="<?php echo $Account->Email; ?>" readonly>
						</div>						
						
				</form>
				<button id="IdUpdate" class="ButtonMenu">Enregistrer</button>
				<hr>
				<button id="idHomePage" class="ButtonMenu">Retour page d'accueil</button>
				<div class="GuiError"></div>
				
				</div>
				</div>

<?php } else { ?>


				<img class="GoldoBuste" src="./Images/GoldoBuste.png" />
				<div style="text-align: center;width:450px;margin:0px auto 0px auto;">
				<div class="GuiUserInfo">
				<h3 style="color:#FFFFFF" >Votre mot de passe a été changé.</h3>
				<button id="idHomePage" class="ButtonMenu">Retour page d'accueil</button>
				</div>
				</div>


<?php } ?>




</body>

</html>