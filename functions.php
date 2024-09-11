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

function createLinesFromFile($filePath, $classGrepSearch)
{
	global $template;

	$linesArray = file($filePath);
	$chapterNo = (int)substr($filePath, -7, 3);
	$htmlLines = "";
	$classGrepSearch->setGlobalCount(0);
	$newLine = "";
	for ($i = 0; $i < count($linesArray); $i++) {
		$verseNo = (int)substr($linesArray[$i], 0, strpos($linesArray[$i], " "));
		$verseText = substr($linesArray[$i], strpos($linesArray[$i], " "));
		$verseText = $classGrepSearch->allStrReplaceTag(
			htmlentities(html_entity_decode($verseText)),
			$template['searchResult']['Verse']['SearchKeyStartTag'],
			$template['searchResult']['Verse']['SearchKeyEndTag']
		);
		if ($classGrepSearch->getGlobalResult()) {
			$htmlLines .= eval("return \"" . $template['searchResult']['Verse']['ProcessHTML'] . "\";");
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
function getBookNames($startSpan, $endSpan)
{
	$Book = array();
	$Book = getBooks();
	$resultArray = array();
	for ($i = $startSpan; $i <= $endSpan; $i++) {
		array_push($resultArray, $Book["All"][$i]);
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
	$Category["11"] = "Apocalyptic Books";
	$Category["3"] = "Books of Moses";
	$Category["9"] = "Epistles";
	$Category["4"] = "Gospels";
	$Category["8"] = "Historical Books";
	$Category["5"] = "Major Prophets";
	$Category["6"] = "Minor Prophets";
	$Category["2"] = "New Testament";
	$Category["1"] = "Old Testament";
	$Category["10"] = "Pauline Epistles";
	$Category["7"] = "Wisdom Books";
	if (isset($Category[$aCategoryNumber])) {
		return $Category[$aCategoryNumber];
	} else {
		return false;
	}
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
	$resultArray = array();
	foreach ($Book[$aCategoryName] as $BookName) {
		array_push($resultArray, $BookName);
	}
	return $resultArray;
}

/**
 * Function to create HTML Lines from database for the given search string.
 *
 * @param array $databaseInfo
 * @param object $classGrepSearch
 * @return string
 */
function createLinesFromDB($databaseInfo, $classGrepSearch)
{
    global $limit, $start_span, $end_span, $bookset, $template, $version;

    // Initialize HTML lines
    $htmlLines = "";

    try {
        // Connect to the database
        $con = connectToDB($databaseInfo);

        // Check for connection errors
        if ($con->connect_error) {
            throw new Exception("Database connection failed.");
        }

        // Get search array
        $searchArray = $classGrepSearch->getSearchArray();
        if (empty($searchArray)) {
            return $template['searchResult']['NoMatches']['StartHTML'] .
                   $template['searchResult']['NoMatches']['EndHTML'];
        }

        // Generate SQL based on search type
        $sql = generateSQLQuery($classGrepSearch, $searchArray, $limit, $start_span, $end_span, $bookset, $databaseInfo['databasetable']);

        // Execute query
        $result = $con->query($sql);

        // Check if query execution failed
        if (!$result) {
            throw new Exception("Failed to execute query.");
        }

        // Process query result and build HTML lines
        $htmlLines = buildHTMLFromResult($result, $classGrepSearch, $template, $version);

        // Handle no results case
        if (empty($htmlLines)) {
            return $template['searchResult']['NoMatches']['StartHTML'] .
                   $template['searchResult']['NoMatches']['EndHTML'];
        }

        // Close tags for the last chapter and book
        $htmlLines .= $template['searchResult']['Chapter']['EndHTML'] .
                      $template['searchResult']['Book']['EndHTML'];

    } catch (Exception $e) {
        // Log the actual error message for developers
        error_log("Error in createLinesFromDB: " . $e->getMessage());

        // Return a user-friendly message without exposing technical details
        return "<p>We encountered an issue while processing your request. Please try again later.</p>";
    }

    return $htmlLines;
}

/**
 * Connects to the database.
 *
 * @param array $databaseInfo
 * @return mysqli
 */
function connectToDB($databaseInfo)
{
    return new mysqli(
        $databaseInfo['databasehost'],
        $databaseInfo['databaseusername'],
        $databaseInfo['databasepassword'],
        $databaseInfo['databasename']
    );
}

/**
 * Generates the SQL query based on the search type and parameters.
 *
 * @param object $classGrepSearch
 * @param array $searchArray
 * @param string $limit
 * @param int $start_span
 * @param int $end_span
 * @param string $bookset
 * @param string $databasetable
 * @return string
 */
function generateSQLQuery($classGrepSearch, $searchArray, $limit, $start_span, $end_span, $bookset, $databasetable)
{
    $Books = getBooks();
    $sql = "SELECT * FROM $databasetable WHERE ";

    // Handle limit cases (bookset, span)
    if ($limit === 'bookset') {
        $sql .= buildBooksetSQL($bookset, $Books);
    } elseif ($limit === 'span') {
        $sql .= buildSpanSQL($start_span, $end_span);
    }

    // Build search conditions
    $sql .= buildSearchSQL($classGrepSearch, $searchArray);

    return $sql;
}

/**
 * Builds SQL for limiting by bookset.
 *
 * @param string $bookset
 * @param array $Books
 * @return string
 */
function buildBooksetSQL($bookset, $Books)
{
    $bookIds = array_map(function($BookName) use ($Books) {
        return array_search($BookName, $Books["All"]);
    }, $Books[getCategoryName($bookset)]);

    return "(" . implode(" OR ", array_map(function($id) {
        return "bookid = $id";
    }, $bookIds)) . ") AND ";
}

/**
 * Builds SQL for limiting by span.
 *
 * @param int $start_span
 * @param int $end_span
 * @return string
 */
function buildSpanSQL($start_span, $end_span)
{
    $spanSQL = "(";
    for ($i = $start_span; $i <= $end_span; $i++) {
        $spanSQL .= "bookid = $i" . ($i !== $end_span ? " OR " : "");
    }
    return $spanSQL . ") AND ";
}

/**
 * Builds SQL for the search conditions.
 *
 * @param object $classGrepSearch
 * @param array $searchArray
 * @return string
 */
function buildSearchSQL($classGrepSearch, $searchArray)
{
    $searchSQL = "(";
    foreach ($searchArray as $search) {
        $binary = $classGrepSearch->getCaseSensitive() ? "BINARY " : "";
        $operator = $classGrepSearch->getSearchType() === "all" ? "AND" : "OR";
        $searchSQL .= "$binary versetext LIKE '%$search%' $operator ";
    }
    return substr($searchSQL, 0, -4) . ");";
}

/**
 * Builds HTML lines from the query result.
 *
 * @param mysqli_result $result
 * @param object $classGrepSearch
 * @param array $template
 * @return string
 */
function buildHTMLFromResult($result, $classGrepSearch, $template, $version)
{
    $Books = getBooks();
    $htmlLines = "";
    $prevBookID = 0;
    $prevChapterNo = 0;
    $bookFirsttime = true;

	while ($row = $result->fetch_assoc()) {
		$newLine = "";
		$BookID = (int)$row['BOOKID'];
		$ChapterNo = (int)$row['CHAPTERNO'];
		$verseNo = (int)$row['VERSENO']; // Get verse number from database
		$verseText = $classGrepSearch->allStrReplaceTag(htmlentities(html_entity_decode($row['VERSETEXT'])), $template['searchResult']['Verse']['SearchKeyStartTag'], $template['searchResult']['Verse']['SearchKeyEndTag']);
	
		if ($classGrepSearch->getGlobalResult()) {
			$newLine = buildVerseHTML($classGrepSearch, $template, $verseText, $BookID, $ChapterNo, $verseNo, $bookFirsttime, $prevBookID, $prevChapterNo, $version); // Pass $version
	
			// Update previous book and chapter variables
			$prevBookID = $BookID;
			$prevChapterNo = $ChapterNo;
			$htmlLines .= $newLine;
		}
	}
	

    return $htmlLines;
}

/**
 * Builds the HTML for a single verse.
 *
 * @param object $classGrepSearch
 * @param array $template
 * @param string $verseText
 * @param int $BookID
 * @param int $ChapterNo
 * @param bool $bookFirsttime
 * @param int $prevBookID
 * @param int $prevChapterNo
 * @return string
 */
function buildVerseHTML($classGrepSearch, $template, $verseText, $BookID, $ChapterNo, $verseNo, &$bookFirsttime, $prevBookID, $prevChapterNo, $version)
{
    // Ensure $version is passed correctly
    $Books = getBooks();
    $bookName = $Books["All"][$BookID];

    // Prepare the HTML for the verse
    $newLine = $template['searchResult']['Verse']['StartHTML'];
    $newLine .= eval("return \"" . $template['searchResult']['Verse']['ProcessHTML'] . "\";");
    $newLine .= $template['searchResult']['Verse']['EndHTML'];

    // Handle book and chapter transitions
    if ($BookID != $prevBookID) {
        $newLine = ($bookFirsttime ? "" : $template['searchResult']['Chapter']['EndHTML'] . $template['searchResult']['Book']['EndHTML']) .
            $template['searchResult']['Book']['StartHTML'] .
            eval("return \"" . $template['searchResult']['Book']['ProcessHTML'] . "\";") .
            $template['searchResult']['Chapter']['StartHTML'] .
            eval("return \"" . $template['searchResult']['Chapter']['ProcessHTML'] . "\";") . $newLine;
        $bookFirsttime = false;
    } elseif ($ChapterNo != $prevChapterNo) {
        $newLine = $template['searchResult']['Chapter']['EndHTML'] .
            $template['searchResult']['Chapter']['StartHTML'] .
            eval("return \"" . $template['searchResult']['Chapter']['ProcessHTML'] . "\";") . $newLine;
    }

    return $newLine;
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
	global $limit, $start_span, $end_span, $bookset;
	global $searchStringStartTag, $searchStringEndTag, $template;
	global $sampleSearch, $version;
	$classGrepSearch->setGlobalCount(0);
	$Books = getBooks();
	$htmlLines = "";
	$newLine = "";
	$prevChapterNo = 0;
	$ChapterNo = 0;
	$BookID = 0;
	$prevBookID = 0;
	$bookFirsttime = true;
	foreach ($sampleSearch as $row) {
		$newLine = "";
		$BookID = (int)$row[0];
		$ChapterNo = (int)$row[1];
		$verseNo = (int)$row[2];
		$bookName = $Books["All"][$BookID];
		$verseText = $classGrepSearch->allStrReplaceTag(
			htmlentities(html_entity_decode($row[3])),
			$template['searchResult']['Verse']['SearchKeyStartTag'],
			$template['searchResult']['Verse']['SearchKeyEndTag']
		);
		if ($classGrepSearch->getGlobalResult()) {
			$newLine .= $template['searchResult']['Verse']['StartHTML'];
			$newLine .= eval("return \"" . $template['searchResult']['Verse']['ProcessHTML'] . "\";");
			$newLine .= $template['searchResult']['Verse']['EndHTML'];

			if (($BookID != $prevBookID)) {
				if ($bookFirsttime) {
					$newLine = $template['searchResult']['Book']['StartHTML'] .
						eval("return \"" . $template['searchResult']['Book']['ProcessHTML'] . "\";") .
						$template['searchResult']['Chapter']['StartHTML'] .
						eval("return \"" . $template['searchResult']['Chapter']['ProcessHTML'] . "\";") .
						$newLine;
					$bookFirsttime = false;
				} else {
					$newLine = $template['searchResult']['Chapter']['EndHTML'] .
						$template['searchResult']['Book']['EndHTML'] .
						$template['searchResult']['Book']['StartHTML'] .
						eval("return \"" . $template['searchResult']['Book']['ProcessHTML'] . "\";") .
						$template['searchResult']['Chapter']['StartHTML'] .
						eval("return \"" . $template['searchResult']['Chapter']['ProcessHTML'] . "\";") .
						$newLine;
				}
			} else
				if (($ChapterNo != $prevChapterNo)) {
				$newLine = $template['searchResult']['Chapter']['EndHTML'] .
					$template['searchResult']['Chapter']['StartHTML'] .
					eval("return \"" . $template['searchResult']['Chapter']['ProcessHTML'] . "\";") .
					$newLine;
			}

			$prevBookID = $BookID;
			$prevChapterNo = $ChapterNo;
			$htmlLines .= $newLine;
		}
	}
	$htmlLines .= $template['searchResult']['Chapter']['EndHTML'];
	$htmlLines .= $template['searchResult']['Book']['EndHTML'];

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

function createAllinChapterSQL($classGrepSearch, $varArray)
{
	$limit = $varArray['limit'];
	$start_span = $varArray['start_span'];
	$end_span = $varArray['end_span'];
	$bookset = $varArray['bookset'];
	$databasetable = $varArray['databasetable'];
	$sql = "select $databasetable.*  from (SELECT * FROM (";
	if ($classGrepSearch->getCaseSensitive()) {
		$binary = "binary";
	} else {
		$binary = "";
	}
	$Books = getBooks();
	$searchRange = "";
	if ($limit == "bookset") {
		$searchRange = "( ";
		foreach ($Books[getCategoryName($bookset)] as $BookName) {
			$bookId = array_search($BookName, $Books["All"]);
			$searchRange .= "bookid = $bookId or ";
		}
		$searchRange = substr($searchRange, 0, (strlen($searchRange) - 3));
		$searchRange .= ") and ";
	}
	if ($limit == "span") {

		for ($i = $start_span; $i <= $end_span; $i++) {
			if ($i == $start_span) {
				$searchRange .= "( ";
			}

			$searchRange .= "bookid = $i ";
			if ($i != $end_span) {
				$searchRange .= " or ";
			} else {
				$searchRange .= ") and ";
			}
		}
	}
	$searchArray = $classGrepSearch->getSearchArray();

	foreach ($searchArray as $search) {
		$sql .= "SELECT DISTINCT bookid,chapterno FROM  $databasetable WHERE $searchRange $binary versetext like '%" . $search . "%'";
		$sql .= "UNION ALL ";
	}
	$sql = substr($sql, 0, (strlen($sql) - 10));
	$sql .= ") AS tbl  GROUP BY tbl.bookid,tbl.chapterno  HAVING COUNT(*) = " . count($searchArray) . ") as tbl2 inner join $databasetable on tbl2.bookid=$databasetable.bookid and tbl2.chapterno=$databasetable.chapterno";

	return $sql;
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

function getChaptersFromDB($databaseInfo, $bookName, $chapterNo)
{
	//	global $verseTextArray;
	$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetable = $databaseInfo['databasetable'];
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'];
	$Books = getBookIndex();
	$con = @mysqli_connect($databasehost, $databaseusername, $databasepassword) or die(mysqli_error());
	mysqli_select_db($con, $databasename);
	if (!isset($Books[$bookName])) {
		return false;
	} else {
		$bookId = (int)$Books[$bookName];
	}
	if ($chapterNo !== false) {
		$sql = " SELECT verseno, versetext FROM " . $databasetable . "  where bookid = $bookId and chapterno = $chapterNo;";
		$result = mysqli_query($con, $sql) or die(mysqli_error());
		$tempstring = "";
		while ($row = mysqli_fetch_array($result)) {
			$verseTextArray[] = $row;
		}
	}
	return $verseTextArray;
}

/**
 *
 * function to return array of Passages
 * from a lookup string
 *
 * @param $passageString string
 * @param  $previousPassageInfo array
 * 
 * return array
 */

function getPassage($passageString, $previousPassageInfo)
{
	$bibleVerseParseInfo["passage"] = $passageString;
	global $BookIndex, $BookAbbrIndex, $BibleChapterInfo, $Book;
	$pattern = "/^[\d]*[\s]*[a-zA-Z ]+/";
	if (preg_match($pattern, trim($passageString), $matches)) {
		$bibleVerseParseInfo['givenBookName'] = trim($matches[0]);
		if (isset($BookIndex[trim($matches[0])])) {
			$indexOfBook = $BookIndex[trim($matches[0])];
		} else
				if (isset($BookAbbrIndex[strtolower(trim($matches[0]))])) {
			$indexOfBook = $BookAbbrIndex[strtolower(trim($matches[0]))];
		} else {
			$bibleVerseParseInfo["status"] = "error";
			$bibleVerseParseInfo["statusMessage"] = "Book " . $bibleVerseParseInfo['givenBookName'] . " Not Found";
			return $bibleVerseParseInfo;
		}
		$bibleVerseParseInfo['bookIndex'] = $indexOfBook;
		$bibleVerseParseInfo['bookName'] = $Book["All"][$indexOfBook];
		$rest = substr(trim($passageString), strlen(trim($matches[0])));
		$rest = preg_replace('/^\./', '', trim($rest));
	} else 
			if (isset($previousPassageInfo['bookIndex'])) {
		$indexOfBook = $previousPassageInfo['bookIndex'];
		$bibleVerseParseInfo['bookIndex'] = $indexOfBook;
		$bibleVerseParseInfo['givenBookName'] = $previousPassageInfo['givenBookName'];
		$bibleVerseParseInfo['bookName'] = $Book["All"][$indexOfBook];
		$bibleVerseParseInfo['bookStatus'] = "calculated";
		$pattern = "/^\d/";
		if (preg_match($pattern, trim($passageString), $matches)) {
			$rest = $passageString;
		} else {
			$bibleVerseParseInfo["status"] = "error";
			$bibleVerseParseInfo["statusMessage"] = "incorrect format for " . $bibleVerseParseInfo['bookName'] . " $passageString";
			return $bibleVerseParseInfo;
		}
	} else {
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "incorrect format for $passageString";
		return $bibleVerseParseInfo;
	}

	$verse = str_replace("\xe2\x80\x93", '-', strtolower($rest)); // endash -> - 

	$nreg = '([0-9]+|[ivxlcm]+|[' . "\x90-\xAA" . "\xD7]+)";
	$reg = '/^' . $nreg . '\.?(\s*[:. ]\s*' . $nreg . ')?(\s*-\s*' . $nreg . '\.?(\s*[:. ]\s*' . $nreg . ')?)?/i';
	if (!preg_match($reg, trim($verse), $matches) && (!trim($rest) == "")) {
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "incorrect format for " . $bibleVerseParseInfo['bookName'] . " $rest";
		return $bibleVerseParseInfo;
	}

	$s_chap = isset($matches[1]) ? num_conv($matches[1]) : null;
	$s_vers = isset($matches[3]) ? num_conv($matches[3]) : null;
	$e_chap = isset($matches[5]) ? num_conv($matches[5]) : null;
	$e_vers = isset($matches[7]) ? num_conv($matches[7]) : null;



	if (isset($bibleVerseParseInfo['bookStatus']) && $bibleVerseParseInfo['bookStatus'] == "calculated") {

		if (
			isset($previousPassageInfo['startVerse']) && $previousPassageInfo['startVerseStatus'] != "calculated" && $previousPassageInfo['startVerseStatus'] != "incorrect"
			&& !$s_vers && (!isset($previousPassageInfo['endVerse']) || $previousPassageInfo['endVerseStatus'] == "calculated")
		) {
			$s_vers = $s_chap;
			$s_chap = $previousPassageInfo['startChap'];
		}

		if (
			isset($previousPassageInfo['endVerse']) && $previousPassageInfo['endVerseStatus'] != "calculated" && $previousPassageInfo['endVerseStatus'] != "incorrect"
			&& !$e_chap
		) {
			$s_vers = $s_chap;
			$s_chap = $previousPassageInfo['endChap'];
		}
	}


	if (!$s_chap) {
		$s_chap = 1;
		$bibleVerseParseInfo['startChapStatus'] = "calculated";
	}

	if (!$e_chap && $s_vers) { // eg 15 or 15:30 
		$e_chap = $s_chap;
		$e_vers = $s_vers;
		$bibleVerseParseInfo['endChapStatus'] = "calculated";
		$bibleVerseParseInfo['endChapStatusConvertedFrom'] = "startChapter";
		$bibleVerseParseInfo['endVerseStatus'] = "calculated";
		$bibleVerseParseInfo['endVerseStatusConvertedFrom'] = "startVerse";
	} elseif ($s_vers and $e_chap and !$e_vers) { // eg 15:30-35 
		$e_vers = $e_chap;
		$e_chap = $s_chap;
		$bibleVerseParseInfo['endChapStatus'] = "calculated";
		$bibleVerseParseInfo['endChapStatusConvertedFrom'] = "startChapter";
		$bibleVerseParseInfo['endVerseStatus'] = "calculated";
		$bibleVerseParseInfo['endVerseStatusConvertedFrom'] = "endChapter";
	} else
				if (!$e_chap && !$s_vers) {
		$e_chap = $s_chap;
		$s_vers = 1;
		$bibleVerseParseInfo['endChapStatus'] = "calculated";
		$bibleVerseParseInfo['startVerseStatus'] = "calculated";
	}


	if (!$s_vers) {
		$s_vers = 1;
		$bibleVerseParseInfo['startVerseStatus'] = "calculated";
	}
	if ($e_chap < $s_chap) {
		$bibleVerseParseInfo['endChapStatus'] = "incorrect";
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "end Chapter Cannot be less than start chapter for " . $bibleVerseParseInfo['bookName'] . " $s_chap-$e_chap";
		return $bibleVerseParseInfo;
	}
	if (($s_chap == $e_chap) && $e_vers && ($e_vers < $s_vers)) {
		$bibleVerseParseInfo['endVerseStatus'] = "incorrect";
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "end Verse Cannot be less than start Verse for " . $bibleVerseParseInfo['bookName'] . " $s_chap:$s_vers-$e_chap:$e_vers";
		return $bibleVerseParseInfo;
	}
	if (!isset($BibleChapterInfo[$indexOfBook]["Chapter"][$s_chap])) {
		$bibleVerseParseInfo['startChapStatus'] = "incorrect";
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "start Chapter not found for " . $bibleVerseParseInfo['bookName'] . " $s_chap";
		return $bibleVerseParseInfo;
	}

	if (!isset($BibleChapterInfo[$indexOfBook]["Chapter"][$e_chap])) {
		$bibleVerseParseInfo['endChapStatus'] = "incorrect";
		$bibleVerseParseInfo["status"] = "error";
		$bibleVerseParseInfo["statusMessage"] = "end Chapter not found for " . $bibleVerseParseInfo['bookName'] . " $e_chap";
		return $bibleVerseParseInfo;
	}

	if ($s_vers == 1 and !$e_vers) {
		$e_vers = $BibleChapterInfo[$indexOfBook]["Chapter"][$e_chap]["NumVerse"];
		$bibleVerseParseInfo['endVerseStatus'] = "calculated";
		$bibleVerseParseInfo['startChap'] = $s_chap;
		$bibleVerseParseInfo['startVerse'] = 1;
		$bibleVerseParseInfo['endChap'] = $e_chap;
		$bibleVerseParseInfo['endVerse'] = $e_vers;
		$bibleVerseParseInfo["status"] = "ok";
		return $bibleVerseParseInfo;
	} else {
		if ($BibleChapterInfo[$indexOfBook]["Chapter"][$s_chap]["NumVerse"] < $s_vers) {
			$bibleVerseParseInfo['startVerseStatus'] = "incorrect";
			$bibleVerseParseInfo["status"] = "error";
			$bibleVerseParseInfo["statusMessage"] = "start verse range exceeded for " . $bibleVerseParseInfo['bookName'] . " $s_chap:$s_vers";
			return $bibleVerseParseInfo;
		}

		if ($BibleChapterInfo[$indexOfBook]["Chapter"][$e_chap]["NumVerse"] < $e_vers) {
			$bibleVerseParseInfo['endVerseStatus'] = "incorrect";
			$bibleVerseParseInfo["status"] = "error";
			$bibleVerseParseInfo["statusMessage"] = "end verse range exceeded for " . $bibleVerseParseInfo['bookName'] . " $s_chap:$s_vers-$e_chap:$e_vers";
			return $bibleVerseParseInfo;
		}
		$bibleVerseParseInfo['startChap'] = $s_chap;
		$bibleVerseParseInfo['startVerse'] = $s_vers;
		$bibleVerseParseInfo['endChap'] = $e_chap;
		$bibleVerseParseInfo['endVerse'] = $e_vers;
		$bibleVerseParseInfo["status"] = "ok";
		return $bibleVerseParseInfo;
	}
}

/**
 *
 * function to convert from 
 * roman numerals to integer
 *
 * @param $r string
 * 
 * return integer
 */
function roman_to_int($r)
{
	$r = strtolower($r);
	$rvals = array('i' => 1, 'v' => 5, 'x' => 10, 'l' => 50, 'c' => 100, 'd' => 500, 'm' => 1000);
	$n = 0;
	for ($i = 0; $i < strlen($r); $i++) {
		if (($i == strlen($r) - 1) or ($rvals[$r[$i]] >= $rvals[$r[$i + 1]]))
			$n += $rvals[$r[$i]];
		else
			$n -= $rvals[$r[$i]];
	}
	return $n;
}


/**
 * Function to convert from Hebrew numerals to integer
 *
 * @param string $h
 * @return int
 */
function hebrew_to_int($h)
{
	$n = 0;
	for ($i = 0; $i < strlen($h); $i++) {
		$pos = ord($h[$i]) - 0x8f;
		switch ($pos) {
			case 11:
			case 12:
				$n += 20;
				break;
			case 13:
				$n += 30;
				break;
			case 14:
			case 15:
				$n += 40;
				break;
			case 16:
			case 17:
				$n += 50;
				break;
			case 18:
				$n += 60;
				break;
			case 19:
				$n += 70;
				break;
			case 20:
			case 21:
				$n += 80;
				break;
			case 22:
			case 23:
				$n += 90;
				break;
			default:
				if ($pos <= 10) $n += $pos;
				elseif ($pos <= 27) $n += ($pos - 23) * 100;
		}
	}
	return $n;
}

/**
 * Function to convert from Hebrew or Roman numerals to integer
 *
 * @param string $n
 * @return int
 */
function num_conv($n)
{
	if (!$n)
		return 0;
	if ($n[0] >= '0' && $n[0] <= '9')
		return intval($n);
	if ($n[0] <= 'z') { // assume Roman 
		return roman_to_int(strtoupper($n));
	}
	if ($n[0] == "\xD7") { // assume Hebrew unicode 
		return hebrew_to_int($n);
	}
	die('Unknown number form.');
}

/**
 * Function to get the short name from a Bible version array
 *
 * @param array $bibVersion
 * @return string
 */
function bibShortnameFunc($bibVersion)
{
	return $bibVersion['shortname'];
}
