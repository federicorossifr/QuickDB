<?php

	mysqli_report(MYSQLI_REPORT_STRICT); 

	class Config {
		private static $props;

		static function conf($prop,$value) {
			self::$props[$prop] = $value;
		}

		static function read($prop) {
			return self::$props[$prop];
		}
	}


	//Example configuration
	Config::conf("dbHost","localhost"); 
	Config::conf("dbUser","root");  
	Config::conf("dbPass","");
	Config::conf("dbCollection","experiments"); //Database name
	Config::conf("displayErrorMessages",0); //1 will display all error messages, 0 will display message below on
											//error
	Config::conf("safeErrorMessage","There was an error"); // Error message to display instead of default error 													   // message from mysqli