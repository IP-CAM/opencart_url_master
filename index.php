<!--
/**
/* Developer 	: Suraj Jain
/* Email 		: mail.surajjain@gmail.com
/* Website 		: http://developersign.com/
/* Blog Website : http://developersurajjain.blogspot.com/
**/
-->
<?php
require 'server/opencart.php';
require 'server/Session.php';
require 'server/Redirect.php';
define('_ROUTE', explode('opencart_url_master', __DIR__)[0]);
$currentUrl = explode('/',$_SERVER['REQUEST_URI']);	
$dirUrl = (isset($currentUrl[1]) && $currentUrl[1] != 'opencart_url_master' ? $currentUrl[1] : '');

$localValue = _getOCLocalValue(_ROUTE . 'index.php');
$localValue = ($localValue) ? $localValue : 'route';		
$localURL 	= _getOCLocalUrl(_ROUTE . 'system/library/url.php', $localValue);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Suraj Jain" />
<title>Opencart - Change URL Pattern | Developersign</title>
<link rel="stylesheet" href="css/devsj.css" media="all" />
</head>
<body>
<div id="main">
<div class="top-menu">
	<ul>
    <?php
		foreach(glob("*.php") as $file) {
			echo '<li><a href="'.$file.'">' . _filter($file) . '</a></li>';	
		}
	?>    	
    </ul>
