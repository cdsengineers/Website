<?php

	try
	{
		// Decode the payload json string
		$payload = json_decode($_REQUEST['payload']);
	}
	catch(Exception $e)
	{
		exit(0);
	}

	// Pushed to master?
	if ($payload->ref === 'refs/heads/master')
	{
		// Log the payload object
		@file_put_contents('logs/github.txt', print_r($payload, TRUE), FILE_APPEND);
		
		shell_exec("git pull origin master");
	}

?>