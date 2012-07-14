<?php
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("admin");
	
	function get_info($cmd)
	{
		$data = file_get_contents("https://api.dreamhost.com/?key=72LEJEEGZ5382VAX&cmd=$cmd&format=json");
		$data = json_decode($data);
		$data = $data->data;
		return $data;
	}
?>

<html>
	<head>
		<title>team2134.com admin</title>
	</head>
	
	<body>
		
		<?php require_once('../members.team2134.com/header.php'); ?>
		
		<div id="header"><h1>Site Admin</h1></div>
		<div id="container">
		
		<?php
		
			$sites = get_info("account-domain_usage");
			foreach($sites as $site)
			{
				if(strpos($site->domain, "team2134.com") !== false)
					printPretty($site->domain);
			}
		?>
		
		</div>
	</body>
</html>