<HTML>
    <title>Unix grep simulator ----- grepSimulator.php?search=stringToBeSearched</title>
<?php
/**
 * PHP Script Coded by Rochak Chauhan to Scan a directory and display  the information
 *
 * (some functions are PHP 5 specific 
 */

// time when this script starts 
$startTime = time();
<%templatecommon%>
require_once('config.inc.php');
// create object of the class
require_once('ClassGrepSearch.inc.php');
//require_once('grep.template.php');
//require_once('bibleSearchCustomFunctions.php');
$classGrepSearch = ClassGrepSearch::getInstance();
echo $testStartTag."just a test".$testEndTag."<br>";

// The extentions to be searched (in this example, the extentions are comma seperated)
$filesWithExtentionsToBeSearched = "php, css, js,txt"; 

// the path of the present working directory (i.e. where this grepSimulator is placed)



$databaseType = "DB";
$searchString="eternal life";
$searchType = "all";
$caseSensitive = "no";
// creates an array of all the provided extentions
$classGrepSearch->createArrayOfExtentions(",",$filesWithExtentionsToBeSearched);
$classGrepSearch->setSearchType($searchType);
$classGrepSearch->setSearchString($searchString);
//$classGrepSearch->setScanDir($scan_dir);
$classGrepSearch->setCaseSensitive(($caseSensitive=="yes")?true:false);

//preg_match_all('/([a-zA-Z]+)/', $searchString, $searchMatch);

//$regex = '[a-zA-Z]+'; 
$regex ='/([a-zA-Z]+)/';
$replace ="<a href=\"thesaurus.php?word=$1&version=$version\">$1</a>";
$searchWithLinks = preg_replace( $regex, $replace, $searchString);


if($databaseType == "DB")
{
	//$databaseInfo['databasehost'] = $dbHost;
	//$databaseInfo['databasename'] = $dbName;
	//$databaseInfo['tableprefix'] = $dbTablePrefix;
	//$databaseInfo['databaseusername'] =$dbUser;
	//$databaseInfo['databasepassword'] = $dbPassword;
	//$databaseInfo['databasetable'] = $databaseInfo['tableprefix'].$version;
	executeFromSample($classGrepSearch);
	exit;

}

if($limit=="bookset")
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookCategory(getCategoryName($bookset)),false);
}
else
{
	$fileCounter = $classGrepSearch->readDirSubdirs($scan_dir,getBookNames($start_span,$end_span),false);
}



// print information
echo "<HR> <H3> <center> UNIX 'grep' command simulator !</H3><center> usage  [grepSimulator.php?search=stringToBeSearched ] </center>";
echo "<HR></center> The files with <font color='green'><b>".$classGrepSearch->lastStrReplace(",", "and", $filesWithExtentionsToBeSearched)."</b></font> extentions are scaned in <font color='red'><B>".$scan_dir."</B></font> Directory";
echo "<HR> The pattern/string '<font color='red'><b>$searchWithLinks</font></b>' was found in following <font color='Green' ><b> $fileCounter  </b> </font>file(s): <BR>";
$arrayOfFilenames = $classGrepSearch->getarrayOfFilenames();
for($i=0,$j=0;$i<sizeof($arrayOfFilenames);$i++) {
    $fileName = str_replace($_SERVER['DOCUMENT_ROOT'],"",$arrayOfFilenames[$i]);
    $linkName = str_replace($_SERVER['DOCUMENT_ROOT'],"Z:",$arrayOfFilenames[$i]);
    $classGrepSearch->setGlobalCount(0);
    //$htmllines = createLinesFromFile($scan_dir.$fileName,$searchString,$searchType,$classGrepSearch);
      $htmllines = createLinesFromFile($scan_dir.$fileName,$classGrepSearch);
	if($htmllines !="")
	{
		echo "<BR><b> # ".(($j++)+1).") </b><font color='green'><b> <a href='"."getFileContents.php?file_path=".urlencode($scan_dir.$fileName)."&search=".$searchString."' target='_blank' style='color:green;text-decoration:none'> ".$fileName." </a> </font></b> [".$classGrepSearch->getGlobalCount(). " time(s)]";
		echo "<BR>".$htmllines;
	}
}

//calulate the time (in seconds) to execute this script
$endTime = time();
$totalTime = ($endTime - $startTime);

// total time taken to execute this script
$timeTaken = $classGrepSearch->convertSecToMins($totalTime);

echo "<BR><BR><hr><center><h4 >Info: Searched in <font color='blue'>".sizeof($classGrepSearch->getDirFile())."</font> Files in <font color='blue'>".sizeof($classGrepSearch->getDirArray())."</font> directories. </h4><hr>Total time taken: <font color='blue'> $timeTaken </font> </center><HR><center></b> Coded by:  Rochak Chauhan<HR>";

