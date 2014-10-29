<?
  error_reporting(0);
  $bd = mysql_escape_string($_GET['bd_name']);
  $user = mysql_escape_string($_GET['bd_user']);
  $pass = mysql_escape_string($_GET['bd_pass']);
  if (mysql_connect('localhost', $user, $pass)) {
    if (mysql_select_db($bd)) {
      $res = array('mess'=>"Всё ок!",'status'=>'true');
    }
    else $res = array('mess'=>"Невозможно выбрать БД ".$bd,'status'=>'error');
  }
  else $res = array('mess'=>"Неправильные логи/пароль",'status'=>'error');
  echo json_encode($res);
?>