<div style="color:white">
	<?php
	require_once('./Classes/DBGoldoFight.php');
	require_once('./Classes/GFError.php');
	require_once('./Classes/MailUtil.php');
	session_start();

	$ErrMsg = null;
	$InfMsg = null;
	$DB = new GoldoFightDB();

	$Nom = $DB->escapeString($_POST["Nom"]);
	$Prenom = $DB->escapeString($_POST["Prenom"]);
	$Email = $DB->escapeString($_POST["Email"]);
	$Pseudo = $DB->escapeString($_POST["Pseudo"]);
	$Password = $DB->escapeString($_POST["Password"]);
	$Md5 = strtoupper(md5($Password));
	
	// Vérifier quand même les informations au cas ou.
	if ($DB->isNullOrEmpty($Nom) == true ||
		$DB->isNullOrEmpty($Prenom) == true ||
		$DB->isNullOrEmpty($Email) == true ||
		$DB->isNullOrEmpty($Pseudo) == true ||
		$DB->isNullOrEmpty($Password) == true)
	{
		$ErrMsg = "Des informations semblent ne pas avoir été renseignées";
	}

	if ($ErrMsg == null && Account::IsEmailExists($DB, $Email)) {
		$ErrMsg = "L'Email $Email existe déjà";
	}

	if ($ErrMsg == null && Account::IsPseudoExists($DB, $Pseudo)) {
		$ErrMsg = "Le Pseudo $Pseudo existe déjà";
	}

	if ($ErrMsg == null) {
		
		$Token = $DB->generateRandomString(10);
		$query = sprintf("INSERT INTO account (Nom, Prenom, Email, Pseudo, Password, Token) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $Nom, $Prenom, $Email, $Pseudo, $Md5, $Token);

		if (!$DB->query($query)) {
			GFError::FatalError("Impossible de créer le compte  : $Nom $Prenom $Email $Pseudo");
		}

		$IdAccount = $DB->lastInsertedID();

		SendActivationAccount ($Nom,$Prenom,$Pseudo,$IdAccount,$Token,$Email,'');

		$InfMsg = "Le compte '$Nom $Prenom' a été créé.<br>Un mail d'activation vous a été envoyé à l'adresse $Email.<br>Pour vous connecter en tant que joueur vous devrez activer votre compte";

	}
	$DB->close();
	

	// $param = "error=" . $ErrMsg . "&info=" . $InfMsg;
	// header('Location: index.php?' . $param);

	$url = "Location: index.php";
	if ($ErrMsg != null) $url .= "?error=$ErrMsg";
	if ($InfMsg != null) $url .= "?info=$InfMsg";
	header($url);
	die();
	?>