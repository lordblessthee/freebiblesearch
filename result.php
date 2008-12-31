<?php
/**
 * PHP Script Coded by Rochak Chauhan to Scan a directory and display  the information
 *
 * (some functions are PHP 5 specific 
 */

// time when this script starts 
$startTime = time();
require_once('data/template.inc.php');
require_once('data/config.inc.php');
$title="Search Result";
require_once("data/header.inc.php");
echo $template['searchResult']['StartHTML'];
// create object of the class
require_once('ClassGrepSearch.inc.php');
require_once('functions.php');
$classGrepSearch = ClassGrepSearch::getInstance();

// The extentions to be searched (in this example, the extentions are comma seperated)
$filesWithExtentionsToBeSearched = "txt"; 


if(isset($_GET['path'])) {
    $scan_dir = $_GET['path'];
}
else
{
   if(isset($_POST['bibleVersion'])) 
	{
	    $version = $_POST['bibleVersion'];
		$scan_dir="bibledb/".$_POST['bibleVersion']."db/";
		$databasetable = "bibledb_".$_POST['bibleVersion'];
	}
	else
	{
		if(isset($_GET['bibleVersion'])) 
		{
			$version = $_GET['bibleVersion'];
			$scan_dir="bibledb/".$_GET['bibleVersion']."db/";
			$databasetable = "bibledb_".$_GET['bibleVersion'];
		}
	}
  // or
}

// set the value for the string to be searched 

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
$replace ="<a href=\"thesaurus.php?word=$1&version=$version\">$1</a>";
$searchWithLinks = preg_replace( $regex, $replace, $searchString);


if($databaseType == "DB")
{
	$databaseInfo['databasehost'] = $dbHost;
	$databaseInfo['databasename'] = $dbName;
	$databaseInfo['tableprefix'] = $dbTablePrefix;
	$databaseInfo['databaseusername'] =$dbUser;
	$databaseInfo['databasepassword'] = $dbPassword;
	$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
	executeFromDB($classGrepSearch,$databaseInfo);
	//exit;

}
else
{
	executeFromFile($classGrepSearch);
}


//calulate the time (in seconds) to execute this script
$endTime = time();
$totalTime = ($endTime - $startTime);

// total time taken to execute this script
$timeTaken = $classGrepSearch->convertSecToMins($totalTime);

/**echo "<BR><BR><hr><center><h4 >Info: Searched in <font color='blue'>".sizeof($classGrepSearch->getDirFile())."</font> Files in <font color='blue'>".sizeof($classGrepSearch->getDirArray())."</font> directories. </h4><hr>Total time taken: <font color='blue'> $timeTaken </font> </center><HR><center></b> Coded by:  Rochak Chauhan<HR>"; **/
echo "<center><hr>Total time taken: <font color='blue'> $timeTaken </font></hr></center>";
echo $template['searchResult']['EndHTML'];
require_once("data/footer.inc.php");


function executeFromDB($classGrepSearch,$databaseInfo)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$searchWithLinks;
	$classGrepSearch->createSearchArray($searchString);
echo "<b><a href='simplesearch.php'>Search Again</a></b>";
echo "<HR> The Search Keyword '<font color='red'><b>$searchWithLinks</font></b>' was found in following <font color='Green' ><b> $fileCounter  </b> </font>file(s): <BR>";
	$htmllines = createLinesFromDB($databaseInfo,$classGrepSearch);
	echo "<BR>".$htmllines;

}

function executeFromFile($classGrepSearch)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$searchWithLinks,$template;
if($limit=="bookset")
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookCategory(getCategoryName($bookset)),false);
}
else
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookNames($start_span,$end_span),false);
}



// print information
echo "<b><a href='simplesearch.php'>Search Again</a></b>";
echo "<HR> The Search Keyword '<font color='red'><b>$searchWithLinks</font></b>' was found in following <font color='Green' ><b> $fileCounter  </b> </font>file(s): <BR>";
$arrayOfFilenames = $classGrepSearch->getarrayOfFilenames();
$bookName="";
$previousBookName="";
echo $template['searchResult']['Book']['StartHTML'];
echo $template['searchResult']['Chapter']['StartHTML'];
for($i=0,$j=0;$i<sizeof($arrayOfFilenames);$i++) {
    $fileName = str_replace($_SERVER['DOCUMENT_ROOT'],"",$arrayOfFilenames[$i]);
    $linkName = str_replace($_SERVER['DOCUMENT_ROOT'],"Z:",$arrayOfFilenames[$i]);
	$chapterNo = (int)substr($fileName,-7,3);
	$bookNameStart=strlen($fileName)-strrpos($fileName,"/");
	$bookName = substr($fileName,-$bookNameStart+1,$bookNameStart-8);
	if(i!=0 && $previousBookName!=$bookName)
	{
		echo $template['searchResult']['Chapter']['EndHTML'];
		echo $template['searchResult']['Chapter']['StartHTML'];

	}
    $classGrepSearch->setGlobalCount(0);
    //$htmllines = createLinesFromFile($scan_dir.$fileName,$searchString,$searchType,$classGrepSearch);
      $htmllines = createLinesFromFile($scan_dir.$fileName,$classGrepSearch);
	if($htmllines !="")
	{
		eval("echo \"".$template['searchResult']['Chapter']['ProcessHTML']."\";");
		echo $template['searchResult']['Verse']['StartHTML'];
		echo $htmllines;
		echo $template['searchResult']['Verse']['EndHTML'];
	}
	$previousBookName=$bookName;
}
echo $template['searchResult']['Chapter']['EndHTML'];
echo $template['searchResult']['Book']['EndHTML'];

}
?>
