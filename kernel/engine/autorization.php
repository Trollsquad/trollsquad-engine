<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

if (isset($_GET['ajax']) && $_GET['ajax'] == 'y') {
	if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
		header('Location: '.$_SERVER['HTTP_REFERER']);
		die('Hacking attempt!');
	}
}

if (!isset($_COOKIE['id']) || !isset($_COOKIE['hash'])) {
	header('Location: /login');
	die();
}
else {
	$result = $db->query("SELECT `id` FROM `ss_admin_session` WHERE `owner_id`='".intval($_COOKIE['id'])."' AND `hash`='".$db->escape($_COOKIE['hash'])."' AND `status`='1'");
	if ($db->numrows($result) == 1) {
		global $_SESSION,$service;
		$_SESSION['userid'] = intval($_COOKIE['id']);
	}
	else {
		setcookie('hash','');
		setcookie('id','');
		header('Location: /login');
		die();
	}
}
?>