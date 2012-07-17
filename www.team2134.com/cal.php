<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$query = $db->query("SELECT * FROM objectives WHERE type='Public' ORDER BY date DESC");
	
	while($row = mysqli_fetch_assoc($query))
	{
		echo '<div class="calendar-date"><div class="left-col">'.date("M", strtotime($row['date'])).'<div style="font-size: 16px;">'.date("jS, Y", strtotime($row['date'])).'</div><br></div><div class="right-col">'.$row['desc'].'</div></div>';	
	}
?>