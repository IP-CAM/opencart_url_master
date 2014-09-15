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
define('_ROUTE', explode('opencart_route_update', __DIR__)[0]);
$currentUrl = explode('/',$_SERVER['REQUEST_URI']);	
$dirUrl = (isset($currentUrl[1]) && $currentUrl[1] != 'opencart_route_update' ? $currentUrl[1] : '');	
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
if(isset($_POST['replaceBy'],$_POST['replaceTo']) && !empty($_POST['replaceBy']) && !empty($_POST['replaceTo'])) {
	/*
	/ This is a new replacement value to change url pattern.
	*/
	$replaceTo	= scan($_POST['replaceTo']);
	$replaceBy 	= scan($_POST['replaceBy']);
	
	$urlPattern = (isset($_POST['url_pattern']) ? trim($_POST['url_pattern']) : 'index');
	
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
		$localValue = _getOCLocalValue(_ROUTE . 'index.php');
		$localValue = ($localValue) ? $localValue : 'route';		
		$localURL 	= _getOCLocalUrl(_ROUTE . 'system/library/url.php', $localValue);	
?>

<form name="ocForm" action="" method="post" onsubmit="return confirm('Are you sure you want to update?');">
	<div class="row">
    	<div class="col-3">
        	<label for="rto" class="pull-right">Select the Pattern : </label>
        </div>
        <div class="col-9">
            <select name="url_pattern" class="select">
            	<option value="index" <?php echo ($localURL === 'index' ? 'selected' : ''); ?>>http://<?php echo $_SERVER['HTTP_HOST'] . '/' . $dirUrl; ?>/index.php?<?php echo $localValue; ?>=common/home</option>
                <option value="noindex" <?php echo ($localURL === 'noindex' ? 'selected' : ''); ?>>http://<?php echo $_SERVER['HTTP_HOST'] . '/' . $dirUrl; ?>/?<?php echo $localValue; ?>=common/home</option>
            </select>
        </div>
    </div>
    <div class="row">
    	<div class="col-3">
        	<label for="rto" class="pull-right">Old Text : </label>
        </div>
        <div class="col-9">
            <input type="text" name="replaceTo" id="rto" autocomplete="off" class="text" value="<?php echo $localValue; ?>" />
        </div>
    </div> 
    <div class="row">
    	<div class="col-3">
        	<label for="rby" class="pull-right">Enter New Text : </label>
        </div>
        <div class="col-9">
            <input type="text" name="replaceBy" id="rby" autocomplete="off" class="text" /><br />
            <small>Note : only characters, number and underscore(_) allowed.</small>
        </div>
    </div>  
    <div class="row">  
        <div class="col-6">
        	<label for="rby">&nbsp;</label>
        </div>
         <div class="col-6">    
            <input type="submit" name="submit-form"  value="Replace" class="btn" />
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
