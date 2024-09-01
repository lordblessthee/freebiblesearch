<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
	<title>Biblesearch Installation</title>
</head>

<body>
	<h2>Biblesearch Installer</h2>
	<table border=1>

		<?php
		require_once('installfunctions.php');
		if (file_exists("data/config.inc.php")) {
			echo "Cannot install probably already installed" . "<br>";
			echo "Please remove data/config.inc.php for Reinstallation" . "<br>";
			exit;
		}
		$databaseInfo = [
			'databasename' => '',
			'databasehost' => '',
			'databaseusername' => '',
			'databasepassword' => '',
			'tableprefix' => ''
		];
		if (isset($_POST['InstallSubmit'])) {
			$noerror = true;
			$message = "";
			/**Increase this time if you are getting Maximum execution time exceeded Fatal Error
	 on Flat File based installation**/
			$flatFilesExecutionLimitPerBible = 45;
			/**Increase this time if you are getting Maximum execution time exceeded Fatal Error
	 on MySQL database based installation**/
			$databaseExecutionLimitPerBible = 45;
			unset($configVars);
			if (isset($_POST['parallelbiblescount']) && ($_POST['parallelbiblescount'] > 1)) {
				$configVars['varName'][] = "parallelBibles";
				$configVars['value'][] = $_POST['parallelbiblescount'];
				$configIndex = count($configVars['varName']) - 1;
				$configVars['Comments'][$configIndex][] = "No of Parallel bibles to search or lookup";
			}
			if (isset($_POST['installMethod'])) {
				$installMethod = $_POST['installMethod'];
			}
			if ($installMethod == "FlatFiles") {
				$configVars['varName'][] = "databaseType";
				$configVars['value'][] = "\"FILE\"";
				$configIndex = count($configVars['varName']) - 1;
				$configVars['Comments'][$configIndex][] = "The Database TYPE";

				set_time_limit(count($_POST['biblename']) * $flatFilesExecutionLimitPerBible);
				if (isset($_POST['bibledirname'])) {
					$bibleDirectory = trim($_POST['bibledirname']);
				}
				if (($bibleDirectory != "") && (substr($bibleDirectory, -1, 1) == '/')) {
					$configVars['varName'][] = "bibleDatabase";
					$configVars['value'][] = "\"" . $bibleDirectory . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex] = array();
					$configVars['Comments'][$configIndex][] = "The Bible Database Directory";
					$bibleCount = 0;
					$configVars['Comments'][$configIndex + 1][] = "Installed Bibles";
					foreach ($_POST['biblename'] as $biblename) {
						echo "Installing $biblename" . "<br>";
						$tempResult = explode(",", $biblename);
						$noerror = installFlatfile($tempResult[0], $bibleDirectory);
						if (!$noerror) {
							break;
						}
						$configVars['varName'][] = "BibleVersion[" . $bibleCount . "][\"name\"]";
						$configVars['value'][] = "\"" . trim($tempResult[1]) . "\"";
						$configIndex = count($configVars['varName']) - 1;
						$configVars['Comments'][$configIndex][] = "The Bible Full Name (" . trim($tempResult[1]) . ")";
						$configVars['varName'][] = "BibleVersion[" . $bibleCount . "][\"shortname\"]";
						$configVars['value'][] = "\"" . trim($tempResult[0]) . "\"";
						$configIndex = count($configVars['varName']) - 1;
						$configVars['Comments'][$configIndex][] = "The Bible Short Name (" . trim($tempResult[0]) . ")";
						echo "Installed successfully" . "<br>";
						$bibleCount++;
					}
				} else {
					$message = "<font color=red>Flat files installation directory not specified</font>";
					$noerror = false;
				}
			} else
		if ($installMethod == "Database") {
				$databaseInfo = [];
				$configVars['varName'][] = "databaseType";
				$configVars['value'][] = "\"DB\"";
				$configIndex = count($configVars['varName']) - 1;
				$configVars['Comments'][$configIndex][] = "The Database TYPE";
				set_time_limit(count($_POST['biblename']) * $databaseExecutionLimitPerBible);
				$databaseInfo['databasehost'] = $_POST['dbhost'];
				$databaseInfo['databasename'] = $_POST['dbname'];
				$databaseInfo['databaseusername'] = $_POST['dbuser'];
				$databaseInfo['databasepassword'] = $_POST['dbpassword'];
				$databaseInfo['tableprefix'] = $_POST['dbtableprefix'];
				if (
					trim($databaseInfo['databasehost']) != "" && trim($databaseInfo['databasename']) != ""
					&& trim($databaseInfo['databaseusername']) != ""
				) {
					$configVars['varName'][] = "dbHost";
					$configVars['value'][] = "\"" . $databaseInfo['databasehost'] . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex][] = "The Database Host Name";
					$configVars['varName'][] = "dbName";
					$configVars['value'][] = "\"" . $databaseInfo['databasename'] . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex][] = "The Database Name";
					$configVars['varName'][] = "dbUser";
					$configVars['value'][] = "\"" . $databaseInfo['databaseusername'] . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex][] = "The Database User Name";
					$configVars['varName'][] = "dbPassword";
					$configVars['value'][] = "\"" . $databaseInfo['databasepassword'] . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex][] = "The Database Password";
					$configVars['varName'][] = "dbTablePrefix";
					$configVars['value'][] = "\"" . $databaseInfo['tableprefix'] . "\"";
					$configIndex = count($configVars['varName']) - 1;
					$configVars['Comments'][$configIndex][] = "The Database Table Prefix";
					$bibleCount = 0;
					foreach ($_POST['biblename'] as $biblename) {
						$biblename = stripslashes($biblename);
						echo $biblename . "<br>";
						$tempResult = explode(",", $biblename);
						$noerror = installDB($databaseInfo, $tempResult[0]);
						echo "called installDB" . $noerror ? "true" : "false";
						if (!$noerror) {
							break;
						}
						$configVars['varName'][] = "BibleVersion[" . $bibleCount . "][\"name\"]";
						$configVars['value'][] = "\"" . trim($tempResult[1]) . "\"";
						$configIndex = count($configVars['varName']) - 1;
						$configVars['Comments'][$configIndex][] = "The Bible Full Name (" . trim($tempResult[1]) . ")";
						$configVars['varName'][] = "BibleVersion[" . $bibleCount . "][\"shortname\"]";
						$configVars['value'][] = "\"" . trim($tempResult[0]) . "\"";
						$configIndex = count($configVars['varName']) - 1;
						$configVars['Comments'][$configIndex][] = "The Bible Short Name (" . trim($tempResult[0]) . ")";
						//fwrite($biblesList,"$biblename\n");
						echo "Installed successfully";
						$bibleCount++;
					}
				} else {
					$message = "<font color=red>";
					if (trim($databaseInfo['databasehost']) == "") {
						$message .= "Database hostname not specified <br>";
					}

					if (trim($databaseInfo['databasename']) == "") {
						$message .= "Database name not specified <br>";
					}


					if (trim($databaseInfo['databaseusername']) == "") {
						$message .= "Database user name not specified <br>";
					}

					$message .= "</font>";
					$noerror = false;
				}
			}
			if ($noerror) {
				writeConfigFile($configVars, "");
				writeHeaderFooterFile("");
				writeTemplateFile($_POST['selectTemplate'], "");
				echo "Installation complete" . "<br>";
				exit;
			}
		}
		echo "<form action=\"" . $_SERVER['PHP_SELF'] . " \" method=\"post\" \n";
		echo "enctype=\"multipart/form-data\">";
		$bibleList = array();
		$handler = opendir("bibles");
		while ($file = readdir($handler)) {
			if ($file != '.' && $file != '..') {
				$ext = substr(strrchr($file, '.'), 1);
				if ($ext == "csv") {
					$bibleList[] = $file;
				}
			}
		}
		closedir($handler);
		foreach ($bibleList as $bibleFile) {
			$handle = fopen("bibles/" . $bibleFile, 'r');
			$data = fgetcsv($handle, 4000, ',');
			$bibleShortName[] = $data[0];
			$bibleName[] = $data[1];
			fclose($handle);
		}
		if (isset($message)) {
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td colspan=3>$message</td></tr>";
		}
		echo "<tr><td>Step .No.</td><td>&nbsp;</td><td colspan=3><br></td></tr>";
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		echo "<tr><td>1.</td><td>&nbsp;</td><td><font color=green><b>Which bible versions you want to install</b></font><br></td></tr>";
		for ($i = 0; $i < count($bibleName); $i++) {
			echo "<tr><td></td><td><input type=\"checkbox\" name=\"biblename[]\" value=\"$bibleShortName[$i],$bibleName[$i]\" checked = \"checked\"></td> <td>$bibleName[$i]<br></td></tr>";
		}
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		echo "<tr><td>2.</td><td>&nbsp;</td><td><font color=green><b>How do you want to install</b></font><br></td></td></tr>";
		if (!isset($installMethod)) {
			$checked = "";
		} else {
			if (isset($installMethod) && ($installMethod == "FlatFiles")) {
				$checked = "checked = \"checked\"";
			} else {
				$checked = "";
			}
		}
		echo "<tr><td></td><td><input type=\"radio\" name=\"installMethod\" value=\"FlatFiles\" checked = \"checked\"></td><td><font color=green>Flat Files (Simple Text Files)</font><br>";
		if (!isset($bibleDirectory)) {
			$bibleDirectory = "bibledb/";
		}
		echo "&nbsp;&nbsp;File Database Directory Name  :<input type=\"text\" name=\"bibledirname\" size=\"25\" value=\"$bibleDirectory\"><br></td></tr>";
		if (!isset($installMethod)) {
			$checked = "checked = \"checked\"";
		} else {
			if (isset($installMethod) && ($installMethod == "Database")) {
				$checked = "checked = \"checked\"";
			} else {
				$checked = "";
			}
		}
		echo "<tr><td></td><td>
