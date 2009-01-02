<?php
require_once('ClassGrepSearch.inc.php');
require_once('data/config.inc.php');
require_once('data/template.inc.php');

/**
 *
 * function to retrieve text lines from file
 *  highlighting the search string
 *
 * @param $filePath string
 * @param $classGrepSearch class Instance
 *
 */  

function createLinesFromFile($filePath,$classGrepSearch)
{
	global $template;

	$linesArray = file($filePath);
	$chapterNo = (int)substr($filePath,-7,3);
	$htmlLines="";
	$classGrepSearch->setGlobalCount(0);
	$newLine = "";
	for($i=0;$i<count($linesArray);$i++) 
	{
		$verseNo=(int)substr($linesArray[$i],0,strpos($linesArray[$i]," "));
		$verseText=substr($linesArray[$i],4);
				//$verseText=htmlentities($verseTextArr);
		$verseText = $classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($verseText)),
			$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag'] );
		if($classGrepSearch->getGlobalResult())
		{
			//$htmlLines= $htmlLines."line no:".$i.":".$newLine; 
			$htmlLines .=eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
		} 
	} 
	$globalSearchCount = $classGrepSearch->getGlobalCount(); 
	return	 $htmlLines; 
}




/**
*
* function to return the Chapter Names
*  from start and end index
*
* @param $startSpan string
* @param $endSpan string
*
*	return array
*/  
function getBookNames_($startSpan,$endSpan)
{
	$Book = array();
	$Book[1]="Chapter_1_3";
	$Book[2]="Chapter_4_6";
	$Book[3]="Chapter_7_9";
	$Book[4]="Chapter_10_12"; 
	$resultArray=array();
	for($i=$startSpan;$i<=$endSpan;$i++)
	{
		array_push($resultArray,$Book[$i]);
	}

	return $resultArray;

}


/**
 *
 * function to get array of all Book Names
 *  from a CSV file
 *
 * return array
 *
 */  

function getBooks()
{
	/**$fileArray = file("BibleBooks2.csv");
	$Book = array();
			
	foreach($fileArray as $line)
	{
		$tempResult = explode(",",$line);
		$Book["All"][(int)trim($tempResult[0])]= trim($tempResult[1]);
		for($i = 2; $i < count($tempResult);$i++)
		{

			$Book[trim($tempResult[$i])][(int)trim($tempResult[0])]= trim($tempResult[1]);
		}
	}**/
	global $Book;
			
	return $Book;
}


function getBookIndex()
{
	/**$fileArray = file("BibleBooks2.csv");
	$Book = array();
			
	foreach($fileArray as $line)
	{
		$tempResult = explode(",",$line);
		$Book["All"][(int)trim($tempResult[0])]= trim($tempResult[1]);
		for($i = 2; $i < count($tempResult);$i++)
		{

			$Book[trim($tempResult[$i])][(int)trim($tempResult[0])]= trim($tempResult[1]);
		}
	}**/
	global $BookIndex;
			
	return $BookIndex;
}

/**
*
* function to return the Book Names
*  from start and end index
*
* @param $start_span mixed
* @param $end_span mixed
*
*	return array
*/  
function getBookNames($startSpan,$endSpan)
{
	$Book = array();
        $Book = getBooks();
	//$Book[1]="Chapter_1_3";
	//$Book[2]="Chapter_4_6";
	//$Book[3]="Chapter_7_9";
	//$Book[4]="Chapter_10_12"; 
	$resultArray=array();
	for($i=$startSpan;$i<=$endSpan;$i++)
	{
		array_push($resultArray,$Book["All"][$i]);
	}

	return $resultArray;

}

/**
*
* function to get category name from
*  a category number
*
*/  

function getCategoryName($aCategoryNumber)
{
	$Category["11"]="Apocalyptic Books";
	$Category["3"]="Books of Moses";
	$Category["9"]="Epistles";
	$Category["4"]="Gospels";
	$Category["8"]="Historical Books";
	$Category["5"]="Major Prophets";
	$Category["6"]="Minor Prophets";
	$Category["2"]="New Testament";
	$Category["1"]="Old Testament";
	$Category["10"]="Pauline Epistles";
	$Category["7"]="Wisdom Books";
	return $Category[$aCategoryNumber];
}

/**
*
* function to get all Book Names
*  in a particular category
*
*/  

function getBookCategory($aCategoryName)
{
	$Book = array();
	$Book = getBooks();
	$resultArray=array();
	foreach($Book[$aCategoryName] as $BookName)
	{
		array_push($resultArray,$BookName);
	}
	return $resultArray;

}

