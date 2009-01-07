<?php
$title="Bible Search";
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
require_once('data/'.$installprefix.'header.inc.php');
require_once('data/'.$installprefix.'config.inc.php');
echo $template['searchForm1']['StartHTML'];
?>
   
    
 <form method="post" action="result.php" name="keysearch">

    
<h3>Enter word(s) or phrase(s)</h3>

Example "Eternal Life"<br />
 <input type="text" name="search" value="" size="35" /><br /> 
 <h3>Select version(s)</h3>

<?php 
	echo "<select name=\"bibleVersion\" >";
	if($preview)
	{
		$BibleVersionPresent = $sampleBibleVersion;
	}
	else
	{
		$BibleVersionPresent = $BibleVersion;
	}
	foreach($BibleVersionPresent as $line)
	{
		echo "<option value=\"".$line["shortname"]."\"";
		if($line["shortname"]=="kjv")
		{
			echo "selected=\"selected\"";
		}
		echo ">".$line["name"]."</option>";
			
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
require_once('data/'.$installprefix.'footer.inc.php');
?>

