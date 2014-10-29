<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
$title = (isset($page['title']) && $page['title'] != '') ? $page['title'] : $page['defaultTitle'];
$keywords = (isset($page['keywords']) && $page['keywords'] != '') ? $page['keywords'] : $page['defaultKeywords'];
$description = (isset($page['description']) && $page['description'] != '') ? $page['description'] : $page['defaultDescription'];
//include_once('menu.php');
$globalCont = <<<HTML
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>$title</title>
  <!-- Core Meta Data -->
  <meta name="description" content="$description">
  <meta name="keywords" content="$keywords">
  <!-- Open Graph Meta Data -->
  <meta property="og:description" content="$description">
  <meta property="og:image" content="$social[image]">
  <meta property="og:site_name" content="$social[name]">
  <meta property="og:title" content="$social[title]">
  <meta property="og:type" content="website">
  <meta property="og:url" content="$social[url]">
  <link rel="stylesheet" type="text/css" href="$config[TEMPLATE_DIR]/css/styles.css">
  <!-- Favicon -->
  <link rel="shortcut icon" href="$config[TEMPLATE_DIR]/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="$config[TEMPLATE_DIR]/favicon.png" type="image/png">
  <script src="$config[TEMPLATE_DIR]/js/tools/modernizr.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
</head>
<body>

    <!--[if lt IE 9]><div class="browsehappy"><div class="browsehappy__dialog">Вы используете <strong>устаревший</strong> браузер. Пожалуйста, <a href="http://browsehappy.com/" >обновите его</a>.</div></div><![endif]-->

    <main class="l-container">

        <div class="l-wrapper">

HTML;
?>