<?php

function writelog($text) {
	
	//Create log line
	$log  = "[" . date("H:i:s") . " - " . $_SERVER['REMOTE_ADDR'] . "] " . $text . PHP_EOL;

	//Save string to log, use FILE_APPEND to append.
	file_put_contents("log/" . date("j_n_Y"), $log, FILE_APPEND);
}

?>
