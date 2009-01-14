<?php

/**
 * File containing code to show the search result
 */

$startTime = time();
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
require_once('data/'.$installprefix.'config.inc.php');
$title="Search Result";
require_once('data/'.$installprefix.'header.inc.php');
echo $template['searchResult']['StartHTML'];
require_once('ClassGrepSearch.inc.php');
require_once('functions.php');
$classGrepSearch = ClassGrepSearch::getInstance();

$filesWithExtentionsToBeSearched = "txt"; 


if(isset($_GET['path'])) {
    $scan_dir = $_GET['path'];
}
else
{
   if(isset($_POST['bibleVersion'])) 
	{
	    $version = $_POST['bibleVersion'];
		$scan_dir=$bibleDatabase.$_POST['bibleVersion']."db/";
		$databasetable = "bibledb_".$_POST['bibleVersion'];
	}
	else
	{
		if(isset($_GET['bibleVersion'])) 
		{
			$version = $_GET['bibleVersion'];
			$scan_dir=$bibleDatabase.$_GET['bibleVersion']."db/";
			$databasetable = "bibledb_".$_GET['bibleVersion'];
		}
		else
		{
			if(!preview)
			{
				echo "<br><br><br>Bible version not specified cannot continue";
				require_once('data/'.$installprefix.'footer.inc.php');
				exit;
			}
		}
	}
}


if(isset($_POST['search'])) {
    $searchString = $_POST['search'];
}
else {
	if(isset($_GET['search'])) {
		$searchString = $_GET['search'];
		$limit="none";
		$searchType="all";
		$start_span="1";
		$end_span="66";
	}
	else
	{
		$searchString = 'Jesus';
	}
}

if(isset($_POST['spanbegin'])) {
	
    	$start_span = $_POST['spanbegin'];
}

if(isset($_POST['spanend'])) {
    $end_span = $_POST['spanend'];
}
if(isset($_POST['limit'])) {
    $limit = $_POST['limit'];
}
if(isset($_POST['bookset'])) {
    $bookset = $_POST['bookset'];
}
if(isset($_POST['searchtype'])) {
    $searchType = $_POST['searchtype'];
}

if(isset($_POST['casesensitive'])) {
    $caseSensitive = $_POST['casesensitive'];
}



// creates an array of all the provided extentions
$classGrepSearch->createArrayOfExtentions(",",$filesWithExtentionsToBeSearched);
$classGrepSearch->setSearchType($searchType);
$classGrepSearch->setSearchString($searchString);
$classGrepSearch->setScanDir($scan_dir);
$classGrepSearch->setCaseSensitive(($caseSensitive=="yes")?true:false);


$regex ='/([a-zA-Z]+)/';
$replace="$1,";
$keyWordList = substr(preg_replace( $regex, $replace, $searchString),0,-1);

if($preview == true)
{
	executeFromSample($classGrepSearch);
}
else
	if($databaseType == "DB")
	{
		$databaseInfo['databasehost'] = $dbHost;
		$databaseInfo['databasename'] = $dbName;
		$databaseInfo['tableprefix'] = $dbTablePrefix;
		$databaseInfo['databaseusername'] =$dbUser;
		$databaseInfo['databasepassword'] = $dbPassword;
		$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
		executeFromDB($classGrepSearch,$databaseInfo);

	}
	else
		if($databaseType == "FILE")
		{
			executeFromFile($classGrepSearch);
		}



//calulate the time (in seconds) to execute this script
$endTime = time();
$totalTime = ($endTime - $startTime);

// total time taken to execute this script
$timeTaken = $classGrepSearch->convertSecToMins($totalTime);

echo "<center><hr>Total time taken: <font color='blue'> $timeTaken </font></hr></center>";
echo $template['searchResult']['EndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');


/**
 *
 * function to create HTML from database
 *
* @param $classGrepSearch class Instance
 * @param $databaseInfo array
 *
 */  

