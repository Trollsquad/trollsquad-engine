<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
if(!defined("FPADMIN") || FPADMIN!==true) die('Hacking attempt!');
$page['title'] = 'Настройки - Админ-панель';
$page['description'] = '';
$page['keywords'] = '';
$cont = '';
if ($post) {
  $db->query("UPDATE `ss_misc` SET `value`='".$db->escape($post['maindesk'])."' WHERE `id`='maindesk'");
  $db->query("UPDATE `ss_misc` SET `value`='".$db->escape($post['workdesk'])."' WHERE `id`='workdesk'");
  $cont = '<h2>Данные успешно обновлены</h2>'.$cont;
}
$maindesk = $db->fetchrow($db->query("SELECT * FROM `ss_misc` WHERE `id`='maindesk'"));
$workdesk = $db->fetchrow($db->query("SELECT * FROM `ss_misc` WHERE `id`='workdesk'"));
$cont .= '
  <form action="/admin/" class="np" method="post">
    Приветственный текст на главной
    <textarea name="maindesk" placeholder="Приветственный текст на главной">'.$maindesk['value'].'</textarea>
    <br/>
    <br/>
    Приветственный текст в портфолио
    <textarea name="workdesk" placeholder="Приветственный текст в портфолио">'.$workdesk['value'].'</textarea>
    <input type="submit" class="big red submit" value="Сохранить"/>
  </form>
';
?>