<input type=\"radio\" name=\"installMethod\" value=\"Database\" " . $checked . " > </td><td><font color=green>MYSQL Database</font><br>";
		echo "&nbsp;&nbsp;Database Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"dbname\" value=\"" . $databaseInfo['databasename'] . "\" size=\"25\" ><br>";
		echo "&nbsp;&nbsp;Database Host Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbhost\" value=\"" . $databaseInfo['databasehost'] . "\"size=\"25\" ><br>";
		echo "&nbsp;&nbsp;Database User Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbuser\" value=\"" . $databaseInfo['databaseusername'] . "\" size=\"25\" ><br>";
		echo "&nbsp;&nbsp;Database Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"password\" name=\"dbpassword\" value=\"" . $databaseInfo['databasepassword'] . "\" size=\"25\" ><br>";
		if (!isset($databaseInfo['tableprefix'])) {
			$databaseInfo['tableprefix'] = "bibledb_";
		}
		echo "&nbsp;&nbsp;Database Table Prefix&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"text\" name=\"dbtableprefix\" size=\"25\" value=\"" . $databaseInfo['tableprefix'] . "\" ><br></td></tr>";
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		if (count($bibleList) > 1) {
			echo "<tr><td>3.</td><td>&nbsp;</td><td><font color=green><b>How many Bibles you want to search in a single search</b></font>
	<input type=\"text\" name=\"parallelbiblescount\" size=\"10\" value=\"" . (isset($_POST['parallelbiblescount']) ? $_POST['parallelbiblescount'] : count($bibleList)) . "\" ><br></td></tr>";
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			$sectionCount = 4;
		} else {
			$sectionCount = 3;
		}

		echo "<tr><td>$sectionCount.</td><td>&nbsp;</td><td><font color=green><b>Select Template</b>(Click on the screenshot to see a preview )</font><br></td></td></tr><br>";
		$template_directory = "./template";
		$template = opendir($template_directory) or die("fail to open");
		$count = 0;
		echo "<tr><td>&nbsp;</td><td></td><td><div style=\"overflow:auto; height:300px; width:700px;
		border: 1px solid #666;
		background-color: #ccc;
		padding: 8px;\">";
		echo "<table border=1>";
		while (!(($design = readdir($template)) === false)) {
			if (is_dir("$template_directory/$design")) {
				if (($design != '.') && ($design != '..') && (file_exists("$template_directory/" . $design . "/default.template.inc.php"))) {
					if ($count == 0) {
						$checked = "checked = \"checked\"";
					} else {
						$checked = "";
					}
					$count++;
					if ($count % 2 != 0) {
						echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr>";
					}
					echo "<td><center><font size=5><b>$design</b></font><br><a href=\"preview.php?template=$design\" target=new ><img src =\"template/$design/screenshot.jpg\" height=194 width=308></a><br><input type=\"radio\" name=\"selectTemplate\" value=\"$design\" $checked ></center>";
					if ($count % 2 == 0) {
						echo "</td></tr>";
					} else {
						echo "<td>&nbsp;&nbsp;</td></td>";
					}
				}
			}
		}
		echo "</table></div></td></tr>";
		closedir($template);
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		echo "<tr><td></td><td></td><td><input type=\"submit\" name=\"InstallSubmit\" value=\"Submit\"> \n";
		echo "<input type=\"submit\" name=\"InstallCancel\" value=\"Cancel\"> \n";
		echo "<input type=\"reset\" name=\"Reset\" value=\"Reset\"></td></tr> \n";
		echo "</form> \n";
		echo "</table>\n";

		echo "</body>\n";
		echo "</html>\n";


		/**
		 *
		 * function to do a flat file based installation
		 *
		 * @param $biblename string
		 * @param $writeDir string
		 *
		 */

		function installFlatfile($biblename, $writeDir)
		{
			global $message;
			$noerror = true;
			$readDir = "bibles/";
			$bibleDir = $writeDir . $biblename . "db" . "/";

			// Create the main directory if it doesn't exist
			if (!is_dir($writeDir)) {
				if (!mkdir($writeDir, 0777, true)) {
					$message = "<font color=red>Error creating $writeDir directory. Please check permissions.</font>";
					return false;
				}
			}

			// Create the Bible directory
			if (!mkdir($bibleDir, 0777, true)) {
				$message = "<font color=red>Error creating $bibleDir directory. Please check permissions.</font>";
				return false;
			}

			// Open the CSV file
			$csvFile = $readDir . $biblename . '.csv';
			if (($handle = fopen($csvFile, 'r')) === false) {
				$message = "<font color=red>Error opening $csvFile.</font>";
				return false;
			}

			$book_prev = "";
			$chapter_prev = "";
			$filecount = 0;
			$fileresult = null;

			// Process each line of the CSV file
			while (($data = fgetcsv($handle, 4000, ',')) !== false) {
				// Skip header rows
				if ($filecount < 2) {
					$filecount++;
					continue;
				}

				$book = $data[1];
				$chapter = $data[2];
				$filedata = $data[3] . ' ' . $data[4];

				// Create book directory if it changes
				if ($book !== $book_prev) {
					if ($fileresult) {
						fclose($fileresult);
					}
					$bookDir = $bibleDir . $book . '/';
					if (!is_dir($bookDir) && !mkdir($bookDir, 0777, true)) {
						$message = "<font color=red>Error creating $bookDir directory. Please check permissions.</font>";
						fclose($handle);
						return false;
					}
					$filePath = $bookDir . $book . str_pad($chapter, 3, '0', STR_PAD_LEFT) . '.txt';
					$fileresult = fopen($filePath, 'w');
					$filecount++;
				}

				// Create chapter file if it changes
				if ($chapter !== $chapter_prev && $fileresult) {
					fclose($fileresult);
					$filePath = $bibleDir . $book . '/' . $book . str_pad($chapter, 3, '0', STR_PAD_LEFT) . '.txt';
					$fileresult = fopen($filePath, 'w');
					$filecount++;
				}

				// Write data to file
				if ($fileresult) {
					fwrite($fileresult, "$filedata \n");
				}

				$book_prev = $book;
				$chapter_prev = $chapter;
			}

			// Close the last file handle if open
			if ($fileresult) {
				fclose($fileresult);
			}
			fclose($handle);

			return $noerror;
		}


		/**
		 *
		 * function to do a database based installation
		 *
		 * @param $databaseInfo array
		 * @param $bibleVersion string
		 *
		 */

		function installDB($databaseInfo, $bibleVersion)
		{
			global $message;
			$noerror = true;
			$databasehost = $databaseInfo['databasehost'];
			$databasename = $databaseInfo['databasename'];
			$databasetableprefix = $databaseInfo['tableprefix'];
			$databasetable = $databaseInfo['tableprefix'] . $bibleVersion;
			$databaseusername = $databaseInfo['databaseusername'];
			$databasepassword = $databaseInfo['databasepassword'];

			$csvfile = "bibles/" . $bibleVersion . ".csv";
			$con = mysqli_connect($databasehost, $databaseusername, $databasepassword);
			if (!$con) {
				$message = "<font color=red>" . "Could not connect: " . mysqli_error() . "</font>";
				$noerror = false;
				return $noerror;
			}
			echo 'Connected successfully';
			$noerror = mysqli_select_db($con, $databasename);
			if (!$noerror) {
				$message .= "<font color = red>";

				$message .= "Can\'t use $databasename: " . mysqli_error();
				$message .= "</font>";
				return $noerror;
			}
			$sql = "DROP TABLE IF EXISTS " . $databasetable . " ;";
			$result = mysqli_query($con, $sql);
			if (!$result) {
				$message .= "<font color = red>";
				$message .= "Invalid query1: " . mysqli_error();
				$message .= "</font>";
				$noerror = false;
				return $noerror;
			}

			$sql = "CREATE TABLE " . $databasetable . "
	(
		BOOKID int,
		CHAPTERNO int,
		VERSENO  int,
		VERSETEXT longtext
		);";
			$result = mysqli_query($con, $sql);
			if (!$result) {
				$message .= "<font color = red>";
				$message .= "Invalid query2: " . mysqli_error();
				$message .= "</font>";
				$noerror = false;
				return $noerror;
			} else {
				echo "$databasetable table created";
			}
			if (!file_exists($csvfile)) {
				$message .= "<font color = red>";
				$message .= " $bibleVersion Bible File not found. Make sure you specified the correct path.\n";
				$message .= "</font>";
				$noerror = false;
				return $noerror;
			} else {
				echo "$bibleVersion Bible file found";

				$file = fopen($csvfile, "r");

				if (!$file) {
					$message .= "<font color = red>";
					$message .= "Error opening $csvfile data file.\n";
					$message .= "</font>";
					$noerror = false;
					return $noerror;
				}

				$size = filesize($csvfile);

				if (!$size) {
					$message .= "<font color = red>";
					$message .= "$csvfile is empty.\n";
					$message .= "</font>";
					$noerror = false;
					return $noerror;
				}


				$lines = 0;
				$queries = "";
				$linearray = array();
				while ($data = fgetcsv($file, 4000, ',')) {
					$lines++;
					if ($lines <= 2) {
						continue;
					}
					$data[4] = addslashes($data[4]);
					$linemysql = "$data[0],$data[2],$data[3],'$data[4]'";
					$query = "insert into $databasetable values($linemysql)";
					$result = mysqli_query($con, $query);
					if (!$result) {
						$message .= "<font color = red>";
						$message .= "Invalid query3: " . mysqli_error();
						$message .= "</font>";
						$noerror = false;
						return $noerror;
					}
				}
				@mysqli_close($con);
				echo "Found a total of $lines records in this $bibleVersion csv file.\n";
				return $noerror;
			}
		}

		?>