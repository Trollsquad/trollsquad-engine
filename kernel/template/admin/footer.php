<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
$globalCont .= <<<HTML
      </div>
    </section>
    <footer class="main">
    	<div class="warp">
        Content Management System <strong>Isotheos Engine</strong> on <a href="http://lprojects.ru">L-Projects</a>
      </div>
    </footer>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript">window.jQuery || document.write('<script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/jquery-1.9.0.min.js"><\/script>')</script>
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/jQuery.plugins.js"></script>
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/jScrollPane.js"></script>
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/cusel-min-2.5.js"></script>
    <script type="text/javascript" src="$config[ADMIN_TEMPLATE_DIR]/js/general.js"></script>
    $page[AddJS]
  </body>
</html>
HTML;
?>