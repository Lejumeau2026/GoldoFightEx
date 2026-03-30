<div style="color:white">
	<?php
	require_once('./Classes/DBGoldoFight.php');
	require_once('./Classes/GFError.php');

	session_start();

	$ErrMsg = null;
	$DB = new GoldoFightDB();

	if (!isset($_POST["Login"]) || !isset($_POST["PasswordCnx"])) {
		GFError::FatalError("Impossible de se connecter sans login et mot de passe");
	}

	$Login = $DB->escapeString($_POST["Login"]);
	$Password = $DB->escapeString($_POST["PasswordCnx"]);

	$account = Account::getAccountByLogin($DB, $Login);
	$DB->close();


	if ($account != null) {

		if (!$account->isActive()) 
		{
			$ErrMsg = "Ce compte n'a pas encore été activé";
		}
		else if (!Account::CheckPassword($Password, $account->Password))
		{
			$ErrMsg = "Echec d'authentification au mot de passe";
		}
		else $_SESSION["IdAccount"] = $account->IdAccount;
		
	} else $ErrMsg = "Le compte n'existe pas";


	/*
	echo "<br>form Login    : ".$Login;
	echo "<br>form Password : ".$Password;
	echo "<br>Recal md5     : ".strtoupper(md5($Password));

	
	echo "<br>IdAccount :".$account->IdAccount;
	echo "<br>Nom       :".$account->Nom;
	echo "<br>Prenom    :".$account->Prenom;
	echo "<br>Email     :".$account->Email;
	echo "<br>Pseudo    :".$account->Pseudo;
	echo "<br>Password  :".$account->Password;
*/


	// header('Location: index.php?error='.$ErrMsg);
	if ($ErrMsg != null)
	{
		$url = "Location: index.php?error=$ErrMsg";
	}
	else
	{
		$CheckPlay = $_POST["IdCheckPlay"];
		$url = ($CheckPlay == true) ? "Location: GoldoFight.php" : "Location: index.php";
	}
	
	// $url = "Location: index.php";
	// if ($ErrMsg != null) $url .= "?error=$ErrMsg";
	header($url);
	die();

	?>