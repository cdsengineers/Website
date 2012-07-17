<?php
	ob_start();
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect();
	
	$user = $_SESSION["user"];
		
	if(!isset($_SERVER['REMOTE_ADDR']))
	{
		$get["action"] = $argv[1];
	}
	else
	{
		$ca->protect();
	}
	
	function exception_handler($exception)
	{
		header_remove();
		header("Status: 500 Internal Server Error");
		echo($exception->getMessage());
		exit(0);
	}

	set_exception_handler("exception_handler");
	
	if($get["action"] == "logout")
	{
		$ca->logout();
	}
	else if($post['action'] == "addEvent")
	{
		$ca->protect("Officer");
		$assign = "";
		if($post['type'] == "Group")
		{
			$assign = $post['assign'];	
		}
		else if($post['type'] == "Public")
		{
			$assign = "All";	
		}
		else
		{
			$assign = $post['assign2'];	
		}
	
	  	$db->query("INSERT INTO objectives (objectives.desc, objectives.date, assign, objectives.type) VALUES ('".$post['desc']."', '".$post['date']."', '".$assign."', '".$post['type']."')");
	}
	else if($post['action'] == "editEvent")
	{
		$ca->protect("Officer");
		$assign = "";
		if($_POST['type'] == "Group")
		{
			$assign = $post['assign'];	
		}
		else if($_POST['type'] == "Public")
		{
			$assign = "All";	
		}
		else
		{
			$assign = $post['assign2'];	
		}
		
	  	$db->query("UPDATE objectives SET objectives.desc='".$post['desc']."', objectives.date='".$post["date"]."', assign='".$assign."', objectives.type='".$post['type']."' WHERE id=".$post['id']);
	}
	else if($post['action'] == "deleteEvent")
	{
		$ca->protect("Officer");
		$db->query("DELETE FROM objectives WHERE id=".$post['id']);	
	}
	else if($post['action'] == "addUser")
	{
		$ca->protect("admin");
		
		$group = ($post['group'] != "")?implode(", ", $post['group']):"";
		$db->query("INSERT INTO members (fullname, username, password, members.group, active) VALUES ('".$post['fullname']."', '".$post['username']."', '".md5($_POST['password'])."', '".$group."', 1)");
		
		$ca->updateSVNAccess();
		
		$data["result"] = "success";
		$data["msg"] = "User was Successfully Added!";
		
		echo json_encode($data);
	}
	else if($post['action'] == "deleteUser")
	{
		$ca->protect("admin");
		$db->query("DELETE FROM members WHERE id=".$post['id']);
		
		$ca->updateSVNAccess();
		
		$data["result"] = "success";
		$data["msg"] = "User was Successfully Deleted!";
		
		echo json_encode($data);
	}
	else if($post['action'] == "editUser")
	{
		$ca->protect("admin");
		
		$group = ($post['group'] != "")?implode(", ", $post['group']):"";
		$db->query("UPDATE members SET fullname='".$post['fullname']."', username='".$post['username']."', email='".$post['email']."', phone='".$post['phone']."', members.group='".$group."' WHERE id=".$post['id']);
	
		$data["result"] = "success";
		
		$ca->updateSVNAccess();
		
		$data["msg"] = "User was Successfully Modified!";
		echo json_encode($data);
	}
	else if($post['action'] == "chgsettings")
	{
		$db-select_db("cdsengineers");
		$error = "";
		
		if($post['email'] != "")
		{
			$query = "UPDATE members SET email='".$post['email']."' WHERE id=".$user['account_id'];
			$db->query($query);	
		}
		else
		{
			$error = "You cannot have a blank email address!";
		}
		
		preg_match("/^\([0-9]{3}\)\ -\ [0-9]{3}\ -\ [0-9]{4}$/g", $post['phone'], $rst);
		
		if($rst[0] != false)
		{
			$query = "UPDATE members SET phone='".$post['phone']."' WHERE id=".$user['account_id'];
			$db->query($query);	
		}
		else
		{
			$error = "Your phone number is formatted incorrectly!";
		}
		
		if($post['password'] == $post['confpassword'] && $post['password'] != "" && $user['accountType'] == "normal")
		{
			$query = "UPDATE members SET password='".md5($post['password'])."' WHERE id=".$user['account_id'];
			$db->query($query);	
		}
		else if($post['password'] != $post['confpassword'])
		{
			$error = "Your passwords do not match!";
		}
		else if($post['password'] == "")
		{
			$error = "Your cannot have a blank password!";
		}
		
		$data["result"] = ($error != "")?"error":"success";
		$data["msg"] = ($error != "")?"$error":"User was Successfully Modified!";
		echo json_encode($data);
	}
	else if($get['action'] == "getUser")
	{	
		$ca->protect("admin");
		$row = $db->query("SELECT * FROM accounts WHERE account_id=".$get["id"]);
		
		echo json_encode(mysqli_fetch_assoc($row));
	}
	else if($post['action'] == "changepwUser")
	{
		$ca->protect("admin");
		if($_POST['password'] == $_POST['confpassword']) {
			
			$db->query("UPDATE members SET password='".md5($_POST['password'])."' WHERE id=".$post['id']);	
			
			$data["result"] = "success";
			$data["msg"] = "User's password was Successfully Changed!";
		}
		else
		{
			$data["result"] = "error";
			$data["msg"] = "The passwords you entered did not match!";
		}
		
		echo json_encode($data);
	}
	else if($post['action'] == "getBuildInfo")
	{	
		$db->select_db("build_db");
		
		$query = "SELECT * FROM build_dates WHERE build_id=".$post["buildId"];
		$result = $db->query($query);
		$row = mysqli_fetch_assoc($result);
		
		$output["build_date"] = date("l, M jS", strtotime($row["build_date"]));
		$output["build_starttime"] = date("g:i a", strtotime($row["build_starttime"]));
		$output["build_endtime"] = date("g:i a", strtotime($row["build_endtime"]));
		
		$output["myStatus"] = "Not Attending";
		$output["email"] = false;
		
		$query = "SELECT * FROM build_signup JOIN cdsengineers.accounts ON build_signup.build_user_id = cdsengineers.accounts.account_id WHERE build_signup.build_id=".$post["buildId"]." ORDER BY account_fullname ASC";
		$result = $db->query($query);
		
		while($row = mysqli_fetch_assoc($result))
		{
			if($row["id"] != $user["id"])
				$output["peopleAttending"][] = $row["account_fullname"];
			else
			{
				$output["peopleAttending"][] = "Me";
				$output["email"] = (boolean)intval($row["build_enable_email"]);
				$output["myStatus"] = "Attending";
			}
		}
		
		if(count($output["peopleAttending"]) == 0 && mysqli_num_rows($result) == 1)
			$output["peopleAttending"][] = "Only You";
		else if(count($output["peopleAttending"]) == 0 && mysqli_num_rows($result) == 0)
			$output["peopleAttending"][] = "Be the first to signup!";
			
		echo json_encode($output);
		
	}
	else if($post["action"] == "changeMyBuildStatus")
	{
		$db->select_db("build_db");
		
		$query = "SELECT * FROM build_signup WHERE build_id=".$post["buildId"]." AND build_user_id=".$user['account_id'];
		$result = $db->query($query);
		$row = mysqli_fetch_assoc($result);
		
		if(mysqli_num_rows($result) == 1)
		{
			$db->query("DELETE FROM build_signup WHERE build_signup_id=".$row['build_signup_id']);
			$output = "Not Attending";
		}
		else if(mysqli_num_rows($result) == 0)
		{
			$db->query("INSERT INTO build_signup (build_id, build_user_id) VALUES (".$post["buildId"].", ".$user["account_id"].")");
			$output = "Attending";
		}
		
		echo json_encode($output);
	}
	else if($post['action'] == "edBuildEmail")
	{
		$db->select("build_db");
		
		$query = "SELECT * FROM build_signup WHERE build_id=".$post["buildId"]." AND build_user_id=".$user['account_id'];
		$result = $db->query($query);
		$row = mysqli_fetch_assoc($result);
		
		if($row["build_enable_email"])
		{
			$db->query("UPDATE build_signup SET build_enable_email=0 WHERE build_id=".$post["buildId"]." AND build_user_id=".$user['account_id']);
			$output["m"] = "Build Email Notifications Disabled";
			$output["b"] = 0;
		}
		else
		{
			$db->query("UPDATE build_signup SET build_enable_email=1 WHERE build_id=".$post["buildId"]." AND build_user_id=".$user['account_id']);
			$output["m"] = "Build Email Notifications Enabled";
			$output["b"] = 1;
		}
		
		echo json_encode($output);
	}
	else if($post["action"] == "createNewBuild")
	{
		$ca->protect("mentor");
		
		$db->select_db("build_db");
		
		if($post["date"] != "")
		{
			$startTime = strtotime($post["startTime"]);
			$endTime = strtotime($post["endTime"]);
			if($startTime < $endTime)
			{
				$db->query("INSERT INTO build_dates (build_date, build_starttime, build_endtime, build_info) VALUES ('".$post["date"]."', '".date("H:i:s", $startTime)."', '".date("H:i:s", $endTime)."', '".$post["buildInfo"]."')");
				
				$result = $db->query("SELECT * FROM build_dates ORDER BY build_id DESC LIMIT 1");
				$row = mysqli_fetch_assoc($result);
				
				$db->query("INSERT INTO local_to_google_action (local_id, google_id, action_type) VALUES ('".$row["build_id"]."', '', 'add')");
				
				$row["build_date"] = intval(date("j", strtotime($row["build_date"])));
				$row["build_starttime"] = intval(date("G", strtotime($row["build_starttime"])));
				$row["build_endtime"] = intval(date("G", strtotime($row["build_endtime"])));
				
				echo json_encode($row);
			}
			else
				throw new Exception("Uh oh! The build ends before it begins!");
		}
		else
			throw new Exception("Please choose a date for the build!");
	}
	else if($post["action"] == "deleteBuild")
	{
		$ca->protect("mentor");
		
		$db->select_db("build_db");
		
		$query = "SELECT * FROM build_dates WHERE build_id=".$post["buildId"];
		$result = $db->query($query);
		$row = mysqli_fetch_assoc($result);
		$db->query("INSERT INTO local_to_google_action (local_id, google_id, action_type) VALUES ('".$row["build_id"]."', '".$row["google_id"]."', 'delete')");
		
		$db->query("DELETE FROM build_dates WHERE build_id = ".$post["buildId"]);
		$db->query("DELETE FROM build_signup WHERE build_id = ".$post["buildId"]);
	}
	else if($post["action"] == "uploadFile")
	{
		$ca->protect("admin");
		
		$db->select_db("cdsengineers");
		
		$badChars = array("-", "|", "%", "@", "!", "$", "#", "%", "^", "&", "*", " ", "\\", "/", ",", ":", ";", "'", "\"", "<", ">", "{", "}", "[", "]", "+", "_");
		$filename = str_replace($badChars, "", strtolower($_FILES['file']['name']));
		
		if(!file_exists('../resources/'. $filename))
		{
			if (move_uploaded_file($_FILES['file']['tmp_name'], '../resources/'. $filename))
			{
			    $output["status"] = true;
				$desc = $post["desc"];
				$db->query("INSERT INTO resources (fileName, fileDescription, fileLocation) VALUES ('$filename', '$desc', 'http://resources.team2134.com/$filename')");
			}
			else
			{
				var_dump($_FILES);
			    $output["status"] = false;
				$output["msg"] = "File error, please try again later.";
			}
		}
		else
		{
			$output["status"] = false;
			$output["msg"] = "The file you selected already exits!";
		}
		
		echo json_encode($output);
	}
	else if($get["action"] == "deleteFile")
	{
		$ca->protect("admin");
		
		$db->select_db("cdsengineers");
		
		$row = mysqli_fetch_assoc($db->query("SELECT fileName FROM resources WHERE id=".$get["id"]));
		$output["status"] = unlink("../resources/".$row["fileName"]);
		
		$db->query("DELETE FROM resources WHERE id=".$get["id"]);
		
		$output["msg"] = "File Deleted!";
		
		echo json_encode($output);
	}
	else if($get["action"] == "getObjectiveComments")
	{
		$db->select_db("cdsengineers");
		
		$query = $db->query("SELECT * FROM  obj_discuss WHERE obj_id=".$get["threadId"]);
		
		while($row = mysqli_fetch_assoc($query))
		{
			$owner = mysqli_fetch_assoc($db->query("SELECT * FROM accounts WHERE account_id = ".$row["owner"]));
			
			$comment["id"] = $row["id"];
			$comment["author"] = $owner["account_fullname"];
			$comment["content"] = $row["comment"];
			$comment["pubdate"] = date("F j \a\\t g:i a", strtotime($row["date"]));
			
			$comments[] = $comment;
		}
		
		echo json_encode($comments);
	}
	else if($get["action"] == "saveObjectiveComment")
	{
		$db->select_db("cdsengineers");
		
		$db->query("INSERT INTO obj_discuss (owner, obj_id, comment) VALUES (".$user["account_id"].", ".$get["threadId"].", '".$get["comment"]."')");
	}
	else if($get["action"] == "deleteObjectiveComment")
	{
		$db->select_db("cdsengineers");
		
		$db->query("DELETE FROM obj_discuss WHERE id=".$get["commentId"]);
	}
	else if($get["action"] == "saveSVNPassword")
	{	
		$error = "Your password cannot be nothing";
		if($get["password"] != "")
		{
			$db->query("UPDATE members SET svnPassword='".$get["password"]."' WHERE id=".$user["id"]);
			$error = "";
		}
		
		$data["result"] = ($error == "")?"success":"error";
		$success = ($user["svnPassword"] != "")?"Password Successfully Reset!":"Password was Successfully Saved!";
		
		$data["msg"] = ($error == "")?$success:$error;

		$ca->updateSVNAccess();

		echo $_GET['callback'] . "(".json_encode($data).");";
	}
	else if($get["action"] == "sendNotification")
	{
		$ca->protect("admin");
		
		sendPushNotification("message", array(103));
	}
	else if($get["action"] == "googleCalendarCallback")
	{	
		require_once('googleOAuthApi/apiClient.php');
		require_once 'googleOAuthApi/contrib/apiCalendarService.php';

		$client = new apiClient();
		$client->setApplicationName("Team 2134 Calendar App");

		// Visit https://code.google.com/apis/console?api=calendar to generate your
		// client id, client secret, and to register your redirect uri.
		$client->setClientId('922269743018.apps.googleusercontent.com');
		$client->setClientSecret('wlBfhZcm-smGIwTEptHjwu_h');
		$client->setRedirectUri('http://members.team2134.com/functions/googleCalendarCallback');
		$client->setDeveloperKey('AIzaSyAxtNGP99ltpWHNWtMiEy22Fw-W6srBCI4');
		$client->setApprovalPrompt("force");
		
		$cal = new apiCalendarService($client);
		
		if($_GET["code"] == "" && $_SESSION["googleAuthToken"] == "" && $_SESSION["googleCalendarAccessToken"] == "")
			header("Location: ".$client->createAuthUrl());
		
		if($_GET["code"] != "" && $_SESSION["googleAuthToken"] == "")
	    { 
			$client->authenticate();
		
			$refreshToken = json_decode($client->getAccessToken())->refresh_token;
			$accessToken = json_decode($client->getAccessToken())->access_token;
			
			$_SESSION["googleCalendarAccessToken"] = $refreshToken;
			$_SESSION["googleAuthToken"] = $accessToken;
					
			header("Location: googleCalendarCallback");
	    }
	
		try
		{ 
			$client->setAccessToken($_SESSION["googleAuthToken"]);
			$googlMmonthEvents = $cal->events->listEvents("cdsengineers@team2134.com");
			var_dump($_SESSION["googleCalendarAccessToken"]);
			$googleStatus = true;
		}
		catch(apiAuthException $e)
		{	
			if($_SESSION["googleCalendarAccessToken"] != "")
			{
				$client->refreshToken($_SESSION["googleCalendarAccessToken"]);
				$_SESSION["googleAuthToken"] = $client->getAccessToken();
			}
			else
			{
				echo "Google Auth Fail";
				die(0);
			}
		}
		
	}
	else if($get["action"] == "syncBuildCalendarWithGoogle")
	{	
		syncBuildCalendarWithGoogle();
	}
	else if($get["action"] == "test")
	{
		preg_match("/^\([0-9]{3}\)\s-\s[0-9]{3}\s-\s[0-9]{4}$/", $get['phone'], $rst);
		var_dump($rst);
	}
?>