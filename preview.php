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
{ /**
    if(!strcmp($dispArea,"left")) 
    { 
        echo "<body bgcolor='#D0DEED'>\r\n"; 
        echo "<font face='Arial,Verdana,Helvetica' color='FF0000' size='3'>PHP Tester</font>\r\n"; 
        echo     "<form method='post' action='".$_SERVER['PHP_SELF']."?dispArea=right' target='theExec'>\r\n"; 
        echo     "<table>\r\n"; 
        echo         "<tr><td align='center'>\r\n"; 
        echo             "<input type='submit' value='Execute'>\r\n"; 
        echo         "</td></tr>\r\n"; 
        echo         "<tr><td>\r\n"; 
        echo             "<textarea name='theCode' cols='65' rows='25' wrap='virtual'>\r\n"; 
        echo                 $theCode."\r\n"; 
        echo             "</textarea>\r\n"; 
        echo         "</td></tr>\r\n"; 
        echo     "</table>\r\n"; 
        echo     "</form>\r\n"; 
    } 
    else if(!strcmp($dispArea,"right")) 
    { 
        echo "<body bgcolor='#FFFFFF'>\r\n"; 
        if(empty($theCode)) 
        { 
            echo "Ready to parse..."; 
        } 
        else 
        { 
            $theCode=ltrim(rtrim(stripSlashes($theCode))); 
            if(!strncmp($theCode,"<?",2)) //if it's full php, remove the tags 
            { 
                if(!strncmp($theCode,"<?php",5)) 
                { 
                    $theCode=substr($theCode,5); 
                } 
                else 
                { 
                    $theCode=substr($theCode,2); 
                } 
                $theCode=substr($theCode,0,strlen($theCode)-2); 
                $theCode=ltrim(rtrim(stripSlashes($theCode))); 
            } 
            eval(stripslashes($theCode)); 
        } 
    } **/
	echo "<html>\n<body>\n";
	echo "<BR><BR><BR>";
	echo "<b><font size=3><a href='result.php?search=eternal%20life&preview' target='thePage' >Search Result</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='search.php?preview' target='thePage' >Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='advancedsearch.php?preview' target='thePage' >Advanced Search Form</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?preview' target='thePage' >Bible Version Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?version=kjv&preview' target='thePage' >Bible Book Index</a></font></b><br><br><br>\n";
	echo "<b><font size=3><a href='readBible.php?version=kjv&book=Psalms&chapter=23&preview' target='thePage' >Bible Verses</a></font></b><br><br><br>\n";
    echo "</body></html>"; 
} 
?>