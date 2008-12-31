<?php
   //$content = htmlentities(file_get_contents("default.samplepage.php"));
   if(isset($_GET['template']))
   {
	   $template = $_GET['template'];
   }
   else
   {
	   echo "template not defined";
	   exit;
   }
   $content = file_get_contents("template/default.samplepage.php");
   $content = str_replace('<%templatecommon%>', 'require_once(\'template/'.$template.'/default.template.inc.php\');', $content);
   eval('?>'.$content);
   //
	//echo $content;


