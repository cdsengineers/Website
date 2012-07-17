<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("admin");
?>
<br><br>
<div id="container">
	<a class='userAction button' action='newUser' href='#' style="float:right">New User</a><div class="clear"></div>
	<!--<div class="success" id="message" style="opacity: 0;"></div>-->
	<table id="admin">
		<thead><tr>
			<th>Name</th>
			<th>Username</th>
			<th>Email</th>
			<th>Phone #</th>
			<th>Group</th>
			<th>Last Login</th>
			<th></th>
		</tr></thead>
		<tbody>
		<?php
			$query = $db->query("SELECT * FROM accounts ORDER BY account_fullname ASC");
		
			while($people = mysqli_fetch_assoc($query))
			{
				echo "<tr>";
				echo "<td>".$people['account_fullname']."</td>";
				echo "<td>".$people['account_username']."</td>";
				echo "<td>".$people['account_email']."</td>";
				echo "<td>".$people['account_phone']."</td>";
				echo "<td>".str_replace(", ", "<br>", $people['account_group'])."</td>";
				echo "<td>".date("M d", strtotime($people['account_date_accessed']))."</td>";
				if($people['account_state'])
					echo "<td><a class='userAction' action='editUser' id='".$people['account_id']."' href='#'>edit</a><br>".(($people['account_type'] != "fb")?"<a class='userAction' action='editUserPassword' id='".$people['account_id']."' href='#'>change pw</a><br>":"")."<a class='userAction' action='deleteUser' id='".$people['account_id']."' href='#'>delete</a></td>";
				else
					echo "<td><a class='activateUser' href=\"http://cas.team2134.com/activateUser?username=" . $people['account_username'] . "&token=" . $people['account_resetToken'] . "\">Confirm User</a></td>";
				echo "</tr>\n";
			}
		?>
		</tbody>
	</table>
</div>
<div id="hiddenDivs" style="display: none;">
	<div id="User">
		<form class="userForm form">
			<fieldset>
				<h3></h3>
				<input type="hidden" name="id" value="" />
				<label for"fullname">Full Name:</label><br />
				<input type="text" name="fullname" class="text" value=""/><br />
			
				<label for"username">Username:</label><br />
	            <input type="text" name="username" class="text" value="" /><br />
	
				<span id="password">
				<label for"password">Password:</label><br />
	            <input type="password" name="password" class="text" value="" /><br />
				</span>
	
				<label for"username">Email Address:</label><br />
	            <input type="text" name="email" class="text" value="" /><br />
	
				<label for"username">Phone Number:</label><br />
	            <input type="text" name="phone" id="phoneNumber" class="text" value="" /><br />
				
				<label for="group">Group:</label><br />
				<select name="group[]" multiple size="6">
				<?php echo (createUserMenu()) ?>
				</select><br />
				
	            <input type="hidden" name="action" value="" />
	            <input type="submit" id="submit" value="Edit User">
			</fieldset>
		</form>
	</div>
	
	<div id="editUserPassword" style="padding:5px;">
		<form class="editUserPasswordForm" method="post">
			<h3>Change Password</h3>
			<label for"password">New Password:</label><br />
			<input type="password" name="password" class="text" /><br />
			
			<label for"confpassword">Confirm Password:</label><br />
			<input type="password" name="confpassword" class="text" /><br />
			
			<input type="hidden" name="id" value="" />
			<input type="hidden" name="action" value="changepwUser" />
			<input type="submit" value="Change Password">
		</form>
	</div>
</div>
<script type="text/javascript">
	$("#phoneNumber").live("keyup", function()
	{
		$(this).val(formatPhone($(this).val()))
	});

	$('.userAction').live("click", function () {
		
		$.fancybox.showActivity();
		
		if($(this).attr("action") == "editUser")
		{	
			$.ajax({
				url: "/functions.php?action=getUser&id="+$(this).attr("id"),
				success: function(msg)
				{
					$("#User h3").html("Edit "+msg["account_fullname"]);
					$("#User [name='id']").val(msg["account_id"]);
					$("#User [name='fullname']").val(msg["account_fullname"]);
					$("#User [name='username']").val(msg["account_username"]);
					$("#User [name='email']").val(msg["account_email"]);
					$("#User [name='phone']").val(msg["account_phone"]);
					$("#User [name='group[]']").val(msg["account_group"].split(", "));
					$("#User [name='action']").val("editUser");
					$("#User #submit").val("Save Changes");
					
					$("#User #password").hide();
					
					$.fancybox.hideActivity();
					
					$("<a href='#User'></a>").fancybox().click();
				},
				dataType: "json"
			});
		}
		else if($(this).attr("action") == "editUserPassword")
		{
			$.fancybox.hideActivity();
			$("#editUserPassword [name='id']").val($(this).attr("id"));
			$("<a href='#editUserPassword'></a>").fancybox().click();
		}
		else if($(this).attr("action") == "deleteUser")
		{
			$.ajax({
				url: "/functions.php",
				type: "POST",
				dataType: "json",
				data: "action=deleteUser&id="+$(this).attr("id"),
				success: function(data)
				{
					reloadPage(data);
				}
			});
		}
		else if($(this).attr("action") == "newUser")
		{
			$("#User [name='id']").val("");
			$("#User [name='fullname']").val("");
			$("#User [name='username']").val("");
			$("#User [name='email']").val("");
			$("#User [name='phone']").val("");
			$("#User [name='group[]']").val("");
			
			$("#User h3").html("Add User");
			$("#User #submit").val("Add User");
			$("#User [name='action']").val("addUser");
			
			$("#User #password").show();
			$.fancybox.hideActivity();
			
			$("<a href='#User'></a>").fancybox().click();
		}
		return false; 
	});
	
	$(".userForm, .editUserPasswordForm").submit(function()
	{
		$.fancybox.showActivity();
		$.ajax({
			url: "/functions.php",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function(data)
			{
				reloadPage(data);
				$.fancybox.close();
			}
		});
		
		return false;
	});
	
	$(".activateUser").live("click", function()
	{	
		$.ajax({
			url: $(this).attr("href"),
			success: function(data)
			{
				reloadPage(data);
			},
			dataType: "jsonp"
		});
		
		return false;
	});
	
	function reloadPage(data)
	{
		$.ajax({
			url: "users",
			success: function(html)
			{
				$("#admin").html($(html).find("#admin").html());
				hudMessage(data["msg"], data["result"]);
				$.fancybox.hideActivity();
			}
		});
	}
</script>