<?php

/**
 * File containing code to show the result of thesaurus search
 */

if(isset($_GET['preview'])) 
{
    $preview =true;
	$installprefix="sample.";
	require_once('previewsampledata.php');
}
else
{
	$preview =false;
	$installprefix="";
}
require_once('data/'.$installprefix.'template.inc.php');
$title="Thesaurus Search";
require_once('data/'.$installprefix.'header.inc.php');
require_once('data/'.$installprefix.'config.inc.php');
if(isset($_GET['word']))
{
	$word=$_GET['word'];
    $url="http://wordnetweb.princeton.edu/perl/webwn?c=1&sub=Change&o2=&o0=&o7=&o5=&o1=1&o6=&o4=&o3=&i=-1&h=00&s=$word";
}
else
	$url="http://wordnetweb.princeton.edu/perl/webwn?c=1&sub=Change&o2=&o0=&o7=&o5=&o1=1&o6=&o4=&o3=&i=-1&h=00&s=test";

if(isset($_GET['version']))
{
	$version=$_GET['version'];
}else
{
	$version = "kjv";
}
echo $template['thesaurus']['StartHTML'];
if(!$preview)
{
	$txt = file_get_contents($url);
	if($txt!==false)
	{
		$pattern2="/<li>([^`]*?)<\/li>/";
		$result=preg_match_all($pattern2,$txt,$yourpost);
		foreach($yourpost[0] as $newtxt)
		{
			if(strpos($newtxt,"S:")!==false)
			{
				$newtxt=strip_tags($newtxt);
				$newtxt=html_entity_decode($newtxt);
				$synonymsarr[]=explode(",",substr($newtxt,strpos($newtxt,")")+strlen(")")));
			}
		}
		if(!isset($synonymsarr[]))
		{
			echo "<br><br><br>Could not extract thesaurus data probably Server down";
			require_once('data/'.$installprefix.'footer.inc.php');
			exit;

		}
	}
	else
	{
		echo "<br><br><br>Could not extract thesaurus data probably not connected to internet";
		require_once('data/'.$installprefix.'footer.inc.php');
		exit;
	}
}
else
{
	$synonymsarr = $sampleSynonymsArray;
}
echo $template['thesaurus']['Word']['StartHTML'];
eval("echo \"".$template['thesaurus']['Word']['ProcessHTML']."\";");
foreach($synonymsarr as $synonyms)
{
	$template['thesaurus']['SynonymsArray']['StartHTML'];
	echo "<b>Synonyms</b>";
    foreach($synonyms as $synonymsentry)
    {
		eval("echo \"".$template['thesaurus']['Synonyms']['ProcessHTML']."\";");
    }
	echo "<br>";
	$template['thesaurus']['SynonymsArray']['EndHTML'];
}
echo $template['thesaurus']['Word']['EndHTML'];
echo $template['thesaurus']['EndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');

?>