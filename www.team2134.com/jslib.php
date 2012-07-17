<?php
	
	if(array_key_exists("js", $_GET))
	{
		header("Content-type: application/javascript");
		
		if(array_key_exists("jq", $_GET))
		{
			echo file_get_contents("http://code.jquery.com/jquery-latest.min.js");
		}
		else if(array_key_exists("jqui", $_GET))
		{
			echo file_get_contents("jslib/js/jquery-1.6.2.min.js", true);
			echo file_get_contents("jslib/js/jquery-ui-1.8.16.custom.min.js", true);
		}
		
		echo file_get_contents("jslib/myJavascript.js", true);
	}
	else if(array_key_exists("css", $_GET))
	{
		header("Content-type: text/css");
		
		if(array_key_exists("jqui", $_GET))
		{
			echo file_get_contents("jslib/css/ui-lightness/jquery-ui-1.8.16.custom.css", true);
		}
	}
	
?>