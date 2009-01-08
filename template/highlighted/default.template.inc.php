<?php
$template['HeaderText']="";
$template['HeaderBodyText']="";
$template['readBible']['ShowBooks']['StartHTML']="
<P>&nbsp;</P><B>
<P><U><CENTER><FONT face=Verdana size=5>Bible Book Index</FONT></CENTER>
";
$template['readBible']['ShowBooks']['EndHTML']="";
$template['readBible']['ShowBooks']['Book']['StartHTML']="
<FONT 
face=Verdana size=3><BR><BR></FONT></U><FONT face=Verdana size=3>
<BLOCKQUOTE>
  <P align=left><FONT face=\"Verdana size=\" 3>";
 $template['readBible']['ShowBooks']['Book']['ProcessHTML']=" <br><a href='readBible.php?version=\$version&book=\$bookName'> \$bookName</a><br> ";
$template['readBible']['ShowBooks']['Book']['EndHTML']="</FONT>
  <P align=center><FONT face=\"Verdana size=\" 3><BR><BR></FONT></FONT></B>
  ";
$template['readBible']['ShowBooks']['ChapterLinks']['StartHTML']="";
$template['readBible']['ShowBooks']['ChapterLinks']['ProcessHTML']=" [<a href='readBible.php?version=\$version&book=\$bookName&chapter=\$chapterNo'>\$chapterNo</a>] ";
$template['readBible']['ShowBooks']['ChapterLinks']['EndHTML']="<br><br>";
$template['readBible']['ShowVerses']['ShowChapterLinks']=true;
$template['readBible']['ShowVerses']['StartHTML']="
<BR>
<!--<P><P></P>-->
<P>&nbsp;</P><B>
<!--<P>-->";
$template['readBible']['ShowVerses']['EndHTML']="";
$template['readBible']['ShowVerses']['BookIndex']['StartHTML']="";
$template['readBible']['ShowVerses']['BookIndex']['ProcessHTML']="<FONT face=Verdana size=3>
<FONT face=Verdana size=3><A 
href='readBible.php?version=\$version'><BR>
<P align=center><A href='readBible.php?version=\$version'><B><FONT 
face=Verdana size=5>Bible Book - INDEX</FONT></B></A></P></A></FONT>
";
$template['readBible']['ShowVerses']['BookIndex']['EndHTML']="";
$template['readBible']['ShowVerses']['Book']['StartHTML']="";
$template['readBible']['ShowVerses']['Book']['ProcessHTML']="<b><FONT COLOR='blue' size=4>\$bookName </FONT></b><br>";
$template['readBible']['ShowVerses']['Book']['EndHTML']="";
$template['readBible']['ShowVerses']['ChapterLinks']['StartHTML']="";
$template['readBible']['ShowVerses']['ChapterLinks']['ProcessHTML']=" [<a href='readBible.php?version=\$version&book=\$bookName&chapter=\$chapterNoLink'>\$chapterNoLink</a>]";
$template['readBible']['ShowVerses']['ChapterLinks']['EndHTML']="<BR><BR>";
$template['readBible']['ShowVerses']['Chapter']['StartHTML']="";
$template['readBible']['ShowVerses']['Chapter']['ProcessHTML']="<i><FONT COLOR='blue'size=4>Chapter  \$ChapterNo </FONT></i>";
$template['readBible']['ShowVerses']['Chapter']['EndHTML']="";
$template['readBible']['ShowVerses']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['readBible']['ShowVerses']['Verse']['ProcessHTML']=" \$ChapterNo:\$verseNo \$verseText <BR><BR> ";
$template['readBible']['ShowVerses']['Verse']['EndHTML']=""; 
$template['readBible']['ShowBibleVersions']['StartHTML']="
<BR>
<h1>Select&nbsp;Version</h1>
<a href=\"search.php\">Bible&nbsp;Search</a>| 
<a href=\"advancedsearch.php\">Advanced Search</a> | <strong>Read the Bible</strong>
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['readBible']['ShowBibleVersions']['EndHTML']="";
$template['readBible']['ShowBibleVersions']['Version']['StartHTML']="
<P><FONT face=Verdana size=3>Versions :<BR><ol>";
$template['readBible']['ShowBibleVersions']['Version']['ProcessHTML']="<a href='readBible.php?version=\$versionShortName'>\$versionName</a><BR><BR>";
$template['readBible']['ShowBibleVersions']['Version']['EndHTML']="</ol></FONT></P>";
$template['thesaurus']['StartHTML']="
<BR>
<h1>Thesaurus&nbsp;Search</h1>";
$template['thesaurus']['Word']['StartHTML']="";
 $template['thesaurus']['Word']['ProcessHTML']="<b>
 The Word '\$word' has the following synonyms</b><br>
 <i>Please click on the synonyms to search the Bible.</i><br><br>";
 $template['thesaurus']['Word']['EndHTML']="";
