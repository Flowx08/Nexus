<?php

/////////////////////////////////////////
//Parameters:
// name: the bot name
// func:
//   get: get return message and clear it
//   set: set the bot return message
// data: the bot return message
/////////////////////////////////////////
	
	include "log.php";

	if ($_GET["name"] and $_GET["func"])
	{
		//Compute filepath
		$url = "return/". $_GET["name"];
	
		//Check for errors
		if (!file_exists($url)) {
			echo "Error : unknown bot name";
			exit();
		}
		
		if ($_GET["func"] == "get")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			//Update log
			writelog("botoutput " . $_GET["name"]);
			
			$data = file_get_contents($url);
			echo $data;
		}
		elseif ($_GET["func"] == "set")
		{
				file_put_contents($url, $_POST["data"]);
				echo "Done";
		}
		else
		{
			echo "Error : invalid paramter 'func' value";
		}
	}
	else
	{
		echo "Error : parameter 'name' or 'func' required";
	}

?>
