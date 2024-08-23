<?php

/**
 * File containing code to show the read bible pages
 */

// time when this script starts 
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
require_once('ClassGrepSearch.inc.php');
require_once('functions.php');
$classGrepSearch = ClassGrepSearch::getInstance();



$verseTextArray =array();

if(!isset($_GET['version']))
{
	$title="Select&nbsp;Version";
	require_once('data/'.$installprefix.'header.inc.php');
	$currentTemplate=$template['readBible']['ShowBibleVersions'];
	echo $currentTemplate['StartHTML'];
	echo $currentTemplate['Version']['StartHTML'];
	if($preview)
	{
		foreach($sampleBibleVersion as $versionInfo)
		{
			$versionName=$versionInfo["name"];
			$versionShortName=$versionInfo["shortname"];
			eval("echo \"".$currentTemplate['Version']['ProcessHTML']."\";");
			
		}
	}
	else
	{
		foreach($BibleVersion as $versionInfo)

		{
			$versionName=$versionInfo["name"];
			$versionShortName=$versionInfo["shortname"];
			eval("echo \"".$currentTemplate['Version']['ProcessHTML']."\";");
			
		}
	}
	echo $currentTemplate['Version']['EndHTML'];
	echo $currentTemplate['EndHTML'];
}
else
{
	$version=$_GET['version'];
	$versionfound=false;
	if($preview)
	{
		$BibleVersion=$sampleBibleVersion;
	}
	for($i=0;$i<count($BibleVersion);$i++)
	{
		if($BibleVersion[$i]["shortname"]==$version)
		{
			$versionfound=true;
			$bibleName = $BibleVersion[$i]["name"];
			break;
		}
	}

	if(!$versionfound)
	{
			$errorMessage = "Bible version $version not present";
			$title=$errorMessage;
			require_once('data/'.$installprefix.'header.inc.php');
			echo $template['readBible']['ErrorMessage']['StartHTML'];
			eval("echo \"".$template['readBible']['ErrorMessage']['ProcessHTML']."\";");
			echo $template['readBible']['ErrorMessage']['EndHTML'];
			echo $template['readBible']['General']['StartHTML'];
			echo $template['readBible']['General']['EndHTML'];
			require_once('data/'.$installprefix.'footer.inc.php');
			exit();

	}
	if($databaseType=="FILE")
	{
		$scanDir=$bibleDatabase.$version."db/";
	}
	else
		if($databaseType=="DB")
		{
			$databasetable = "bibledb_".$version;
			$databaseInfo['databasehost'] = $dbHost;
			$databaseInfo['databasename'] = $dbName;
			$databaseInfo['tableprefix'] = $dbTablePrefix;
			$databaseInfo['databaseusername'] =$dbUser;
			$databaseInfo['databasepassword'] = $dbPassword;
			$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
		}


	if(isset($template['readBible']['ShowBooks']['ChapterLinks'])) 
	{
		$showChapterLinks = $template['readBible']['ShowBooks']['ChapterLinks'];
	}
	else
	{
		$showChapterLinks=false;  
	}

	if(isset($_GET['book'])) 
	{
		$bookName = $_GET['book'];
		$currentTemplate=$template['readBible']['ShowVerses'];
		$chapterCount = $BibleChapterInfo[$BookIndex[$bookName]]["ChapterCount"];
		if(!isset($BookIndex[$bookName]))
		{
			$errorMessage = "Bible Book not present";
			$title=$errorMessage;
			require_once('data/'.$installprefix.'header.inc.php');
			echo $template['readBible']['ErrorMessage']['StartHTML'];
			eval("echo \"".$template['readBible']['ErrorMessage']['ProcessHTML']."\";");
			echo $template['readBible']['ErrorMessage']['EndHTML'];
			echo $template['readBible']['General']['StartHTML'];
			echo $template['readBible']['General']['EndHTML'];
			require_once('data/'.$installprefix.'footer.inc.php');
			exit();
		}
		if(isset($template['readBible']['ShowVerses']['ShowChapterLinks'])) 
		{
			$showChapterLinks = $template['readBible']['ShowVerses']['ShowChapterLinks'];
		}
		else
		{
			$showChapterLinks=false;  
		}
		if(isset($_GET['chapter'])) 
		{
			$ChapterNo = (int)$_GET['chapter'];
				
		}
		else
		{
			$ChapterNo = 1;

		}
		if($ChapterNo > $chapterCount||$ChapterNo <1)
		{
			$errorMessage = "Bible Chapter $ChapterNo not present for the book $bookName ";
			$title=$errorMessage;
			require_once('data/'.$installprefix.'header.inc.php');
			echo $template['readBible']['ErrorMessage']['StartHTML'];
			eval("echo \"".$template['readBible']['ErrorMessage']['ProcessHTML']."\";");
			echo $template['readBible']['ErrorMessage']['EndHTML'];
			echo $template['readBible']['General']['StartHTML'];
			echo $template['readBible']['General']['EndHTML'];
			require_once('data/'.$installprefix.'footer.inc.php');
			exit();
		}
		$title=$bookName." ".$ChapterNo;
		require_once('data/'.$installprefix.'header.inc.php');
		if($databaseType=="FILE")
		{
			$chapterText = $bookName.str_pad($ChapterNo,3,"0",STR_PAD_LEFT).".txt";
		}
		else
			if($databaseType=="DB")
			{
				$verseTextArray=getChaptersFromDB($databaseInfo,$bookName,$ChapterNo);
			}
	


		echo $currentTemplate['StartHTML'];
		echo $currentTemplate['BookIndex']['StartHTML'];
		eval("echo \"".$currentTemplate['BookIndex']['ProcessHTML']."\";");
		echo $currentTemplate['BookIndex']['EndHTML'];
		echo $currentTemplate['BibleVersion']['StartHTML'];
		eval("echo \"".$currentTemplate['BibleVersion']['ProcessHTML']."\";");
		echo $currentTemplate['BibleVersion']['EndHTML'];
		echo $currentTemplate['Book']['StartHTML'];
		if($showChapterLinks)
		{

			echo $currentTemplate['ChapterLinks']['StartHTML'];
			for($chapterNoLink=1;$chapterNoLink<=$chapterCount;$chapterNoLink++)
			{

				eval("echo \"".$currentTemplate['ChapterLinks']['ProcessHTML']."\";");

			}
			echo $currentTemplate['ChapterLinks']['EndHTML'];
		}
		echo $currentTemplate['Book']['StartHTML'];
		eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
		echo $currentTemplate['Chapter']['StartHTML'];
		eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");
		echo $currentTemplate['Verse']['StartHTML'];
		if($preview)
		{
			$verseTextArray=getVersesFromSample($bookName,$chapterNo);
			foreach($verseTextArray as $verseTextArr)
			{
				$verseNo=$verseTextArr[0];
				$verseText=stripslashes($verseTextArr[1]);
				$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
			}
		}
		else
			if($databaseType=="FILE")
			{
				$file_path=$scanDir.$bookName."/".$chapterText;
				$fileContents=file($file_path);
				foreach($fileContents as $verseText)
				{
					$verseNo=(int)substr($verseText,0,strpos($verseText," "));
					$verseText=substr($verseText,strpos($verseText," "));
					$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
				}
			}
			else
				if($databaseType=="DB")
				{
					foreach($verseTextArray as $verseTextArr)
					{
						$verseNo=$verseTextArr[0];
						$verseText=$verseTextArr[1];
						$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
					}
				}

		echo $txt;
		echo $currentTemplate['Verse']['EndHTML'];
		echo $currentTemplate['Chapter']['EndHTML'];
		echo $currentTemplate['Book']['EndHTML'];
		echo $currentTemplate['EndHTML'];
    
	}else
	{
		$title="Bible Book Index";
		require_once('data/'.$installprefix.'header.inc.php');
		$start_span=1;
		$end_span=66;
		$currentTemplate=$template['readBible']['ShowBooks'];
		$allBookList=getBookNames($start_span,$end_span);
		echo $currentTemplate['StartHTML'];
		echo $currentTemplate['BibleVersion']['StartHTML'];
		eval("echo \"".$currentTemplate['BibleVersion']['ProcessHTML']."\";");
		echo $currentTemplate['BibleVersion']['EndHTML'];
		echo $currentTemplate['Book']['StartHTML'];
		foreach($allBookList as $bookName)
		{
			eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
			$chapterCount = $BibleChapterInfo[$BookIndex[$bookName]]["ChapterCount"];
			echo $currentTemplate['ChapterLinks']['StartHTML'];
			
			for($chapterNo=1;$chapterNo<=$chapterCount;$chapterNo++)
			{
				eval("echo \"".$currentTemplate['ChapterLinks']['ProcessHTML']."\";");
			}
			echo $currentTemplate['ChapterLinks']['EndHTML'];
		}
		echo $currentTemplate['Book']['EndHTML'];
		echo $currentTemplate['EndHTML'];

	}

}
require_once('data/'.$installprefix.'footer.inc.php');



/**
 *
 * function to return all Verses
 * in a book 
 *
 * @param $bookName string
 * @param  $chapterNo mixed
 * 
 * return array
 */

function getVersesFromSample($bookName,$chapterNo)
{
	global $sampleVerses;
	global $verseTextArray;
	$Books=getBookIndex();
	$bookId=(int)$Books[$bookName];
	if($chapterNo!==false)
	{
		foreach($sampleVerses as $row)
		{
			$verseTextArray[]=$row;
		}
	}
	
	return $verseTextArray;
    
}
?>
