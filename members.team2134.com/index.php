<?php 	
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect();
	
	$user = $_SESSION["user"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Members Home</title>
		
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-27481354-2']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		
	</head>
	<body>
		<?php require_once('header.php'); ?>
		<?php
	
		$stack;
	
		$currentYear = date("Y");
		$currentMonth = date("m");
		$currentDay = date("d");
	
		function createCalendar($shift)
		{	
			global $user, $db;
			
			$currentYear = date("Y");
			$currentMonth = date("m");
			$currentDay = date("d");
		
			if( ($currentMonth + $shift) > 12)
			{
					$temp = 12-$currentMonth;
					$shift -= $temp;
					$currentYear++;
					$currentMonth = $shift;
			}
			else
			{
				$currentMonth += $shift;
			}
		
			global $rows;
		
			$dates = $db->query("SELECT * FROM objectives WHERE date >= '".$currentYear."-".$currentMonth."-1' AND date <= '".$currentYear."-".$currentMonth."-31' ORDER BY `date` ASC");	
			$rowsD = mysqli_fetch_assoc($dates);
		
		
		
			$num = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
		
			$startDate = date("N", strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'));
		
			$rows = (int)(($num)/7) + 1;
		
			if($startDate > 5)
				$rows++;
		
			$a = 1;
		
			echo "<div class='month-name'>".date("F", strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'))."</div><div class='calendar-month'><table border='1' style='display:inline-block;' class='calendar' id='calendar-".date('F-Y', strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'))."'><tr class='day-names'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td></tr>";
		
			for($i = 1; $i <= $rows; $i++)
			{
				echo "<tr class='week'>";
				for($z = 0; $z < 7; $z++)
				{
					if( ($z < $startDate) and ($i == 1) )
					{
						echo "<td></td>";
					}
					else if($a > $num)
					{
						echo "<td></td>";
					}
					else
					{
						$itemDay = date("j", strtotime($rowsD['date']));
						$itemMonth = date("n", strtotime($rowsD['date']));
						$itemYear = date("Y", strtotime($rowsD['date']));
					
						if($itemYear == $currentYear && $itemMonth == $currentMonth && $itemDay == $a)
						{
							if($rowsD['type'] != "Person" || ($rowsD['type'] == "Person" && $rowsD["assign"] == $user["id"]))
							{
								echo "<td class='dot'>".$a."</td>";
							}
							else
							{
								echo "<td>".$a."</td>";
							}
							
							while($itemYear == $currentYear && $itemMonth == $currentMonth && $itemDay == $a)
							{

								$rowsD = mysqli_fetch_assoc($dates);
								$itemDay = date("j", strtotime($rowsD['date']));
								$itemMonth = date("n", strtotime($rowsD['date']));
								$itemYear = date("Y", strtotime($rowsD['date']));
							}
						}
						else
						{
							echo "<td>".$a."</td>";
						}
						$a++;
					}
				}
				echo "</tr>";
			}
			echo "</table>";
			$con = "";
			$es = $db->query("SELECT * FROM objectives WHERE date >= '".$currentYear."-".$currentMonth."-1' AND date <= '".$currentYear."-".$currentMonth."-31' ORDER BY `date` ASC");
			
			$stack = array();
			while($row = mysqli_fetch_assoc($es))
			{	
				if($row['type'] == "Person" && $row["assign"] == $user["id"])
				{
					$con .= "<li>".date("n/j", strtotime($row['date']))." - ".$row['desc']."</li>";
				}
				else if($row['type'] == "Group" && strpos($user["account_group"], $row["assign"]) !== false)
				{
					if(count($stack[$row['assign']]) < 1)
					{
						$stack[$row['assign']] = array();	
					}
					array_push($stack[$row['assign']], "<li>".date("n/j", strtotime($row['date']))." - ".$row['desc']."</li>");
				}
			}
			
			$groups = "";
			$keys = array_keys($stack);
			
			for($keysIndex = 0; $keysIndex < count($stack); $keysIndex++)
			{	
				$groups .= "<li><strong>".$keys[$keysIndex]."</strong><ul>";
				
				for($itemIndex = 0; $itemIndex < count($stack[$keys[$keysIndex]]); $itemIndex++)
				{
					$groups .= $stack[$keys[$keysIndex]][$itemIndex];
				}
				$groups .= "</ul></li>";
			}
			
			if(strlen($con) > 0)
			{
				$con = $groups."<li><strong>Personal Tasks</strong><ul>".$con."</ul></li>";
			}
			else
			{
				$con = $groups;
			}
			
			echo "<div class='calendar-events-list' id='calendar-".date('F-Y', strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'))."-list'><ul>".$con."</ul></div></div>\n<script>$(function() { $('#calendar-".date('F-Y', strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'))."-list').css('height', $('#calendar-".date('F-Y', strtotime($currentMonth.'/01/'.$currentYear.' 00:00:00'))."').height()); });</script>";
		}
		?>
		<div id="header"><h1>Members Home</h1></div>
		<div id="container">
			<div id="left-col">
				<div style="display: inline-block"></div>
				<div class="calendar-box">
					<?php createCalendar(0); ?>
					<?php createCalendar(1); ?>
					<?php createCalendar(2); ?>
				</div>
				<div class="clear">&nbsp;</div>

			</div>
			<div id="right-col">
				<div id="countdown"></div>
				<div id="info">
					Welcome to the CDS Engineers members area! Feel free to play around and please make suggestions for stuff you wanna see either here or on the main site. Send suggestions to <a href="mailto:admin@team2134.com">admin@team2124.com</a>
				</div>
				<?php
					$nextDate = date("d", mktime(0, 0, 0, date("m")  , date("d")+14, date("Y")));
					$nextMonth = date("m", mktime(0, 0, 0, date("m")  , date("d")+14, date("Y")));
					$nextYear = date("Y", mktime(0, 0, 0, date("m")  , date("d")+14, date("Y")));

					$dates = $db->query("SELECT * FROM objectives WHERE date >= '".$currentYear."-".$currentMonth."-".$currentDay."' AND date <= '".$nextYear."-".$nextMonth."-".$nextDate."' ORDER BY date ASC");	

					$todo = "None";
					$personal = "None";

					while($rows = mysqli_fetch_assoc($dates))
					{
						if( ($rows['type'] == "Group") && ((strpos(" ".$user['account_group'], $user['assign'])) || (strpos(" ".$user['account_group'], "admin"))) )
						{
							if($todo == "None") {$todo = "";}
							$todo .= "<div align='center'><strong>".$rows['desc']."</strong> by <strong>".date("m/d", strtotime($rows['date']))."</strong></div>";
						}
						else if($rows['type'] == "Person" && $rows['desc'] == $user['id'])
						{	
							if($personal == "None") {$personal = "";}
							$personal .= "<div align='center'><strong>".$rows['desc']."</strong> by <strong>".date("m/d", strtotime($rows['date']))."</strong></div>";
						}
					}
					
					
					$groups = explode(", ", str_replace("Mentor, ", "", str_replace("admin, ", "", $user['account_group'])));
				?>
				
                <div id="myteams">
                	<h1>My Teams</h1>
                    <?php 
						if($user['account_group'] != "")
						{
							for($i = 0; $i < count($groups); $i++)
							{
								?><div style="display:inline-block; margin-right:10px; margin-bottom:10px; vertical-align:top;" align="center"><h5><?php echo ucfirst($groups[$i]); ?></h5><?php
								$people = $db->query("SELECT * FROM accounts WHERE account_group LIKE '%".$groups[$i]."%' ORDER BY account_fullname ASC");
								while($rows = mysqli_fetch_assoc($people))
								{
									echo $rows['account_fullname']."<br />";	
								}
								?></div><?php
							}
						}
						else
						{
							echo "You are not registered for any teams at this time.";
						}
					?>
					<div style="wdith: 100%;">&nbsp;</div>
					<a href="#teams" class="fancybox">All Teams</a>
                </div>
			</div>
		</div>
		<div id="hiddenShit" style="display: none;">
			<div id="teams">
				<h1>All Teams</h1>
                <?php 
						for($i = 0; $i < count($allGroups[1][0]); $i++)
						{
							?><div style="display:inline-block; margin-right:20px; margin-bottom:10px; vertical-align:top;" align="center"><h5><?php echo $allGroups[1][0][$i]; ?></h5><?php
							$people = $db->query("SELECT * FROM members WHERE members.group LIKE '%".$allGroups[1][0][$i]."%' ORDER BY fullname ASC");
							while($rows = mysqli_fetch_assoc($people)) 
							{
								echo $rows['fullname']."<br />";	
							}
							?></div><?php
						}
					?>
			</div>
		</div>
        
        <script type="text/javascript">
		$(function () {
			$('.fancybox').fancybox();
			
			
			setInterval(function()
			{
				var curDate = new Date();
				var futureDate = new Date(2012, 1, 21, 8, 0, 0, 0);
			
				var newDate = new Date(futureDate.getTime() - curDate.getTime());
				
				var monthsLeft = newDate.getUTCMonth();
				var daysLeft = newDate.getUTCDate();
				var hoursLeft = newDate.getUTCHours();
				var minutesLeft = newDate.getUTCMinutes();
				var secondsLeft = newDate.getUTCSeconds();
				
				if(hoursLeft < 10)
					hoursLeft = "0"+hoursLeft;
					
				if(minutesLeft < 10)
					minutesLeft = "0"+minutesLeft;
					
				if(secondsLeft < 10)
					secondsLeft = "0"+secondsLeft;
				
				if(monthsLeft > 0)
					$("#countdown").html("<div>" + monthsLeft + " "+((monthsLeft == 1)?"month":"months")+" and " + daysLeft + " days left</div>Until the Robot Ships");
				else
					$("#countdown").html("<div>" + daysLeft + " days and " + hoursLeft + ":" + minutesLeft + ":" + secondsLeft + "</div>Until the Robot Ships");
			}, 200);
			
			
		});
		</script>
        
	</body>
</html>