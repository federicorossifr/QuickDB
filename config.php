<?php

	class Config {
		private static $props;

		static function conf($prop,$value) {
			self::$props[$prop] = $value;
		}

		static function read($prop) {
			return self::$props[$prop];
		}
	}


	Config::conf("dbHost","");
	Config::conf("dbUser","");
	Config::conf("dbPass","");
	Config::conf("dbCollection","");