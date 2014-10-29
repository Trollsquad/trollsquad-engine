<?
@define('FPINDEX',true);
@session_start();

if (is_dir('install')) header('location:/install/index.php');
$get = $_GET;
$post = $_POST;
$legalact = array('index','admin','login','upload');
$adminact = array('admin');
$act = (isset($get['act']) && in_array($get['act'],$legalact)) ? $get['act'] : 'index';

$globalCont = '';
$social = array(
  'name'=>'L-Projects',
  'url'=>$_SERVER['REQUEST_URI'],
  'image'=>'',
  'title'=>'L-Projects'
);
$popups = '';

include_once('engine/config.php');
include_once($config['SITE_DIR'].'/engine/functions.php');
include_once($config['SITE_DIR'].'/engine/db.php');

if (in_array($act,$adminact) && $act != 'login') include_once($config['SITE_DIR'].'/engine/autorization.php');
error_reporting(0);

switch($act) {
	case 'login':
	include_once($config['SITE_DIR'].'/content/login.php');
	break;
  
	case 'index':
	include_once($config['SITE_DIR'].'/content/main.php');
	break;
  
	case 'upload':
	include_once($config['SITE_DIR'].'/content/upload.php');
	break;
  
	case 'admin':
	include_once($config['SITE_DIR'].'/content/admin.php');
	break;
}
if (!isset($cacheControl)) $cacheControl = 'private';
header("Expires: ".gmdate("D, d M Y H:i:s",time() - 86400)." GMT");
header("Cache-Control: ".$cacheControl);
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");
header("X-Powered-By: Isotheos Engine 1.18");
header("X-UA-Compatible: IE=edge,chrome=1");

// ///// Configuration //////////////////
// $PREFER_DEFLATE = false; // prefer deflate over gzip when both are supported
// $FORCE_COMPRESSION = false; // force compression even when client does not report support
// //////////////////////////////////////

function compress_output_gzip($output) {
	return gzencode($output);
}

function compress_output_deflate($output) {
	return gzdeflate($output, 3);
}
$AE = '';
if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
	$AE = $_SERVER['HTTP_ACCEPT_ENCODING'];
}

$support_gzip = (strpos($AE, 'gzip') !== FALSE) || $FORCE_COMPRESSION;
$support_deflate = (strpos($AE, 'deflate') !== FALSE) || $FORCE_COMPRESSION;

if($support_gzip && $support_deflate) {
	$support_deflate = $PREFER_DEFLATE;
}

if ($support_deflate) {
	header("Content-Encoding: deflate");
	ob_start("compress_output_deflate");
}
else {
	if($support_gzip){
		header("Content-Encoding: gzip");
		ob_start("compress_output_gzip");
	}
	else {
		ob_start();
	}
}

echo $globalCont;
  // $pass = '12qwaszx';
  // $login = 'allit';
  // $hash = generateCode(32);
  // $hashpass = hashPassword($hash,$pass);
 // $db->query("INSERT INTO `ss_admin` SET `login`='".$login."',`password`='".$hashpass."',`hash`='".$hash."',`level`=1");

//include_once($config['SITE_DIR'].'/engine/fileRemove.php');
?>