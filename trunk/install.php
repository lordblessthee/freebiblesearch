<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Biblesearch Installation</title>
	<!--<?=$head?>-->
</head>

<body>
<h2>Biblesearch Installer</h2>
<table border=1>

<?php
require_once('installfunctions.php');
if(file_exists("data/config.inc.php"))
{
		echo "Cannot install probably already installed"."<br>";
		echo "Please remove data/config.inc.php for Reinstallation"."<br>";
		exit;
}
if(isset($_POST['InstallSubmit']))
{
	$noerror=true;
	$message="";
	$flatFilesExecutionLimitPerBible=45;
	if(isset($_POST['installMethod']))
	{
		$installMethod = $_POST['installMethod'];
	}
   // $biblesList=fopen("Bibles.txt","w");
	unset($configVars);
	if($installMethod=="FlatFiles")
	{
                $configVars['varName'][]="databaseType";
                $configVars['value'][]="\"FILE\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database TYPE";

				set_time_limit(count($_POST['biblename'])*$flatFilesExecutionLimitPerBible);
		if(isset($_POST['bibledirname']))
		{
			$bibleDirectory = $_POST['bibledirname'];
		}
		if(trim($bibleDirectory)!="")
		{
			$configVars['varName'][]="bibleDatabase";
            $configVars['value'][]="\"".$bibleDirectory."\"";
			$configIndex=count($configVars['varName'])-1;
			$configVars['Comments'][$configIndex]=array();
			$configVars['Comments'][$configIndex][]="The Bible Database Directory";
			$bibleCount=0;
			$configVars['Comments'][$configIndex+1][]="Installed Bibles";
			foreach($_POST['biblename'] as $biblename)
			{
				echo "Installing $biblename"."<br>";
				$tempResult = explode(",",$biblename);
				$noerror = installFlatfile($tempResult[0],$bibleDirectory);
				if(!$noerror)
				{
					break;
				}
			///	fwrite($biblesList,"$biblename\n");
				$configVars['varName'][]="BibleVersion[".$bibleCount."][\"name\"]";
				$configVars['value'][]="\"".trim($tempResult[1])."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Bible Full Name (".trim($tempResult[1]).")";
				$configVars['varName'][]="BibleVersion[".$bibleCount."][\"shortname\"]";
				$configVars['value'][]="\"".trim($tempResult[0])."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Bible Short Name (".trim($tempResult[0]).")";
				echo "Installed successfully"."<br>";
				$bibleCount++;
			}
		}
		else
		{
			$message = "<font color=red>Flat files installation directory not specified</font>";
			$noerror=false;
		}
	}else
		if($installMethod=="Database")
		{
			$configVars['varName'][]="databaseType";
			$configVars['value'][]="\"DB\"";
			$configIndex=count($configVars['varName'])-1;
			$configVars['Comments'][$configIndex][]="The Database TYPE";
			$databaseExecutionLimitPerBible=45;
			set_time_limit(count($_POST['biblename'])*$databaseExecutionLimitPerBible);
			$databaseInfo['databasehost'] = $_POST['dbhost'];
			$databaseInfo['databasename'] = $_POST['dbname'];
			$databaseInfo['databaseusername'] =$_POST['dbuser'];
			$databaseInfo['databasepassword'] = $_POST['dbpassword'];
			$databaseInfo['tableprefix'] = $_POST['dbtableprefix'];
			if(trim($databaseInfo['databasehost'])!=""&&trim($databaseInfo['databasename'])!=""
				&&trim($databaseInfo['databaseusername'])!="")
			{
				$configVars['varName'][]="dbHost";
				$configVars['value'][]="\"".$databaseInfo['databasehost']."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database Host Name";
				$configVars['varName'][]="dbName";
				$configVars['value'][]="\"".$databaseInfo['databasename']."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database Name";
				$configVars['varName'][]="dbUser";
				$configVars['value'][]="\"".$databaseInfo['databaseusername']."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database User Name";
				$configVars['varName'][]="dbPassword";
				$configVars['value'][]="\"".$databaseInfo['databasepassword']."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database Password";
				$configVars['varName'][]="dbTablePrefix";
				$configVars['value'][]="\"".$databaseInfo['tableprefix']."\"";
				$configIndex=count($configVars['varName'])-1;
				$configVars['Comments'][$configIndex][]="The Database Table Prefix";
				$bibleCount=0;
				foreach($_POST['biblename'] as $biblename)
				{
					echo $biblename."<br>";
					$tempResult = explode(",",$biblename);
					$noerror=installDB($databaseInfo,$tempResult[0]);
					echo "called installDB".$noerror?"true":"false";
					if(!$noerror)
					{
						break;
					}
					$configVars['varName'][]="BibleVersion[".$bibleCount."][\"name\"]";
					$configVars['value'][]="\"".trim($tempResult[1])."\"";
					$configIndex=count($configVars['varName'])-1;
					$configVars['Comments'][$configIndex][]="The Bible Full Name (".trim($tempResult[1]).")";
					$configVars['varName'][]="BibleVersion[".$bibleCount."][\"shortname\"]";
					$configVars['value'][]="\"".trim($tempResult[0])."\"";
					$configIndex=count($configVars['varName'])-1;
					$configVars['Comments'][$configIndex][]="The Bible Short Name (".trim($tempResult[0]).")";
					//fwrite($biblesList,"$biblename\n");
					echo "Installed successfully";
					$bibleCount++;
				}
			}else
			{
				$message="<font color=red>";
				if(trim($databaseInfo['databasehost'])=="")
				{
					$message .= "Database hostname not specified <br>";
				}

				if(trim($databaseInfo['databasename'])=="")
				{
					$message .= "Database name not specified <br>";
				}
				

				if(trim($databaseInfo['databaseusername'])=="")
				{
					$message .= "Database user name not specified <br>";
				}

				$message .="</font>";
				$noerror=false;


			}
		}
		if($noerror)
		{	
			writeConfigFile($configVars,"");
			writeHeaderFooterFile("");
			writeTemplateFile( $_POST['selectTemplate'],"");
			echo "Installation complete"."<br>";
			exit;
		}

}
echo "<form action=\"". $_SERVER['PHP_SELF']." \" method=\"post\" \n";
echo "enctype=\"multipart/form-data\">";
// create an array to hold directory list
$bibleList = array();
// create a handler for the directory
$handler = opendir("bibles");
// keep going until all files in directory have been read
while ($file = readdir($handler)) 
{
	// if $file isn't this directory or its parent, 
	// add it to the results array
	if ($file != '.' && $file != '..')
	{
		$ext = substr(strrchr($file, '.'), 1);
		if($ext =="csv")
		{
			$bibleList[] = $file;
		}
    }
}
// tidy up: close the handler
closedir($handler);
foreach($bibleList as $bibleFile)
{
	$handle = fopen ("bibles/".$bibleFile,'r');
    $data = fgetcsv ($handle, 4000, ',');
    $bibleShortName[] = $data[0];
	$bibleName[] = $data[1];
	fclose($handle);
}
if(isset($message))
{
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td colspan=3>$message</td></tr>";
}
echo "<tr><td>Step .No.</td><td>&nbsp;</td><td colspan=3><br></td></tr>";
echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
echo "<tr><td>1.</td><td>&nbsp;</td><td><font color=green><b>Which bible versions you want to install</b></font><br></td></tr>";
for($i =0;$i<count($bibleName); $i++)
{
	echo "<tr><td></td><td><input type=\"checkbox\" name=\"biblename[]\" value=\"$bibleShortName[$i],$bibleName[$i]\" checked = \"checked\"></td> <td>$bibleName[$i]<br></td></tr>";
}
echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
echo "<tr><td>2.</td><td>&nbsp;</td><td><font color=green><b>How do you want to install</b></font><br></td></td></tr>";
if(!isset($installMethod))