function executeFromDB($classGrepSearch,$databaseInfo)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$searchWithLinks;
	$classGrepSearch->createSearchArray($searchString);
	echo "<HR> <H3> <center> UNIX 'grep' command simulator !</H3><center> usage  [grepSimulator.php?search=stringToBeSearched ] </center>";
echo "<HR></center> The files with <font color='green'><b>".$classGrepSearch->lastStrReplace(",", "and", $filesWithExtentionsToBeSearched)."</b></font> extentions are scaned in <font color='red'><B>".$scan_dir."</B></font> Directory";
echo "<HR> The pattern/string '<font color='red'><b>$searchWithLinks</font></b>' was found in following <font color='Green' ><b> $fileCounter  </b> </font>file(s): <BR>";
	$htmllines = createLinesFromDB($databaseInfo,$classGrepSearch);
	echo "<BR>".$htmllines;

}

function executeFromSample($classGrepSearch)
{
	global $limit,$scan_dir,$start_span,$end_span,$bookset,
		$filesWithExtentionsToBeSearched,$searchString,$databasetable,$version,$searchWithLinks;
	$classGrepSearch->createSearchArray($searchString);
	echo "<HR> <H3> <center> UNIX 'grep' command simulator !</H3><center> usage  [grepSimulator.php?search=stringToBeSearched ] </center>";
echo "<HR></center> The files with <font color='green'><b>".$classGrepSearch->lastStrReplace(",", "and", $filesWithExtentionsToBeSearched)."</b></font> extentions are scaned in <font color='red'><B>".$scan_dir."</B></font> Directory";
echo "<HR> The pattern/string '<font color='red'><b>$searchWithLinks</font></b>' was found in following <font color='Green' ><b> $fileCounter  </b> </font>file(s): <BR>";
	$htmllines = createLines($classGrepSearch);
	echo "<BR>".$htmllines;

}

function createLines($classGrepSearch)
{
        global $limit,$start_span,$end_span,$bookset;
		global $searchStringStartTag,$searchStringEndTag;
	/**$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetable = $databaseInfo['databasetable'];
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'] ;**/
	$classGrepSearch->setGlobalCount(0);
	$Books=getBooks();
	$htmlLines="";
	/**$con = @mysql_connect($databasehost,$databaseusername,$databasepassword) or die(mysql_error());
	mysql_select_db($databasename);
	$searchArray=$classGrepSearch->getSearchArray();
	if($classGrepSearch->getSearchType()=="allInFile")
	{
		$varArray['limit']=$limit;
		$varArray['start_span']=$start_span;
		$varArray['end_span']=$end_span;
		$varArray['bookset']=$bookset;
		$varArray['databasetable']=$databasetable;
		$sql = createAllinChapterSQL($classGrepSearch,$varArray);
	}
	else
	{
		$sql = " SELECT * FROM ".$databasetable."  where ";

		if($limit == "bookset")
		{
			$sql .="( ";
			foreach($Books[getCategoryName($bookset)] as $BookName)
			{
				$bookId=array_search($BookName, $Books["All"]);
				$sql .="bookid = $bookId or ";
			}
			$sql=substr($sql,0,(strlen($sql)-3));
			$sql .=") and ";
		}
		if($limit == "span")
		{
		
			for($i=$start_span;$i<=$end_span;$i++)
			{
				if($i==$start_span)
				{
					$sql .="( ";
				}

				$sql .="bookid = $i ";
				if($i != $end_span)
				{
					$sql .=" or ";
				}
				else
				{
					$sql .=") and ";
				}
			}
		}
	  foreach($searchArray as $search)
	  {

		 if($classGrepSearch->getCaseSensitive())
		  {
			$sql .="binary ";
		  }
			if($classGrepSearch->getSearchType()=="all")
			{
				$sql .= "versetext like '%".$search."%' and ";
			}else
			{
				$sql .= "versetext like '%".$search."%' or  ";
			}
		}
		$sql=substr($sql,0,(strlen($sql)-4)).";";
	}
      $result=mysql_query($sql) or die(mysql_error()); **/
	  $newLine ="";
	  $chapterNo="";
	  $bookID="";
	  $kjvdb=fopen("samplekjv.csv","r");
	  while($row=fgetcsv ($kjvdb, 8000, ","))
	  {
		  		$newLine ="v$row[2] ".$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[3])),$searchStringStartTag,$searchStringEndTag)."<br>"; /**$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[3])),"<b><font color='green'>","</font></b>" )."<br>";**/
				

			if(($chapterNo!=$row[1])||($bookID!=$row[0]))
			{
				$chapterNo = $row[1];
				$bookID = $row[0];
				$newLine = "<BR><b><font color='green'>".$Books["All"][$bookID]." ".$chapterNo."</font></b><br>".$newLine;


			}
			$htmlLines= $htmlLines.$newLine; 
	 }
	 
	  return $htmlLines;
}

