<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

$page['title'] = '';
$page['description'] = '';
$page['keywords'] = '';
$legaldo = array('login');
$do = (isset($get['do']) && in_array($get['do'],$legaldo)) ? $get['do'] : 'login';
if (isset($_SESSION['userid'])) header('Location: /admin');
if ($do == 'login') {
$loginForm = <<<HTML
      <div class="content">
        <div class="loginForm">
          <form action="/login/" class="np" method="post">
            {ERROR}
            <h1>Вход</h1>
            <div>
            	<input type="text" name="login" class="big text" placeholder="логин" value="{PHONE}" />
            </div>
            <div>
            	<input type="password" class="big text" name="password" placeholder="password" value="" />
            </div>
            <div>
            	<label for="lfmemory"><input type="checkbox" name="memory" id="lfmemory" value="yes" /> запомнить меня</label>
            </div>
            <div>
            	<input type="submit" value="Войти" class="button" />
            </div>
          </form>
        </div>
			</div>
HTML;
$addJs =<<<JS
<script>
$('input[name=passText]').focus(function() {
$(this).hide();
$('input[name=password]').removeClass('hidden').focus();
});
</script>
JS;
$phone = $err = '';
	if (isset($post['login']) && isset($post['password'])) {
		$userip = $_SERVER['REMOTE_ADDR'];
		$login = $phone = $db->escape($post['login']);
		if (!preg_match('/^[a-zA-Z0-9_-]{4,16}$/',$login)) {
			$error = 'Логин некорректен ';
		}
		if (!preg_match("/^[a-zA-Z0-9]{4,16}$/",$post['password'])) {
			$error = 'Допустимая длина пароля - от четырёх до шестнадцати символов латинского алфавита и цифр';
		}
    // $d = $db->fetchrow($db->query("SELECT `count` FROM `ss_login_errors` WHERE `ip`='".$userip."'"));
     // if ($d['count'] > 2) {
			// $error = 'Вы совершили более трех неудачных попыток входа. Пожалуйста, подождите один час и попробуйте снова';
		// }
		if (!isset($error)) {
			if ($row = $db->query("SELECT `id`,`password`,`hash` FROM `ss_admin` WHERE `login`='".$login."'")) {
				$row = $db->fetchrow($row);
			}
			else $error = 'Неверная пара логин/пароль';
			if (isset($row['password']) && checkPassword($row['password'],$row['hash'],$post['password'])) {
				$time = (isset($post['mem']) && $post['mem'] == 'yes') ? time() + 86400 : NULL;
				$hash = md5(mt_rand(1,999).time().$row['id']);
				setcookie("hash", $hash, $time,'/');
				setcookie("id", $row['id'], $time,'/');
				$db->query("UPDATE `ss_admin_session` SET `status`='0' WHERE `owner_id`='".$row['id']."'");
				$db->query("INSERT INTO `ss_admin_session` SET `owner_id`='".$row['id']."',`hash`='".$hash."',`status`='1',`logged`='".time()."',`ip`='".$_SERVER['REMOTE_ADDR']."'");
				$content = 'Вы успешно вошли';
				header('Location:/admin/');
			}
			else $error = 'Неверная пара логин/пароль';
		}
		if (isset($error)) {
			$time = time() + 3600;
			$err = $error;
			$db->query("INSERT INTO `ss_login_errors` SET `ip`='".$userip."',`count`=1,`time`='".$time."' ON DUPLICATE KEY UPDATE `count`=`count`+1,`time`='".$time."'");
			$content = str_replace(array('{ERROR}','{PHONE}'),array('<h2>Ошибка!</h2>'.$err.'<br />',$post['login']),$loginForm);
		}
	}
	else {
		$content = str_replace(array('{ERROR}','{PHONE}'),array('',$phone),$loginForm);
	}
}
	include_once($config['SITE_DIR'].$config['ADMIN_TEMPLATE_DIR'].'/header.php');
  $globalCont .= $content;
	include_once($config['SITE_DIR'].$config['ADMIN_TEMPLATE_DIR'].'/footer.php');
?>