<?php
require_once('./Classes/DBGoldoFight.php');
require_once('./Classes/GFError.php');
require_once('./Classes/MailUtil.php');
session_start();

// Il faut toujours un login de renseigné
if (!isset($_GET['id']) || !isset($_GET['token']))
{
	GFError::FatalError("Impossible d'activer un compte il manque des informations");
	return;
}

$DB = new GoldoFightDB();
$IdAccount = $DB->escapeString($_GET["id"]);
$Token = $DB->escapeString($_GET["token"]);

// Prendre le compte correspondant
$Account = Account::getAccount ($DB,$IdAccount);
if ($Account == null)
{
	GFError::FatalError("Le compte demandé n'existe pas");
	return;
}

// Vérifier si il est déjà activé
if ($Account->isActive())
{
	GFError::FatalError("Le compte est déjà activé");
	return;
}


// Vérifier le Token de provenance et le Token enregistré
if ($Account->Token != $Token)
{
	GFError::FatalError("Des informations ne correspondent pas à l'activation du compte");
	return;
}


if (Account::ActivateAccount ($DB,$IdAccount) == false)
{
	GFError::FatalError("Une erreur technique s'est produite pendant l'activation du compte");
	return;
}
$DB->close();

?>


<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<title>GoldoFight Activation de compte</title>
	<script src=" https://code.jquery.com/jquery-3.6.0.min.js "></script>
	<link rel="stylesheet" href="./Css/GoldoFightAccueil.css">

	<script>
		$(document).ready(function() {
			
			$("#idHomePage").click(function () {
				window.location.href = "index.php";
			});
			
		});
	</script>
</head>


	<img class="GoldoBuste" src="./Images/GoldoBuste.png" />
	<div style="text-align: center;width:450px;margin:0px auto 0px auto;">
	<div class="GuiUserInfo">
	<h3 style="color:#FFFFFF">
		Votre compte <?php echo $Account->Nom." ".$Account->Prenom; ?> a été activé.<br>
		Pour jouer et enregistrer vos scores, il vous est maintenant possible de vous connecter.
	</h3>
	<hr>
	<button id="idHomePage" class="ButtonMenu">Retour page d'accueil</button>


	<div class="GuiError"></div>
	</div>
	</div>



</body>

</html>