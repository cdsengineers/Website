<?php 	
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("Programming");

	require_once 'Github/Autoloader.php';
	Github_Autoloader::register();
	$github = new Github_Client();
	
	$user = $_SESSION["user"];
	
	//Login
	$github->authenticate("admin@team2134.com", $GITHUB_PASSWORD, Github_Client::AUTH_HTTP_PASSWORD);
?>
<html>
	<head>
		<title>CDS Engineers</title>
		
		<link rel="stylesheet" href="styles.css" type="text/css">
	</head>
	
	<body>
		<?php require_once('/home/alampiss/team2134.com/members/header.php'); ?>
		
		<div id="header"><h1>GitHub Interface</h1></div>
		<div id="container">
			<div id="github">
			<?php
				//$user = $github->getUserApi()->show('cdsengineers');
				//echo "User Info <br>";
				//print "<pre>".print_r($user, true)."</pre>";

				var_dump($github->getUserApi());
				exit();
				
				$repos = $github->getRepoApi()->getUserRepos('cdsengineers');
				$robotRepos;
				
				echo "<div id='repos'>";
				foreach($repos as $repo)
				{
					if(strpos($repo["name"], "code") ==! false)
					{
						$robotRepos[] = $repo;
						echo "<span id='$repo[name]_tab' class='repo_tab'>$repo[name]</span>";
					}
				}
				echo "</div>";
				
				
				echo "<div id='branches'>";
				$robotBranches;
				foreach($robotRepos as $repo)
				{
					$branches = $github->getRepoApi()->getRepoBranches('cdsengineers', $repo["name"]);
					foreach($branches as $branchName => $branchId)
					{	
						$row["repo_name"] = $repo["name"];
						$row["branch_name"] = $branchName;
						$row["branch_id"] = $branchId;
						$robotBranches[] = $row;
						echo "<span id='{$branchName}_branch' branchName='$branchName' repoName='$repo[name]' class='branch_tab'>$branchName</span>";
					}
				}
				echo "</div>";
				
				echo "<div id='commits'>";
				foreach($robotBranches as $branch)
				{
					$commits = $github->getCommitApi()->getBranchCommits('cdsengineers', $branch["repo_name"], $branch["branch_name"]);
					echo "<div id='$branch[repo_name]_$branch[branch_name]' class='branch $branch[repo_name] $branch[branch_name]'>";
					foreach($commits as $commit)
					{
						echo "<div id='$commit[id]' class='commit'>
								{$commit[committer][name]}<br>
								$commit[message]<br>
								".date("F j, Y \a\\t g:ia", strtotime($commit["committed_date"]))."
								
								<div class='deploy' commitId='$commit[id]'>Deploy</div>
							</div>";
					}
					echo "</div>";
				}
				echo "</div>";
			?>
			</div>	
		</div>
		
		<script>
			$(".repo_tab:first-of-type, .branch_tab:first-of-type").addClass("selected");
			
			$(".branch_tab").die().live("click", function() {
				branch = $(this).attr("branchName");
				$(".branch").hide();
				$(".branch."+branch).show();
				
				$(".branch_tab").removeClass("selected");
				$(this).addClass("selected");
			})
		</script>
	</body>
</html>