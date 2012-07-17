<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("Officer");
?>
<br><br>
<div id="container">
	<a href="#new-event" style="float:right"  class="fancybox button">New Event</a><div class="clear"></div>
	<div class="success" id="success-message" style="display: none;"></div>
	<table id="admin">
		<thead><tr>
			<th>Description</th>
			<th>Date</th>
			<th>Type</th>
			<th>Assigned To</th>
			<th></th>
		</tr></thead>
		<tbody>
			<?php
				$query = $db->query("SELECT * FROM objectives ORDER BY objectives.date DESC");
			
				while ($events = mysqli_fetch_assoc($query))
				{
					echo "<tr>";
					echo "<td>".$events['desc']."</td>";
					echo "<td>".$events['date']."</td>";
					echo "<td>".$events['type']."</td>";
					if($events['type'] == "Group") {
						echo "<td>".$events['assign']."</td>";
					} elseif ($events['type'] == "Person") {
						$query2 = $db->query("SELECT * FROM accounts WHERE account_id=".$events['assign']);
						$person = mysqli_fetch_assoc($query2);
						echo "<td>".$person['account_fullname']."</td>";
					} else {
						echo "<td>".$events['assign']."</td>";
					}
					echo "<td><a class='fancybox' href='#edit-event-".$events['id']."'>edit</a> <a href='#delete-event-".$events['id']."' class='fancybox'>delete</a></td>";
					echo "</tr>\n";
				}
			?>
		</tbody>
	</table>
</div>
<div id="hiddenShit" style="display: none;">
	<div id="new-event">
	    <form id="calendarEventsFormAdd" method="post" class="form">
	        <fieldset>
	        	<h3>Add Event</h3>
	        	<label for="desc">Event Description:</label><br />
	        	<input id="desc" name="desc" size="20" type="text" class="text"><br/>

	            <label for="date">Event Date:</label><br />
	        	<input name="date" size="20" type="text" class="text date"><br/>

	            <label for="type">Type:</label>
	            <select id="type" name="type" class="event-type"><option value="Public">Public</option><option value="Group">Group</option><option value="Person">Person</option></select><br/>

	            <label for="assign" id="assingedTo" style="display:none;">Who is it assigned to:</label>
	            <select id="groupType" name="assign" style="display:none;"><?= createUserMenu() ?></select>


	            <select id="peopleType" style="display:none;" name="assign2">
	            	<?php
					$query = $db->query("SELECT * FROM accounts ORDER BY account_fullname ASC");
					while($people = mysqli_fetch_assoc($query)) { ?>
	                	<option value="<?php echo $people['account_id']; ?>"><?php echo $people['account_fullname']; ?></option>
	                <?php } ?>
	            </select><br/>

	            <input type="hidden" name="action" value="addEvent" />
	            <input type="submit" value="Add Event">
	        </fieldset>
	    </form>
	</div>
	<?php
	$query = $db->query("SELECT * FROM objectives ORDER BY objectives.date ASC");
	while ($event = mysqli_fetch_assoc($query)) {
		echo '<div id="edit-event-'.$event['id'].'">';
		$desc = $event['desc'];
		$date = $event['date'];
		$id = $event['id'];
		$type_output = '<option value="Public"'.(($event['type'] == "Public") ? " selected='selected'" : "" ).'>Public</option><option value="Group"'.(($event['type'] == "Group") ? " selected='selected'" : "").'>Group</option><option value="Person"'.(($event['type'] == "Person") ? " selected='selected'" : "" ).'>Person</option></select>';
		$group_output = createUserMenu($event['assign']);
		$group_hidden = ($event['type'] == "Group" ? "" : ' style="display:none;"');
		$people_hidden = ($event['type'] == "Person" ? "" : ' style="display:none;"');
		$assign_label = (($event['type'] == "Person" || $event['type'] == "Group") ? "" : ' style="display:none;"');
		$eof = <<<EOF
		    <form class="calendarEventsFormEdit form" method="post">
		        <fieldset>
					<h3>Edit Event "$desc"</h3>
					<input type="hidden" name="id" value="$id" />
		        	<label for="desc">Event Description:</label><br />
		        	<input id="desc" name="desc" size="20" type="text" class="text" value="$desc"><br/>

		            <label for="date">Event Date:</label><br />
		        	<input name="date" size="20" type="text" class="text date" value="$date"><br/>

		            <label for="type">Type:</label>
		            <select id="type" name="type" class="event-type">$type_output<br/>

		            <label for="assign" id="assingedTo"$assign_label>Who is it assigned to:</label>
		            <select id="groupType" name="assign"$group_hidden>$group_output</select>


		            <select id="peopleType"$people_hidden name="assign2">
EOF;
		$query2 = $db->query("SELECT * FROM accounts ORDER BY account_fullname ASC");
		while($people = mysqli_fetch_assoc($query2)) {
			// $eof .= "<option value='ddddd'>ads</option>";
			$eof .= '<option value="'.$people['account_id'].'"'.($event['assign'] == $people['id'] ? " selected='selected'" : "").'>'.$people['account_fullname'].'</option>';
			// '.($event['assign'] == $people['id'] ? " selected='selected'" : "").'
		}
		$eof .= '</select><br/>';
		$eof .= <<<EOF

		            <input type="hidden" name="action" value="editEvent" />
		            <input type="submit" value="Edit Event">
		        </fieldset>
		    </form>
EOF;
		echo $eof;
		echo '</div>';
		echo '<div id="delete-event-'.$event['id'].'" style="padding:5px;">';
		echo '<form class="calendarEventsFormDelete" method="post">';
		echo '<h3>Delete Event</h3>';
		echo '<input type="hidden" name="id" value="'.$event['id'].'" />';
		echo '<input type="hidden" name="action" value="deleteEvent" />';
		echo '</form>';
		echo 'Are you sure you want to delete this event? <a href="#" class="button delete-event">Yes</a> or <a href="javascript:$.fancybox.close();" class="button">Cancel</a>';
		echo '</div>';
	} ?>
