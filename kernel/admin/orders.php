<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
if(!defined("FPADMIN") || FPADMIN!==true) die('Hacking attempt!');
$page['title'] = 'Сообщения - Админ-панель';
$page['description'] = '';
$page['keywords'] = '';
$cont = 'Обращения';
$res = $db->query("SELECT * FROM `ss_order` ORDER BY `id` DESC");
if ($db->numrows($res) == 0) $cont = '<div class="addForm tCenter">Сообщений нет</div>';
else {
  $cont = '<div class="content"><table class="table">
    <thead>
      <th>ID</th>
      <th>Имя</th>
      <th>E-mail</th>
      <th>Сообщение</th>
      <th>Дата</th>
    </thead>
    <tbody>
  ';
  while ($row=$db->fetchrow($res)) {
    $cont .= '
    <tr>
    	<td>'.$row['id'].'</td>
    	<td>'.$row['name'].'</td>
    	<td>'.$row['email'].'</td>
    	<td>'.$row['comment'].'</td>
    	<td>'.date('H:i:s d.m.Y',$row['time']).'</td>
    </tr>';
  }
  $cont .= '</tbody></table></div>';
}
?>