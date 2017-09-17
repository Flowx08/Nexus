<?php

/////////////////////////////////////////
//Parameters:
// name: the bot name
/////////////////////////////////////////
	
	if ($_GET["name"]) 
	{
		if (!file_exists("status/". $_GET["name"])) {
			$file = fopen("status/". $_GET["name"], "w") or die("E0");
			fclose($file);
		}
		if (!file_exists("command/". $_GET["name"])) {
			$file = fopen("command/". $_GET["name"], "w") or die("E1");
			fclose($file);
		}
		if (!file_exists("return/". $_GET["name"])) {
			$file = fopen("return/". $_GET["name"], "w") or die("E2");
			fclose($file);
		}
		echo "Done"; //Done
	}
	else
	{
		echo "Error"; //Error
	}

?>