{
	$checked="";
}
else
{
	if(isset($installMethod)&&($installMethod=="FlatFiles"))
	{
		$checked="checked = \"checked\"";
	}
	else
	{
		$checked="";
	}
}
echo "<tr><td></td><td><input type=\"radio\" name=\"installMethod\" value=\"FlatFiles\" checked = \"checked\"></td><td><font color=green>Flat Files (Simple Text Files)</font><br>";
if(!isset($bibleDirectory))
{
	$bibleDirectory="bibledb/";
}
echo "&nbsp;&nbsp;File Database Directory Name  :<input type=\"text\" name=\"bibledirname\" size=\"25\" value=\"$bibleDirectory\"><br></td></tr>";
if(!isset($installMethod))

{
	$checked="checked = \"checked\"";
}
else
{
	if(isset($installMethod)&&($installMethod=="Database"))
	{
		$checked="checked = \"checked\"";
	}
	else
	{
		$checked="";
	}
}
echo "<tr><td></td><td>
<input type=\"radio\" name=\"installMethod\" value=\"Database\" ".$checked." > </td><td><font color=green>MYSQL Database</font><br>";
echo "&nbsp;&nbsp;Database Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"dbname\" value=\"".$databaseInfo['databasename']."\" size=\"25\" ><br>";
echo "&nbsp;&nbsp;Database Host Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbhost\" value=\"".$databaseInfo['databasehost']."\"size=\"25\" ><br>";
echo "&nbsp;&nbsp;Database User Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbuser\" value=\"".$databaseInfo['databaseusername']."\" size=\"25\" ><br>";
echo "&nbsp;&nbsp;Database Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"password\" name=\"dbpassword\" value=\"".$databaseInfo['databasepassword']."\" size=\"25\" ><br>";
if(!isset($databaseInfo['tableprefix']))
{
	$databaseInfo['tableprefix']="bibledb_";
}
echo "&nbsp;&nbsp;Database Table Prefix&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbtableprefix\" size=\"25\" value=\"".$databaseInfo['tableprefix']."\" ><br></td></tr>";
//Code to get template folder names into a select box
//echo "<input type=\"radio\" name=\"selectTemplate\" value=\"FlatFiles\" checked = \"checked\">Flat Files<br>";
//echo '<select name="select_template">';
echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
echo "<tr><td>3.</td><td>&nbsp;</td><td><font color=green><b>Select Template</b>(Click on the screenshot to see a preview )</font><br></td></td></tr><br>";
	$template_directory = "./template";
	$template = opendir($template_directory) or die("fail to open");
	$count=0;
	echo "<tr><td>&nbsp;</td><td></td><td><div style=\"overflow:auto; height:300px; width:700px;
		border: 1px solid #666;
		background-color: #ccc;
		padding: 8px;\">";
		echo "<table border=1>";
		//</div>";
	while(!(($design = readdir($template))===false)){
     	if(is_dir("$template_directory/$design")){
     	if ($design =='.'){
     	}elseif ($design =='..'){
     	}else{
			if($count==0)
			{
				$checked="checked = \"checked\"";
			}
			else
			{
				$checked="";
			}
			$count++;
			if($count%2!=0)
			{
				echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr>";
			}
			echo "<td><center>$design<br><a href=\"preview.php?template=$design\" target=new ><img src =\"template/$design/screenshot.jpg\"></a><br><input type=\"radio\" name=\"selectTemplate\" value=\"$design\" $checked ></center>";
			if($count%2==0)
			{
				echo "</td></tr>";
			}else
			{
				echo "<td>&nbsp;&nbsp;</td></td>";
			}
        }
		
     }
}
echo "</table></div></td></tr>";
//echo '</select><br>';
	closedir($template);
	//End of code to get template folder names into a select box
echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
echo "<tr><td></td><td></td><td><input type=\"submit\" name=\"InstallSubmit\" value=\"Submit\"> \n";
echo "<input type=\"submit\" name=\"InstallCancel\" value=\"Cancel\"> \n";
echo "<input type=\"reset\" name=\"Reset\" value=\"Reset\"></td></tr> \n";
echo "</form> \n";
echo "</table>\n";

echo "</body>\n";
echo "</html>\n";



function installFlatfile($biblename,$writeDir)
{
	global $message;
	$noerror=true;
	$readDir="bibles/";
	//$writeDir="bibledb/";
	if(!is_dir($writeDir))
	{
		mkdir($writeDir, 0777);
    }
	$bibleDir=$writeDir.$biblename."db"."/";
	$noerror = mkdir($bibleDir, 0777);
	if(!$noerror)
	{
		$message="<font color=red>Error creating $bibleDir directory please empty<br>
		          the contents of $writeDir</font>";
		return $noerror;
	}
	$row = 1;
	$handle = fopen ($readDir.$biblename.'.csv','r');
	$book_prev="";
	$chapter_prev="";
	$filecount=0;
	while ($data = fgetcsv ($handle, 4000, ',')) 
	{
		if ($row <3)
		{
			$row++;
			continue;
		}
		$book=$data[1];
		$chapter=$data[2];
		if($book != $book_prev)
		{
			$noerror=mkdir($bibleDir.$book, 0777);
			if(!$noerror)
			{
				$message="<font color=red>Error creating $bibleDir.$book directory please empty<br>
		          the contents of $writeDir</font>";
				return $noerror;
			}
			if($filecount!=0)
			{
				fclose($fileresult);
			}
			$fileresult=fopen($bibleDir.$book.'/'.$book.$chapter.'.txt', 'w');
			$filecount++;
		}else
		if($chapter != $chapter_prev)
		{
			if($filecount!=0)
			{
				fclose($fileresult);
			}
			$fileresult=fopen($bibleDir.$book.'/'.$book.$chapter.'.txt', 'w');
			$filecount++;
		}
		$num = count ($data);
		/**print ' '.$num.'fields in line '.$row. '\n';**/
		$row++;
		//$filedata=$data[1].' '.$data[2].':'.$data[3].' '.$data[4];
		$filedata=$data[3].' '.$data[4];
		fwrite($fileresult,"$filedata \n");
		$book_prev=$book;
		$chapter_prev=$chapter;
	}
	//fclose ($handle);
	return $noerror;
}

