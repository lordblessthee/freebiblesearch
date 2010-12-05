<?php

/**
 * File containing the data for processing templates
 */

$template['HeaderText']="<STYLE fprolloverstyle>A:hover {
	COLOR: #ff8080; FONT-VARIANT: small-caps; TEXT-DECORATION: none
}
</STYLE>";
$template['HeaderBodyText']="text=#020280 vLink=#574d95 aLink=#ff80c0 link=#6bc552 bgProperties=fixed 
bgColor=#ffffff background=\"data/".$installprefix."images/bak.gif\"";

$template['readBible']['ErrorMessage']['StartHTML']="<centre><FONT face=Verdana Color = red size=3>";
$template['readBible']['ErrorMessage']['ProcessHTML']="<center>\$errorMessage <BR> </center>";
$template['readBible']['ErrorMessage']['EndHTML']="</FONT><BR>";
$template['readBible']['ShowBooks']['StartHTML']="
<P>&nbsp;</P>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a><br>
<B><CENTER><FONT face=Verdana size=5>Bible Book Index</FONT></CENTER>
";
$template['readBible']['ShowBooks']['EndHTML']="</B>";
$template['readBible']['ShowBooks']['Book']['StartHTML']="
<FONT 
face=Verdana size=3><BR><BR>
<BLOCKQUOTE>
  <P align=left>";
 $template['readBible']['ShowBooks']['Book']['ProcessHTML']=" <br><a href='readbible.php?version=\$version&book=\$bookName'> \$bookName</a><br> ";
 $template['readBible']['ShowBooks']['Book']['EndHTML']="</P></BLOCKQUOTE></FONT>";
$template['readBible']['ShowBooks']['ChapterLinks']['StartHTML']="";
$template['readBible']['ShowBooks']['ChapterLinks']['ProcessHTML']=" [<a href='readbible.php?version=\$version&book=\$bookName&chapter=\$chapterNo'>\$chapterNo</a>] ";
$template['readBible']['ShowBooks']['ChapterLinks']['EndHTML']="<br><br>";
$template['readBible']['ShowVerses']['ShowChapterLinks']=true;
$template['readBible']['ShowVerses']['StartHTML']="
<BR>
<P>&nbsp;</P>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a><br>
<b>";
$template['readBible']['ShowVerses']['EndHTML']="</b>";
$template['readBible']['ShowVerses']['BookIndex']['StartHTML']="";
$template['readBible']['ShowVerses']['BookIndex']['ProcessHTML']="
<BR>
<P align=center><A href='readbible.php?version=\$version'><B><FONT 
face=Verdana size=5>Bible Book - INDEX</FONT></B></A></P>
";
$template['readBible']['ShowVerses']['BookIndex']['EndHTML']="";
$template['readBible']['ShowVerses']['Book']['StartHTML']="";
$template['readBible']['ShowVerses']['Book']['ProcessHTML']="<FONT COLOR='blue' size=4 face=Verdana>\$bookName </FONT><br>";
$template['readBible']['ShowVerses']['Book']['EndHTML']="";
$template['readBible']['ShowVerses']['ChapterLinks']['StartHTML']="<FONT 
face=Verdana size=3>";
$template['readBible']['ShowVerses']['ChapterLinks']['ProcessHTML']="<b>[<a href='readbible.php?version=\$version&book=\$bookName&chapter=\$chapterNoLink'>\$chapterNoLink</a>]</b>";
$template['readBible']['ShowVerses']['ChapterLinks']['EndHTML']="</FONT><BR><BR>";
$template['readBible']['ShowVerses']['Chapter']['StartHTML']="";
$template['readBible']['ShowVerses']['Chapter']['ProcessHTML']="<i><FONT COLOR='blue' face=Verdana size=4>Chapter  \$ChapterNo </FONT></i>";
$template['readBible']['ShowVerses']['Chapter']['EndHTML']="";
$template['readBible']['ShowVerses']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['readBible']['ShowVerses']['Verse']['ProcessHTML']=" \$ChapterNo:\$verseNo \$verseText <BR><BR> ";
$template['readBible']['ShowVerses']['Verse']['EndHTML']="</FONT></BLOCKQUOTE>"; 
$template['readBible']['ShowBibleVersions']['StartHTML']="
<BR>
<h1>Select&nbsp;Version</h1>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <strong>Read the Bible</strong>
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['readBible']['ShowBibleVersions']['EndHTML']="</B>";
$template['readBible']['General']['StartHTML']="
<BR>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a>
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['readBible']['General']['EndHTML']="</B>";
$template['readBible']['ShowBibleVersions']['Version']['StartHTML']="
<P><FONT face=Verdana size=3>Versions :<BR><ol>";
$template['readBible']['ShowBibleVersions']['Version']['ProcessHTML']="<a href='readbible.php?version=\$versionShortName'>\$versionName</a><BR><BR>";
$template['readBible']['ShowBibleVersions']['Version']['EndHTML']="</ol></FONT></P>";
$template['thesaurus']['StartHTML']="
<BR>
<h1>Thesaurus&nbsp;Search</h1>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a>
<BR>
<BR>";
$template['thesaurus']['Word']['StartHTML']="";
 $template['thesaurus']['Word']['ProcessHTML']="<b>
 The Word '\$word' has the following synonyms</b><br>
 <i>Please click on the synonyms to search the Bible.</i><br><br>";
 $template['thesaurus']['Word']['EndHTML']="";
