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
	$currentTemplate['Version']['EndHTML'];
	$currentTemplate['EndHTML'];
}
else
{
	$version=$_GET['version'];
	$versionfound=false;
	if(!$preview)
	{
		for($i=0;$i<count($BibleVersion);$i++)
		{
			if($BibleVersion[$i]["shortname"]==$version)
			{
				$versionfound=true;
				break;
			}
		}

		if(!$versionfound)
		{
                require_once('data/'.$installprefix.'header.inc.php');
				echo "<br><br><br>Bible version not present";
				require_once('data/'.$installprefix.'footer.inc.php');
				exit;
	
		}
	}
	
	if($preview)
	{
	}
	else
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
	if($databaseType=="FILE")
	{
		$allChapters=listFilesInDir($scanDir.$bookName);
		if($allChapters===false)
		{
			require_once('data/'.$installprefix.'header.inc.php');
			echo "<br><br><br>Bible Book not present";
			require_once('data/'.$installprefix.'footer.inc.php');
			exit;
		}
		sort($allChapters);
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
	$title=$bookName." ".$ChapterNo;
	require_once('data/'.$installprefix.'header.inc.php');
	if($preview)
	{
		$allChapters=getChaptersFromSample($bookName,$chapterNo);
	}
	else
		if($databaseType=="FILE")
		{
			$chapterText = $bookName.str_pad($ChapterNo,3,"0",STR_PAD_LEFT).".txt";
		}
		else
			if($databaseType=="DB")
			{
				$allChapters=getChaptersFromDB($databaseInfo,$bookName,$ChapterNo);
				if($allChapters===false)
				{
					require_once('data/'.$installprefix.'header.inc.php');
					echo "<br><br><br>Bible Book not present";
					require_once('data/'.$installprefix.'footer.inc.php');
					exit;

				}
			}
	
if($ChapterNo > count($allChapters)||$ChapterNo <1)
{
	require_once('data/'.$installprefix.'header.inc.php');
	echo "<br><br><br>Bible Chapter not present";
	require_once('data/'.$installprefix.'footer.inc.php');
	exit;
}

    echo $currentTemplate['StartHTML'];
	$currentTemplate['BookIndex']['StartHTML'];
	eval("echo \"".$currentTemplate['BookIndex']['ProcessHTML']."\";");
	$currentTemplate['BookIndex']['EndHTML'];
	if($showChapterLinks)
	{

		echo $currentTemplate['ChapterLinks']['StartHTML'];
		foreach($allChapters as $chapterFile)
		{
			if($preview)
			{
				$chapterNoLink = $chapterFile;
			}
			else
				if($databaseType=="FILE")
				{
					if(ereg ("([0-9]+)", $chapterFile, $chapters)) 
					{
						$chapterNoLink = (int)substr($chapterFile,-7,3);
					}
				}else
					if($databaseType=="DB")
					{
						$chapterNoLink = $chapterFile;
					}

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
    
}else
{
	$title="Bible Book Index";
	require_once('data/'.$installprefix.'header.inc.php');
$start_span=1;
$end_span=66;
$currentTemplate=$template['readBible']['ShowBooks'];
$allBookList=getBookNames($start_span,$end_span);
echo $currentTemplate['StartHTML'];
echo $currentTemplate['Book']['StartHTML'];
foreach($allBookList as $bookName)
{
	eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");

	if($preview)
	{
		$allChapters=getChaptersFromSample($bookName,false);
	}
	else
		if($databaseType=="FILE")
		{
			$allChapters=listFilesInDir($scanDir.$bookName);
			sort($allChapters);
		}
		else
			if($databaseType=="DB")
			{
				$allChapters=getChaptersFromDB($databaseInfo,$bookName,false);
			}
			echo $currentTemplate['ChapterLinks']['StartHTML'];
			
			foreach($allChapters as $chapterFile)
			{
				if($preview)
				{
					$chapterNo = $chapterFile;
				}
				else
					if($databaseType=="FILE")
					{
					
						if(ereg ("([0-9]+)", $chapterFile, $chapters)) 
						{
							$chapterNo = (int)substr($chapterFile,-7,3);
						}
					}
					else
						if($databaseType=="DB")
						{
							$chapterNo = $chapterFile;
						}
					eval("echo \"".$currentTemplate['ChapterLinks']['ProcessHTML']."\";");
			}
			echo $currentTemplate['ChapterLinks']['EndHTML'];
				

	


}
echo $currentTemplate['Book']['EndHTML'];

}

}
require_once('data/'.$installprefix.'footer.inc.php');



/**
 *
 * function to get all files in a directory
 *  as a string array
 *
 * @param $start_dir string
 * 
 * return array
 */


function listFilesInDir($start_dir)
{
        
  /*
  returns an array of files in $start_dir (not recursive)
  */
			
  $files = array();
  $dir = opendir($start_dir);
  if($dir===false)
  {
	  return false;
  }

  while(($myfile = readdir($dir)) !== false)
  {
    if($myfile != '.' && $myfile != '..' && !is_file($myfile) && $myfile != 'resource.frk' && !eregi('^Icon',$myfile) )
    {
     $files[] = $myfile;
    }
  }
  closedir($dir);
  return $files;
}

/**
 *
 * function to return all chapterNos
 * in a book and also optional text
 * from database
 *
 * @param $databaseInfo array
 * @param $bookName string
 * @param  $chapterNo mixed
 * 
 * return array
 */

function getChaptersFromDB($databaseInfo,$bookName,$chapterNo)
{
	global $verseTextArray;
	$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetable = $databaseInfo['databasetable'];
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'] ;
	$Books=getBookIndex();
	$con = @mysql_connect($databasehost,$databaseusername,$databasepassword) or die(mysql_error());
    mysql_select_db($databasename);
	if(!isset($Books[$bookName]))
	{
		return false;
	}
	else
	{
		$bookId=(int)$Books[$bookName];
	}
	if($chapterNo!==false)
	{
		$sql = " SELECT verseno, versetext FROM ".$databasetable."  where bookid = $bookId and chapterno = $chapterNo;";
		$result=mysql_query($sql) or die(mysql_error());
		$tempstring="";
		while($row=mysql_fetch_array($result))
		{
			$verseTextArray[]=$row;
		}
	}
	$sql = " SELECT distinct chapterno FROM ".$databasetable."  where bookid = $bookId;";
	$result=mysql_query($sql) or die(mysql_error()); 
	while($row=mysql_fetch_array($result))
	{
		$chapterNoArray[]=$row[0];

	}
	return $chapterNoArray;
    
}

/**
 *
 * function to return all chapterNos
 * in a book and also optional text
 * from sample data
 *
 * @param $bookName string
 * @param  $chapterNo mixed
 * 
 * return array
 */

function getChaptersFromSample($bookName,$chapterNo)
{
	global $sampleBookChapters;
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
	
	return $sampleBookChapters[$bookName];
    
}
?>