function installDB($databaseInfo,$bibleVersion)
{
	global $message;
	$noerror=true;
	$databasehost = $databaseInfo['databasehost'];
	$databasename = $databaseInfo['databasename'];
	$databasetableprefix= $databaseInfo['tableprefix'];
	$databasetable = $databaseInfo['tableprefix'].$bibleVersion;
	$databaseusername = $databaseInfo['databaseusername'];
	$databasepassword = $databaseInfo['databasepassword'] ;

	$csvfile = "bibles/".$bibleVersion.".csv";
	//$con = @mysql_connect($databasehost,$databaseusername,$databasepassword) or die(mysql_error());
	$con = mysql_connect($databasehost, $databaseusername, $databasepassword);
	if (!$con) {
		$message = "<font color=red>"."Could not connect: ".mysql_error()."</font>";
		$noerror=false;
		return $noerror;
	}
	echo 'Connected successfully';
	$noerror = mysql_select_db($databasename,$con);
	if (!$noerror) 
	{
		$message .="<font color = red>";
		
		$message .="Can\'t use $databasename: " . mysql_error();
		$message .="</font>";
		return $noerror;
	}
    $sql = "DROP TABLE IF EXISTS ".$databasetable." ;";
    $result=mysql_query($sql);
    if(!$result)
    {
		$message .="<font color = red>";
		$message .="Invalid query1: " . mysql_error();
		$message .="</font>";
		$noerror=false;
		return $noerror;

		
	}

	$sql = "CREATE TABLE ".$databasetable."
	(
		BOOKID int,
		CHAPTERNO int,
		VERSENO  int,
		VERSETEXT longtext
		);";
	$result=mysql_query($sql);
	if(!$result)
	{
		$message .="<font color = red>";
		$message .="Invalid query2: " . mysql_error();
		$message .="</font>";
		$noerror=false;
		return $noerror;
	}
	else
	{
		echo "$databasetable table created";
	}
	if(!file_exists($csvfile)) 
	{
		$message .="<font color = red>";
		$message .=" $bibleVersion Bible File not found. Make sure you specified the correct path.\n";
		$message .="</font>";
		$noerror=false;
		return $noerror;
		
	}
	else
	{
		echo "$bibleVersion Bible file found";

		$file = fopen($csvfile,"r");

		if(!$file) 
		{
			$message .="<font color = red>";
			$message .="Error opening $csvfile data file.\n";
			$message .="</font>";
			$noerror=false;
			return $noerror;

		}

		$size = filesize($csvfile);

		if(!$size) 
		{
			$message .="<font color = red>";
			$message .="$csvfile is empty.\n";
			$message .="</font>";
			$noerror=false;
			return $noerror;
		}


		$lines = 0;
		$queries = "";
		$linearray = array();
        while ($data = fgetcsv ($file, 4000, ','))
        {
			$lines++;
            if($lines <=2)
			{
                  continue;
			}
			$data[4]=addslashes($data[4]);
			$linemysql="$data[0],$data[2],$data[3],'$data[4]'";
			$query = "insert into $databasetable values($linemysql)";
			$result=mysql_query($query);
			if(!$result)
			{
				$message .="<font color = red>";
				$message .="Invalid query3: " . mysql_error();
				$message .="</font>";
				$noerror=false;
				return $noerror;
			}
                
		

        } 
        @mysql_close($con);
 		echo "Found a total of $lines records in this $bibleVersion csv file.\n";
		return $noerror;
	}
}

