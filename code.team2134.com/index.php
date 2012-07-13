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
	$github->authenticate("cdsengineers", $GITHUB_PASSWORD, Github_Client::AUTH_HTTP_PASSWORD);
?>
<html>
	<head>
		<title>CDS Engineers</title>
		
		<link rel="stylesheet" href="styles.css" type="text/css">
	</head>
	
	<body>
		<?php require_once('../members.team2134.com/header.php'); ?>
		
		<div id="header"><h1>GitHub Interface</h1></div>
		<div id="container">
			<div id="github">
			<?php
				//$user = $github->getUserApi()->show('cdsengineers');
				//echo "User Info <br>";
				//print "<pre>".print_r($user, true)."</pre>";
				
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
					foreach($branches as $branch)
					{
						$row["repo_name"] = $repo["name"];
						$row["branch_name"] = $branch["name"];
						$row["branch_id"] = $branchId;
						$robotBranches[] = $row;
						echo "<span id='{$branch[name]}_branch' branchName='$branch[name]' repoName='$repo[name]' class='branch_tab'>$branch[name]</span>";
					}
				}
				echo "</div>";
				
				echo "<div id='commits'>";
				foreach($robotBranches as $branch)
				{
					$commits = $github->getCommitApi()->getBranchCommits('cdsengineers', $branch["repo_name"], $branch["branch_name"]);
					
					echo "<div id='$branch[repo_name]_$branch[branch_name]' class='branch $branch[repo_name] $branch[branch_name]'>";
					echo count($commits);
					exit();
					foreach($commits as $key => $commit)
					{
						if($key == "commit");
						{
							/*echo "<div id='$commit[sha]' class='commit'>
								{$commit[commit][committer][name]}<br>
								{$commit[commit][message]}<br>
								".date("F j, Y \a\\t g:ia", strtotime($commit["commit"]["committer"]["date"]))."
								
								<div class='deploy' commitId='$commit[sha]'>Deploy</div>
							</div>";*/
							printPretty($commit);
						}
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