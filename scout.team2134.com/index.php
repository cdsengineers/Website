<?php

	require_once("team2134.com.inc");
	require_once("functions.inc");
	
	$ca = new CentralAuth();
	$ca->protect();
?>

<html>
	<head>
		<title>Team 2134 Scouts</title>
		
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		<script src="/jquery.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="/styles.css">
		
	</head>
	
	<body>
		<?php 
			$action = ($get["action"] != "")?$get["action"]:"teams";
			$page = $get["page"];
		?>
		<div id="titleBar">
			<?php echo $_SESSION["user"]["account_fullname"] ?>
			<a href="/functions/logout" style="float: right;">logout</a>
		</div>
		
		<div id="navBar">
			<div class="item link no_underline red" href="/teams">Teams</div>
			<div class="item link no_underline" href="/matches">Matches</div>
		</div>
		
		<?php
		
			$db->select_db("team2134_scout_db");
			echo "<div id='container'>";
			
			if($_SESSION["message"]["text"] != "")
			{
				echo "<div class='{$_SESSION[message][status]}'>{$_SESSION[message][text]}</div>";
				$_SESSION["message"]["text"] = "";
			}
			
			if($action == "teams")
			{
				if($page == "")
				{
					echo "<table id='teamTable'>
						<tr>
							<th>Rank</th>
							<th>Team Number</th>
							<th></th>
						</tr>";
				
					$query = $db->query("SELECT * FROM teams ORDER BY team_rank ASC");
					while($team = mysqli_fetch_assoc($query))
						echo "<tr><td>$team[team_rank]</td><td class='link' href='/teams/$team[team_number]'>$team[team_number]</td><td class='link' href='edit/team/$team[team_number]'>Edit</td></tr>";
					
					echo "</table>";
				}
				else if(is_numeric($page))
				{
					$team = mysqli_fetch_assoc($db->query("SELECT * FROM teams WHERE team_number = $page"));
					echo "<h3>Team $page</h3>
					<div id='team_info'>
						Team Name: $team[team_name]<br>
						Team Desc: $team[team_desc]<br>
						Team Rank: $team[team_rank]
					</div>
					
					<h3>Matches</h3>
					<table id='matchTableLittle'>
						<tr>
							<th>Match</th>
							<th>Team Mates</th>
							<th>Result</th>
						</tr>";
					$matches = $db->query("SELECT *  FROM matches WHERE 
															red_team1 = $page OR 
															red_team2 = $page OR 
															red_team3 = $page OR 
															blue_team1 = $page OR 
															blue_team2 = $page OR 
															blue_team3 = $page");
					
					while($match = mysqli_fetch_assoc($matches))
					{
						$search = array_flip($match);
						$match_result = "Lost";
						if(strpos($search[$page], "red") !== false && $match["match_status"] == "red")
							$match_result = "Won";
						else if(strpos($search[$page], "blue") !== false && $match["match_status"] == "blue")
							$match_result = "Won";
						if($match["match_status"] == "tie" || $match["match_status"] == "pending")
							$match_result = $match["match_status"];
							
						
						preg_match_all("(red|blue)", $search[$page], $out);
						$team_color = $out[0][0];
						$team = null;
						
						$team["{$team_color}_team1"] = $match["{$team_color}_team1"];
						$team["{$team_color}_team2"] = $match["{$team_color}_team2"];
						$team["{$team_color}_team3"] = $match["{$team_color}_team3"];
						unset($team[$search[$page]]);
						
						$team_mates = "";
						foreach($team as $mate)
						{
							$team_mates .= "<span class='link' href='/teams/$mate'>$mate</span>, ";
						}
						
						$team_mates = substr($team_mates, 0, -2);
						
						echo "<tr>
								<td class='link' href='/matches/$match[match_id]'>$match[match_type] $match[match_number]</td>
								<td>$team_mates</td>
								<td>$match_result</td>
							</tr>";
					}
					
					echo "</table>";
				}
			
				echo "</div>";
			}
			else if($action == "matches")
			{
				if($page == "")
				{
					echo "<table id='matchTable'>
						<tr>
							<th>Match</th>
							<th>Type</th>
							<th colspan=3 class='red_team'>Red Team</th>
							<th colspan=3 class='blue_team'>Blue Team</th>
							<th style='text-align: center;'>Result</th>
						</tr>";
				
					$query = $db->query("SELECT * FROM matches ORDER BY match_type, match_number ASC");
					while($match = mysqli_fetch_assoc($query))
					{
						$score = ($match["match_status"] != "pending")?("(".(($match["match_status"] == "blue")?("$match[blue_team_total] - $match[red_team_total]"):("$match[red_team_total] - $match[blue_team_total]")).")"):"";
						echo "<tr>
								<td class='link' href='/matches/$match[match_id]'>$match[match_number]</td>
								<td>$match[match_type]</td>
								<td class='red_team link' href='/teams/$match[red_team1]'>$match[red_team1]</td>
								<td class='red_team link' href='/teams/$match[red_team2]'>$match[red_team2]</td>
								<td class='red_team link' href='/teams/$match[red_team3]'>$match[red_team3]</td>
								<td class='blue_team link' href='/teams/$match[blue_team1]'>$match[blue_team1]</td>
								<td class='blue_team link' href='/teams/$match[blue_team2]'>$match[blue_team2]</td>
								<td class='blue_team link' href='/teams/$match[blue_team3]'>$match[blue_team3]</td>
								<td class='$match[match_status]_team'>$match[match_status] $score</td>
							</tr>";
					}
					echo "</table>
					<br>
					<div style='float: right;' class='link' href='/matches/new'>Add Match</div>";
				}
				else if(is_numeric($page))
				{
					$match = mysqli_fetch_assoc($db->query("SELECT * FROM matches WHERE match_id = $page"));
					echo "<h3>$match[match_type] $match[match_number]</h3>";
				}
				else if($page == "new")
				{
					$options = enum_select("matches", "match_type");
					echo "
						<h3>new match</h3>
						<form action='/functions/newMatch' method='post' id='new_match_form'>
							<div class='label'>Match Number:</div> <input type='number' name='match_number' />
							<div class='label'>Match Type:</div> <select name='match_type'>";
							
							foreach($options as $option)
								echo "<option value='$option'>$option</option>";
							echo "</select>
							<div class='label'>Red Team:</div>
								<div class='right_float'>
									<input type='number' name='red_team1' />
									<input type='number' name='red_team2' />
									<input type='number' name='red_team3' />
								</div>
							<div class='label'>Blue Team:</div> 
								<div class='right_float'>
									<input type='number' name='blue_team1' />
									<input type='number' name='blue_team2' />
									<input type='number' name='blue_team3' />
								</div>
							<input type='submit' style='clear: both;' value='Create Match' />
						</form>
					";
					
				}
			}
		?>
		
		<script>
			$("body").css("min-height", $("body").css("height"));
			$("body").css("min-height", "+=60");
			setTimeout("window.scroll(0,1);", 100);
			
			$(".link").live("click touchend", function() {
				window.location.href = $(this).attr("href");
			});
			
			setTimeout("$('.success').slideUp(1000)", 3000);
		</script>
	</body>
</html>