<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
@define('FPADMIN',true);
$page['title'] = 'Админ-панель';
$page['description'] = '';
$page['keywords'] = '';
$legaldo = array('index');
$adminTitle = 'Isotheos Engine смиренно приветствует тебя, властитель доступа';
$do = (isset($get['do']) && in_array($get['do'],$legaldo)) ? $get['do'] : 'index';
$menuArr = array(
	"index" => 'Настройки'
);
$menu = '';
foreach ($menuArr as $key => $value) {
	$class = ($key == $do) ? ' class="active"' : '';
	$menu .= '
        <a href="'.$config['site_url'].'" target="_blank">Перейти на сайт</a>
				<a href="/admin/'.str_replace('index','',$key).'"'.$class.'>'.$value.'</a>';
}
switch($do) {
  case 'index':
  include_once($config['SITE_DIR'].'/admin/settings.php');
  break;
}
if (isset($get['ajax']) && $get['ajax'] == 'y') echo $cont;
else {
  $content = '
    <h1 class="text_center">'.$adminTitle.'</h1>
     <div class="content">
      '.$cont.'
     </div>
  ';
  include_once($config['SITE_DIR'].$config['ADMIN_TEMPLATE_DIR'].'/header.php');
  $globalCont .= $content;
  include_once($config['SITE_DIR'].$config['ADMIN_TEMPLATE_DIR'].'/footer.php');
}
?>