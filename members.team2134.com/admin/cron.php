<?php

	require_once('/home/alampiss/team2134.com/members/admin/googleActions.php');	
	
	updateAllAlbums(" via Cron Job");
	
	if(!$updated)
	{
		echo "All up to date!";
	}
	else
	{
		print_r($changed);
	}
?>