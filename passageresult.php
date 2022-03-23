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
	$versions = $samplePostedVersion;
}
else
{
	$preview =false;
	$installprefix="";
}
require_once('data/'.$installprefix.'template.inc.php');
require_once('data/'.$installprefix.'config.inc.php');
require_once('functions.php');
$title="Passage Lookup Result";
require_once('data/'.$installprefix.'header.inc.php');
if(isset($_POST['bibleVersion'])) 
{
	    $versions = $_POST['bibleVersion'];
}

if(isset($_POST['lookup'])) {
    $lookupString = stripslashes($_POST['lookup']);
}
$diagnosticMessage="";
if(!checkParameters())
{
	$errorMessage = $diagnosticMessage;
	echo $template['LookupResult']['ErrorMessage']['StartHTML'];
	eval("echo \"".$template['LookupResult']['ErrorMessage']['ProcessHTML']."\";");
	echo $template['LookupResult']['ErrorMessage']['EndHTML'];
	echo $template['LookupResult']['StartHTML'];
	echo $template['LookupResult']['EndHTML'];
	require_once('data/'.$installprefix.'footer.inc.php');
    exit;
}

$pattern = '/[;,]/'; 
 
$verseArray= preg_split( $pattern, $lookupString ) ;


$bibleVerseParseInfo =array();
foreach($verseArray as $content)
{
	$parseInfo = getPassage($content,$parseInfo);
	if($parseInfo["status"]=="ok")
	{
		$bibleVerseParseInfo[]=$parseInfo;
		$verseListArray[]= getVerse($parseInfo);
	}
	else
	{
		$errorMessageArray[] = $parseInfo["statusMessage"];
	}
}
if(isset($errorMessageArray))
{
	echo $template['LookupResult']['ErrorMessage']['StartHTML'];
	foreach($errorMessageArray as $errorMessage)
	{
		eval("echo \"".$template['LookupResult']['ErrorMessage']['ProcessHTML']."\";");
	}
	echo $template['LookupResult']['ErrorMessage']['EndHTML'];
}
echo $template['LookupResult']['StartHTML'];
echo $template['LookupResult']['EndHTML'];

?>
<form method="post" action="passageresult.php" name="lookup">
 <input type="text" name="lookup" value=<?php echo "\"$lookupString\"";?>  size="60" /><input type="submit" value="Update" />
<?php
foreach($versions as $version)
{
	echo "<input type=\"hidden\" name=\"bibleVersion[]\" value=\"$version\" >\n";

}
echo "</form>";
if(isset($verseListArray))
{
	echo $template['LookupResult']['VerseListMessage']['StartHTML'];
	foreach($verseListArray as $verseListMessage)
	{
		eval("echo \"".$template['LookupResult']['VerseListMessage']['ProcessHTML']."\";");
	}
	echo $template['LookupResult']['VerseListMessage']['EndHTML'];
}
else
{
	require_once('data/'.$installprefix.'footer.inc.php');
    exit;
}
foreach($bibleVersionArray as $bibVersion)
{
	$bibleName=$bibVersion['name'];
	$version=$bibVersion['shortname'];
	$currentTemplate=$template['LookupResult'];
	echo $currentTemplate['BibleVersion']['StartHTML'];
	eval("echo \"".$currentTemplate['BibleVersion']['ProcessHTML']."\";");
	echo $currentTemplate['BibleVersion']['EndHTML'];
	foreach($bibleVerseParseInfo as $parseInfo)
	{

		if($databaseType=="FILE")
		{
			$scanDir=$bibleDatabase.$version."db/";
			$bookName =  $parseInfo['bookName'];
			$chapterText = $bookName.str_pad($parseInfo['startChap'],3,"0",STR_PAD_LEFT).".txt";
			$file_path=$scanDir.$bookName."/".$chapterText;
			unset($fileContentsStruct);
			$fileContentsStruct[]=array($parseInfo['startChap'],array_slice(file($file_path),$parseInfo['startVerse']-1));
			for($i=$parseInfo['startChap']+1;$i<=$parseInfo['endChap'];$i++)
			{
				$ChapterNo=$i;
				$chapterText = $bookName.str_pad($i,3,"0",STR_PAD_LEFT).".txt";
				$file_path=$scanDir.$bookName."/".$chapterText;
				$fileContents=array($ChapterNo,file($file_path));
				$fileContentsStruct[] = $fileContents;
			}
			$fileContentsStruct[count($fileContentsStruct)-1][1]=array_slice($fileContentsStruct[count($fileContentsStruct)-1][1],0,(($parseInfo['endVerse']+1)-$parseInfo['startVerse']));
			echo $currentTemplate['Book']['StartHTML'];
			eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
			foreach($fileContentsStruct as $chapterText)
			{
				$ChapterNo=$chapterText[0];
				echo $currentTemplate['Chapter']['StartHTML'];
				eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");
				echo $currentTemplate['Verse']['StartHTML'];
				foreach($chapterText[1] as $verseText)
				{
					$verseNo=(int)substr($verseText,0,strpos($verseText," "));
					$verseText=substr($verseText,strpos($verseText," "));
					$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
					echo $txt;
				}
				echo $currentTemplate['Verse']['EndHTML'];
				echo $currentTemplate['Chapter']['EndHTML'];
			}
			echo $currentTemplate['Book']['EndHTML'];
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
				$scanDir=$bibleDatabase.$version."db/";
				$bookName =  $parseInfo['bookName'];
				$verseTextArray=getChaptersFromDB($databaseInfo,$bookName,$parseInfo['startChap']);
				unset($fileContentsStruct);
				$fileContentsStruct[]=array($parseInfo['startChap'],array_slice($verseTextArray,$parseInfo['startVerse']-1));
				for($i=$parseInfo['startChap']+1;$i<=$parseInfo['endChap'];$i++)
				{
					$ChapterNo=$i;
					$verseTextArray=getChaptersFromDB($databaseInfo,$bookName,$ChapterNo);
					$fileContents=array($ChapterNo,$verseTextArray);
					$fileContentsStruct[] = $fileContents;
				}
				$fileContentsStruct[count($fileContentsStruct)-1][1]=array_slice($fileContentsStruct[count($fileContentsStruct)-1][1],0,(($parseInfo['endVerse']+1)-$parseInfo['startVerse']));
				echo $currentTemplate['Book']['StartHTML'];
				eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
				foreach($fileContentsStruct as $chapterText)
				{
					$ChapterNo=$chapterText[0];
					echo $currentTemplate['Chapter']['StartHTML'];
					eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");
					echo $currentTemplate['Verse']['StartHTML'];
					foreach($chapterText[1] as $verseText)
					{
						$verseNo=$verseText[0];
						$verseText=$verseText[1];
						$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
						echo $txt;
					}
					echo $currentTemplate['Verse']['EndHTML'];
					echo $currentTemplate['Chapter']['EndHTML'];
				}
				echo $currentTemplate['Book']['EndHTML'];
			}
			else
				if($preview)
				{
					$bookName =  $parseInfo['bookName'];
					unset($fileContentsStruct);
					$fileContentsStruct[]=array($parseInfo['startChap'],array_slice($sampleLookupChapter[$bookName.$parseInfo['startChap']],$parseInfo['startVerse']-1));
			for($i=$parseInfo['startChap']+1;$i<=$parseInfo['endChap'];$i++)
			{
				$ChapterNo=$i;
				$chapterText = $bookName.str_pad($i,3,"0",STR_PAD_LEFT).".txt";
				$file_path=$scanDir.$bookName."/".$chapterText;
				$fileContents=array($ChapterNo,$sampleLookupChapter[$bookName.$i]);
				$fileContentsStruct[] = $fileContents;
			}
			$fileContentsStruct[count($fileContentsStruct)-1][1]=array_slice($fileContentsStruct[count($fileContentsStruct)-1][1],0,(($parseInfo['endVerse']+1)-$parseInfo['startVerse']));
			echo $currentTemplate['Book']['StartHTML'];
			eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
			foreach($fileContentsStruct as $chapterText)
			{
				$ChapterNo=$chapterText[0];
				echo $currentTemplate['Chapter']['StartHTML'];
				eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");
				echo $currentTemplate['Verse']['StartHTML'];
				foreach($chapterText[1] as $verseText)
				{
					$verseNo=(int)substr($verseText,0,strpos($verseText," "));
					$verseText=substr($verseText,strpos($verseText," "));
					$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
					echo $txt;
				}
				echo $currentTemplate['Verse']['EndHTML'];
				echo $currentTemplate['Chapter']['EndHTML'];
			}
			echo $currentTemplate['Book']['EndHTML'];
					
				}
	}
	echo $template['LookupResult']['EndHTML'];

}
    require_once('data/'.$installprefix.'footer.inc.php');


