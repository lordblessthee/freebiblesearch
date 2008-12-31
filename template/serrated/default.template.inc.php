<?php
$template['HeaderText']="<STYLE fprolloverstyle>A:hover {
	COLOR: #ff8080; FONT-VARIANT: small-caps; TEXT-DECORATION: none
}
</STYLE>";
$template['HeaderBodyText']="text=#020280 vLink=#574d95 aLink=#ff80c0 link=#6bc552 bgProperties=fixed 
bgColor=#ffffff background=\"data/images/bak.gif\"";
$template['readBible']['ShowBooks']['ChapterLinks']=true;
$template['readBible']['ShowBooks']['StartHTML']="
<P>&nbsp;</P><B>
<P><U><FONT face=Verdana size=5>Bible Book Index</FONT>
";
$template['readBible']['ShowBooks']['EndHTML']="";
$template['readBible']['ShowBooks']['Book']['StartHTML']="
<FONT 
face=Verdana size=3><BR><BR></FONT></U><FONT face=Verdana size=3>
<BLOCKQUOTE>
  <P align=left><FONT face=\"Verdana size=\" 3>";
$template['readBible']['ShowBooks']['Book']['EndHTML']="</FONT>
  <P align=center><FONT face=\"Verdana size=\" 3><BR><BR></FONT></FONT></B>
  ";
$template['readBible']['ShowBooks']['Chapter']['StartHTML']="";
$template['readBible']['ShowBooks']['Chapter']['EndHTML']="<br><br>";
$template['readBible']['ShowBooks']['Book']['ProcessHTML']=" <br><a href='readBible.php?version=\$version&book=\$bookName'> \$bookName</a> ";
$template['readBible']['ShowBooks']['Chapter']['ProcessHTML']=" [<a href='readBible.php?version=\$version&book=\$bookName&chapter=\$chapterNo'>\$chapterNo</a>] ";
$template['readBible']['ShowVerses']['ChapterLinks']=true;
$template['readBible']['ShowVerses']['StartHTML']="
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['readBible']['ShowVerses']['EndHTML']="";
$template['readBible']['ShowVerses']['Book']['StartHTML']="
<P><FONT face=Verdana size=3>";
$template['readBible']['ShowVerses']['Book']['EndHTML']="";
$template['readBible']['ShowVerses']['Chapter']['StartHTML']="";
$template['readBible']['ShowVerses']['Chapter']['EndHTML']="";
$template['readBible']['ShowVerses']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['readBible']['ShowVerses']['Verse']['EndHTML']="";
$template['readBible']['ShowVerses']['Book']['ProcessHTML']="<FONT face=Verdana size=3><A 
href='readBible.php?version=\$version'><BR>
<P align=center><A href='readBible.php?version=\$version'><B><FONT 
face=Verdana size=5>Bible Book - INDEX</FONT></B></A></P></A></FONT>";
$template['readBible']['ShowVerses']['Chapter']['ProcessHTML']=" [<a href='readBible.php?version=\$version&book=\$bookName&chapter=\$chapterNoLink'>\$chapterNoLink</a>]";
$template['readBible']['ShowVerses']['Verse']['ProcessHTML']=" \$chapterNo:\$verseNo \$verseText <BR><BR> ";
$template['readBible']['ShowBibleVersions']['StartHTML']="
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['readBible']['ShowBibleVersions']['EndHTML']="";
$template['readBible']['ShowBibleVersions']['Version']['StartHTML']="
<P><FONT face=Verdana size=3>Versions :<BR><ol>";
$template['readBible']['ShowBibleVersions']['Version']['EndHTML']="</ol></FONT></P>";
$template['readBible']['ShowBibleVersions']['Version']['ProcessHTML']="<a href='readBible.php?version=\$versionShortName'>\$versionName</a><BR><BR>";
$template['thesaurus']['StartHTML']="
<BR>
<P><P></P>
<P>&nbsp;</P><B>";
$template['thesaurus']['EndHTML']="";
$template['thesaurus']['SynonymsArray']['StartHTML']="
<P><FONT face=Verdana size=3>Synonyms :";
$template['thesaurus']['SynonymsArray']['EndHTML']="<BR><BR>";
$template['thesaurus']['Synonyms']['StartHTML']="";
$template['thesaurus']['Synonyms']['EndHTML']="";
$template['thesaurus']['Synonyms']['ProcessHTML']="  [<a href='testSimulator.php?bibleVersion=\$version&search=\".trim(\$synonymsentry).\"' target='_blank' >\$synonymsentry</a> ] ";
$template['searchResult']['StartHTML']="
<BR>
<P><P></P>
<P>&nbsp;</P>";
$template['searchResult']['EndHTML']="";
$template['searchResult']['Book']['StartHTML']="
<P><FONT face=Verdana size=3>";
$template['searchResult']['Book']['EndHTML']="";
$template['searchResult']['Chapter']['StartHTML']="";
$template['searchResult']['Chapter']['EndHTML']="";
$template['searchResult']['Verse']['StartHTML']="
<BLOCKQUOTE>
  <P align=justify><FONT face=Verdana size=3>";
$template['searchResult']['Verse']['EndHTML']="</P></FONT></BLOCKQUOTE>";
$template['searchResult']['Book']['ProcessHTML']="";
$template['searchResult']['Chapter']['ProcessHTML']="<b><FONT COLOR='blue'> \$bookName \$chapterNo </FONT></b> ";
$template['searchResult']['Verse']['ProcessHTML']=" \$chapterNo:\$verseNo \$verseText <BR> ";
$template['searchResult']['Verse']['SearchKeyStartTag']="<b><font color ='red'>";
$template['searchResult']['Verse']['SearchKeyEndTag']="</font></b>";
$template['searchForm1']['StartHTML']="";
$template['searchForm1']['EndHTML']="";
$template['searchForm2']['StartHTML']="";
$template['searchForm2']['EndHTML']="";

?>