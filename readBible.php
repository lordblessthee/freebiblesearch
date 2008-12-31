<?php
/**
 * PHP Script Coded by Rochak Chauhan to Scan a directory and display  the information
 *
 * (some functions are PHP 5 specific 
 */

// time when this script starts 
$startTime = time();

// create object of the class
require_once('data/template.inc.php');
require_once("data/header.inc.php");
require_once('data/config.inc.php');
require_once('ClassGrepSearch.inc.php');
require_once('functions.php');
$classGrepSearch = ClassGrepSearch::getInstance();


if(isset($_GET['path'])) {
    $scan_dir = $_GET['path'];
}
else
{
  $scanDir="kjvdb2/";    
 
}

//$databaseType = "DB";
$verseTextArray =array();

if(!isset($_GET['version']))
{
	//echo "<br>1<br>";
	//print_r($template['readBible']['ShowBibleVersions']);
	//echo "<br>2<br>";
	$currentTemplate=$template['readBible']['ShowBibleVersions'];
	//print_r($currentTemplate);

	//$biblesArray = file("Bibles.txt");
	//echo "Select Version<br>";
	//echo "<br>3<br>";
	//print_r($currentTemplate['StartHTML']);
	echo $currentTemplate['StartHTML'];
	echo $currentTemplate['Version']['StartHTML'];
	//foreach($biblesArray as $line)
	foreach($BibleVersion as $versionInfo)

	{
		$versionName=$versionInfo["name"];
		$versionShortName=$versionInfo["shortname"];
		//$tempResult = explode(",",$line);
         //       echo "<font color='green'><b> <a href='"."readBible.php?version=".$versionInfo["shortname"]."'> //".$versionInfo["name"]." </a> </font></b><br>";
		 eval("echo \"".$currentTemplate['Version']['ProcessHTML']."\";");
			
	}
	$currentTemplate['Version']['EndHTML'];
	$currentTemplate['EndHTML'];
}else
{
$version=$_GET['version'];
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
	}


	if(isset($template['readBible']['ShowVerses']['ChapterLinks'])) 
	{
		$showChapterLinks = $template['readBible']['ShowVerses']['ChapterLinks'];
	}
	else
	{
		$showChapterLinks=false;  
	}
	if(isset($_GET['chapter'])) 
	{
		$chapterNo = $_GET['chapter'];
			
	}
	else
	{
		$chapterNo = 1;

	}

	if($databaseType=="FILE")
	{
		$chapterText = $bookName.str_pad($chapterNo,3,"0",STR_PAD_LEFT).".txt";
	}else
		if($databaseType=="DB")
		{
			$allChapters=getChaptersFromDB($databaseInfo,$bookName,$chapterNo);
		}

    echo $currentTemplate['StartHTML'];
	eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");
	if($showChapterLinks)
	{

		//echo $template['chapterStartHTML'];
		echo $currentTemplate['Chapter']['StartHTML'];
		foreach($allChapters as $chapterFile)
		{
			if($databaseType=="FILE")
			{
				if(ereg ("([0-9]+)", $chapterFile, $chapters)) 
				{
					//$chapterNo = (int)$chapters[count($chapters)-1];
					$chapterNoLink = (int)substr($chapterFile,-7,3);
				}
			}else
				if($databaseType=="DB")
				{
					$chapterNoLink = $chapterFile;
				}

			/**echo "<font color='green'><b> <a href='"."readBible.php?version=".$version."&book=".$bookName."&chapter=".$chapterNo."&chapterlinks=true"."'  style='color:green;text-decoration:none'> ".$chapterNo." </a> </font></b>";**/
			//eval("\$chaptertest = \"$chapterHTML\";");

			//str_replace(array("<%chapter%>","<%version%>"),array($chapterNo,$version),$chapterHTML);
			/**echo str_replace(array("<%chapter%>","<%version%>","<%book%>"),array($chapterNo,$version,$bookName),$chapterHTML);**/
			eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");

		}
		echo $currentTemplate['Chapter']['EndHTML'];
	}
		echo $currentTemplate['Verse']['StartHTML'];
        if($databaseType=="FILE")
		{
			$file_path=$scanDir.$bookName."/".$chapterText;
			//$fileContents=file_get_contents($file_path);
            $fileContents=file($file_path);
			foreach($fileContents as $verseText)
			{
					//$txt .="v".$verseText[0]." ".$verseText[1]."<br>";
				$verseNo=(int)substr($verseText,0,strpos($verseText," "));
				$verseText=substr($verseText,4);
				//$verseText=htmlentities($verseTextArr);
				$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
				}
			//$txt=htmlentities($fileContents);
			//$txt = ereg_replace( "\n", '<br />', $txt );
		}
		else
			if($databaseType=="DB")
			{
				foreach($verseTextArray as $verseTextArr)
				{
					//$txt .="v".$verseText[0]." ".$verseText[1]."<br>";
					$verseNo=$verseTextArr[0];
					$verseText=$verseTextArr[1];

					$txt .=eval("echo \"".$currentTemplate['Verse']['ProcessHTML']."\";");
				}
			}

		echo $txt;
		echo $currentTemplate['Verse']['EndHTML'];
    
}else
{
$start_span=1;
$end_span=66;
$currentTemplate=$template['readBible']['ShowBooks'];
$allBookList=getBookNames($start_span,$end_span);
//echo $template['bookStartHTML'];
echo $currentTemplate['StartHTML'];
echo $currentTemplate['Book']['StartHTML'];
foreach($allBookList as $bookName)
{
  		/**echo "<BR><font color='green'><b> <a href='"."readBible.php?version=".$version."&book=".$book."&chapterlinks=true"."'  style='color:green;text-decoration:none'> ".$book." </a> </font></b>";**/
		//echo str_replace(array("<%version%>","<%book%>"),array($version,$book),$bookHTML);
	eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");

	if($showChapterLinks)
	{
		if($databaseType=="FILE")
		{
			$allChapters=listFilesInDir($scanDir.$bookName);
		}
		else
			if($databaseType=="DB")
			{
				$allChapters=getChaptersFromDB($databaseInfo,$bookName,false);
			}
			echo $currentTemplate['Chapter']['StartHTML'];
			foreach($allChapters as $chapterFile)
			{
				if($databaseType=="FILE")
				{
					
					if(ereg ("([0-9]+)", $chapterFile, $chapters)) 
					{
						//$chapterNo = (int)$chapters[count($chapters)-1];
						$chapterNo = (int)substr($chapterFile,-7,3);
					}
					}else
						if($databaseType=="DB")
					{
						$chapterNo = $chapterFile;
					}
					/**echo "<font color='green'><b> <a href='"."readBible.php?version=".$version."&book=".$book."&chapter=".$chapterNo."&chapterlinks=true"."'  style='color:green;text-decoration:none'> ".$chapterNo." </a> </font></b>";**/
					eval("echo \"".$currentTemplate['Chapter']['ProcessHTML']."\";");
			}
			echo $currentTemplate['Chapter']['EndHTML'];
				
	}


}
echo $currentTemplate['Book']['EndHTML'];

}

}
//echo $template['chapterEndHTML'];
require_once("data/footer.inc.php");






function getLargestArchiveIndex($archiveFileArray)
{
		$bigNum = 0;
		foreach ($archiveFileArray as $fileName) 
		{
			if(ereg ("([0-9]+)", $fileName, $numbers)) 
			{
				$currNum = (int)$numbers[0];
			}

			if($bigNum < $currNum) 
			{
				$bigNum = $currNum;
			}
		}

		return $bigNum;

}

function listFilesInDir($start_dir)
{
        
  /*
  returns an array of files in $start_dir (not recursive)
  */
			
  $files = array();
  $dir = opendir($start_dir);
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
	$bookId=(int)$Books[$bookName];
	if($chapterNo!==false)
	{
		$sql = " SELECT verseno, versetext FROM ".$databasetable."  where bookid = $bookId and chapterno = $chapterNo;";
		$result=mysql_query($sql) or die(mysql_error());
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
?>
