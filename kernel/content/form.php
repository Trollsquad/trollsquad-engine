<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
if (!isset($get['ajax'])) die();
$result = array();
if ($db->query("INSERT INTO `ss_order` SET `name`='".$db->escape($post['name'])."',`email`='".$db->escape($post['email'])."',`comment`='".$db->escape($post['text'])."',`time`='".time()."'")) {
  $result['status'] = 'true';
  $result['mess'] = 'Спасибо за сообщение!';
}
else {
 $result['status'] = 'error';
 $result['mess'] = 'Ошибка. Попробуйте позже';
}
echo json_encode($result);
die();
?>