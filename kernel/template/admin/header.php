<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
$title = (isset($page['title']) && $page['title'] != '') ? $page['title'] : $page['defaultTitle'];
$keywords = (isset($page['keywords']) && $page['keywords'] != '') ? $page['keywords'] : $page['defaultKeywords'];
$description = (isset($page['description']) && $page['description'] != '') ? $page['description'] : $page['defaultDescription'];
//include_once('menu.php');
$globalCont = <<<HTML
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <!--[if IE]>
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <title>$title</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=1024px">
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="$config[ADMIN_TEMPLATE_DIR]/css/template_styles.css" />
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/modernizr.custom.js"></script>
    <link rel="shortcut icon" href="$config[ADMIN_TEMPLATE_DIR]/favicon.ico" type="image/x-icon" />
  </head>
  <body>
    <header class="main">
      <div class="warp clearfix">
        <div class="header_logo"><img src="$config[ADMIN_TEMPLATE_DIR]/images/header_logo.png" alt=""/></div>
        <nav class="menu">
          $menu
        </nav>
      </div>
    </header>
    <section class="main">
      <div class="warp">

HTML;
?>