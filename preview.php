<?php

/**
 * File containing code to preview sample pages
 */

require_once('installfunctions.php');
require_once('previewsampledata.php');
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
	echo "<html>\n";
	echo "<title>Preview Template $selectedTemplate</title>\n";
    echo "<frameset cols='20%,80%'>\r\n"; 
    echo     "<frame name='theLink' src='preview.php?showlinks'>\r\n"; 
    echo     "<frame name='thePage' src='result.php?search=eternal%20life&preview'>\r\n"; 
    echo "</frameset>\r\n";
	echo "</html>"; 
} 
else 
{  
		$bibShortnameFunc = function($bibVersion) {
    return $bibVersion['shortname'];
    };
    $version=implode("|",array_map($bibShortnameFunc,$sampleBibleVersion));
	echo "<html>\n";
	echo "<title>Preview Template $selectedTemplate</title>\n";
	echo"<body>\n";
	echo "<BR><BR><BR>";
	echo "<b><font size=3><a href='result.php?search=eternal%20life&preview' target='thePage' >Search Result</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='search.php?preview' target='thePage' >Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='advancedsearch.php?preview' target='thePage' >Advanced Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='passagelookup.php?preview' target='thePage' >Passage Lookup Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='passageresult.php?preview' target='thePage' >Passage Lookup Result</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='thesaurus.php?word=eternal%20life&preview&version=$version' target='thePage' >Thesaurus Search</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readbible.php?preview' target='thePage' >Bible Version Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readbible.php?version=kjv&preview' target='thePage' >Bible Book Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readbible.php?version=kjv&book=Psalms&chapter=23&preview' target='thePage' >Bible Verses</a></font></b><br><br><br>\n";
    echo "</body></html>"; 
} 
?>