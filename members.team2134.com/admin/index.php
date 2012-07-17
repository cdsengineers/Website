<?php
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("Officer");
	
	$user = $_SESSION["user"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Pages Admin</title>
	</head>
	<body>
		<?php require_once('../header.php'); ?>
		<div id="header"><h1>Admin Center</h1></div>
		<div id="container">
			<?php if($ca->isLevel("admin")) { ?><a href="users" class="button">Manage Users</a>&nbsp; &nbsp;<?php } ?>
			<?php if($ca->isLevel("admin")) { ?><a href="pages" class="button">Manage Pages</a>&nbsp; &nbsp;<?php } ?>
			<a href="calendar" class="button">Manage Calendar</a>&nbsp; &nbsp;
			<?php if($ca->isLevel("admin")) { ?><a href="links" class="button">Manage Links</a>&nbsp; &nbsp;<?php } ?>
			<a href="google" class="button">Manage Content</a>
		</div>
		<script type="text/javascript">
		$(function () {
			$('.fancybox').fancybox();
		});
		</script>
		
		<?php
			$page = ($get['page'])?$get['page']:(($ca->isLevel("admin"))?"users":"calendar");
			include_once($page.".php");
		?>
	</body>
</html>