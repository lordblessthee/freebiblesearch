<?php 
require_once('data/template.inc.php');
require_once("data/header.inc.php");
require_once('data/config.inc.php');
if(isset($_GET['word']))
{
	$word=$_GET['word'];
$url="http://thesaurus.reference.com/browse/$word";
}
else
	$url="http://thesaurus.reference.com/browse/test";

if(isset($_GET['version']))
{
	$version=$_GET['version'];
}else
{
	$version = "kjv";
}
echo $template['thesaurus']['StartHTML'];
//$txt=file_get_contents_proxy($url);
//$txt = file_get_contents($url);
$txt=file_get_contents("thesaurustest.txt");
$pattern2="/<tr>([^`]*?)<\/tr>/";
$result=preg_match_all($pattern2,$txt,$yourpost);
foreach($yourpost[0] as $newtxt)
{
	if(strpos($newtxt,"Synonyms:")!==false)
	{
		$newtxt=strip_tags($newtxt);
		$newtxt=html_entity_decode($newtxt);
		$synonymsarr[]=explode(",",substr($newtxt,strpos($newtxt,"Synonyms:")+strlen("Synonyms:")));
	}
}

foreach($synonymsarr as $synonyms)
{
	$template['thesaurus']['SynonymsArray']['StartHTML'];
	echo "<b>Synonyms</b>";
    foreach($synonyms as $synonymsentry)
    {
		eval("echo \"".$template['thesaurus']['Synonyms']['ProcessHTML']."\";");
      /**echo " <font color='green'><b> <a href='"."testSimulator.php?bibleVersion=".$version."&search=".trim($synonymsentry)."' target='_blank' style='color:green;text-decoration:none'> ".$synonymsentry."&nbsp"." </a> </b></font>"; **/
    }
	echo "<br>";
	$template['thesaurus']['SynonymsArray']['EndHTML'];
}

echo $template['thesaurus']['EndHTML'];//}

function file_get_contents_proxy($url)
{
$proxies = "150.236.18.16:8080";
$pCurl = curl_init();
curl_setopt ($pCurl, CURLOPT_URL, $url);
curl_setopt($pCurl, CURLOPT_PROXY, $proxies);
curl_setopt($pCurl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($pCurl, CURLOPT_RETURNTRANSFER, true);
$file_contents=curl_exec($pCurl);
curl_close($pCurl);
return $file_contents;
}
?>