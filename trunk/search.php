<?php
$title="Simple Search";
require_once("data/template.inc.php");
require_once("data/header.inc.php");
echo $template['searchForm1']['StartHTML'];
?>
<h1>Simple&nbsp;Search</h1>

 
     <strong>Simple&nbsp;Search</strong> | 
	 <a href="advancedsearch.php">Advanced Search</a> | <a href="readbible.php">Read the Bible</a>
   
   
    
 <form method="post" action="result.php" name="keysearch">

    
<h3>Enter word(s) or phrase(s)</h3>

Example "Jesus love"<br />
 <input type="text" name="search" value="" size="35" /><br /> 
 <h3>Select version(s)</h3>

<?php 
	$biblesArray = file("Bibles.txt");
	echo "<select name=\"bibleVersion\" >";
	foreach($biblesArray as $line)
	{
		$tempResult = explode(",",$line);
		echo "<option value=\"$tempResult[0]\"";
		if($tempResult[0]=="kjv")
		{
			echo "selected=\"selected\"";
		}
		echo ">$tempResult[1]</option>";
			
	}
	echo "</select>";
?>
<br>
<input type="hidden" name="limit" value="none" >
<input type="hidden" name="searchtype" value="all" >
<input type="hidden" name="spanbegin" value="1" >
<input type="hidden" name="spanend" value="66" >
  <input type="submit" value="Search for keyword or phrase" />
 
 </form>
 
<?php 
echo $template['searchForm1']['EndHTML'];
require_once("data/footer.inc.php");
?>

