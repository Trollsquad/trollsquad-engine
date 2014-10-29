<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

function smsSend($num,$text,$type='text',$tts=0,$translit=0,$sender=false,$id=0,$double=0) {
  global $config;
	$num = str_replace(array('(',')','-',chr(32)),'',$num);
	$number = (!is_array($num)) ? $num : implode(', ',$num);
  include_once($config['SITE_DIR'].'/engine/smsc_api.php');
  $result = send_sms($number, $text, $translit, $tts, $id, false, $sender);
  if ($result[1] == '-6') {
    $text.=chr(160);
    unset ($result);
  }
	if (!isset($result)) {
		if ($double == 0)  {
			if (smsSend($num,$text,'','','',false,'',1)) return true;
		}
		else return false;
	}
	else { return $result; }
}
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;  
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];  
    }
    return $code;
}
function checkPassword($oldhash, $hash,$password) {
    $new_hash = hashPassword($hash,$password);
    return ($oldhash == $new_hash);
}
function hashPassword($hash,$password) {
	$full_salt = substr('$2a$10$'.$hash,0,29);
	return crypt($password, $full_salt);
}
function getNumCode($length=6) {
    $code = "";
    while (strlen($code) < $length) {
      $code .= mt_rand(0,9);  
    }
    return $code;
}
function clear($text,$act) {
	switch($act) {
		case 'html':
		$result = htmlspecialchars($text);
		break;
	}
	return $result;
}
function imageResize($src, $width, $height,$dest, $quality=100) {
  if(!file_exists($src)) return 1; // исходный файля не найден
  $size=getimagesize($src);
  if($size===false) return 2; // не удалось получить параметры файла

  // Определяем исходный формат по MIME-информации и выбираем соответствующую imagecreatefrom-функцию.
  $format=strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc="imagecreatefrom".$format;
  if(!function_exists($icfunc)) return 3; // не существует подходящей функции преобразования

  // Определяем необходимость преобразования размера так чтоб вписывалась наименьшая сторона
  if( $width<$size[0] || $height<$size[1] )
    $ratio = max($width/$size[0],$height/$size[1]);
  else
    $ratio=1;
  
  if($width/$size[0] > $height/$size[1]) { // срезать верх и низ
    $dx = 0 ;
    $dy = floor((($size[1] - $height) * $ratio) / 2) ; // отступ сверху
  }
  else { // срезать справа и слева
    $dx = floor((($size[0] - $width) * $ratio) / 2) ; // отступ слева
    $dy = 0 ;
  }
  // скока пикселов считывать с источника
  $wsrc = floor($width/$ratio) ;  // по ширине
  $hsrc = floor($height/$ratio) ; // по высоте

  $isrc=$icfunc($src);
  $idest=imagecreatetruecolor($width,$height);

  imagecopyresampled($idest, $isrc, 0, 0, $dx, $dy, $width, $height, $wsrc, $hsrc);
  imagejpeg($idest,$dest,$quality);
  chmod($dest,0666);
  imagedestroy($isrc);
  imagedestroy($idest);
  return $dest; // успешно
}

function img_resize_clip($src, $width, $height,$dest, $quality=100) {
  if(!file_exists($src)) return 1; // исходный файля не найден
  $size=getimagesize($src);
  if($size===false) return 2; // не удалось получить параметры файла

  // Определяем исходный формат по MIME-информации и выбираем соответствующую imagecreatefrom-функцию.
  $format=strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc="imagecreatefrom".$format;
  if(!function_exists($icfunc)) return 3; // не существует подходящей функции преобразования

  // Определяем необходимость преобразования размера так чтоб вписывалась наименьшая сторона
  if( $width<$size[0] || $height<$size[1] )
    $ratio = max($width/$size[0],$height/$size[1]);
  else
    $ratio=1;
  
  if($width/$size[0] > $height/$size[1]) { // срезать верх и низ
    $dx = 0 ;
    $dy = floor((($size[1] - $height) * $ratio) / 2) ; // отступ сверху
  }
  else { // срезать справа и слева
    $dx = floor((($size[0] - $width) * $ratio) / 2) ; // отступ слева
    $dy = 0 ;
  }
  // скока пикселов считывать с источника
  $wsrc = floor($width/$ratio) ;  // по ширине
  $hsrc = floor($height/$ratio) ; // по высоте

  $isrc=$icfunc($src);
  $idest=imagecreatetruecolor($width,$height);

  imagecopyresampled($idest, $isrc, 0, 0, $dx, $dy, $width, $height, $wsrc, $hsrc);
  imagejpeg($idest,$dest,$quality);
  //chmod($dest,0666);
  imagedestroy($isrc);
  imagedestroy($idest);
  return $dest; // успешно
}
function rusmonth($date) {
	$rus = array(
		'1' => 'янв',
		'2' => 'фев',
		'3' => 'мар',
		'4' => 'апр',
		'5' => 'мар',
		'6' => 'июн',
		'7' => 'июл',
		'8' => 'авг',
		'9' => 'сен',
		'10' => 'окт',
		'11' => 'ноя',
		'12' => 'дек'
	);
	return $rus[intval($date)];
}
function clearPhone($text) {
  return str_replace(array('+',' '),'',$text);
}
?>