$template['thesaurus']['EndHTML']="<br><br>Courtesy http://wordnet.princeton.edu/ &nbsp; &nbsp;Please see 
license <a href='http://wordnet.princeton.edu/license'>details here</a>";
$template['thesaurus']['SynonymsArray']['StartHTML']="
<P><FONT face=Verdana size=3>Synonyms :";
$template['thesaurus']['SynonymsArray']['EndHTML']="<BR><BR>";
$template['thesaurus']['Synonyms']['StartHTML']="";
$template['thesaurus']['Synonyms']['ProcessHTML']="  [<a href='result.php?bibleVersion=\$version&search=\".trim(\$synonymsentry).\"' target='_blank' >\$synonymsentry</a> ] ";
$template['thesaurus']['Synonyms']['EndHTML']="";
$template['searchResult']['StartHTML']="
<BR>
<P><P></P>
<P>&nbsp;</P>
     <a href=\"search.php\">Bible&nbsp;Search</a> | 
	 <a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"readbible.php\">Read the Bible</a><br>";
$template['searchResult']['EndHTML']="";
$template['searchResult']['KeywordList']['StartHTML']="<HR> The Search Keyword '<b>";
$template['searchResult']['KeywordList']['ProcessHTML']="<a href='thesaurus.php?word=\$keyword&version=\$version'>\$keyword</a>";
$template['searchResult']['KeywordList']['EndHTML']="</b><superscript>*</superscript>' was found in following verse(s): <BR> <superscript>*</superscript> <small>Click on the keywords to search the thesaurus for synonyms</small>";
$template['searchResult']['Book']['StartHTML']="
<P><FONT face=Verdana size=3>";
$template['searchResult']['Book']['ProcessHTML']="<b><FONT COLOR='blue'>\$bookName </FONT></b><br>";
$template['searchResult']['Book']['EndHTML']="";
$template['searchResult']['Chapter']['StartHTML']="";
$template['searchResult']['Chapter']['ProcessHTML']="<i><a href='readBible.php?version=\$version&book=\$bookName&chapter=\$ChapterNo'>Chapter  \$ChapterNo </a></i> ";
$template['searchResult']['Chapter']['EndHTML']="";
$template['searchResult']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['searchResult']['Verse']['ProcessHTML']=" v\$verseNo \$verseText <BR> ";
$template['searchResult']['Verse']['EndHTML']="</P></FONT></BLOCKQUOTE>";
$template['searchResult']['Verse']['SearchKeyStartTag']="<b><font 
style=\"BACKGROUND-COLOR: yellow\">";
$template['searchResult']['Verse']['SearchKeyEndTag']="</font></b>";
$template['searchForm1']['StartHTML']="<h1>Bible&nbsp;Search</h1>
										<strong>Bible&nbsp;Search</strong> | 
	 <a href=\"advancedsearch.php\">Advanced Search</a> | <a href=\"readbible.php\">Read the Bible</a>";
$template['searchForm1']['EndHTML']="";
$template['searchForm2']['StartHTML']="<h1>Advanced&nbsp;Search</h1>
										<a href=\"search.php\">Bible&nbsp;Search</a>| 
										<strong>Advanced Search</strong> | <a href=\"readbible.php\">Read the Bible</a>";
$template['searchForm2']['EndHTML']="";

?>