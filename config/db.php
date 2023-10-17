<?php
class Db {

	private static $dbInstance = null;
    private static $dbUser = 'root';
    private static $dbPass = '';

    private static $dbName = '';
    private static $dbHost='mysql:host=127.0.0.1;dbname=dynamicFormSolution';
	
	// Prevent from creating instance
	private function __construct(){

	}
	
	// Prevent cloning the object
	private function __clone(){

	}

	public static function getInstance() {

		// Check if database is null
		if ( self::$dbInstance == null  ) {
			
			// Create a new PDO connection
			try {
				self::$dbInstance = new PDO(self::$dbHost, self::$dbUser, self::$dbPass);
			} catch (Exception $e) {
				echo $e->getMessage();			
			}
		}
		return self::$dbInstance;
	}
}