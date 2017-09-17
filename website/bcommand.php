<?php
/////////////////////////////////////////
//Parameters:
// name: the bot name
// func:
//   get: get command and clear it
//   set: set the bot command
// data: the bot command
/////////////////////////////////////////

	include "log.php";

	if ($_GET["name"] and $_GET["func"])
	{
		//Compute filepath
		$url = "command/". $_GET["name"];
	
		//Check for errors
		if (!file_exists($url)) {
			echo "Error : unknown bot name";
			exit();
		}
		
		if ($_GET["func"] == "get")
		{
			$data = file_get_contents($url);
			echo $data;
			if ($data) file_put_contents($url, "");
		}
		elseif ($_GET["func"] == "set")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			//Update log
			writelog("botcmd " . $_GET["name"] . " " . $_GET["data"]);

			//Set command
			file_put_contents($url, $_GET["data"]);
			echo "Done";
		}
		else if ($_GET["func"] == "check")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			//Update log
			writelog("botcheck " . $_GET["name"]);

			$data = file_get_contents($url);
			echo $data;
		}
		else
		{
			echo "Error : invalid parameter 'func' value";
		}
	}
	else
	{
		echo "Error : parameter 'name' or 'func' required";
	}

?>
