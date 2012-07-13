<link rel="stylesheet" href="http://members.team2134.com/style.css" type="text/css" charset="utf-8" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://team2134.com/jslibrary.inc?js&jqui" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="http://members.team2134.com/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="http://members.team2134.com/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<div id="top">
	<div id="top-inner">
		<a href="/"><?php echo $user['account_fullname']; ?></a> | 
		<a href='#change-pw' class="fancybox">settings</a>
		
		<div id="admin-function-links">
			<?php if($ca->isLevel("Programming")) { ?><a href="http://docs.team2134.com">docs</a> | <?php } ?>
			<a href="http://members.team2134.com/schedule">schedule</a> | 
			<a href="http://members.team2134.com/bulletin">bulletin</a> | 
			<a href="http://cas.team2134.com/?app=wiki">wiki</a>
			<?php if($ca->isLevel("Officer")) { ?> | <a href="http://members.team2134.com/admin/">admin</a><?php } ?> | 
			<a href="http://members.team2134.com/functions/logout">logout</a>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<div id="hiddenDivs" style="display:none;">
		<div id="change-pw">
			<form id="accountSettings" style="padding: 5px;">
				<h3>Account Settings</h3>
				<label for"password">Email Address:</label><br />
				<input type="text" name="email" value="<?= $user['email'] ?>" class="text" /><br />
				<label for"password">Phone Number:</label><br />
				<input type="text" name="phone" id="userPhoneNumber" value="<?= $user['phone'] ?>" class="text" /><br />
				<?php if($user['account_type'] == "normal") {?><label for"password">New Password:</label><br />
				<input type="password" name="password" class="text" /><br />
				<label for"confpassword">Confirm Password:</label><br />
				<input type="password" name="confpassword" class="text" /><br /><?php } ?>
				<input type="hidden" name="id" value="<?= $user['id'] ?>" />
				<input type="hidden" name="action" value="chgsettings" />
				<input type="submit" value="Save Settings">
			</form>
		</div>
	</div>
</div>
<script>
function formatPhone(phonenum) {
    var regexObj = /^\(?([0-9]{0,3})\)?\s?-?\s?([0-9]{0,3})?\s?-?\s?([0-9]{0,4})?$/;
    if (regexObj.test(phonenum)) {
        var parts = phonenum.match(regexObj);
        var phone = parts[1];
        if (parts[1] && parts[2]) { phone = "(" + parts[1] + ") " + parts[2]; }
		if (parts[3]) { phone += " - " + parts[3]; }
        return phone;
    }
    else {
        //invalid phone number
        return null;
    }
}
	$("#accountSettings").submit(function()
	{
		loadingMessage("Saving Account Settings");
		$.ajax({  
			type: "POST",
			url: "/functions.php",
			data: $(this).serialize(),
			success: function(data)
			{
				loadingMessage();
				hudMessage(data["msg"], data["result"]);
				
				if(data["result"] == "success")
					$.fancybox.close();
			},
			dataType: "json"
		});  
		return false;
	})

	$("#userPhoneNumber").live("keydown", function(event)
	{
		result = formatPhone($(this).val());
		console.log(result);
		
		if(result != null)
		{
			$(this).val(result);
		}
		else if($(this).val() != "")
		{
			event.preventDefault();
		}
	});
</script>