<?php 
require_once('installfunctions.php');
if(isset($_REQUEST['showlinks']))
{
	$showlinks=true;
}
else
{
	$showlinks=false;
} 
if(!$showlinks) 
{
	if(isset($_GET['template']))
	{
		$selectedTemplate=$_GET['template'];
	}
	else
	{
		echo "Template not given in url";
		exit;
	}
	writeConfigFile($configVars,"sample.");
	writeHeaderFooterFile("sample.");
	writeTemplateFile($selectedTemplate,"sample.");
    echo "<frameset cols='20%,80%'>\r\n"; 
    echo     "<frame name='theLink' src='preview.php?showlinks'>\r\n"; 
    echo     "<frame name='thePage' src='result.php?search=eternal%20life&preview'>\r\n"; 
    echo "</frameset>\r\n"; 
} 
else 
{  
	echo "<html>\n<body>\n";
	echo "<BR><BR><BR>";
	echo "<b><font size=3><a href='result.php?search=eternal%20life&preview' target='thePage' >Search Result</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='search.php?preview' target='thePage' >Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='advancedsearch.php?preview' target='thePage' >Advanced Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='thesaurus.php?preview' target='thePage' >Thesaurus Search</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?preview' target='thePage' >Bible Version Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?version=kjv&preview' target='thePage' >Bible Book Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?version=kjv&book=Psalms&chapter=23&preview' target='thePage' >Bible Verses</a></font></b><br><br><br>\n";
    echo "</body></html>"; 
} 
?>