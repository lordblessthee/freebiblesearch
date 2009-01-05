<?php
$title="Advanced Search";
if(isset($_GET['preview'])) 
{
    $preview =true;
	$installprefix="sample.";
}
else
{
	$preview =false;
	$installprefix="";
}
require_once('data/'.$installprefix.'template.inc.php');
require_once('data/'.$installprefix.'header.inc.php');
echo $template['searchForm2']['StartHTML'];
?>

<!--<h1>Keyword&nbsp;Search</h1>

 
     <strong>Keyword&nbsp;Search</strong> |--> 
   
   
    
 <form method="post" action="result.php" name="keysearch">

    
<h3>Enter word(s) or phrase(s)</h3>

Example "Jesus love"<br />
 <input type="text" name="search" value="" size="35" /><br />
 <select name="searchtype" >
   
<option value="all" selected="selected" >Match ALL words</option>
<option value="any">Match ANY word</option>

<option value="phrase">Match EXACT phrase</option>

<option value="allInFile">Match ALL words in a chapter</option>
    
 </select>
 <br>
 <input id="casesensitive" type="checkbox" name="casesensitive" value="yes" /> 
CaseSensitive
 <input id="wholewordsonly" type="checkbox" name="wholewordsonly" value="yes" /> 
 
Match whole words only
 <h3>Select version(s)</h3>

<?php
	require_once('data/config.inc.php');
	//$biblesArray = file("Bibles.txt");
	echo "<select name=\"bibleVersion\" >";
	//foreach($biblesArray as $line)
	foreach($BibleVersion as $line)
	{
		//$tempResult = explode(",",$line);
		echo "<option value=\"".$line["shortname"]."\"";
		if($line["shortname"]=="kjv")
		{
			echo "selected=\"selected\"";
		}
		echo ">".$line["name"]."</option>";
			
	}
	echo "</select>";
?>
 <h3>Options:</h3>


 Display Options
 <br>
 <select name="displayas" >
  
<option value="short">References only</option>
<option value="long" selected="selected" >Verses + references</option>
    
 </select>
 </span><br />

 Display 
 <select name="resultspp" >
    
<option value="10">10</option>
<option value="25" selected="selected" >25</option>
<option value="50">50</option>
<option value="100">100</option>

<option value="250">500</option>
    
 </select>
     results per page <br />
     Sort by <br>
 <select name="sort" >

    

<option value="bookorder" selected="selected" >Book order</option>
<option value="relevance">Relevance</option>
    
 </select>
 <br>
 <input type="radio" name="limit" value="none" id="limit-none"  checked="checked" >
 Search entire Bible

 <br />

 <input type="radio" name="limit" value="bookset" id="limit-bookset" />
Limit search to 
<select name="bookset" id="limit-bookset-dropdown"  >
 
<option value="11">Apocalyptic Books</option>
<option value="3">Books of Moses</option>

<option value="9">Epistles</option>
<option value="4">Gospels</option>
<option value="8">Historical Books</option>
<option value="5">Major Prophets</option>
<option value="6">Minor Prophets</option>
<option value="2">New Testament</option>
<option value="1">Old Testament</option>
<option value="10">Pauline Epistles</option>

<option value="7">Wisdom Books</option>

    
</select>
<br>
 <input type="radio" name="limit" value="span" id="limit-span" 
 
  />

Search from 
 <select name="spanbegin" id="limit-span-begin" >

    
<option value="1" selected="selected" >Genesis</option>

<option value="2">Exodus</option>
<option value="3">Leviticus</option>
<option value="4">Numbers</option>
<option value="5">Deuteronomy</option>
<option value="6">Joshua</option>
<option value="7">Judges</option>
<option value="8">Ruth</option>

<option value="9">1 Samuel</option>
<option value="10">2 Samuel</option>

<option value="11">1 Kings</option>
<option value="12">2 Kings</option>
<option value="13">1 Chronicles</option>
<option value="14">2 Chronicles</option>
<option value="15">Ezra</option>
<option value="16">Nehemiah</option>

<option value="17">Esther</option>
<option value="18">Job</option>
<option value="19">Psalm</option>

<option value="20">Proverbs</option>
<option value="21">Ecclesiastes</option>
<option value="22">Song of Solomon</option>
<option value="23">Isaiah</option>
<option value="24">Jeremiah</option>

<option value="25">Lamentations</option>
<option value="26">Ezekiel</option>
<option value="27">Daniel</option>
<option value="28">Hosea</option>