$template['thesaurus']['EndHTML']="<br><br>Courtesy http://wordnet.princeton.edu/ &nbsp; &nbsp;Please see 
license <a href='http://wordnet.princeton.edu/license'>details here</a>";
$template['thesaurus']['SynonymsArray']['StartHTML']="
<P><FONT face=Verdana size=3>Synonyms :";
$template['thesaurus']['SynonymsArray']['EndHTML']="</FONT></P><BR><BR>";
$template['thesaurus']['Synonyms']['StartHTML']="";
$template['thesaurus']['Synonyms']['ProcessHTML']="  [<a href='result.php?bibleVersion=\$version&search=\".trim(\$synonymsentry).\"' target='_blank' >\$synonymsentry</a> ] ";
$template['thesaurus']['Synonyms']['EndHTML']="";
$template['searchResult']['StartHTML']="
<h1>Search&nbsp;Result</h1>
     <a href=\"search.php\">Bible&nbsp;Search</a> | 
	 <a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a><br>";
$template['searchResult']['EndHTML']="";
$template['searchResult']['ErrorMessage']['StartHTML']="<centre><FONT face=Verdana Color = red size=3>";
$template['searchResult']['ErrorMessage']['ProcessHTML']="<center>\$errorMessage <BR> </center>";
$template['searchResult']['ErrorMessage']['EndHTML']="</FONT><BR>";
$template['searchResult']['KeywordList']['StartHTML']="<HR> The Search Keyword '<b>";
$template['searchResult']['KeywordList']['ProcessHTML']="<a href='thesaurus.php?word=\$keyword&version=\$version'>\$keyword</a>";
$template['searchResult']['KeywordList']['EndHTML']="</b><superscript>*</superscript>' was found in following verse(s): <BR> <superscript>*</superscript> <small>Click on the keywords to search the thesaurus for synonyms</small>";
$template['searchResult']['BibleVersion']['StartHTML']="
<P><FONT face=Verdana size=4>";
$template['searchResult']['BibleVersion']['ProcessHTML']="<b><FONT COLOR='blue'>\$bibleName</FONT></b><br>";
$template['searchResult']['BibleVersion']['EndHTML']="</FONT></P>";
$template['searchResult']['Book']['StartHTML']="
<P><FONT face=Verdana size=3>";
$template['searchResult']['Book']['ProcessHTML']="<b><FONT COLOR='blue'>\$bookName </FONT></b><br>";
$template['searchResult']['Book']['EndHTML']="</FONT></P>";
$template['searchResult']['Chapter']['StartHTML']="";
$template['searchResult']['Chapter']['ProcessHTML']="<i><a href='readbible.php?version=\$version&book=\$bookName&chapter=\$ChapterNo'>Chapter  \$ChapterNo </a></i> ";
$template['searchResult']['Chapter']['EndHTML']="";
$template['searchResult']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['searchResult']['Verse']['ProcessHTML']=" v\$verseNo \$verseText <BR> ";
$template['searchResult']['Verse']['EndHTML']="</P></FONT></BLOCKQUOTE>";
$template['searchResult']['Verse']['SearchKeyStartTag']="<b><font color ='red'>";
$template['searchResult']['Verse']['SearchKeyEndTag']="</font></b>";
$template['searchResult']['NoMatches']['StartHTML']="<i>No Matches Found";
$template['searchResult']['NoMatches']['EndHTML']="</i>";
$template['searchForm']['StartHTML']="<h1>Bible&nbsp;Search</h1>
										<strong>Bible&nbsp;Search</strong> | 
	 <a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a>";
$template['searchForm']['EndHTML']="";
$template['advSearchForm']['StartHTML']="<h1>Advanced&nbsp;Search</h1>
										<a href=\"search.php\">Bible&nbsp;Search</a>| 
										<strong>Advanced Search</strong> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a>";
$template['advSearchForm']['EndHTML']="";
$template['LookupForm']['StartHTML']="<h1>Passage&nbsp;Lookup</h1>
										<a href=\"search.php\">Bible&nbsp;Search</a>| 
										<a href=\"advancedsearch.php\">Advanced Search</a> | <strong>Passage Lookup</strong> | <a href=\"readbible.php\">Read the Bible</a>";
$template['LookupForm']['EndHTML']="";

$template['LookupResult']['StartHTML']="<h1>Passage&nbsp;Lookup Result</h1>
										<a href=\"search.php\">Bible&nbsp;Search</a>| 
										<a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"passagelookup.php\">Passage Lookup</a> | <a href=\"readbible.php\">Read the Bible</a><br>";
$template['LookupResult']['EndHTML']="";
$template['LookupResult']['ErrorMessage']['StartHTML']="<FONT face=Verdana Color = red size=3>";
$template['LookupResult']['ErrorMessage']['ProcessHTML']="<center>\$errorMessage <BR> </center>";
$template['LookupResult']['ErrorMessage']['EndHTML']="</FONT><BR>";
$template['LookupResult']['EndHTML']="";
$template['LookupResult']['VerseListMessage']['StartHTML']="<BR><BR><b><FONT face=Verdana  size=3>Referred Passage : ";
$template['LookupResult']['VerseListMessage']['ProcessHTML']="\$verseListMessage ;";
$template['LookupResult']['VerseListMessage']['EndHTML']="</FONT></b><BR>";

?>