</div>
<script type="text/javascript">
$(function () {
	$('.fancybox').fancybox();
	$(".date").datepicker({ dateFormat: 'yy-mm-dd' });
	$('.event-type').live('change', function () {
		if($(this).val() =='Group') {
			$('#assingedTo', $(this).parent()).show(1000);
			$('#groupType', $(this).parent()).show(1000);
			$('#peopleType', $(this).parent()).hide(1000);
		} else if($(this).val() =='Person') {
			$('#assingedTo', $(this).parent()).show(1000);
			$('#groupType', $(this).parent()).hide(1000);
			$('#peopleType', $(this).parent()).show(1000);
		} else {
			$('#assingedTo', $(this).parent()).hide(1000);
			$('#groupType', $(this).parent()).hide(1000);
			$('#peopleType', $(this).parent()).hide(1000);
		}
	});
	$('.delete-event').live('click', function () {
		$("form", $(this).parent()).submit();
		return false;
	});
	$("#calendarEventsFormAdd").submit(function() { 
		$.ajax({  
			type: "POST",
			url: "/functions.php",
			data: $(this).serialize(),
			success: function(){
				$.fancybox.close();
				$('#success-message').text("Event has been added");
				$('#success-message').slideDown();
				setTimeout("$('#success-message').slideUp();window.location.reload();", 5000);
			}  
		});  
		return false;  
	});
	$('.calendarEventsFormDelete').live('submit', function () {
		$.ajax({  
			type: "POST",
			url: "/functions.php",
			data: $(this).serialize(),
			success: function()
			{
				$.fancybox.close();
				$('#success-message').text("Event has been deleted");
				$('#success-message').slideDown();
				setTimeout("$('#success-message').slideUp();window.location.reload();", 5000);  
			}  
		});  
		return false;  
	});
	$('.calendarEventsFormEdit').live('submit', function () {
		$.ajax({
			type: "POST",
			url: "/functions.php",
			data: $(this).serialize(),
			success: function()
			{
				$.fancybox.close();
				$('#success-message').text("Event has been edited");
				$('#success-message').slideDown();
				setTimeout("$('#success-message').slideUp();window.location.reload();", 5000);  
			}
		});
		return false;
	});
});
</script>