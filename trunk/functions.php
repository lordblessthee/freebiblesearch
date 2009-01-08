<?php

/**
 * File containing miscellaneous functions
 */
require_once('ClassGrepSearch.inc.php');

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
		$verseText=substr($linesArray[$i],strpos($linesArray[$i]," "));
		$verseText = $classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($verseText)),
			$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag'] );
		if($classGrepSearch->getGlobalResult())
		{
			$htmlLines .=eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
		} 
	} 
	$globalSearchCount = $classGrepSearch->getGlobalCount(); 
	return	 $htmlLines; 
}






/**
 *
 * function to get array of all Book Names
 *
 * return array
 *
 */  

function getBooks()
{
	global $Book;
			
	return $Book;
}

/**
 *
 * function to get array of all Book Index from 
 *  Book Names
 * return array
 *
 */  


function getBookIndex()
{
	global $BookIndex;
			
	return $BookIndex;
}

/**
*
* function to return the Book Names
*  from start and end index
*
* @param $start_span number
* @param $end_span number
*
*	return array
*/  
function getBookNames($startSpan,$endSpan)
{
	$Book = array();
        $Book = getBooks();
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
* @param $aCategoryNumber number
*
*	return array
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
* @param $aCategoryName string
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

/**
*
* function to create HTML Lines
* from database for the given
* search string.
*
* @param $databaseInfo array
* @param $classGrepSearch class Instance
*
* return string
*/  

function createLinesFromDB($databaseInfo,$classGrepSearch)
{
        global $limit,$start_span,$end_span,$bookset,$template,$version;
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
	$htmlLines="";
	$newLine ="";
	$prevChapterNo=0;
	$ChapterNo=0;
	$BookID=0;
	$prevBookID=0;
	$bookFirsttime=true;
	  while($row=mysql_fetch_array($result))
	  {



		$newLine="";
		$BookID=(int)$row[BOOKID];
		$ChapterNo=(int)$row[CHAPTERNO];
		$verseNo=(int)$row[VERSENO];	
		$bookName=$Books["All"][$BookID];	$verseText=$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[VERSETEXT]))
			,$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag']);
		if($classGrepSearch->getGlobalResult())
		{
			$newLine .=$template['searchResult']['Verse']['StartHTML'];
			$newLine .=eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
			$newLine .=$template['searchResult']['Verse']['EndHTML'];

			if(($BookID!=$prevBookID))
			{
				if($bookFirsttime)
				{
					$newLine =$template['searchResult']['Book']['StartHTML'].
					eval("return \"".$template['searchResult']['Book']['ProcessHTML']."\";").
						$template['searchResult']['Chapter']['StartHTML'].
					eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
					$bookFirsttime=false;

				}else
				{
					$newLine =$template['searchResult']['Chapter']['EndHTML'].
						$template['searchResult']['Book']['EndHTML'].
						$template['searchResult']['Book']['StartHTML'].
					eval("return \"".$template['searchResult']['Book']['ProcessHTML']."\";").
						$template['searchResult']['Chapter']['StartHTML'].
					eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
				}
			}
			else
				if(($ChapterNo!=$prevChapterNo))
				{
					$newLine =$template['searchResult']['Chapter']['EndHTML'].
						$template['searchResult']['Chapter']['StartHTML'].
						eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
					
				}
			
			$prevBookID=$BookID;
			$prevChapterNo=$ChapterNo;
			$htmlLines .=$newLine;
		}
 
	 }
	 $htmlLines .=$template['searchResult']['Chapter']['EndHTML'];
	$htmlLines .=$template['searchResult']['Book']['EndHTML'];
	  return $htmlLines;

}

/**
*
* function to create HTML Lines
* for sample search for the given
* search string.
*
* @param $classGrepSearch class Instance
*
* return string
*/  

function createLinesFromSample($classGrepSearch)
{
	global $limit,$start_span,$end_span,$bookset;
	global $searchStringStartTag,$searchStringEndTag,$template;
	global $sampleSearch;
	$classGrepSearch->setGlobalCount(0);
	$Books=getBooks();
	$htmlLines="";
	$newLine ="";
	$prevChapterNo=0;
	$ChapterNo=0;
	$BookID=0;
	$prevBookID=0;
	$bookFirsttime=true;
	foreach($sampleSearch as $row)
	{
		$newLine="";
		$BookID=(int)$row[0];
		$ChapterNo=(int)$row[1];
		$verseNo=(int)$row[2];	
		$bookName=$Books["All"][$BookID];	$verseText=$classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row[3]))
			,$template['searchResult']['Verse']['SearchKeyStartTag'],
				$template['searchResult']['Verse']['SearchKeyEndTag']);
		if($classGrepSearch->getGlobalResult())
		{
			$newLine .=$template['searchResult']['Verse']['StartHTML'];
			$newLine .=eval("return \"".$template['searchResult']['Verse']['ProcessHTML']."\";");
			$newLine .=$template['searchResult']['Verse']['EndHTML'];

			if(($BookID!=$prevBookID))
			{
				if($bookFirsttime)
				{
					$newLine =$template['searchResult']['Book']['StartHTML'].
					eval("return \"".$template['searchResult']['Book']['ProcessHTML']."\";").
						$template['searchResult']['Chapter']['StartHTML'].
					eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
					$bookFirsttime=false;

				}else
				{
					$newLine =$template['searchResult']['Chapter']['EndHTML'].
						$template['searchResult']['Book']['EndHTML'].
						$template['searchResult']['Book']['StartHTML'].
					eval("return \"".$template['searchResult']['Book']['ProcessHTML']."\";").
						$template['searchResult']['Chapter']['StartHTML'].
					eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
				}
			}
			else
				if(($ChapterNo!=$prevChapterNo))
				{
					$newLine =$template['searchResult']['Chapter']['EndHTML'].
						$template['searchResult']['Chapter']['StartHTML'].
						eval("return \"".$template['searchResult']['Chapter']['ProcessHTML']."\";").
						$newLine;
					
				}
			
			$prevBookID=$BookID;
			$prevChapterNo=$ChapterNo;
			$htmlLines .=$newLine;
		}
	 }
	 $htmlLines .=$template['searchResult']['Chapter']['EndHTML'];
	$htmlLines .=$template['searchResult']['Book']['EndHTML'];
	 
	  return $htmlLines;
}

/**
*
* function to create SQL for all in chapter
* search for database
*
* @param $classGrepSearch class Instance
* @param $varArray array
*
* return string
*/ 

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