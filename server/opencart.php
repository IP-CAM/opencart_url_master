<?php
session_start();

$opencart = array(
	'DIR' => array(		
				'admin/controller/',
				'admin/view/template/',
				'catalog/controller/'
				),
	'FILE' => array(
				'htaccess.txt',
				'.htaccess',
				'index.php',
				'admin/index.php',
				'system/library/url.php',			
				'admin/view/javascript/common.js',
				'catalog/view/javascript/common.js'						
				),
	'THEME' => array(	
				'catalog/view/theme/'
				)			
);

/*
/ Functions
*/
function _updateOCFile($text, $replace_text, $file, $urlPattern) {
	$old = array("'" . $text . "'", $text . "=");
	$new = array("'" . $replace_text . "'", $replace_text . "=");
	if(file_exists($file)) {	
		$fileStr = file_get_contents($file);
		$fileStr = str_replace($old, $new, $fileStr, $count);
		
		if($urlPattern === 'index') {
			$indexO = array('?' . $replace_text . '=');
			$indexN = array('index.php?' . $replace_text . '=');
			$fileStr = str_replace($indexO, $indexN, $fileStr);
		}
		else if($urlPattern === 'noindex') {
			$indexO = array('index.php?' . $replace_text . '=');
			$indexN = array('?' . $replace_text . '=');
			$fileStr = str_replace($indexO, $indexN, $fileStr);
		}
		
		file_put_contents($file, $fileStr);
		return $count;
	}
	return false;
}

function _getOCLocalValue($file) {
	if(file_exists($file)) {	
		$fileStr = file_get_contents($file);
		$break = explode('new Action($request->get[\'', $fileStr);
		if(isset($break[1])) { return explode('\']', $break[1])[0]; }
		return false;
	}
	return false;
}

function _getOCLocalUrl($file, $val) {
	if(file_exists($file)) {	
		$fileStr = file_get_contents($file);
		return (strpos($fileStr, 'index.php?' . $val . '=') !== false ? 'index' : 'noindex');
	}
	return false;
}

function scan($str) {
	if(!preg_match("#^[a-zA-Z0-9\_\-\|\$\@]+$#", $str)) {
    	return false;
	}
	if(preg_match('/\s/',$str)) {
	  return false;
	}
	if(strcspn($str, 'abcdefghijklmnopqrstuvwxyz') === strlen($str)) {
	  return false;
	}	
	if(strcspn($str, '0123456789') === strlen($str) && strcspn($str, '_-|$@') === strlen($str)) {
	  return false;
	}	
	return true;
}

function _filter($str) {
	$value = strtoupper(str_replace(array('_','-'), ' ', trim(explode('.php', $str)[0])));
	return ucwords(strtolower(($value === 'INDEX' ? 'CHANGE URL PATTERN' : $value)));
}

?>
