<?php

/////////////////////////////////////////
//Parameters:
// name: the bot name
/////////////////////////////////////////

	if ($_GET["name"])
	{
		//Compute filepath
		$url = "status/". $_GET["name"];

		//Check for errors
		if (!file_exists($url)) {
			echo "Error : unknown bot name";
			exit();
		}

		file_put_contents($url, time());
		echo "Done"; //Done
	}
	else
	{
		echo "Error : parameter 'name' required";	//Error
	}

?>
