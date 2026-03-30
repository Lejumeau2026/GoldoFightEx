<?php

// Paramétré chez OVH
// user : goldorcgoldodb
// pass : GoldoFight2024Db

class DatabaseSettings
{
	var $settings;

	function getSettings()
	{
		// Database variables for localhost
		// Host name
		$settings['dbhost'] = 'localhost';
		// Database name
		$settings['dbname'] = 'GoldoFight';
		// Username
		$settings['dbusername'] = 'root';
		// Password
		$settings['dbpassword'] = '';
		
		
/*		// Database variables for OVH
		// Host name
		$settings['dbhost'] = 'goldorcgoldodb.mysql.db';
		// Database name
		$settings['dbname'] = 'goldorcgoldodb';
		// Username
		$settings['dbusername'] = 'goldorcgoldodb';
		// Password
		$settings['dbpassword'] = 'GoldoFight2024Db';
	*/	
		
		return $settings;
	}
}?>
