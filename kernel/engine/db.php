<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
class db
{
    /**#@+
     * @access private
     * @var boolean
     */
    var $conid = null;
    var $queryres = null;
    /**#@+
     * @access public
     * @var integer
     */
    var $totaltime = 0;
    var $sqlcount = 0;
    /**#@+
     * @access public
     * @var string
     */
    var $errormsg = '';
    var $explain = '';
    /**
     * Функция для создания временных меток
     */
    function dbtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
    /**
     * Функция соединения с MySQL сервером и базой данных
     */
    function db($server, $user, $password, $database)
    {
    $start = $this->dbtime();
    if ($this->conid == 0) {
        if (empty($password)) {
            $this->conid = @mysql_connect($server, $user);
        } else {
            $this->conid = @mysql_connect($server, $user, $password);
        }
        if ($this->conid) {
            if ($database) {
                if (@mysql_select_db($database, $this->conid)) {
					@mysql_query('SET NAMES utf-8');
                    $this->totaltime += sprintf('%.5f', $this->dbtime() - $start);
                } else {
                    $this->errormsg = 'Not selected DB ! Не возможно выбрать БД !';
                    $this->error();
                }
            }
        } else {
            $this->errormsg = 'Not connect server ! Нет соединения с базой данных !';
            $this->error();
        }
    }
    }
    /**
     * Функция экранирования данных
     */
    function escape($query)
    {
        return @mysql_escape_string($query);
    }
    /**
     * Функция выполнения MySQL запросов
     */
    function query($query = '')
    {
        global $conf;
        $this->queryres = '';
        $start = $this->dbtime();
        if ($query != '') {
            $this->queryres = @mysql_query($query,$this->conid);
        }
        if ($this->queryres) {
            $this->totaltime += sprintf('%.5f', $this->dbtime() - $start);
            $this->explain.= '<li>EXPLAIN ' . $query . ' ' . sprintf('%.5f', $this->dbtime() - $start) . '</li>';
            ++$this->sqlcount;
            return $this->queryres;
        } else {
            $this->errormsg = $query;
            $this->error();
        }
    }

