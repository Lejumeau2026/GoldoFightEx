<?php
require_once('DBSettings.php');

class DBClass extends DatabaseSettings
{
	var $classQuery;
	var $cnx;
	
	var $errno = '';
	var $error = '';
	
	// Connects to the database
	function __construct()
	{
		// Load settings from parent class
		$settings = DatabaseSettings::getSettings();
		
		// Get the main settings from the array we just loaded
		$host = $settings['dbhost'];
		$name = $settings['dbname'];
		$user = $settings['dbusername'];
		$pass = $settings['dbpassword'];

		// Connect to the database
		$this->cnx = new mysqli($host, $user, $pass, $name);
		
		if ($this->cnx->connect_error) exit("<p><h3 style='color:#FF0000'>Impossible de se connecter à la base de données</h3></p>");
	}
	
	// Executes a database query
	function query( $query ) 
	{
		$this->classQuery = $query;
		return $this->cnx->query( $query );
	}
	
	function escapeString( $query )
	{
		// return $this->cnx->escape_string( $query );
		return mysqli_real_escape_string($this->cnx,$query);
	}
	
	// Get the data return int result
	function numRows( $result )
	{
		return $result->num_rows;
	}
	
	function lastInsertedID()
	{
		return $this->cnx->insert_id;
	}
	
	// Get query using assoc method
	function fetchAssoc( $result )
	{
		return $result->fetch_assoc();
	}
	
	// Gets array of query results
	function fetchArray( $result , $resultType = MYSQLI_ASSOC )
	{
		return $result->fetch_array( $resultType );
	}
	
	// Fetches all result rows as an associative array, a numeric array, or both
	function fetchAll( $result , $resultType = MYSQLI_ASSOC )
	{
		return $result->fetch_all( $resultType );
	}
	
	// Get a result row as an enumerated array
	function fetchRow( $result )
	{
		return $result->fetch_row();
	}
	
	// Free all MySQL result memory
	function freeResult( $result )
	{
		// $this->cnx->free_result( $result );
		mysqli_free_result($result);
	}
	
	//Closes the database connection
	function close() 
	{
		$this->cnx->close();
	}
	
	function sql_error()
	{
		if( empty( $error ) )
		{
			$errno = $this->cnx->errno;
			$error = $this->cnx->error;
		}
		return $errno . ' : ' . $error;
	}
	
	function generateRandomString($length = 10) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}	
}	
?>
