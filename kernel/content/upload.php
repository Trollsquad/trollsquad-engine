<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	header('Location: '.$_SERVER['HTTP_REFERER']);
}

include_once($config['SITE_DIR'].'/engine/fileUpload.php');
$uploader = new fpFileUploader($config['FileAllowedExtensions'],$config['maxFsUnautoriz']);
$result = $uploader->handleUpload($config['uploadPath']);
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
die();
?>