</div>
<h1>Opencart - Change URL Pattern</h1>
<?php
if(isset($_POST['replaceBy'],$_POST['replaceTo'],$_POST['url_pattern'],$_POST['currentTheme']) && !empty($_POST['replaceBy']) && !empty($_POST['replaceTo']) && !empty($_POST['currentTheme'])) {
	extract($_POST);
	/*
	/ This is a new replacement value to change url pattern.
	*/
	if(!scan($replaceBy)) {
		Session::_setSession('status', '<p style="margin-top: 5px;color: red;">Invalid Text.</p>');	
		Redirect::to('index.php');
	}
	$replaceTo	= trim($replaceTo);
	$replaceBy 	= trim($replaceBy);
	$theme		= trim($currentTheme);
	$urlPattern = (isset($url_pattern) && !empty($url_pattern)? trim($url_pattern) : '');
	/*
	/ Please do Not Change anything
	*/
	$totalAffectedfiles = $totalPlaces = 0;
	$output = '';	
	$output = '<ul id="files">';
	foreach($opencart as $type => $value) {
		foreach($value as $dir) {
			if($type === 'DIR' && !empty($dir)) {
				if(is_dir(_ROUTE . $dir)) {
					if($dh = opendir(_ROUTE . $dir)) {
						while(($file = readdir($dh)) !== false) {
							if($file == '.' || $file == '..')continue;
							if($dh2 = opendir(_ROUTE . $dir . $file)) {
								while(($filename = readdir($dh2)) !== false) {
									if($filename == '.' || $filename == '..')continue;
									$actionFile = _ROUTE . $dir . $file . '/' . $filename;
									$totalPlaces += _updateOCFile($replaceTo, $replaceBy, $actionFile, $urlPattern);
									$output .= '<li>' . $actionFile . '</li>';
									$totalAffectedfiles++;	
								}
								closedir($dh2);
							}
						}
						closedir($dh);
					}
				}
			}
			else if($type === 'FILE' && !empty($dir)) {			
			  $actionFile = _ROUTE . $dir;
			  $totalPlaces += _updateOCFile($replaceTo, $replaceBy, $actionFile, $urlPattern);	
			   $output .= '<li>' . $actionFile . '</li>';
			  $totalAffectedfiles++;
			}
			else if($type === 'THEME' && !empty($dir)) {
				$dir = $dir . $theme . '/template/';
				if(is_dir(_ROUTE . $dir)) {
					if($dh = opendir(_ROUTE . $dir)) {
						while(($file = readdir($dh)) !== false) {
							if($file == '.' || $file == '..')continue;
							if($dh2 = opendir(_ROUTE . $dir . $file)) {
								while(($filename = readdir($dh2)) !== false) {
									if($filename == '.' || $filename == '..')continue;
									$actionFile = _ROUTE . $dir . $file . '/' . $filename;
									$totalPlaces += _updateOCFile($replaceTo, $replaceBy, $actionFile, $urlPattern);
									$output .= '<li>' . $actionFile . '</li>';
									$totalAffectedfiles++;	
								}
								closedir($dh2);
							}
						}
						closedir($dh);
					}
				}
			}
		}
	}
	$output .= '</ul>';	
	
	Session::_setSession('text_data', $output);
	Session::_setSession('total_files', $totalAffectedfiles);	
	Session::_setSession('total_places', $totalPlaces);	
	Redirect::to('index.php');	
	
} else {	
	if(Session::_sessionExists('text_data') && Session::_sessionExists('total_files') && Session::_sessionExists('total_places')) {
		echo '<div id="overlay-text"><h2 class="redcolor">Warning!!<h2><p>Do not refresh page.</p></div>';
		echo Session::_flashSession('text_data');
		echo '<div id="bottom-files" style="display:none">
				<div class="row"><div class="total_rows">Total Updated Files : <span style="color:#000">' .  Session::_flashSession('total_files') . '</span></div></div>
				<div class="row"><div class="total_rows">Total Words Replaced : <span style="color:#000">' .  Session::_flashSession('total_places') . '</span></div></div>
				<div class="row"><div class="col-6"><a href="index.php" class="btn pull-right">Close</a></div>
    		  </div></div>';
	} else {	
			
?>

<form name="ocForm" action="" method="post" onsubmit="return confirm('Are you sure you want to update?');">
	<div class="row">
    	<div class="col-3">
        	<label for="rto" class="pull-right">Select URL Pattern <span style="color:blue;font-size:11px;">(Optional)</span>: </label>
        </div>
        <div class="col-9">
            <select name="url_pattern" class="select">
            	<option value="">Want to change?</option>
            	<option value="<?php echo ($localURL === 'index' ? 'noindex' : 'index'); ?>">http://<?php echo $_SERVER['HTTP_HOST'] . '/' . $dirUrl; ?>/<?php echo ($localURL === 'index' ? '' : 'index.php'); ?>?<?php echo $localValue; ?>=common/home</option>
            </select>
        </div>
    </div>
    <div class="row">
    	<div class="col-3">
        	<label for="rto" class="pull-right">Select Your Current Theme <span style="color:red;font-size:11px;">(Required)</span>: </label>
        </div>
        <div class="col-9">
            <select name="currentTheme" class="select">
                 <?php
					foreach(glob(_ROUTE . "catalog/view/theme/*") as $theme) {
						$themeName = explode(_ROUTE . "catalog/view/theme/",$theme)[1];
						echo '<option value="'.$themeName.'">'.$themeName.'</option>';	
					}
				?>  
            </select>
        </div>
    </div>    
    <div class="row">
    	<div class="col-3">
        	<label for="rto" class="pull-right">Old Text <span style="color:red;font-size:11px;">(Required)</span>: </label>
        </div>
        <div class="col-9">
            <input type="text" name="replaceTo" id="rto" autocomplete="off" class="text" value="<?php echo $localValue; ?>" readonly="readonly" style="color:#999" />
        </div>
    </div> 
    <div class="row">
    	<div class="col-3">
        	<label for="rby" class="pull-right">Enter New Text <span style="color:red;font-size:11px;">(Required)</span>: </label>
        </div>
        <div class="col-9">
            <input type="text" name="replaceBy" id="rby" autocomplete="off" class="text" value="" /><br />
            <small>Note : <br />
            	1) Only characters <strong>[a-zA-Z]</strong>, number <strong>[0-9]</strong> and special characters <strong>(_ - | $ @)</strong> are allowed.<br />
                2) The text must have one character and one (number or special character).<br />
                3) Valid formats	 =  <span style="color:green">_dir, 23-dir, pp-39, |page</span><br />
                3) Invalid formats 	 =  <span style="color:red">_232, page, |33, _, route</span></small>
        </div>
    </div>  
    <div class="row">  
        <div class="col-3">
        	<label for="rby">&nbsp;</label>
        </div>
         <div class="col-9">    
            <input type="submit" name="submit-form"  value="Replace" class="btn" />
            <?php if(Session::_sessionExists('status')) { echo Session::_flashSession('status'); } ?>
        </div>
    </div>    
</form>

<?php	
	}
}
?>
</div>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/devsj.js"></script>
</body>
</html>
