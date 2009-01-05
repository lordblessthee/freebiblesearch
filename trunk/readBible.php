<?php
/**
 * PHP Script Coded by Rochak Chauhan to Scan a directory and display  the information
 *
 * (some functions are PHP 5 specific 
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

// create object of the class
require_once('data/'.$installprefix.'template.inc.php');
require_once('data/'.$installprefix.'header.inc.php');
require_once('data/'.$installprefix.'config.inc.php');
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
	if($preview)
	{
		foreach($sampleBibleVersion as $versionInfo)
		{
			$versionName=$versionInfo["name"];
			$versionShortName=$versionInfo["shortname"];
			//$tempResult = explode(",",$line);
			//       echo "<font color='green'><b> <a href='"."readBible.php?version=".$versionInfo["shortname"]."'> //".$versionInfo["name"]." </a> </font></b><br>";
			eval("echo \"".$currentTemplate['Version']['ProcessHTML']."\";");
			
		}
	}
	else
	{
		foreach($BibleVersion as $versionInfo)

		{
			$versionName=$versionInfo["name"];
			$versionShortName=$versionInfo["shortname"];
			//$tempResult = explode(",",$line);
			//       echo "<font color='green'><b> <a href='"."readBible.php?version=".$versionInfo["shortname"]."'> //".$versionInfo["name"]." </a> </font></b><br>";
			eval("echo \"".$currentTemplate['Version']['ProcessHTML']."\";");
			
		}
	}
	$currentTemplate['Version']['EndHTML'];
	$currentTemplate['EndHTML'];
}else
{
$version=$_GET['version'];
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
	if($preview)
	{
		$allChapters=getChaptersFromSample($bookName,$chapterNo);
	}
	else
		if($databaseType=="FILE")
		{
			$chapterText = $bookName.str_pad($chapterNo,3,"0",STR_PAD_LEFT).".txt";
		}
		else
			if($databaseType=="DB")
			{
				$allChapters=getChaptersFromDB($databaseInfo,$bookName,$ChapterNo);
			}
	

    echo $currentTemplate['StartHTML'];
	$currentTemplate['BookIndex']['StartHTML'];
	eval("echo \"".$currentTemplate['BookIndex']['ProcessHTML']."\";");
	$currentTemplate['BookIndex']['EndHTML'];
	if($showChapterLinks)
	{

		//echo $template['chapterStartHTML'];
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
						//$chapterNo = (int)$chapters[count($chapters)-1];
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
		echo $currentTemplate['Chapter']['EndHTML'];
		echo $currentTemplate['Book']['EndHTML'];
    
}else
{
$start_span=1;
$end_span=66;
$currentTemplate=$template['readBible']['ShowBooks'];
$allBookList=getBookNames($start_span,$end_span);
//echo $template['bookStartHTML'];
echo $currentTemplate['StartHTML'];
echo $currentTemplate['Book']['StartHTML'];
//$tempstring="";
foreach($allBookList as $bookName)
{
  		/**echo "<BR><font color='green'><b> <a href='"."readBible.php?version=".$version."&book=".$book."&chapterlinks=true"."'  style='color:green;text-decoration:none'> ".$book." </a> </font></b>";**/
		//echo str_replace(array("<%version%>","<%book%>"),array($version,$book),$bookHTML);
	eval("echo \"".$currentTemplate['Book']['ProcessHTML']."\";");

	//if($showChapterLinks)
	//{
	if($preview)
	{
		$allChapters=getChaptersFromSample($bookName,false);
	}
	else
		if($databaseType=="FILE")
		{
			$allChapters=listFilesInDir($scanDir.$bookName);
		}
		else
			if($databaseType=="DB")
			{
				$allChapters=getChaptersFromDB($databaseInfo,$bookName,false);
			}
			echo $currentTemplate['ChapterLinks']['StartHTML'];
			
			//$tempstring .="\$sampleBookChapters['".$bookName."']=array(";
			foreach($allChapters as $chapterFile)
			{
				//$tempstring .=$chapterFile.",";
				if($preview)
				{
					$chapterNo = $chapterFile;
				}
				else
					if($databaseType=="FILE")
					{
					
						if(ereg ("([0-9]+)", $chapterFile, $chapters)) 
						{
							//$chapterNo = (int)$chapters[count($chapters)-1];
							$chapterNo = (int)substr($chapterFile,-7,3);
						}
					}
					else
						if($databaseType=="DB")
						{
							$chapterNo = $chapterFile;
						}
					/**echo "<font color='green'><b> <a href='"."readBible.php?version=".$version."&book=".$book."&chapter=".$chapterNo."&chapterlinks=true"."'  style='color:green;text-decoration:none'> ".$chapterNo." </a> </font></b>";**/
					eval("echo \"".$currentTemplate['ChapterLinks']['ProcessHTML']."\";");
			}
			//$tempstring =substr($tempstring,0,-1).");\n\n";
			echo $currentTemplate['ChapterLinks']['EndHTML'];
				
	//}

	


}
//file_put_contents("sampletestfile.txt",$tempstring);
echo $currentTemplate['Book']['EndHTML'];

}

}
//echo $template['chapterEndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');






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
		$tempstring="";
		while($row=mysql_fetch_array($result))
		{
			$verseTextArray[]=$row;
			//$tempstring .="\$sampleVerses[".count($verseTextArray)."][0]=".((int)$row[0]).";\n";
			//$tempstring .="\$sampleVerses[".count($verseTextArray)."][1]="."\"".addslashes($row[1])."\"".";\n";
		}
		//file_put_contents("sampletestfile2.txt",$tempstring);
	}
	$sql = " SELECT distinct chapterno FROM ".$databasetable."  where bookid = $bookId;";
	$result=mysql_query($sql) or die(mysql_error()); 
	while($row=mysql_fetch_array($result))
	{
		$chapterNoArray[]=$row[0];

	}
	return $chapterNoArray;
    
}

function getChaptersFromSample($bookName,$chapterNo)
{
	global $sampleBookChapters;
	global $sampleVerses;
	global $verseTextArray;
	/**$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetable = $databaseInfo['databasetable'];
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'] ;**/
	$Books=getBookIndex();
	/**$con = @mysql_connect($databasehost,$databaseusername,$databasepassword) or die(mysql_error());
    mysql_select_db($databasename);**/
	$bookId=(int)$Books[$bookName];
	if($chapterNo!==false)
	{
		/**$sql = " SELECT verseno, versetext FROM ".$databasetable."  where bookid = $bookId and chapterno = $chapterNo;";
		$result=mysql_query($sql) or die(mysql_error());
		$tempstring="";
		while($row=mysql_fetch_array($result))
		{
			$verseTextArray[]=$row;
			//$tempstring .="\$sampleVerses[".count($verseTextArray)."][0]=".((int)$row[0]).";\n";
			//$tempstring .="\$sampleVerses[".count($verseTextArray)."][1]="."\"".addslashes($row[1])."\"".";\n";
		}
		//file_put_contents("sampletestfile2.txt",$tempstring);**/
	foreach($sampleVerses as $row)
	{
		$verseTextArray[]=$row;
	}
	}
	/**$sql = " SELECT distinct chapterno FROM ".$databasetable."  where bookid = $bookId;";
	$result=mysql_query($sql) or die(mysql_error());**/ 
	/**while($row=mysql_fetch_array($result))
	{
		$chapterNoArray[]=$row[0];

	}**/

	return $sampleBookChapters[$bookName];
    
}
?>
