<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Preview Template(s)</title>
</head>

<body>
<h2>Preview Template(s)</h2>
<table border=1>

<?php

echo "<tr><td><font color=green><b>Preview Template</b>(Click on the screenshot to see a preview )</font><br></td></tr><br>";
	$template_directory = "./template";
	$template = opendir($template_directory) or die("fail to open");
	$count=0;
	echo "<tr><td><div style=\"overflow:auto; height:300px; width:700px;
		border: 1px solid #666;
		background-color: #ccc;
		padding: 8px;\">";
		echo "<table border=1>";
	while(!(($design = readdir($template))===false))
	{
		if (($design !='.')&&($design!='..')&&(
		 file_exists("$template_directory/".$design."/default.template.inc.php")))
		{
			$count++;
			if($count%2!=0)
			{
				echo "<tr></tr><tr>";
			}
			echo "<td><center>$design<br><a href=\"preview.php?template=$design\" target=new ><img src =\"template/$design/screenshot.jpg\" height=194 width=308></a><br><!--<input type=\"radio\" name=\"selectTemplate\" value=\"$design\" $checked >--></center>";
			if($count%2==0)
			{
				echo "</td></tr>";
			}
			else
			{
				echo "<td>&nbsp;&nbsp;</td></td>";
			}
		}
		
	}
echo "</table></div></td></tr>";
	closedir($template);
echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
echo "</table>\n";

echo "</body>\n";
echo "</html>\n";




?>