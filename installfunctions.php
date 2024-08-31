<?php

/**
 * File containing miscellaneous functions
 */


/**
 *
 * function to write the configuration file after 
 * installation
 *
 * @param $configVars array
 * @param $installprefix string
 *
 */  

function writeConfigFile($configVars,$installprefix)
{
    
        // Ensure the 'data' directory exists
        if (!is_dir("data")) {
            mkdir("data", 0755, true);
        }
        
		$defaultConfig=file_get_contents("template/default.config.inc.php");
        $configStr="";
        // installfunctions.php line 28
        if (is_array($configVars) && (count($configVars) > 0)) {
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
            myfile_put_contents("data/".$installprefix."config.inc.php",$defaultConfigModified);
        } else {
            // Handle the case where $configVars is not an array
            //$configVars = [];
            // You can add more logic here as needed, or handle the error
            $defaultConfigModified=str_replace("<%main%>","",$defaultConfig);
            myfile_put_contents("data/".$installprefix."config.inc.php",$defaultConfigModified);
        }



}

/**
 *
 * function to write the header and footer file after 
 * installation
 *
 * @param $installprefix string
 *
 */ 

function writeHeaderFooterFile($installprefix)
{
// Ensure the 'data' directory exists
if (!is_dir("data")) {
mkdir("data", 0755, true);
}
        
	$defaultHeader=file_get_contents("template/default.header.inc.php");
	myfile_put_contents("data/".$installprefix."header.inc.php",$defaultHeader);
	$defaultFooter=file_get_contents("template/default.footer.inc.php");
	myfile_put_contents("data/".$installprefix."footer.inc.php",$defaultFooter);
}

/**
 *
 * function to write the template file after 
 * installation
 *
 *
  * @param $templateName string
 * @param $installprefix string
 *
 */ 

function writeTemplateFile($templateName,$installprefix)
{
    
    // Ensure the 'data/images' directory exists
    if (!is_dir("data/" . $installprefix . "images")) {
        mkdir("data/" . $installprefix . "images", 0755, true);
    }
    // Ensure the 'data/images' directory exists
    if (!is_dir("data/".$installprefix."images")) {
        mkdir("data/".$installprefix."images", 0755, true);
    }
        
	$defaultTemplate=file_get_contents("template/".$templateName."/"."default.template.inc.php");
	myfile_put_contents("data/".$installprefix."template.inc.php",$defaultTemplate);
	if(is_dir("template/".$templateName."/images/"))
	{
		$handler = opendir("data/".$installprefix."images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!=".."&&!is_dir("data/".$installprefix."images/".$images))
			{
				unlink("data/".$installprefix."images/".$images);
			}
		}
		closedir($handler);
		$handler = opendir("template/".$templateName."/images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!=".."&&!is_dir("data/".$installprefix."images/".$images))
			{
				copy("./template/".$templateName."/images/".$images, "./data/".$installprefix."images/".$images); 

			}

		}
		closedir($handler);
	}
}

/**
 *
 * This function was added to make file_put_contents
 * comaptaible with PHP v4 file_put_contents is not available
 * on PHP v4
 *
 * @param $filename string
 * @param $data string
 *
 */ 

function myfile_put_contents($filename, $data)
{
	if (!function_exists('file_put_contents')) 
	{
        $f = @fopen($filename, 'w');
        if (!$f) 
		{
            return false;
        } 
		else 
		{
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
	else
	{
		return file_put_contents($filename,$data);
	}

}

function bibShortnameFunc($bibVersion)
{
    return $bibVersion['shortname'];
}
?>