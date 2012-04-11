<html>
	<head>
		<title>Test Page</title>
		
		<style>
			#content {
				margin: 0px auto;
				width: 800px;
			}
			
			#header {
				width: 500px;
				height: 100px;
				
				font-size: 60px;
				line-height: 60px;
				
				color: black;
				-webkit-mask-image: -webkit-gradient(linear, left top, left bottom, from(rgba(0,0,0,.5)), to(rgba(0,0,0,1)));
			}
			
			#navBar {
				margin-top: 10px;
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#4B4B4B), color-stop(100%,#181818));
				height: 28px;
			}
			
			#navBar .menuItem {
				color: white;
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#4B4B4B), color-stop(100%,#181818));
				float: left;
				padding: 5px 23px;
				text-decoration: none;
			}
			
			#navBar .menuItem:hover {
				background: #181818;
			}
			
			#navBar .menuItem:active, #navBar .menuItem.active{
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#181818), color-stop(100%,#4B4B4B));
			}
			
			#navBar .menuItem {
				border-right: 1px white solid;
			}
		</style>
	</head>

	<body>
		<div id="content">
			<div id="header">FRC TEAM 2134</div>
			<div id="navBar">
				<a class="menuItem <?php echo ($_GET['page'] == "")?"active":""?>" href="/">Home</a>
				<a class="menuItem <?php echo ($_GET['page'] == "agenda")?"active":""?>" href="/agenda">Agenda</a>
				<a class="menuItem <?php echo ($_GET['page'] == "blog")?"active":""?>" href="/blog">Blog</a>
				<a class="menuItem <?php echo ($_GET['page'] == "media")?"active":""?>" href="/media">Media</a>
				<a class="menuItem <?php echo ($_GET['page'] == "members")?"active":""?>" href="/members">Members</a>
				<a class="menuItem <?php echo ($_GET['page'] == "sponsors")?"active":""?>" href="/sponsors">Sponsors</a>
				<a class="menuItem <?php echo ($_GET['page'] == "wise")?"active":""?>" href="/wise">WISE</a>
			</div>
		</div>
	</body>
</html>