/***function writeConfigFile($configVars)
{
		echo "in writeConfigFile"."<br>";
        
		$defaultConfig=file_get_contents("template/default.config.inc.php");
        $configStr="";
        for($i=0;$i<count($configVars['varName']);$i++)
        {
			if(isset($configVars['Comments'][$i]))
			{
				foreach($configVars['Comments'][$i] as $comments)
				{
					$configStr .="// ".$comments."\n";
				}

			}
			$configStr .="\$".$configVars['varName'][$i]."=".$configVars['value'][$i].";"."\n";
        }
        $defaultConfigModified=str_replace("<%main%>",$configStr,$defaultConfig);
        file_put_contents("data/config.inc.php",$defaultConfigModified);

}

function writeHeaderFooterFile()
{
       echo "in  writeHeaderFooterFile"."<br>";
        
	$defaultHeader=file_get_contents("template/default.header.inc.php");
	file_put_contents("data/header.inc.php",$defaultHeader);
	$defaultFooter=file_get_contents("template/default.footer.inc.php");
	file_put_contents("data/footer.inc.php",$defaultFooter);
}

function writeTemplateFile($templateName)
{
       echo "in  writeTemplateFile"."<br>";
        
	$defaultTemplate=file_get_contents("template/".$templateName."/"."default.template.inc.php");
	file_put_contents("data/template.inc.php",$defaultTemplate);
	if(is_dir("template/".$templateName."/images/"))
	{
		$handler = opendir("data/images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!="..")
			{
				unlink("data/images/".$images);
			}
		}
		closedir($handler);
		$handler = opendir("template/".$templateName."/images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!="..")
			{
				copy("./template/".$templateName."/images/".$images, "./data/images/".$images); 

			}

		}
		closedir($handler);
	}
}**/
?>