<?php

class Session {

	public static function _setSession($name, $value) {
		$_SESSION[$name] = $value;
		return true;
	}
	
	public static function _flashSession($name) {
		if(isset($_SESSION[$name])) {
			$data = $_SESSION[$name];
			unset($_SESSION[$name]);
			return $data;
		} 
	}
	
	public static function _sessionExists($name) {
		return (isset($_SESSION[$name])) ? true : false;
	}
	
}

?>
