<?php
	
	include "log.php";

	if ($_GET["func"])
	{
		if ($_GET["func"] == "list")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			//Update log
			writelog("fileslist");
			
			$bots = scandir("files/");
			foreach ($bots as $b) {
				if ($b != "." and $b != "..") {
					echo $b, PHP_EOL;
				}
			}
		}
		elseif ($_GET["func"] == "upload")
		{
        	$file = $_FILES['file'];
			$uploadfile = "files/" . $file['name'];
          	if(is_uploaded_file($file['tmp_name']))
        	{
				move_uploaded_file($file['tmp_name'], $uploadfile);
				echo "File successfully uploaded";
			}
			else
			{
				echo "Error: an error occurred while uploading the file";
			}
		}
		elseif ($_GET["func"] == "remove")
		{
			//Start secure session
			session_start();
			
			//Check permissions
			if (!$_SESSION['logged']) {
				echo "You need to login first!";
				return;
			}
			
			//Update log
			writelog("removefile " . $_GET["fname"]);
			
			if ($_GET["fname"]) {
                array_map('unlink', glob("files/" . $_GET["fname"]));
				echo "Done";
			}
			else
			{
				echo "Error: parameter 'fname' not found";
			}
		}
		else
		{
			echo "Error : invalid parameter 'func' value";
		}
	}
	else
	{
		echo "Error : parameter 'func' not found";
	}

?>
