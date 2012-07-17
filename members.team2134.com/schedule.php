<?php
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect();
	
	$user = $_SESSION["user"];
	
	$offset = ($getValues["offset"] != "")?$getValues["offset"]:0;
	
	$startDate = strtotime("sunday last week +$offset weeks");
	$endDate = strtotime("+6 days", $startDate);
	$startTime = strtotime("midnight");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Build Schedule</title>
	</head>
	<body>
		<?php require_once('header.php'); ?>
		
		<style>
			h6 {
				color: grey;
				font-size: 10px;
				margin: 0px;
				font-weight: lighter;
			}
			
			h6 > a{
				color: grey;
				font-size: 10px;
				margin: 0px;
				font-weight: normal;
				text-decoration: none;
			}
		
			#weekCalendar th {
				margin: 0px 5px;
				width: 80px;
			}
			
			#weekCalendar td {
				border: 1px darkgrey solid;
			}
			
			#weekCalendar th {
				border-bottom: 1px darkgrey solid;
			}
			
			#weekCalendar {
				border: 1px darkgrey solid;
				width: 704px;
			}
			
			.buildBlock {
				background-color: lightgreen;
				cursor: pointer;
			}
			
			.myBuildBlock {
				background-color: lightblue;
				cursor: pointer;
			}
			
			.buildBlock div, .myBuildBlock div {
				margin: 3px;
				font-size: 10px;
				text-align: center;
			}
			
			#instructions {
				float: left;
				color: grey;
				font-size: 12px;
				padding-left: 98px;
			}
			
			#fancyboxElements {
				display: none;
			}
			
			#buildInfo {
				width: 340px;
			}
			
			#buildInfo span {
				font-weight: bold;
			}
			
			.disabled {
				font-weight: normal;
				font-size: 10px;
				color: grey;
			}
			
			.enabled {
				font-weight: normal;
				font-size: 10px;
				color: black;
			}
			
			.disabled a, .enabled a {
				font-weight: normal;
				font-size: 10px;
				color: black;
			}
		</style>
		
		<div id="header"><h1>Build Schedule</h1></div>
		<div id="container" align="center">
			<h2 id="weekOf">Week of <?= date("F jS", $startDate)." - ".((date("F", $startDate) != date("F", $endDate))?date("F", $endDate):"")." ".date("jS", $endDate)?></h2>
			<br>
			<table id="weekCalendar">
				<tr>
					<th><br><h6><a id="lastWeek" href="#"><-back</a>&nbsp;&nbsp;&nbsp;&nbsp;<a id="nextWeek" href="#">forth-></a></h6></th>
					<?php
						for($i = 0; $i < 7; $i++)
						{
							echo "<th>".date("<\h6>l</\h6> jS", strtotime("+$i days", $startDate))."</th>";
						}
					?>
				</tr>
				<?php
				
					for($i = 6; $i <= 22; $i++)
					{ 
						
						?>
						<tr>
						<?php
							echo "<th>".date("ga", strtotime("+$i hours", $startTime))."</th>";
							for($j = 0; $j < 7; $j++)
							{
								echo "<td id='".date("j", strtotime("+$j days", $startDate))."-$i'></th>";
							}
						?>
						</tr>
					<?php }
				?>
			</table>
			
			
			<div style="float: right; padding-right: 98px;" align="left">
				<div style="width: 20px; height:20px; display: inline-block; vertical-align:middle" class="buildBlock"></div>
				<span>Scheduled Build</span><br>
				<div style="width: 20px; height:20px; display: inline-block; vertical-align:middle" class="myBuildBlock"></div>
				<span>Attending Build</span>
			</div>
			<div id="instructions">*Click on a calendar event to see build details</div>
			<?php if($ca->isLevel("mentor")) { ?>
			<a class='fancybox' href="#addBuild">Add Builds</a>
			<?php } ?>
		</div>
		
		<script>
		weekOffset = 0;
		
		$(".activeBuildBox").live("click", function()
		{
			curBuild = $(this).attr("build");
			
			$.fancybox.showActivity();
			$.ajax(
			{
				url: "functions.php",
				type: "POST",
				data: "action=getBuildInfo&buildId="+$(this).attr("build"),
				success: function(msg)
				{
					console.log(msg);
					
					$("#buildInfo #date").html(msg["build_date"]);
					$("#buildInfo #startTime").html(msg["build_starttime"]);
					$("#buildInfo #endTime").html(msg["build_endtime"]);
					$("#buildInfo #myStatus").html(msg["myStatus"]);
					$("#buildInfo #emailMe").attr("checked", msg["email"]);
					$("#buildInfo #peopleAttending").html("");
					
					if(msg["myStatus"]=="Attending")
					{
						$("#buildInfo #emailMe").attr("disabled", false);
						$("#buildInfo #emailConfig").removeClass("disabled");
						$("#buildInfo #emailConfig").addClass("enabled");
					}
					else
					{
						$("#buildInfo #emailMe").attr("disabled", true);
						$("#buildInfo #emailConfig").addClass("disabled");
						$("#buildInfo #emailConfig").removeClass("enabled");
					}
					
					for(i = 0; i < msg["peopleAttending"].length; i++)
						$("#buildInfo #peopleAttending").append("<li>"+msg["peopleAttending"][i]+"</li>")
					
					$.fancybox.hideActivity();
					if(fancyIsOpen == false)
					{
						$("<a href='#buildInfo'></a>").fancybox(
						{
							'onClosed': function() {fancyIsOpen = false},
							'onComplete': function() {fancyIsOpen = true}
						}).click();
					}
				},
				dataType: "json"
			})
		})
		
		$("#buildInfo #changeStatus").live("click", function()
		{
			$(this).hide();
			$.fancybox.showActivity();
			$.ajax(
			{
				url: "functions.php",
				type: "POST",
				data: "action=changeMyBuildStatus&buildId="+curBuild,
				success: function(msg)
				{	
					$("#buildInfo #changeStatus").show();
					$(".activeBuildBox[build="+curBuild+"]").click();
					$(".activeBuildBox[build="+curBuild+"]").toggleClass("myBuildBlock");
					$(".activeBuildBox[build="+curBuild+"]").toggleClass("buildBlock");
				},
				dataType: "json"
			})
			
			return false;
		})
		
		$("#buildInfo #emailMe").live("click", function()
		{
			$.fancybox.showActivity();
			$.ajax(
			{	
				url: "functions.php",
				type: "POST",
				data: "action=edBuildEmail&buildId="+curBuild,
				success: function(msg)
				{
					$.fancybox.hideActivity();
					$("#buildInfo #emailMe").attr("checked", msg["b"]);
					hudMessage(msg["m"]);
				},
				dataType: "json"
			})
			
			return false;
		});
		
		$("#buildInfo #deleteBuild").live("click", function()
		{
			$.fancybox.showActivity();
			$.ajax(
			{	
				url: "functions.php",
				type: "POST",
				data: "action=deleteBuild&buildId="+curBuild,
				success: function(msg)
				{
					removeBlock(curBuild);
					$.fancybox.hideActivity();
					hudMessage("Build Deleted Successfully");
					$.fancybox.close();
				},
				dataType: "json"
			})
			
			return false;
		});
		
		$("#addBuild #addBuildForm").live("submit", function()
		{
			$("#addBuild #errorDiv").slideUp();
			$.fancybox.showActivity();
			$.ajax(
			{	
				url: "functions.php",
				type: "POST",
				data: "action=createNewBuild&"
						+$(this).serialize(),
				success: function(msg)
				{	
					makeBuildSlot(msg["build_id"], msg["build_date"], msg["build_starttime"], msg["build_endtime"], msg["build_info"], "buildBlock");
					$.fancybox.close();
					hudMessage("Build has been Scheduled!");
				},
				error: function(msg, textStatus, errorThrown)
				{
					$("#addBuild #errorDiv").html(msg.responseText);
					$("#addBuild #errorDiv").slideDown();
				},
				complete: function()
				{
					$.fancybox.hideActivity();
				},
				dataType: "json"
			})
			
			return false;
		})
		
		function makeBuildSlot(buildId, day, startTime, endTime, info, slotType)
		{	
			$("#"+day+"-"+startTime).html("<div>"+info+"</div>");
			$("#"+day+"-"+startTime).attr("rowspan", (endTime-startTime));
			$("#"+day+"-"+startTime).attr("build", buildId);
			$("#"+day+"-"+startTime).addClass(slotType);
			for(i = startTime+1; i < endTime; i++)
				$("#"+day+"-"+i).remove();
				
			$("#"+day+"-"+startTime).addClass("activeBuildBox");
		}
		
		function removeBlock(buildId)
		{
			var id = $("[build="+buildId+"]").attr("id");
			var rows = parseInt($("[build="+buildId+"]").attr("rowspan"));
			
			$("[build="+buildId+"]").html("");
			$("[build="+buildId+"]").removeClass();
			$("[build="+buildId+"]").attr("rowspan", 1);
			$("[build="+buildId+"]").attr("build", "");
			
			var day = id.split("-")[0] - 1;
			var hour = parseInt(id.split("-")[1]);
			
			for(i = (hour + 1); i < (hour + rows); i++)
			{
				if(day < 0)
				{
					wrap = $("#"+(day + 2)+"-"+i).clone().wrap("<div>").parent().html();
					$("#"+(day + 2)+"-"+i).replaceWith("<td id='"+(day + 1)+"-"+i+"'></td>"+wrap);
				}
				else
				{
					wrap = $("#"+day+"-"+i).clone().wrap("<div>").parent().html();
					$("#"+day+"-"+i).replaceWith(wrap+"<td id='"+(day + 1)+"-"+i+"'></td>");
				}
			}
		}
		
		function updatePage()
		{
			$.ajax({
				url: "schedule.php?offset="+weekOffset, 
				success: function(html)
				{
					$("#weekCalendar").html($(html).find("#weekCalendar").html());
					$("#weekOf").html($(html).find("#weekOf").html());
					
					eval(html.substring(html.indexOf("script id=\"calendarScript\"")+30, html.lastIndexOf("script")-3));
				}
			});
		}
		
		</script>
		
		<div id="fancyboxElements">
			<div id="buildInfo">
				<div style="float: right;">
					<div style="text-align: right; margin-top: 4px;"><?php if($ca->isLevel("mentor")) { ?><a href="#" id="deleteBuild">delete</a><?php } else echo "&nbsp;"?></div><br>
					People Attending <ul id="peopleAttending"></ul></div>
				<div style="float:left;">
					<h2>Build Info</h2>
					Date: <span id="date"></span><br>
					Starts at <span id="startTime"></span><br>
					Ends at <span id="endTime"></span><br><br>
					I am <span id="myStatus"></span> - 
					<span class="disabled" id="emailConfig"><a href="#" id="changeStatus">Change</a><br>
					<input type="checkbox" disabled id="emailMe">Send me a Reminder Email</span>
				</div>
			</div>
			<div id="addBuild">
				<form id="addBuildForm">
					<div class="error" id="errorDiv"></div>
					<h2>Schedule a Build</h2>
					<div style="float: right;">
					
					</div>
					<div style="float:left;">
						Date: <input type="hidden" name="date" value="<?= date("Y-m-j") ?>" /><span id="dateValue"> <?= date("Y-m-j") ?></span><br>
						Starts at <select name="startTime" id ="startTime"></select><br>
						Ends at <select name="endTime" id="endTime"></select><br><br>
						Objective is <input type="text" name="buildInfo" />
						
						<input type="submit" value="Schedule!">
					</div>
				</form>
				
				<script>
					
					$("#addBuild #startTime").html("");
					$("#addBuild #endTime").html("")
					for(h = 6; h < 22; h++)
					{
						for(m = 0; m < 60; m+=10)
						{
							$("#addBuild #startTime").append("<option value='"+h+":"+m+"'>"+((h-12 <= 0)?h:(h-12))+":"+((m==0)?"00":m)+" "+((h-12 <= 0)?"AM":"PM")+"</option>");
							$("#addBuild #endTime").append("<option value='"+h+":"+m+"'>"+((h-12 <= 0)?h:(h-12))+":"+((m==0)?"00":m)+" "+((h-12 <= 0)?"AM":"PM")+"</option>");
						}
					}
					
					$('.fancybox').fancybox();
				
					$("#addBuild input[name=date]").datepicker({
						onSelect: function(dateText, inst)
						{
							$("#addBuild #dateValue").html("  "+dateText)
						},
						showOn: "button",
						buttonImage: "images/calendar.gif",
						buttonImageOnly: true,
						dateFormat: 'yy-m-d'
					});
					
					$("#nextWeek").live("click", function()
					{
						weekOffset++;
						updatePage();
						return false;
					});
					
					$("#lastWeek").live("click", function()
					{
						weekOffset--;
						updatePage();
						return false;
					});
					
				</script>
			</div>
		</div>
		
		<script id="calendarScript">
			fancyIsOpen = false;
		
		<?php
			printBuildDatesForJavaScript($startDate, $endDate);
		?>
		</script>
		
	</body>
</html>