<?php

	require_once("team2134.com.inc");
	require_once("functions.inc");
	
	$ca = new CentralAuth();
	$ca->protect();
	$db->select_db("team2134_scout_db");
	
	$action = $get["action"];
	
	if($action == "newMatch")
	{
		if(is_numeric($post["match_number"]))
			$db->query("INSERT INTO matches (match_number, match_type, red_team1, red_team2, red_team3, blue_team1, blue_team2, blue_team3) VALUES 
										($post[match_number], '$post[match_type]', $post[red_team1], $post[red_team2], $post[red_team3], 
										 $post[blue_team1], $post[blue_team2], $post[blue_team3])");
										
			$_SESSION["message"]["text"] = "Added $post[match_type] match $post[match_number] successfully.";
			$_SESSION["message"]["status"] = "success";
			header("location: /matches");
	}
	else if($action == "test")
	{
		preg_match_all("(red|blue)", "blue_team1", $out);
		printPretty($out[0][0]);
	}
	else
	{
		echo "<h2>SERVER</h2>";
		printPretty($_SERVER);
		
		echo "<h2>Session</h2>";
		printPretty($_SESSION);
		echo "The method $action does not exist";
	}
?>