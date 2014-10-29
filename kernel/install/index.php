<?
@define('FPINDEX',true);
@session_start();

$get = $_GET;
$post = $_POST;
  if (!$post) {
    $menu = '';
    $title="Isotheos Engine";
    $content = '<div class="content">
      <h1>Добро пожаловать в установку Isotheos Engine</h1>
      <form action="install.php">
        <fieldset>
          <legend>Параметры баз данных</legend>
          <div>
          	<input required="required" type="text" name="bd_name" placeholder="Название базы данных" class="input_text"/>
          </div>
          <div>
          	<input required="required" type="text" name="bd_user" placeholder="Имя пользователя БД" class="input_text"/>
          </div>
          <div>
          	<input type="text" name="bd_pass" placeholder="Пароль пользователя БД" class="input_text"/>
          </div>
          <a href="" id="checkbd" onclick="checkBD();return false;" title="Проверить" class="bt icon disc"></a>
        </fieldset>
        <fieldset>
        	<legend>Администратор</legend>
        	<div>
            <input required="required" type="text" name="user_name" placeholder="Логин" class="input_text"/>
          </div>
        	<div>
            <input required="required" type="text" name="user_email" placeholder="E-mail" class="input_text"/>
          </div>
        	<div>
            <input required="required" type="text" name="user_pass" placeholder="Пароль" class="input_text"/>
          </div>
        </fieldset>
        <fieldset>
          <legend>Включите нужные модули</legend>
          <div><label><input type="checkbox" onchange="checkModules()" name="module_forms"/> Обратная связь</label></div>
          <div><label><input type="checkbox" onchange="checkModules()" name="module_portfolio"/> Портфолио</label></div>
        </fieldset>
        <input type="submit" class="bt green" value="Начать установку"/>
      </form>
    </div>
    ';
  }
  // $pass = '12qwaszx';
  // $login = 'allit';
  // $hash = generateCode(32);
  // $hashpass = hashPassword($hash,$pass);
 // $db->query("INSERT INTO `ss_admin` SET `login`='".$login."',`password`='".$hashpass."',`hash`='".$hash."',`level`=1");
 	include_once('template/header.php');
  $globalCont .= $content;
	include_once('template/footer.php');
  echo $globalCont;
?>