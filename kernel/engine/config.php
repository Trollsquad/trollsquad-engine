<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

global $config;

date_default_timezone_set('Europe/Moscow');


/*	PAGE Block	*/
$page['defaultTitle'] = '';
$page['defaultKeywords'] = '';
$page['defaultDescription'] = '';
$page['AddJS'] = '';

/*	GENERAL Config	*/
$config['TEMPLATE_DIR'] = '/template/fp';
$config['ADMIN_TEMPLATE_DIR'] = '/template/admin';
$config['SITE_DIR'] = '/var/www/nencu/data/www/bx.isotheos.pw';
$config['SITE_URL'] = 'http://bx.isotheos.pw/';
$config['PICT_URL'] = $config['SITE_URL'].'f/';
$config['PREV_URL'] = $config['SITE_URL'].'m/';
/*	DB Config	*/
$config['DbServer'] = 'localhost';
$config['DbPort'] = '';
$config['DbUser'] = '';
$config['DbPassword'] = '';
$config['DbBase'] = '';
$config['DbPrefix'] = '';

/*	IMAGE Config	*/
$config['maxWidth'] = '';
$config['maxHeight'] = '';
$config['maxFsUnautoriz'] = 10 * 1024 * 1024;
$config['maxFsAutoriz'] = 15 * 1024 * 1024;
$config['maxFsPremium'] = 50 * 1024 * 1024;
$config['uploadPath'] = $config['SITE_DIR'].'/f/';
$config['previewPath'] = $config['SITE_DIR'].'/m/';
$config['FileAllowedExtensions'] = array('png','jpg','jpeg','gif');

?>