function getVerse($bibleParseInfo)
{
	if($bibleParseInfo['startVerseStatus']=="calculated"&&$bibleParseInfo['endChapStatus']=="calculated")
	{
		return $bibleParseInfo['bookName']." ".$bibleParseInfo['startChap']; 
	}
	else
		if($bibleParseInfo['startVerseStatus']=="calculated"&&$bibleParseInfo['endVerseStatus']=="calculated")
		{
			return $bibleParseInfo['bookName']." ".$bibleParseInfo['startChap']."-".$bibleParseInfo['endChap'];

		}
		else
			if($bibleParseInfo['endChapStatus']=="calculated"&&$bibleParseInfo['endVerseStatus']=="calculated"&&$bibleParseInfo['endVerseStatusConvertedFrom']=="startVerse")
			{
				return $bibleParseInfo['bookName']." ".$bibleParseInfo['startChap'].":".$bibleParseInfo['startVerse'];

			}
			else
			if($bibleParseInfo['endChapStatus']=="calculated"&&$bibleParseInfo['endVerseStatus']=="calculated"&&$bibleParseInfo['endVerseStatusConvertedFrom']=="endChapter")
			{
				return $bibleParseInfo['bookName']." ".$bibleParseInfo['startChap'].":".$bibleParseInfo['startVerse']."-".$bibleParseInfo['endVerse'];

			}
			else
			{
				return $bibleParseInfo['bookName']." ".$bibleParseInfo['startChap'].":".$bibleParseInfo['startVerse']."-".$bibleParseInfo['endChap'].":".$bibleParseInfo['endVerse'];
			}


}


function checkParameters()
{
	global $diagnosticMessage,$Book,$preview,$BibleVersion,$versions,$lookupString
		,$sampleBibleVersion,$sampleLookupString,$bibleVersionArray;
	$versionfound =false;
	if(!isset($versions)&&!$preview)
	{
		$diagnosticMessage="Bible version not specified cannot continue<br>";
		return false;

	}
	if($preview)
	{
		$bibleVersionArray = $sampleBibleVersion;
		$lookupString = $sampleLookupString;

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
			$diagnosticMessage="Bible version not found<br>";
			return false;
		}
	}

	if(!isset($lookupString)||trim($lookupString)=="")
	{
		$diagnosticMessage="Lookup Passage not given cannot continue<br>";
		return false;

	}
	return true;
}

?>