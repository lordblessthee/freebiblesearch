<?php

/**
 * Main file
 */
if(file_exists("data/config.inc.php"))
{
	require_once('search.php');
}
else
{
	require_once('install.php');
}
?>