function executeFromDB($classGrepSearch,$databaseInfo)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$keyWordList,
		$template;

	$classGrepSearch->createSearchArray($searchString);
	$keyWordArray=explode(",",$keyWordList);
	echo $template['searchResult']['KeywordList']['StartHTML'];
	foreach($keyWordArray as $keyword)
	{
		eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
	}
	echo $template['searchResult']['KeywordList']['EndHTML'];
	$htmllines = createLinesFromDB($databaseInfo,$classGrepSearch);
	echo "<BR>".$htmllines;

}

/**
*
* function to create HTML from Files
*
*  @param $classGrepSearch class Instance
*
*/  

function executeFromFile($classGrepSearch)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$keyWordList,
		$template,$version;
if($limit=="bookset")
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookCategory(getCategoryName($bookset)),false);
}
else
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookNames($start_span,$end_span),false);
}



$keyWordArray=explode(",",$keyWordList);
echo $template['searchResult']['KeywordList']['StartHTML'];
foreach($keyWordArray as $keyword)
{
	eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
}
echo $template['searchResult']['KeywordList']['EndHTML'];
$arrayOfFilenames = $classGrepSearch->getarrayOfFilenames();
$bookName="";
$previousBookName="";
$prevChapterNo=0;
$bookFirsttime=true;
for($i=0,$j=0;$i<sizeof($arrayOfFilenames);$i++) {
    $fileName = str_replace($_SERVER['DOCUMENT_ROOT'],"",$arrayOfFilenames[$i]);
    $linkName = str_replace($_SERVER['DOCUMENT_ROOT'],"Z:",$arrayOfFilenames[$i]);
	$ChapterNo = (int)substr($fileName,-7,3);
	$bookNameStart=strlen($fileName)-strrpos($fileName,"/");
	$bookName = substr($fileName,-$bookNameStart+1,$bookNameStart-8);
    $classGrepSearch->setGlobalCount(0);
      $htmllines = createLinesFromFile($scan_dir.$fileName,$classGrepSearch);
	if($htmllines !="")
	{
		if($previousBookName!=$bookName)
		{
			if($bookFirsttime)
			{
				echo $template['searchResult']['Book']['StartHTML'];
				eval("echo \"".$template['searchResult']['Book']['ProcessHTML']."\";");
				echo	$template['searchResult']['Chapter']['StartHTML'];
				eval("echo \"".$template['searchResult']['Chapter']['ProcessHTML']."\";");
				$bookFirsttime=false;

			}
			else
			{
				echo $template['searchResult']['Chapter']['EndHTML'];
				echo $template['searchResult']['Book']['EndHTML'];
				echo $template['searchResult']['Book']['StartHTML'];
				eval("echo \"".$template['searchResult']['Book']['ProcessHTML']."\";");
				echo $template['searchResult']['Chapter']['StartHTML'];
				eval("echo \"".$template['searchResult']['Chapter']['ProcessHTML']."\";");
			}
		}
		else
			if($ChapterNo!=$prevChapterNo)
			{
				echo $template['searchResult']['Chapter']['EndHTML'];
				echo $template['searchResult']['Chapter']['StartHTML'];
				eval("echo \"".$template['searchResult']['Chapter']['ProcessHTML']."\";");
					
			}
		echo $template['searchResult']['Verse']['StartHTML'];
		echo $htmllines;
		echo $template['searchResult']['Verse']['EndHTML'];
		$previousBookName=$bookName;
		$prevChapterNo=$ChapterNo;
	}

}
echo $template['searchResult']['Chapter']['EndHTML'];
echo $template['searchResult']['Book']['EndHTML'];

}

/**
*
* function to create HTML from Sample data
*
*  @param $classGrepSearch class Instance
*
*/

function executeFromSample($classGrepSearch)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$keyWordList,
		$template,$version;
	$classGrepSearch->createSearchArray($searchString);
	$keyWordArray=explode(",",$keyWordList);
	echo $template['searchResult']['KeywordList']['StartHTML'];
	foreach($keyWordArray as $keyword)
	{
		eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
	}
	echo $template['searchResult']['KeywordList']['EndHTML'];
	$htmllines = createLinesFromSample($classGrepSearch);
	echo "<BR>".$htmllines;

}
?>
