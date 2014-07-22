<?php
$userSettings = class_settings::getInstance();
$context = class_context::getInstance();
// get the user country-language
$lang=$keys->getLang();
// extract the language
$lang=explode('-', $lang);
$lang = $lang[0];
// get the username if logged in
$title = '';
if($context->isUserLoggedIn()) {
    $title = ' - '.$userSettings->getUsername();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang;?>" lang="<?php echo $lang;?>">
<head>
  <title><?php echo $keys->appTitle; echo $title; ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<style type="text/css" media="screen"> /*<![CDATA[*/ <!--
    @import url(style/default.css?ver=<?php echo CB_VERSION;?>);
    @import url(style/layout.css?ver=<?php echo CB_VERSION;?>);
    @import url(style/tables.css?ver=<?php echo CB_VERSION;?>);
    --> /*]]>*/
  </style>
  <link type="text/css" href="style/css/ui-darkness/jquery-ui-1.7.1.custom.css" rel="stylesheet" />
  <style type="text/css" media="print"> /*<![CDATA[*/ <!--
    @import url(style/budget_print.css?ver=<?php echo CB_VERSION;?>);
    @import url(style/tables_print.css?ver=<?php echo CB_VERSION;?>);
    --> /*]]>*/
  </style>
  <link rel="shortcut icon" href="favicon.ico"/>
  <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript" src="js/jquery.tablesorter.pager.js"></script>
  <script type="text/javascript" src="js/cookie.js"></script>
  <script type="text/javascript" src="js/ui.core.js"></script>
  <script type="text/javascript" src="js/ui.datepicker.js"></script>
  <script src="http://simile.mit.edu/timeline/api/timeline-api.js" type="text/javascript"></script>
	<?php
	if($lang != 'en') echo '<script type="text/javascript" src="js/ui.datepicker-'.$lang.'.js"></script>';
  ?>
	<script type="text/javascript" src="js/ajaxfileupload.js"></script>
  <script type="text/javascript">
  eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(1($){$.9.D=$.9.g=1(b){b=b||p;i=1(a){j(a);a.4=$("r",a);a.4.q(":o(0)").l().C();a.5=0;h(a)};h=1(a){a.m=t(1(){f(a)},b)};j=1(a){s(a.m)};8=1(a){a.3=7};d=1(a){a.3=c};f=1(a){e(a.3)6;a.3=7;$(a.4[a.5]).n("k",1(){$(2).l();a.5=++a.5%(a.4.B());$(a.4[a.5]).z("k",1(){a.3=c})})};2.y(1(){e(2.x.w()!="A")6;i(2)}).v("g").u(1(){8(2)},1(){d(2)});6 2}})(E);',41,41,'|function|this|pause|items|currentitem|return|true|pauseTicker|fn|||false|resumeTicker|if|doTick|newsticker|startTicker|initTicker|stopTicker|slow|hide|tickfn|fadeOut|eq|4000|not|li|clearInterval|setInterval|hover|addClass|toLowerCase|nodeName|each|fadeIn|ul|size|end|newsTicker|jQuery'.split('|'),0,{}))
  </script>
  <script type="text/javascript" src="js/process.<?php echo CB_VERSION;?>.pack.js"></script>
</head>
<body id="subpage" class="bgFrame12">
<div id="bar">
	<div id="topmenu">
    <ul style="float: none;">
      <li><a class="link1" href="#home"><img src="style/logoSmall2.png"/></a></li>
      <li><a class="link1" href="#home"><img src="style/icons/chart_pie.png" /> <?php echo $keys->link_overview; ?></a></li>
      <li><a class="link1" href="#wizard"><img src="style/icons/transmit_edit.png" /> <?php echo $keys->link_menuWizard; ?></a></li>
      <li><a class="link1" href="#actions"><img src="style/icons/wrench.png" /> <?php echo $keys->link_menuActions; ?></a></li>
      <li><a class="link1" href="#search" onClick="searchFormSubmit()"  title="<?php echo $keys->text_Search; ?>"><img src="style/icons/zoom.png" id="searchIcon" alt="<?php echo $keys->text_Search; ?>"/> <input size="5" alt="search..." type="text" name="search" id="keys" onkeyup="searchFormSubmit()"/></a></li>
      <li><a class="link1" href="#share"><img src="style/icons/email_link.png" id="shareIcon"/> <?php echo $keys->text_Share; ?></a></li>
      <li><a class="link1" href="#settings"><img src="style/icons/user_edit.png" /> <?php echo $keys->link_pageSettings; ?></a></li>
      <?php
      if($userSettings->secureAccess() === '1') {
      ?>
      <li><a class="link1" href="#login"><img src="style/icons/lock.png" /> <?php echo $keys->link_logout; ?></a></li>
      <?php } ?>
      
      <li class="last"><a class="link1"><img src="style/icons/accept.png" width="19" height="19" id="loading"/></a></li>
    </ul>
  </div>
</div>
<div id="content">
  <div id="main"><?php echo $this->output; ?></div>
</div>
<div id="footer" class="bg2">
	<p class="text6"><a href="http://www.clearbudget.net/contact.html" target="_blank"><?php echo $keys->link_contact; ?></a>
   | <a href="http://www.clearbudget.net/?checkVersion&version=<?php echo CB_VERSION;?>" target="_blank"><?php echo $keys->link_checkPreviousVersion ?></a>
    | <a href="#credits"><?php echo $keys->link_credits ?></a>
    | <a class="donate" href="http://www.clearbudget.net/Donate-to-the-clearBudget-team.html">Donate</a>
  | ClearBudget <?php echo CB_VERSION.' ('.CB_RELEASE; ?>)</p>
</div>
</body>
</html>