<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
$menuArr = array(
	$config['SITE_URL']."about/" => 'О компании',
	$config['SITE_URL']."services/" => 'Услуги',
	$config['SITE_URL']."works/" => 'Наши работы',
	$config['SITE_URL']."contacts/" => 'Контактная информация',
	$config['SITE_URL']."clients/" => 'Наши клиенты'
);
$act = $_SERVER['REQUEST_URI'];
$menu = '';
foreach ($menuArr as $key => $value) {
	$class = ($key == $act) ? ' class="active"' : '';
	$menu .= '
				<a href="'.$key.'"'.$class.'>'.$value.'</a></li>';
}
?>