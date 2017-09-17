<?php
	
/////////////////////////////////////////
//Parameters:
// type:
//	 bots: get bots list
//	 status: get status list
/////////////////////////////////////////
		
	if ($_GET["type"])
	{
		if ($_GET["type"] == "bots")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			$bots = scandir("status/");
			foreach ($bots as $b) {
				if ($b != "." and $b != "..") {
					echo $b . "," . file_get_contents("status/" . $b) . "," . (file_get_contents("command/" . $b) == "" ? "Not working" : "Busy") . PHP_EOL;
				}
			}
		}
		else
		{
			echo "Error : invalid parameter 'func' value";
		}
	}
	else
	{
		echo "Error : parameter 'type' not found";
	}

?>
