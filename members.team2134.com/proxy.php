<?php

	if($_GET["id"] == "calendar")
	{	
		preg_match_all('/^[\w]+/i', $_SERVER["HTTP_USER_AGENT"], $ide);
		$ide = $ide[0][0];
		
		$file = fopen("cal.log", "a");
		fwrite($file, var_export($ide, true)."\n");
		fclose($file);
		
		if($ide == "Apple")
		{
			header("Content-type: text/plain");
			
			$lines = file("http://www.google.com/calendar/feeds/cdsengineers%40team2134.com/public/basic");
			foreach($lines as $line)
			{
			    echo($line);
			}
		}
		else if($ide == "Mozilla")
		{	
			$lines = file("http://www.google.com/calendar/embed?src=cdsengineers%40team2134.com&ctz=America/Phoenix");
			foreach($lines as $line)
			{
			    echo($line);
			}
		}
		else
		{
			header("Content-Type: text/Calendar");
			header("Content-Disposition: attachment; filename=my_ical.ics");
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			
			$lines = file("http://www.google.com/calendar/ical/cdsengineers%40team2134.com/public/basic.ics");
			foreach($lines as $line)
			{
				if(strpos($line, "VERSION:2.0") === false)
			    	echo($line);
			
				if(strpos($line, "BEGIN:VCALENDAR") !== false)
					echo("VERSION:2.0\r\n");
			}
		}
	}
?>