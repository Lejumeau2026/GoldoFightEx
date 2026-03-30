<?php

class GFError
{
    public static function FatalError($errMsg)
    {
		header("Location: Die.php?error=$errMsg");
		die();
    }	
}	
?>
