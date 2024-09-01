<?php
/**
 * Class containing function require to search Files like Unix Grep
 */



class ClassGrepSearch 
{
      private static $instance = 0;
      var		$searchType = "";
      var		$caseSensitive = false;
      var		$searchArray = array() ;
      var		$globalResult = true;
      var		$globalCount = 0;
      var		$fileCounter = 0;
      var		$arrayOfFilenames = array();
      var		$dirFile = array();
      var		$dirArr = array();
      var		$searchString = "";
      var		$newArrayOfExtentions = array();
      var		$scanDir = "";

 /**
 * returns an instance of the ClassForGrep object.
 * @access public
 * @static
 *
 */

public static function getInstance() 
{
	if (ClassGrepSearch::$instance === 0) 
	{
		ClassGrepSearch::$instance = new ClassGrepSearch();
	}
	return ClassGrepSearch::$instance;
}

public $classGrepSearch;

/**
* creates a new ClassGrepSearch object.
* @access protected
*/ 
public function __construct()
{
	return $this->classGrepSearch;
}

/**
* to create and array of extentions
*
*
* @param $separator string/char
* @param $filesWithExtentionsToBeSearched string
*
* @return array
*/
public function createArrayOfExtentions($separator,$filesWithExtentionsToBeSearched)
{
	$arrayOfExtentions = explode($separator,$filesWithExtentionsToBeSearched);
        foreach($arrayOfExtentions as $items ) 
	{
		$newArrayOfExtentions[] = trim($items);
        }
        $this->newArrayOfExtentions = $newArrayOfExtentions;
}
                
/**
*
* function to convert time in secs into mins
*
* @param $timeInSec int
*
* $return string
*/
public function convertSecToMins($timeInSec) 
{
	if( $timeInSec < 60 )
	{
		if( $timeInSec > 1)
		{
			$secString = "secs";
		}
		else
		{
			$secString = "sec";
		}
            	$timeTaken = "$timeInSec $secString";
        }
        else
	{
		$seconds = ($timeInSec % 60);
            	$minutes = ($timeInSec/60);
            	$minutes = sprintf("%01.0f", $minutes);
            	if( $minutes > 1)
		{
			$minString = "mins";            
            	}
            	else
		{
			$minString = "min";            
            	}       
            	if( $seconds > 1) 
		{        
                	 $secString = "secs";
		}
		else 
		{
			$secString = "sec";            
            	}
            	$timeTaken = "$minutes $minString $seconds $secString";
	}
	return $timeTaken;
}


/**
*
* function to replace last occurrences of the search string with 
* the replacement string .  
*
* @param $search string
* @param $replace string
* @param $subject string
*
* $return string 
*/
public function lastStrReplace($search, $replace, $subject)
{
	$positionOfLastString = strrpos($subject,$search);
        $stingBeforeSearchSting = substr($subject, 0, $positionOfLastString)." "; 
        $stingAfterSearchSting = substr($subject, $positionOfLastString+1, strlen($subject)); 
        return $newString = $stingBeforeSearchSting.$replace.$stingAfterSearchSting;
}

/**
*
* function to replace all occurrences of the search string with 
* the replacement string .  
*
* @param $replace string 
* @param $subject string 
*
* $return string 
*/

public function allStrReplace($subject, $replace) 
{
	$result="";
    	$strPositions=$this->searchMultipleStrPositions($subject);
	if(count($strPositions)!=0)
	{
		$this->globalCount += count($strPositions); 
	  	$currentString = $subject; 
	  	$offset = 0;
	  	foreach($strPositions as $pos) 
	  	{
			$stringBeforeSearchString = substr($currentString, 0, $pos[0]-$offset);
	    		$stringAfterSearchString = substr($currentString,
	    		$pos[0]+$pos[1]-$offset, strlen($currentString));
	    		$result=$result.$stringBeforeSearchString.$replace; 
	    		$currentString=$stringAfterSearchString;
	    		$offset+= strlen($stringBeforeSearchString)+$pos[1];
		}
	  	$result=$result.$stringAfterSearchString;
	  	$this->globalResult = true;
	  	return $result; 
	 }
	 else 
	 {
		$this->globalResult = false; 
		return $subject;
	} 
	$subject = $result; 
	$this->globalResult = true; 
	return $subject;
}

/**
*
* function to replace all occurrences of the search string with 
* the string enclosed in search and end tags .  
*
* @param $search string 
* @param $startTag string 
* @param $endTag string 
*
* $return string 
*/
public function allStrReplaceTag($subject,$startTag,$endTag)
{
	$result="";
    	$strPositions=$this->searchMultipleStrPositions($subject);
	if(count($strPositions)!=0) 
	{
		$this->globalCount += count($strPositions); 
	  	$currentString = $subject; 
	  	$offset = 0;
	  	foreach($strPositions as $pos)
		{
			$stringBeforeSearchString = substr($currentString, 0, $pos[0]-$offset);
	    		$stringAfterSearchString = substr($currentString,
	    		$pos[0]+$pos[1]-$offset, strlen($currentString));
	    		$result=$result.$stringBeforeSearchString.$startTag.substr($subject,$pos[0] , $pos[1]).$endTag; 
	    		$currentString=$stringAfterSearchString;
	    		$offset+= strlen($stringBeforeSearchString)+$pos[1];
		}
	  	$result=$result.$stringAfterSearchString;
	  	$this->globalResult = true;
	  	return $result;
	}
	else
	{
		$this->globalResult = false; 
		return $subject; 
	} 
	$subject = $result; 
	$this->globalResult = true; 
	return $subject;
}


/** 
* 
* function to search all string occurences and return   
*    the array of occurences.   
* 
* @param $search string  
* @param $subject string 
* 
* $return array 
*/
     
public function searchStrPositions($search, $subject)
{
	$searchOccurrences = array(); 
	$positionOfFirstString =
        strpos($subject,$search);
	if($positionOfFirstString)
	{
		$positionOfCurrentString= $positionOfFirstString;
        	$currentString=$subject; $stringOffset=0;
        	while($positionOfCurrentString)
		{
			array_push($searchOccurrences,$positionOfCurrentString);
         		$stringAfterSearchString = substr($currentString,
         		$positionOfCurrentString+strlen($search),
				strlen($currentString));
         		$currentString=$stringAfterSearchString;
         		$positionOfCurrentString = strpos($currentString,$search); 
		}
	}
	return $searchOccurrences;
}

/**
*
* function to search all string occurences of multiple
* strings in array and return  
* the array of occurences.   
* 
* @param $subject string 
* 
* $return array 
*/

public function searchMultipleStrPositions($subject) 
{
	$searchOccurrences = array();
	$stringOffset = 0;
	while(true)
	{
		$newPosition = false;
		foreach($this->searchArray as $search)
		{

			$search = trim($search);
			if($this->caseSensitive)
			{
				$positionOfCurrentString = strpos($subject,$search,$stringOffset);
			}
			else
			{
				$positionOfCurrentString = stripos($subject,$search,$stringOffset);
			}
			
			if(($newPosition === false||$positionOfCurrentString<$newPosition  ) 
								  && $positionOfCurrentString!==false)
			{
				$newPosition= $positionOfCurrentString;
				$currentSearchTerm = $search;
			}

		}

		if($newPosition === false)
		{
			break;
		}
            	$positionOfCurrentString = $newPosition;
		$searchTermArray=array($positionOfCurrentString,strlen($currentSearchTerm));
		array_push($searchOccurrences,$searchTermArray);
		$stringOffset=$positionOfCurrentString+strlen($currentSearchTerm);
	}
		
	if($this->searchType=="all")
	{
		$theNewString = " ";
		foreach($searchOccurrences as $pos)
		{
			$theNewString = $theNewString.substr($subject,$pos[0],$pos[1]);
		}
		$allSearchStringsPresent = true;
            	foreach($this->searchArray as $search)
		{
			$search = trim($search);

			if($this->caseSensitive)
			{
				$positionOfCurrentString = strpos($theNewString,$search);
			}
			else
			{
				$positionOfCurrentString = stripos($theNewString,$search);
			}
			if(!$positionOfCurrentString)
			{
				$allSearchStringsPresent = false;
				break;
			}

		}
		if(!$allSearchStringsPresent)
		{
			$searchOccurrences = array();
			return $searchOccurrences;
		}
	}
	return $searchOccurrences;
}

/**
*
*  function to read all files in the
*  given array of subdirectories
*  and path .
*
* @param $path string 
* @param $subdirectories array of strings
*
*/    

public function readDirSubDirs($path,$subDirectories) 
{
	foreach($subDirectories as $subDir)
	{
		$subDir = $path . $subDir . "/" ;
            	array_push($this->dirArr,$subDir) ; 
		$this->readFiles($subDir);
	}
}
    
/**
*
* function to get the search count  
*  of keyword in a specified file. 
*
* @param $filePath string 
*
*/   

public function getSearchCount($filePath) 
{
	$fileContents=file_get_contents($filePath);
	$searchCount = 0;
	foreach($this->searchArray as $searchStr)
	{
        if($this->caseSensitive)
		{
			$searchCount += substr_count($fileContents,$searchStr);
		}
		else
		{
			$searchCount += substr_count(strtoupper($fileContents),strtoupper($searchStr));
		}
	}
	return  $searchCount;
}

/**
*
* function to craete a search Array of strings
* from a given Search String. 
*
* @param $searchString string 
*
*/   

public function createSearchArray($searchString) 
{
	$newSearchString=trim(stripslashes ($searchString));
	if($this->searchType == "phrase")
	{
		$newSearchString = "\"".$searchString."\"";
	}
	$this->searchArray = preg_split( "/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|[\s,]+/", $newSearchString, 0, PREG_SPLIT_DELIM_CAPTURE );
	$this->searchArray = array_filter(array_map('trim', $this->searchArray));

}

/**
 *
 * function to read the directories and files in
 * the specified path for keyword
 *
 * @param $path string 
 *
*/

public function readDir($path)  
{
	$handle = @opendir($path); 
  	while($file = @readdir($handle))  
  	{
		$totalCount = 0;
		if ($file != "." && $file != ".." && is_dir($path . $file))
		{
			$subDir = $path . $file . "/";
			array_push($this->dirArr, $file);
			$this->readDir($subDir); 
		} 
		else if ($file != "." && $file != ".." && is_file($path . $file))  
		{
			$subDir = $path . $file;
			$temp = explode(".", $file); 
			$ext = end($temp); 
			$filePath = $subDir;
			$displayFilename = str_replace($this->scanDir, './', $filePath);

			// Check if the file extension is in the allowed extensions array
			if (in_array(trim($ext), $this->newArrayOfExtentions)) 
			{
				$fileContents = file_get_contents($filePath);
				$this->createSearchArray($this->searchString);

				$matchFound = ($this->searchType === "allInFile");
				foreach ($this->searchArray as $searchStr)
				{
					if ($this->caseSensitive)
					{
						$searchCount = strpos($fileContents, $searchStr);
					}
					else
					{
						$searchCount = stripos($fileContents, $searchStr);
					}

					if ($this->searchType === "allInFile")
					{
						// For "allInFile", ensure all words must match
						if ($searchCount === false) 
						{
							$matchFound = false;
							break;
						}
					}
					else
					{
						// For other search types, we just need one match
						if ($searchCount !== false)
						{
							$matchFound = true;
							break;
						}
					}
				}

				if ($matchFound) 
				{
					array_push($this->arrayOfFilenames, $displayFilename);
					$this->fileCounter++;
				}
				array_push($this->dirFile, $file);
			}
		}
	}
	return $this->fileCounter;
}


/**
*
* function to read only files in
* the specified path for keyword
*
* @param $path string 
*
*/
public function readFiles($path) 
{
    $handle = @opendir($path);
    while ($file = @readdir($handle)) 
    {
        if ($file != "." && $file != ".." && is_file($path . $file)) 
        {
            $subDir = $path . $file;
            $temp = explode(".", $file);
            $ext = end($temp);
            $filePath = $subDir;
            $displayFilename = str_replace($this->scanDir, './', $filePath);

            // Start searching and replacing
            // Searches only files with extensions in the given array
            if (in_array(trim($ext), $this->newArrayOfExtentions)) 
            {
                $fileContents = file_get_contents($filePath);

                $matchFound = ($this->searchType === "allInFile");
                foreach ($this->searchArray as $searchStr) 
                {
                    if ($this->caseSensitive) 
                    {
                        $searchCount = strpos($fileContents, $searchStr);
                    } 
                    else 
                    {
                        $searchCount = stripos($fileContents, $searchStr);
                    }

                    if ($this->searchType === "allInFile") 
                    {
                        // For "allInFile", ensure all words must match
                        if ($searchCount === false) 
                        {
                            $matchFound = false;
                            break;
                        }
                    } 
                    else 
                    {
                        // For other search types, we just need one match
                        if ($searchCount !== false) 
                        {
                            $matchFound = true;
                            break;
                        }
                    }
                }

                if ($matchFound) 
                {
                    array_push($this->arrayOfFilenames, $displayFilename);
                    $this->fileCounter++;
                }
                array_push($this->dirFile, $file);
            }
        }
    }
    return $this->fileCounter;
}


/**
*
* function to initialize the array 
* of file names
*
*/

public function initializeArrayOfFilenames()
{
	$this->arrayOfFilenames = array();
	$this->fileCounter = 0;

	return $this->arrayOfFilenames ;
}

/**
*
* Miscellaneous setters and getters
*
**/


public function setGlobalCount($aCount)
{
	$this->globalCount = $aCount;
}

public function getGlobalCount()
{
	return $this->globalCount ;
}

public function setSearchType($aSearchType)
{
	$this->searchType = $aSearchType;
}

public function setSearchString($aSearchString)
{
	$this->searchString = $aSearchString;
}

public function getSearchType()
{
	return $this->searchType ;
}

public function getSearchString()
{
	return $this->searchString;
}

public function getarrayOfFilenames()
{
	return $this->arrayOfFilenames ;
}


public function setScanDir($aScanDir)
{
	$this->scanDir = $aScanDir;
}

public function getScanDir()
{
	return $this->scanDir;
}

public function setCaseSensitive($aCaseSensitive)
{
	$this->caseSensitive = $aCaseSensitive;
}

public function getCaseSensitive()
{
	return $this->caseSensitive;
}
public function getDirFile()
{
	return $this->dirFile;
}

public function getDirArray()
{
	return $this->dirArr;
}

public function setGlobalResult($aResult)
{
	$this->globalResult = $aResult;
}

public function getGlobalResult()
{
	return $this->globalResult ;
}

public function getSearchArray()
{
	return $this->searchArray ;
}


}

?> 