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
		<title>Bulletin Board</title>
	</head>
	<body>
		<?php require_once('header.php'); ?>
		<div id="header"><h1>Bulletin Board</h1></div>
		<div id="container">
			<h3>Individual Tasks</h3>
			<table class="tasksTable">
			<?php
				$db->select_db("cdsengineers");
			?>
			<tr>
				<th>Pending</th>
			</tr>
			<?php
				$query = $db->query("SELECT * FROM objectives WHERE type='Person' AND status=0 ORDER BY date ASC");
				
				if(mysqli_num_rows($query) == 0)
				{
					echo("<tr><td>None</td></tr>");
				}
				else
				{
					while($row = mysqli_fetch_assoc($query))
					{
						$num = mysqli_num_rows($db->query("SELECT * FROM obj_discuss WHERE obj_id=".$row["id"]));
						$num = $num." ".(($num ==1)?"reply":"replies");
						
						$at = $ca->getUserById($row["assign"]);
						$assignedTo = ($user["id"] == $row["assign"])?"Me":$at["account_fullname"];
						
						echo("<tr><td><div id='task'><a class='expandTask' href='#' thread='".$row["id"]."'>".$row["desc"]."</a><span style='float:right;'>".date("M j", strtotime($row["date"]))." <span id='replies'>($num)</span></span><div class='date'>Assigned to $assignedTo</div></div></td></tr>");
					}
				}
			?>
			<tr>
				<th>Completed</th>
				<?php
					$query = $db->query("SELECT * FROM objectives WHERE type='Person' AND status=1 ORDER BY date ASC");

					if(mysqli_num_rows($query) == 0)
					{
						echo("<tr><td>None</td></tr>");
					}
					else
					{
						while($row = mysqli_fetch_assoc($query))
						{
							$num = mysqli_num_rows($db->query("SELECT * FROM obj_discuss WHERE obj_id=".$row["id"]));
							$num = $num." ".(($num ==1)?"reply":"replies");
							
							$at = $ca->getUserById($row["assign"]);
							$assignedTo = ($user["id"] == $row["assign"])?"Me":$at["account_fullname"];
							echo("<tr><td><div id='task'><a class='expandTask' href='#' thread='".$row["id"]."'>".$row["desc"]."</a> <span style='float:right;'>".date("M j", strtotime($row["date"]))." <span id='replies'>($num)</span></span><div class='date'>Assigned to $assignedTo</div></div></td></tr>");
						}
					}
				?>
			</tr>
			</table>
			
			<h3>Team Tasks</h3>
			<table class="tasksTable">
			<?php

				$groups = explode(", ", str_replace("admin, ", "", $user["account_group"]));
				
				foreach($groups as $group)
				{
			?>
			<tr>
				<th><?=$group?></th>
			</tr>
			<?php
				$query = $db->query("SELECT * FROM objectives WHERE type='Group' AND assign='$group' ORDER BY date ASC");
				
				if(mysqli_num_rows($query) == 0)
				{
					echo("<tr><td>None</td></tr>");
				}
				else
				{
					while($row = mysqli_fetch_assoc($query))
					{
						$num = mysqli_num_rows($db->query("SELECT * FROM obj_discuss WHERE obj_id=".$row["id"]));
						$num = $num." ".(($num ==1)?"reply":"replies");
						echo("<tr><td><div id='task'><a class='expandTask' href='#' thread='".$row["id"]."'>".$row["desc"]."</a> <span style='float:right;'>".date("M j", strtotime($row["date"]))." <span id='replies'>($num)</span></span></div></td></tr>");
					}
				}
			?>
		
			<?php
				}
			?>
			<tr>
				<th>Completed</th>
			</tr>
			<?php
				$query = $db->query("SELECT * FROM objectives WHERE type='Group' AND status=1 ORDER BY date ASC");
				
				if(mysqli_num_rows($query) == 0)
				{
					echo("<tr><td>None</td></tr>");
				}
				else
				{
					while($row = mysqli_fetch_assoc($query))
					{
						$num = mysqli_num_rows($db->query("SELECT * FROM obj_discuss WHERE obj_id=".$row["id"]));
						$num = $num." ".(($num ==1)?"reply":"replies");
						echo("<tr><td><div id='task'><a class='expandTask' href='#' thread='".$row["id"]."'>".$row["desc"]."</a> <span style='float:right;'>".date("M j", strtotime($row["date"]))." <span id='replies'>($num)</span></span></div></td></tr>");
					}
				}
			?>
			</table>
		</div>
		
		<script>
			$(".expandTask").live("click", function()
			{
				var target = $(this);
				var result = "";
				
				target.append("<img style='padding-left: 10px' height='12' src='http://members.team2134.com/images/loading.gif' />");
				
				$.ajax({
					url: "/functions.php?action=getObjectiveComments&threadId="+$(this).attr("thread"),
					success: function(data)
					{
						for(arr in data)
						{
							result += "<div class='comment'><strong>"+data[arr]["author"]+ "</strong> " +data[arr]["content"]+"<div class='date'>"+data[arr]["pubdate"]+"</div><div commentId='"+data[arr]["id"]+"' class='deleteComment'>X</div></div>";
						}
						
						result += "<div class='reply'><input thread='"+target.attr("thread")+"' id='replyBox' type='text' value='write a reply'/></div>";
						
						target.find("img").remove();
						if(data != null)
							target.parent().find("span > #replies").html("("+data.length + " "+((data.length ==1)?"reply":"replies")+")");
						
						target.parent().parent().html(target.parent());
						target.parent().parent().append(result);
					},
					dataType: "json"
				});
				
				return false;
			});
			
			$("#replyBox").live('focus',function(e)
			{
				if(this.value == "write a reply") {
					this.value = "";
					$(this).css("color", "black");
				}
			});
			
			$("#replyBox").live('blur',function(e)
			{
				if(this.value == "") {
					this.value = "write a reply";
					$(this).css("color", "grey");
				}
			});
			
			$(".comment > .deleteComment").live("click", function()
			{
				var target = $(this);
				
				$.ajax({
					url: "/functions.php?action=deleteObjectiveComment&commentId="+$(this).attr("commentId"),
					success: function(data)
					{
						target.parent().parent().find("a").click();
					},
					dataType: "json"
				});
			});
			
			$("#replyBox").live('keyup',function(e)
			{
				if(e.keyCode == 13)
				{
					var target = $(this);
					
					$.ajax({
						url: "/functions.php?action=saveObjectiveComment&threadId="+$(this).attr("thread")+"&comment="+$(this).val(),
						success: function(data)
						{
							target.parent().parent().find("a").click();
						},
						dataType: "json"
					});
				}
			});			
			
		</script>
		
	</body>
</html>