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
require_once('ClassGrepSearch.inc.php');
require_once('functions.php');
$classGrepSearch = ClassGrepSearch::getInstance();

$filesWithExtentionsToBeSearched = "txt"; 
$bibleVersionArray=array();

if(isset($_GET['path'])) {
    $scan_dir = $_GET['path'];
}
else
{
   if(isset($_POST['bibleVersion'])) 
	{
	    $versions = $_POST['bibleVersion'];
	}
	else
	{
		if(isset($_GET['bibleVersion'])) 
		{
			$versions = explode("|",$_GET['bibleVersion']);
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
	
    	$start_span = (int)$_POST['spanbegin'];
}

if(isset($_POST['spanend'])) {
    $end_span = (int)$_POST['spanend'];
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

$diagnosticMessage="";
if(!checkParameters())
{
	$errorMessage = $diagnosticMessage;
	echo $template['searchResult']['ErrorMessage']['StartHTML'];
	eval("echo \"".$template['searchResult']['ErrorMessage']['ProcessHTML']."\";");
	echo $template['searchResult']['ErrorMessage']['EndHTML'];
    echo $template['searchResult']['StartHTML'];
	echo $template['searchResult']['EndHTML'];
	require_once('data/'.$installprefix.'footer.inc.php');
    exit();
}
require_once('data/'.$installprefix.'header.inc.php');
echo $template['searchResult']['StartHTML'];
// creates an array of all the provided extentions
$classGrepSearch->createArrayOfExtentions(",",$filesWithExtentionsToBeSearched);
$classGrepSearch->setSearchType($searchType);
$classGrepSearch->setSearchString($searchString);
//$classGrepSearch->setScanDir($scan_dir);
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
		//$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
		executeFromDB($classGrepSearch,$databaseInfo);

	}
	else
		if($databaseType == "FILE")
		{

				executeFromFile($classGrepSearch,$versions);
			
		}



//calulate the time (in seconds) to execute this script
$endTime = time();
$totalTime = ($endTime - $startTime);

// total time taken to execute this script
$timeTaken = $classGrepSearch->convertSecToMins($totalTime);

echo "<center><hr>Total time taken: <font color='blue'> $timeTaken </font></hr></center>";
echo $template['searchResult']['EndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');


function checkParameters()
{
	global $limit,$start_span,$end_span,$limit,$bookset,$searchType,$diagnosticMessage,$Book,$preview,
		$BibleVersion,$versions,$bibleVersionArray,$sampleBibleVersion;
	$versionfound =false;
	if(!isset($versions)&&!$preview)
	{
		$diagnosticMessage="Bible version not specified cannot continue<br>";
		return false;

	}
	if($preview)
	{
		$bibleVersionArray = $sampleBibleVersion;
	}
	else
	{
		foreach($versions as $version)
		{
			if($version =="")
			{
				continue;
			}
			for($i=0;$i<count($BibleVersion);$i++)
			{
				if($BibleVersion[$i]["shortname"]==$version)
				{
					$versionfound=true;
					$bibleVersionArray[]=$BibleVersion[$i];
					break;
				}
			}
			if(!$versionfound)
			{
				break;
			}
		}

		if(!$versionfound)
		{
			$diagnosticMessage="Bible version  $version not found<br>";
			return false;
		}
	}

	$searchTypeValueArray=array("all","any","phrase","allInFile");
    $searchTypeValueFound=false;
	foreach($searchTypeValueArray as $searchTypeValue)
	{
		if($searchType==$searchTypeValue)
		{
			$searchTypeValueFound=true;
			break;
		}

	}
	if(!$searchTypeValueFound)
	{
		$diagnosticMessage="Invalid search type <br>";
		return false;
	}

	$limitValueArray=array("none","bookset","span");
    $limitValueFound=false;
	foreach($limitValueArray as $limitValue)
	{
		if($limit==$limitValue)
		{
			$limitValueFound=true;
			break;
		}

	}
	if(!$limitValueFound)
	{
		$diagnosticMessage="Invalid search range <br>";
		return false;
	}

    if($limit=="span")
	{
		if(($start_span < 1)||($start_span > count($Book["All"])))
		{
			$diagnosticMessage="Invalid search range <br>";
			return false;
		}

		if(($end_span < 1)||($end_span > count($Book["All"])))
		{
			$diagnosticMessage="Invalid search range <br>";
			return false;
		}

		if(($start_span >$end_span))
		{
			$diagnosticMessage="Invalid search range <br>";
			return false;
		}

	}

	if($limit=="bookset")
	{
		if(getCategoryName($bookset)===false)
		{
			$diagnosticMessage="Invalid search category <br>";
			return false;
		}
	}
	

	return true;
}

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
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$versions,$keyWordList,
		$template,$BibleVersion,$bibleVersionArray,$version;
	$classGrepSearch->createSearchArray($searchString);

	$keyWordArray=explode(",",$keyWordList);
	$version=implode("|",array_map("bibShortnameFunc",$bibleVersionArray));
	echo $template['searchResult']['KeywordList']['StartHTML'];
	foreach($keyWordArray as $keyword)
	{
		eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
	}
	echo $template['searchResult']['KeywordList']['EndHTML'];
	foreach($bibleVersionArray as $bibVersion)
	{
		$version = $bibVersion['shortname'];
		$bibleName=$bibVersion['name'];
		$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
		echo $template['searchResult']['BibleVersion']['StartHTML'];
		eval("echo \"".$template['searchResult']['BibleVersion']['ProcessHTML']."\";");
		echo $template['searchResult']['BibleVersion']['EndHTML'];
		$htmllines = createLinesFromDB($databaseInfo,$classGrepSearch);
		echo "<BR>".$htmllines;

	}
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
		$template,$bibleDatabase,$BibleVersion,$bibleVersionArray;



	$keyWordArray=explode(",",$keyWordList);
	$version=implode("|",array_map("bibShortnameFunc",$bibleVersionArray));
	echo $template['searchResult']['KeywordList']['StartHTML'];
	foreach($keyWordArray as $keyword)
	{
		eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
	}
	echo $template['searchResult']['KeywordList']['EndHTML'];
	foreach($bibleVersionArray as $bibVersion)
	{
		$bibleName=$bibVersion['name'];
		$version=$bibVersion['shortname'];

		echo $template['searchResult']['BibleVersion']['StartHTML'];
		eval("echo \"".$template['searchResult']['BibleVersion']['ProcessHTML']."\";");
		echo $template['searchResult']['BibleVersion']['EndHTML'];
		$scan_dir=$bibleDatabase.$version."db/";
		$classGrepSearch->setScanDir($scan_dir);
		$classGrepSearch->initializeArrayOfFilenames();
		if($limit=="bookset")
		{
			$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookCategory(getCategoryName($bookset)),false);
		}
		else
		{
			if($limit=="none")
			{
				$start_span="1";
				$end_span="66";

			}
			$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookNames($start_span,$end_span),false);
		}
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
		if($htmllines=="")
		{
			echo $template['searchResult']['NoMatches']['StartHTML'];
			echo $template['searchResult']['NoMatches']['EndHTML'];
		}
		$htmllines="";
		echo $template['searchResult']['Chapter']['EndHTML'];
		echo $template['searchResult']['Book']['EndHTML'];
	}

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
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$keyWordList,
		$template,$bibleVersionArray,$version;
	$classGrepSearch->createSearchArray($searchString);
	$keyWordArray=explode(",",$keyWordList);
	$version=implode("|",array_map("bibShortnameFunc",$bibleVersionArray));
	echo $template['searchResult']['KeywordList']['StartHTML'];
	foreach($keyWordArray as $keyword)
	{
		eval("echo \"".$template['searchResult']['KeywordList']['ProcessHTML']."\";");
	}
	echo $template['searchResult']['KeywordList']['EndHTML'];
	foreach($bibleVersionArray as $bibleVersion)
	{
		$bibleName=$bibleVersion['name'];
		$version = $bibleVersion['shortname'];
		echo $template['searchResult']['BibleVersion']['StartHTML'];
		eval("echo \"".$template['searchResult']['BibleVersion']['ProcessHTML']."\";");
		echo $template['searchResult']['BibleVersion']['EndHTML'];
		$htmllines = createLinesFromSample($classGrepSearch);
		echo "<BR>".$htmllines;
	}

}
?>
