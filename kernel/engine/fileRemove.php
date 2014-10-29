<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

$result = $db->query("SELECT `dir`,`name`,`ext` FROM `fp_files` WHERE `delete_date` > 0 AND `delete_date` < '".time()."';");
while ($row = $db->fetchrow($result)) {
	unlink($config['SITE_DIR'].'/f/'.$row['dir'].'/'.$row['name'].$row['ext']);
	unlink($config['SITE_DIR'].'/m/'.$row['dir'].'/'.$row['name'].'.jpg');
	$db->query("DELETE FROM `fp_files` WHERE `name`='".$row['name']."' AND `dir`='".$row['dir']."';");
}

?>