<?
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');
$globalCont .= <<<HTML
      </div>
  </main>

  <!-- Popup START -->
    $popups
  <!-- Popup END -->

  <footer class="l-footer">
      <div class="company-data">
          <span class="company-data__item">+7 843 233-00-00</span>
          <span class="company-data__item">info@lprojects.ru</span>
          <span class="company-data__item">(c) 2013 L-Projects</span>
      </div>
  </footer>

  <!-- Preloader START -->
  <div id="js-preloader_global" class="preloader preloader_global"><div class="preloader__icon"></div></div>
  <!-- Preloader END -->

  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="$config[TEMPLATE_DIR]/js/jquery.2.1.0.min.js"><\/script>')</script>
  <script src="$config[TEMPLATE_DIR]/js/tools/other.min.js"></script>
  <script src="$config[TEMPLATE_DIR]/js/main.min.js"></script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-50890131-1', 'lprojects.ru');
	  ga('send', 'pageview');
	</script>
  $page[AddJS]
</body>
</html>
HTML;
?>