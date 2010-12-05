/*******************************************/
/* Free Bible Search PHP Script            */
/* Readme                                  */
/* Version 1.5 , GNU      India            */
/* Author Cinu Chacko                      */
/* http://www.lordblessthee.com             */

/*******************************************/

This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

-------------------------------------------------------------------------------------
Installation:

1) Copy all files along with directories in a directory of your server.
2) Make sure the bibles(e.g kjv.csv) are present in the "bibles" directory.Only 
   1 bible are provided with this release. Please see Bible Format section for the
   format of the bibles.
3) Make sure the "data" directory has write permissions.
4) You can make changes in the "template" directory including
   a) default.header.inc.php  to define your own header in each page.
   b) default.footer.inc.php  to define your own footer in each page.

5) Start the file "index.php" with your webbrowser.
6) Select either File based Installation or MYSQL database based installation.
   and provide the parameters for file or database. Also select the appropriate
   template you want to use.
7) If the installation proceeds successfully you can call index.php again. It will 
   go to the default search page.
8) That's it.

-------------------------------------------------------------------------------------
Re-Installation:

1) Delete the "data/config.inc.php" file.
2) If you have a file based installation. Remove  all files from
   File database directory, (e.g bibledb/).
3) Rerun the index.php script.
4) You should see the installation page.

-------------------------------------------------------------------------------------
Creating new templates:

1) Every directory in "template" directory is a template. To create a new template
   copy any one of the directory to a new name(e.g template/classic to template/newclassic).
   Make sure "default.template.inc.php" file is present in the template(e.g template/newclassic)
   directory.
2) Make changes in the "default.template.inc.php" (e.g template/newclassic/default.template.inc.php)
   to suit your requirements. You can put images in "images" directory(e.g template/newclassic/images)
   if you are using images.
3) Run script previewtemplate.php and click on the appropriate template to see the changes.
4) You can make changes again in "default.template.inc.php" and refresh the template page 
   to see the changes.
6) To create a template preview thumnail do the following steps. 
	a) Open the preview page and take a screenshot.
	b) Copy the screenshot in an image editor e.g MSpaint. 
	c) Resize it to width=308 pixels and height=194 pixels and 
	   save as screenshot.jpg in template directory. 
	 d) Run previewtemplate.php to see the preview.
5) If you are satisfied with the changes you can Re-install the script with the new template.
   See Re-installation steps above.

-------------------------------------------------------------------------------------
Bible Format

The CSV bibles provided with this release have the following format

Line No 1:<Bible short name e.g kjv >,<Bible long name e.g "KING JAMES VERSION">
Line No 2:---Blank Line----
Line No 3:<2 digit bible book id e.g 01 for Genesis 02 for exodus and so on>,<Bible book name e.g Genesis>,<3 digit Bible book chapter no e.g 003,007,029 etc >,<3 digit Bible  verse no e.g 003,007,029 etc >,<Verse text in Quotes>
Line No 4:--Same format as above and so on---


Example given below


kjv,"KING JAMES VERSION"

01,Genesis,001,001,"In the beginning God created the heaven and the earth."
01,Genesis,001,002,"And the earth was without form, and void; and darkness was upon the face of the deep. And the Spirit of God moved upon the face of the waters."

-------------------------------------------------------------------------------------
FAQ:
Q:- I am getting Maximum execution time exceeded fatal error during installation.
A:- This error comes when the time required to execute the installation script
    exceeds due to time required to write the bibles. Increase the value of 
    $flatFilesExecutionLimitPerBible    or $databaseExecutionLimitPerBible given 
    in install.php at around line 26 if you face such problems.

. 

-------------------------------------------------------------------------------------
To post a comment or if you have any questions Go to   ....  http://www.lordblessthee.com
