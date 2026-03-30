<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

function SendActivationAccount ($Nom,$Prenom,$Pseudo,$IdAccount,$Token,$Email,$From)
{
	$subject = "GoldoFight : Activation de votre compte";
	$html = file_get_contents('./MailTemplate/Activation.html', true);
	$message = str_replace("<TAG_NOM>",$Nom,$html);
	$message = str_replace("<TAG_PRENOM>",$Prenom,$message);
	$message = str_replace("<TAG_EMAIL>",$Email,$message);
	$message = str_replace("<TAG_PSEUDO>",$Pseudo,$message);
	$message = str_replace("<TAG_DATE>",date("d/m/Y à h:i:s"),$message);
	$message = str_replace("<TAG_ACCOUNTID>",$IdAccount,$message);
	$message = str_replace("<TAG_TOKEN>",$Token,$message);

	SendMail ($Email,$From,$subject,$message);
	return $message;
}

function SendReinitPassword ($Nom,$Prenom,$Secret,$Email,$From)
{
	$subject = "GoldoFight : Réinitialisation de votre mot de passe";
	$html = file_get_contents('./MailTemplate/ReinitPassword.html', true);
	$message = str_replace("<TAG_NOM>",$Nom,$html);
	$message = str_replace("<TAG_PRENOM>",$Prenom,$message);
	$message = str_replace("<TAG_SECRET>",$Secret,$message);
	SendMail ($Email,$From,$subject,$message);
	return $message;
}



function SendMail ($to,$from,$subject,$message)
{
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	// $headers .= 'From: <'.$from.'>' . "\r\n";
	$headers .= 'From: <Goldofight2024@noreply.fr>' . "\r\n";
	$headers .= 'Cc: myboss@example.com' . "\r\n";

	// echo "To      :".$to."<br>";
	// echo "Sujet   :".$subject."<br>";
	// echo "headers :".$headers."<br><br>";
	// echo "<hr><br>";

	// echo "message :".$message."<br>";
	mail($to,$subject,$message,$headers);
}




?>