    function select($database = '', $display = 1)
    {
        $this->works = 0;
        if ($database != '') $this->database = $database;
        if (!mysql_select_db($this->database,$this->conid)) {
            if ($display == 1) {
                $this->errormsg = 'Not connect server ! Нет соединения с базой данных !';
                $this->error();
            }
        } else {
            $this->works = 1;
        }
    }
    /**
     * Функция возвращает количество рядов результата запроса
     */
    function numrows($qid = 0)
    {
        return @mysql_num_rows($qid);
    }
    /**
     * Функция обрабатывает ряды результата запроса, возвращая ассоциативный массив, численный массив или оба
     */
    function fetchrow($qid = 0)
    {
        return @mysql_fetch_array($qid);
    }
    /**
     * Функция возвращает количество полей результата запроса
     */
    function numfields($qid = 0)
    {
        return @mysql_num_fields($qid);
    }
    /**
     * Функция возвращает название колонки с указанным индексом
     */
    function fieldname($offset,$qid = 0)
    {
        return @mysql_field_name($qid,$offset);
    }
    /**
     * Функция возвращает ID, сгенерированный колонкой с AUTO_INCREMENT последним запросом INSERT к серверу
     */
    function insertid()
    {
        return @mysql_insert_id($this->conid);
    }
    /**
     * Функция возвращает количество рядов, задействованных в последнем запросе INSERT, UPDATE или DELETE
     */
    function affrows()
    {
        return @mysql_affected_rows($this->conid);
    }
    /**
     * Функция возвращает текст сообщения об ошибке предыдущей операции с MySQL
     */
    function geterrdesc()
    {
        $this->error = mysql_error();
        return $this->error;
    }
    /**
     * Функция Возвращает численный код ошибки выполнения последней операции с MySQL
     */
    function geterrno()
    {
        $this->errno = mysql_errno();
        return $this->errno;
    }
    /**
     * Функция выводит HTML-визуализацию ошибки при операциях с MySQL
     */
    function error()
    {
        global $conf;
        $langcharset = (isset($conf['langcharset'])) ? $conf['langcharset'] : 'windows-1251';
        echo '<HTML>'
             .'<HEAD>'
             .'<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset='.$langcharset.'">'
             .'<TITLE>MySQL Debugging '.$conf['version'].'</TITLE>'
             .'</HEAD>'
             .'<div align="left" style="border:1px solid #999; font-size:11px; font-family:tahoma,verdana,arial; background-color:#f3f3f3; color:#A73C3C; margin:5px; padding:5px;">'
             .'<div align="left" style="border:1px solid #999; font-size:11px; background-color:#f9f9f9; color:#666; margin:0px; padding:5px;">'
             .'<strong>MySQL Debugging '.$conf['version'].'</strong></div><br />'
             .'<li><b>SQL.q :</b> <div style="color:#888;">' . $this->errormsg . '</div></li>'
             .'<li><b>MySQL.e :</b> <div style="color:#888;">' . $this->geterrdesc() . '</div></li>'
             .'<li><b>MySQL.e.№ :</b> <div style="color:#888;">' . $this->geterrno() . '</div></li>'
             .'<li><b>PHP.v :</b> <div style="color:#888;">' . phpversion() . '</div></li>'
             .'<li><b>Data :</b> <div style="color:#888;">' . date("d.m.Y H:i") . '</div></li>'
             .'<li><b>Script :</b> <div style="color:#888;">' . getenv("REQUEST_URI") . '</div></li>'
             .'<li><b>Refer :</b> <div style="color:#888;">' . getenv("HTTP_REFERER") . '</li></div>'
             .'</BODY>'
             .'</HTML>';
       exit();
    }
    /**
     * Функция закрывает соединение с сервером MySQL
     */
    function close()
    {
        if ($this->conid) {
            if ($this->queryres) {
                @mysql_free_result($this->queryres);
            }
            $result = @mysql_close($this->conid);
            return $result;
        }
    }
    /**
     * Функция возвращает структуру дампа
     */
    function structure($name, $drop = 0)
    {
        $field = $keys = array();
        $res = $this->query("SHOW FIELDS FROM ".$name);
        //$code = $this->fetchrow($this->query("SELECT CHARSET('".$name."')"));
        $r = "";
        $r.= ($drop) ? "DROP TABLE IF EXISTS `".$name."`;\n" : "";
        $r.= "CREATE TABLE IF NOT EXISTS `".$name."` (\n";
        while ($row = $this->fetchrow($res)) {
          $null = ($row['Null'] == 'NO') ? " NOT NULL" : " NULL";
          $def = (empty($row['Default'])) ? "" : " DEFAULT '".$row['Default']."'";
          $extra = (empty($row['Extra'])) ? "" : " ".$row['Extra'];
          $field[] = "`".$row['Field']."` ".$row['Type'].$null.$def.$extra;
        }
        $r.= implode(",\n",$field);
        $res = $this->query("SHOW KEYS FROM ".$name);
        while ($row = $this->fetchrow($res)) {
            if ($row['Non_unique'] == 0 && $row['Key_name'] == 'PRIMARY') {
                $keys[] = "PRIMARY KEY (`".$row['Column_name']."`)";
            } else {
            	$keys[] = "KEY `".$row['Key_name']."` (`".$row['Column_name']."`)";
            }
        }
        $r.= ",\n".implode(",\n",$keys);
        $r.= "\n) ENGINE=MyISAM;\n";
        return $r;
    }

    function field($name)
    {
        $field = array();
        $res = $this->query("SHOW FIELDS FROM ".$name);
        while ($row = $this->fetchrow($res)) {
             $field[] = $row['Field'];
        }
        return $field;
    }

    function download($file, $data, $type = '')
    {
        if (empty($type)) {
            if (preg_match("/Opera\/[0-9]\.[0-9]{1,2}/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/MSIE [0-9]\.[0-9]{1,2}/", $_SERVER['HTTP_USER_AGENT'])) {
            	$type = 'application/octetstream';
            } else {
            	$type = 'application/octet-stream';
            }
        }
        header('Content-Type: '.$type);
        header('Expires: '.gmdate('D, d M Y H:i').' GMT');
        header('Content-Disposition: attachment; filename="'.$data.'"');
        header('Content-Length: '.strlen($file));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        print $file;
        exit();
    }
}
/* ==================================================== ##
## END CLASS DB                                         ##
## ==================================================== */
/* ==================================================== ##
## $db - DECLARE CONNECT                                ##
## ==================================================== */
$db = new db($config['DbServer'],$config['DbUser'],$config['DbPassword'],$config['DbBase']);
?>