function createLinesFromDB($databaseInfo,$classGrepSearch)
{
        global $limit,$start_span,$end_span,$bookset,$template;
	$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetable = $databaseInfo['databasetable'];
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'] ;
	$classGrepSearch->setGlobalCount(0);
	$Books=getBooks();
	$htmlLines="";
	$con = @mysql_connect($databasehost,$databaseusername,$databasepassword) or die(mysql_error());
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
      $result=mysql_query($sql) or die(mysql_error()); 
	  $newLine ="";
	  $chapterNo="";
	  $bookID="";
	  while($row=mysql_fetch_array($result))
	  {
		        $verseNo=$row[VERSENO];
				$verseText=$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[VERSETEXT])),$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag']);
				$newLine =eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
		  		/**$newLine ="v$row[VERSENO] ".**/
			    /** $newLine =eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";").	$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[VERSETEXT])),$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag'] )."<br>";**/

			if(($chapterNo!=$row[CHAPTERNO])||($bookID!=$row[BOOKID]))
			{
				$chapterNo = $row[CHAPTERNO];
				$bookID = $row[BOOKID];
				$bookName=$Books["All"][$bookID];
				//$newLine = "<BR><b><font color='green'>".$Books["All"][$bookID]." //".$chapterNo."</font></b><br>".$newLine;
				$newLine .=eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";")."<br>";


			}
			$htmlLines= $htmlLines.$newLine; 
	 }
	 
	  return $htmlLines;
}

function createLinesFromSample($classGrepSearch)
{
	global $limit,$start_span,$end_span,$bookset;
	global $searchStringStartTag,$searchStringEndTag;
	$classGrepSearch->setGlobalCount(0);
	$Books=getBooks();
	$htmlLines="";
	$newLine ="";
	$chapterNo="";
	$bookID="";
	$kjvdb=fopen("samplekjv.csv","r");
	while($row=fgetcsv ($kjvdb, 8000, ","))
	{
		$verseNo=$row[2];
				$verseText=$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[2])),$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag']);
				$newLine =eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
		  		/**$newLine ="v$row[VERSENO] ".**/
			    /** $newLine =eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";").	$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[VERSETEXT])),$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag'] )."<br>";**/

			if(($chapterNo!=$row[1])||($bookID!=$row[0]))
			{
				$chapterNo = $row[1];
				$bookID = $row[0];
				$bookName=$Books["All"][$bookID];
				//$newLine = "<BR><b><font color='green'>".$Books["All"][$bookID]." //".$chapterNo."</font></b><br>".$newLine;
				$newLine .=eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";")."<br>";
			}
			$htmlLines= $htmlLines.$newLine; 
	 }
	 
	  return $htmlLines;
}

function createAllinChapterSQL($classGrepSearch,$varArray)
{
		$limit=$varArray['limit'];
		$start_span=$varArray['start_span'];
		$end_span=$varArray['end_span'];
		$bookset=$varArray['bookset'];
		$databasetable=$varArray['databasetable'];
	$sql = "select $databasetable.*  from (SELECT * FROM (";
	if($classGrepSearch->getCaseSensitive())
	{
		$binary = "binary";
	}
	else
	{
		$binary="";
	}
	$Books=getBooks();
	$searchRange="";
	if($limit == "bookset")
	{
		$searchRange ="( ";
		foreach($Books[getCategoryName($bookset)] as $BookName)
		{
			$bookId=array_search($BookName, $Books["All"]);
			$searchRange .="bookid = $bookId or ";
		}
		$searchRange=substr($searchRange,0,(strlen($searchRange)-3));
		$searchRange .=") and ";
	}
	if($limit == "span")
	{
		
		for($i=$start_span;$i<=$end_span;$i++)
		{
			if($i==$start_span)
			{
				$searchRange .="( ";
			}

			$searchRange .="bookid = $i ";
			if($i != $end_span)
			{
				$searchRange .=" or ";
			}
			else
			{
				$searchRange .=") and ";
			}
		}
	}
	$searchArray=$classGrepSearch->getSearchArray();

	foreach($searchArray as $search)
	{
		$sql.="SELECT DISTINCT bookid,chapterno FROM  $databasetable WHERE $searchRange $binary versetext like '%".$search."%'";
		$sql .= "UNION ALL ";
	}
	$sql=substr($sql,0,(strlen($sql)-10));
    $sql.=") AS tbl  GROUP BY tbl.bookid,tbl.chapterno  HAVING COUNT(*) = ".count($searchArray).") as tbl2 inner join $databasetable on tbl2.bookid=bibledb_kjv.bookid and tbl2.chapterno=bibledb_kjv.chapterno" ;

return $sql;

}



?> 