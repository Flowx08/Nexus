<?php

	include "log.php";

	//Start secure session
	session_start();
	
	//Update log
	writelog("login " . $_GET["psw"]);

    if ($_GET["psw"] == "francis")
    {
		$_SESSION['logged'] = True;
    	echo "True";
    }
    else
    {
		$_SESSION['logged'] = False;
    	echo "False";
    }
?>
