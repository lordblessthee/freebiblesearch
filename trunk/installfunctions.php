<?php
function writeConfigFile($configVars,$installprefix)
{
		//echo "in writeConfigFile"."<br>";
        
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
        file_put_contents("data/".$installprefix."config.inc.php",$defaultConfigModified);

}

function writeHeaderFooterFile($installprefix)
{
       //echo "in  writeHeaderFooterFile"."<br>";
        
	$defaultHeader=file_get_contents("template/default.header.inc.php");
	file_put_contents("data/header.inc.php",$defaultHeader);
	$defaultFooter=file_get_contents("template/default.footer.inc.php");
	file_put_contents("data/".$installprefix."footer.inc.php",$defaultFooter);
}

function writeTemplateFile($templateName,$installprefix)
{
       //echo "in  writeTemplateFile"."<br>";
        
	$defaultTemplate=file_get_contents("template/".$templateName."/"."default.template.inc.php");
	file_put_contents("data/".$installprefix."template.inc.php",$defaultTemplate);
	if(is_dir("template/".$templateName."/images/"))
	{
		$handler = opendir("data/".$installprefix."images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!="..")
			{
				unlink("data/".$installprefix."images/".$images);
			}
		}
		closedir($handler);
		$handler = opendir("template/".$templateName."/images");
		while(!(($images = readdir($handler))===false))
		{
			if($images!="."&&$images!="..")
			{
				copy("./template/".$templateName."/images/".$images, "./data/".$installprefix."images/".$images); 

			}

		}
		closedir($handler);
	}
}
?>