<option value="29">Joel</option>
<option value="30">Amos</option>
<option value="31">Obadiah</option>
<option value="32">Jonah</option>

<option value="33">Micah</option>
<option value="34">Nahum</option>
<option value="35">Habakkuk</option>
<option value="36">Zephaniah</option>
<option value="37">Haggai</option>

<option value="38">Zechariah</option>
<option value="39">Malachi</option>
<option value="40">Matthew</option>

<option value="41">Mark</option>
<option value="42">Luke</option>
<option value="43">John</option>
<option value="44">Acts</option>
<option value="45">Romans</option>
<option value="46">1 Corinthians</option>

<option value="47">2 Corinthians</option>
<option value="48">Galatians</option>

<option value="49">Ephesians</option>
<option value="50">Philippians</option>
<option value="51">Colossians</option>
<option value="52">1 Thessalonians</option>
<option value="53">2 Thessalonians</option>
<option value="54">1 Timothy</option>
<option value="55">2 Timothy</option>

<option value="56">Titus</option>

<option value="57">Philemon</option>
<option value="58">Hebrews</option>
<option value="59">James</option>
<option value="60">1 Peter</option>
<option value="61">2 Peter</option>
<option value="62">1 John</option>
<option value="63">2 John</option>
<option value="64">3 John</option>

<option value="65">Jude</option>
<option value="66">Revelation</option>
  
 </select> 
     to 
 <select name="spanend" id="limit-span-end" >
    
<option value="1">Genesis</option>
<option value="2">Exodus</option>
<option value="3">Leviticus</option>
<option value="4">Numbers</option>
<option value="5">Deuteronomy</option>
<option value="6">Joshua</option>
<option value="7">Judges</option>
<option value="8">Ruth</option>

<option value="9">1 Samuel</option>
<option value="10">2 Samuel</option>

<option value="11">1 Kings</option>
<option value="12">2 Kings</option>
<option value="13">1 Chronicles</option>
<option value="14">2 Chronicles</option>
<option value="15">Ezra</option>
<option value="16">Nehemiah</option>

<option value="17">Esther</option>
<option value="18">Job</option>
<option value="19">Psalm</option>

<option value="20">Proverbs</option>
<option value="21">Ecclesiastes</option>
<option value="22">Song of Solomon</option>
<option value="23">Isaiah</option>
<option value="24">Jeremiah</option>

<option value="25">Lamentations</option>
<option value="26">Ezekiel</option>
<option value="27">Daniel</option>
<option value="28">Hosea</option>

<option value="29">Joel</option>
<option value="30">Amos</option>
<option value="31">Obadiah</option>
<option value="32">Jonah</option>

<option value="33">Micah</option>
<option value="34">Nahum</option>
<option value="35">Habakkuk</option>
<option value="36">Zephaniah</option>
<option value="37">Haggai</option>

<option value="38">Zechariah</option>
<option value="39">Malachi</option>
<option value="40">Matthew</option>

<option value="41">Mark</option>
<option value="42">Luke</option>
<option value="43">John</option>
<option value="44">Acts</option>
<option value="45">Romans</option>
<option value="46">1 Corinthians</option>

<option value="47">2 Corinthians</option>
<option value="48">Galatians</option>

<option value="49">Ephesians</option>
<option value="50">Philippians</option>
<option value="51">Colossians</option>
<option value="52">1 Thessalonians</option>
<option value="53">2 Thessalonians</option>
<option value="54">1 Timothy</option>
<option value="55">2 Timothy</option>

<option value="56">Titus</option>

<option value="57">Philemon</option>
<option value="58">Hebrews</option>
<option value="59">James</option>
<option value="60">1 Peter</option>
<option value="61">2 Peter</option>
<option value="62">1 John</option>
<option value="63">2 John</option>
<option value="64">3 John</option>

<option value="65">Jude</option>
<option value="66" selected="selected" >Revelation</option>
 </select>

 <br>
 <br>
 <input type="hidden" id="showmoreversions" name="showmoreversions" value="closed">
 <input type="submit" value="Search for keyword or phrase" />
 
 </form>
 
 <ul style="margin-top: 20px; padding-left:0;" class="txt-sm">
 <li>Bibles containing Deuterocanonical books have been excluded (<a 

href="/preferences/deuterocanon.php?previous_searchtype=keyword" title="include">include</a>)</li></ul></td></tr></table>

<?php 
echo $template['searchForm2']['EndHTML'];
require_once('data/'.$installprefix.'footer.inc.php');
?>

