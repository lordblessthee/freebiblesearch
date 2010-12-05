<?php

/**
 * File containing code to show the simple search form
 */

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
echo $template['LookupForm']['StartHTML'];
?>
   
    
 <form method="post" action="passageresult.php" name="lookup">

    
<h3>Enter passage for lookup</h3>

Example "John 3:16"<br />
 <input type="text" name="lookup" value="" size="60" /><br /> 
 <h3>Select version(s)</h3>

<?php 
	echo "<select name=\"bibleVersion[]\" >";
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
 if(isset($parallelBibles))
 {

	 for($i=0;$i<($parallelBibles-1);$i++)
	 {
		    echo "<br><br>";
		 	echo "<select name=\"bibleVersion[]\" >";
			if($preview)
			{
				$BibleVersionPresent = $sampleBibleVersion;
			}
			else
			{
				$BibleVersionPresent = $BibleVersion;
			}
			echo "<option value=\"\"" ;
			echo "selected=\"selected\"";
			echo ">Select Another Bible</option>";
			foreach($BibleVersionPresent as $line)
			{
				echo "<option value=\"".$line["shortname"]."\"";
				echo ">".$line["name"]."</option>";
			
			}
			echo "</select>";

	 }
	 echo "<br>";
 }
?>
<br>
  <input type="submit" value="Lookup passage" />
 
 </form>
 
<?php 
echo $template['LookupForm']['EndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');
?>