function getBooks()
{
	$Book["All"][1]="Genesis";
$Book["Old Testament"][1]="Genesis";
$Book["All"][2]="Exodus";
$Book["Old Testament"][2]="Exodus";
$Book["All"][3]="Leviticus";
$Book["Old Testament"][3]="Leviticus";
$Book["All"][4]="Numbers";
$Book["Old Testament"][4]="Numbers";
$Book["All"][5]="Deuteronomy";
$Book["Old Testament"][5]="Deuteronomy";
$Book["All"][6]="Joshua";
$Book["Old Testament"][6]="Joshua";
$Book["Historical Books"][6]="Joshua";
$Book["All"][7]="Judges";
$Book["Old Testament"][7]="Judges";
$Book["Historical Books"][7]="Judges";
$Book["All"][8]="Ruth";
$Book["Old Testament"][8]="Ruth";
$Book["Historical Books"][8]="Ruth";
$Book["All"][9]="1 Samuel";
$Book["Old Testament"][9]="1 Samuel";
$Book["Historical Books"][9]="1 Samuel";
$Book["All"][10]="2 Samuel";
$Book["Old Testament"][10]="2 Samuel";
$Book["Historical Books"][10]="2 Samuel";
$Book["All"][11]="1 Kings";
$Book["Old Testament"][11]="1 Kings";
$Book["Historical Books"][11]="1 Kings";
$Book["All"][12]="2 Kings";
$Book["Old Testament"][12]="2 Kings";
$Book["Historical Books"][12]="2 Kings";
$Book["All"][13]="1 Chronicles";
$Book["Old Testament"][13]="1 Chronicles";
$Book["Historical Books"][13]="1 Chronicles";
$Book["All"][14]="2 Chronicles";
$Book["Old Testament"][14]="2 Chronicles";
$Book["Historical Books"][14]="2 Chronicles";
$Book["All"][15]="Ezra";
$Book["Old Testament"][15]="Ezra";
$Book["Historical Books"][15]="Ezra";
$Book["All"][16]="Nehemiah";
$Book["Old Testament"][16]="Nehemiah";
$Book["Historical Books"][16]="Nehemiah";
$Book["All"][17]="Esther";
$Book["Old Testament"][17]="Esther";
$Book["Historical Books"][17]="Esther";
$Book["All"][18]="Job";
$Book["Wisdom Books"][18]="Job";
$Book["Old Testament"][18]="Job";
$Book["All"][19]="Psalms";
$Book["Wisdom Books"][19]="Psalms";
$Book["Old Testament"][19]="Psalms";
$Book["All"][20]="Proverbs";
$Book["Wisdom Books"][20]="Proverbs";
$Book["Old Testament"][20]="Proverbs";
$Book["All"][21]="Ecclesiastes";
$Book["Wisdom Books"][21]="Ecclesiastes";
$Book["Old Testament"][21]="Ecclesiastes";
$Book["All"][22]="Song of Solomon";
$Book["Wisdom Books"][22]="Song of Solomon";
$Book["Old Testament"][22]="Song of Solomon";
$Book["All"][23]="Isaiah";
$Book["Old Testament"][23]="Isaiah";
$Book["Major Prophets"][23]="Isaiah";
$Book["All"][24]="Jeremiah";
$Book["Old Testament"][24]="Jeremiah";
$Book["Major Prophets"][24]="Jeremiah";
$Book["All"][25]="Lamentations";
$Book["Old Testament"][25]="Lamentations";
$Book["Major Prophets"][25]="Lamentations";
$Book["All"][26]="Ezekiel";
$Book["Old Testament"][26]="Ezekiel";
$Book["Major Prophets"][26]="Ezekiel";
$Book["All"][27]="Daniel";
$Book["Old Testament"][27]="Daniel";
$Book["Major Prophets"][27]="Daniel";
$Book["Apocalyptic Books"][27]="Daniel";
$Book["All"][28]="Hosea";
$Book["Old Testament"][28]="Hosea";
$Book["Minor Prophets"][28]="Hosea";
$Book["All"][29]="Joel";
$Book["Old Testament"][29]="Joel";
$Book["Minor Prophets"][29]="Joel";
$Book["All"][30]="Amos";
$Book["Old Testament"][30]="Amos";
$Book["Minor Prophets"][30]="Amos";
$Book["All"][31]="Obadiah";
$Book["Old Testament"][31]="Obadiah";
$Book["Minor Prophets"][31]="Obadiah";
$Book["All"][32]="Jonah";
$Book["Old Testament"][32]="Jonah";
$Book["Minor Prophets"][32]="Jonah";
$Book["All"][33]="Micah";
$Book["Old Testament"][33]="Micah";
$Book["Minor Prophets"][33]="Micah";
$Book["All"][34]="Nahum";
$Book["Old Testament"][34]="Nahum";
$Book["Minor Prophets"][34]="Nahum";
$Book["All"][35]="Habakkuk";
$Book["Old Testament"][35]="Habakkuk";
$Book["Minor Prophets"][35]="Habakkuk";
$Book["All"][36]="Zephaniah";
$Book["Old Testament"][36]="Zephaniah";
$Book["Minor Prophets"][36]="Zephaniah";
$Book["All"][37]="Haggai";
$Book["Old Testament"][37]="Haggai";
$Book["Minor Prophets"][37]="Haggai";
$Book["All"][38]="Zechariah";
$Book["Old Testament"][38]="Zechariah";
$Book["Minor Prophets"][38]="Zechariah";
$Book["All"][39]="Malachi";
$Book["Old Testament"][39]="Malachi";
$Book["Minor Prophets"][39]="Malachi";
$Book["All"][40]="Matthew";
$Book["New Testament"][40]="Matthew";
$Book["Gospels"][40]="Matthew";
$Book["All"][41]="Mark";
$Book["New Testament"][41]="Mark";
$Book["Gospels"][41]="Mark";
$Book["All"][42]="Luke";
$Book["New Testament"][42]="Luke";
$Book["Gospels"][42]="Luke";
$Book["All"][43]="John";
$Book["New Testament"][43]="John";
$Book["Gospels"][43]="John";
$Book["All"][44]="Acts";
$Book["New Testament"][44]="Acts";
$Book["All"][45]="Romans";
$Book["New Testament"][45]="Romans";
$Book["Pauline Epistles"][45]="Romans";
$Book["All"][46]="1 Corinthians";
$Book["New Testament"][46]="1 Corinthians";
$Book["Pauline Epistles"][46]="1 Corinthians";
$Book["All"][47]="2 Corinthians";
$Book["New Testament"][47]="2 Corinthians";
$Book["Pauline Epistles"][47]="2 Corinthians";
$Book["All"][48]="Galatians";
$Book["New Testament"][48]="Galatians";
$Book["Pauline Epistles"][48]="Galatians";
$Book["All"][49]="Ephesians";
$Book["New Testament"][49]="Ephesians";
$Book["Pauline Epistles"][49]="Ephesians";
$Book["All"][50]="Philippians";
$Book["New Testament"][50]="Philippians";
$Book["Pauline Epistles"][50]="Philippians";
$Book["All"][51]="Colossians";
$Book["New Testament"][51]="Colossians";
$Book["Pauline Epistles"][51]="Colossians";
$Book["All"][52]="1 Thessalonians";
$Book["New Testament"][52]="1 Thessalonians";
$Book["Pauline Epistles"][52]="1 Thessalonians";
$Book["All"][53]="2 Thessalonians";
$Book["New Testament"][53]="2 Thessalonians";
$Book["Pauline Epistles"][53]="2 Thessalonians";
$Book["All"][54]="1 Timothy";
$Book["New Testament"][54]="1 Timothy";
$Book["Pauline Epistles"][54]="1 Timothy";
$Book["All"][55]="2 Timothy";
$Book["New Testament"][55]="2 Timothy";
$Book["Pauline Epistles"][55]="2 Timothy";
$Book["All"][56]="Titus";
$Book["New Testament"][56]="Titus";
$Book["Pauline Epistles"][56]="Titus";
$Book["All"][57]="Philemon";
$Book["New Testament"][57]="Philemon";
$Book["Pauline Epistles"][57]="Philemon";
$Book["All"][58]="Hebrews";
$Book["New Testament"][58]="Hebrews";
$Book["Pauline Epistles"][58]="Hebrews";
$Book["All"][59]="James";
$Book["New Testament"][59]="James";
$Book["Epistles"][59]="James";
$Book["All"][60]="1 Peter";
$Book["New Testament"][60]="1 Peter";
$Book["Epistles"][60]="1 Peter";
$Book["All"][61]="2 Peter";
$Book["New Testament"][61]="2 Peter";
$Book["Epistles"][61]="2 Peter";
$Book["All"][62]="1 John";
$Book["New Testament"][62]="1 John";
$Book["Epistles"][62]="1 John";
$Book["All"][63]="2 John";
$Book["New Testament"][63]="2 John";
$Book["Epistles"][63]="2 John";
$Book["All"][64]="3 John";
$Book["New Testament"][64]="3 John";
$Book["Epistles"][64]="3 John";
$Book["All"][65]="Jude";
$Book["New Testament"][65]="Jude";
$Book["Epistles"][65]="Jude";
$Book["All"][66]="Revelation";
$Book["New Testament"][66]="Revelation";
$Book["Apocalyptic Books"][66]="Revelation";

return $Book;
}
?>
