<html>
<head>
	<title>FRC Tutorials</title>
	<link rel="stylesheet" href="/style.css" type="text/css">
	<script src="http://team2134.com/jslibrary.inc?js&jq"></script>
	
	<script src="http://members.team2134.com/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" href="http://members.team2134.com/fancybox/jquery.fancybox-1.3.4.css" type="text/css"/>
	
</head>
<body>
	<div id="container">
		<a class="button" style="float:right; line-height: 18px; display: none" id="back" href="back">Back</a>
		
		<div id="start">
			<h2>Select a Tutorial</h2>
			<a class="button" href="netbeans">Install Netbeans</a>
			<a class="button" href="eclipse">Install Eclipse</a>
		</div>
		
		<div class="tutorial" id="eclipse">
			<h2>Select an Operating System</h2>
			<a class="button" href="mac">Mac</a>
			
			<div class="tutorial" id="mac">
				
				Step 1: <a href="MacEclipse.tar.gz">Download Eclipse for Mac</a><br>
				Step 2: Decompress file and install eclipse<br>
				Step 3: Choose a workspace<br><br>
				<a class="fancybox" href="images/eclipse/mac/step3.png">
					<img src="images/eclipse/mac/step3.png" height="200">
				</a><br><br>
				Step 4: Click on "help" then click "install new software"<br><br>
				<a class="fancybox" href="images/eclipse/mac/step4.png">
					<img src="images/eclipse/mac/step4.png" height="200">
				</a><br><br>
				Step 5: Enter "http://first.wpi.edu/FRC/java/eclipse/update/stable/" for the "Work with" field. Then click next.<br><br>
				<a class="fancybox" href="images/eclipse/mac/step5.png">
					<img src="images/eclipse/mac/step5.png" height="200">
				</a><br><br>
				Step 6: Follow the instructions<br><br>
				<a class="fancybox" rel="eclipse_mac_step6" href="images/eclipse/mac/step6-a.png">
					<img src="images/eclipse/mac/step6-a.png" height="200">
				</a>
				<a class="fancybox" rel="eclipse_mac_step6" href="images/eclipse/mac/step6-b.png">
					<img src="images/eclipse/mac/step6-b.png" height="200">
				</a>
				<a class="fancybox" rel="eclipse_mac_step6" href="images/eclipse/mac/step6-c.png">
					<img src="images/eclipse/mac/step6-c.png" height="200">
				</a><br><br>
				Step 7: Click "OK" when asked about certificates<br><br>
				<a class="fancybox" href="images/eclipse/mac/step7.png">
					<img src="images/eclipse/mac/step7.png" height="200">
				</a><br><br>
				Step 8: Restart eclipse after install is done<br><br>
				<a class="fancybox" href="images/eclipse/mac/step8.png">
					<img src="images/eclipse/mac/step8.png" height="200">
				</a><br><br>
				Step 9: Click on "File", then "New", then "Project"<br><br>
				<a class="fancybox" href="images/eclipse/mac/step9.png">
					<img src="images/eclipse/mac/step9.png" height="200">
				</a><br><br>
				Step 10: Open the "FRC Robot Projects" tab and select either Iterative or Simple.<br><br>
				<a class="fancybox" href="images/eclipse/mac/step10.png">
					<img src="images/eclipse/mac/step10.png" height="200">
				</a><br><br>
				Step 11: Give the project a name, click next. Change "org.usfirst.frc0" to "org.usfirst.frc2134" then click "Finish"<br><br>
				<a class="fancybox" rel="eclipse_mac_step11" href="images/eclipse/mac/step11-a.png">
					<img src="images/eclipse/mac/step11-a.png" height="200">
				</a>
				<a class="fancybox" rel="eclipse_mac_step11" href="images/eclipse/mac/step11-b.png">
					<img src="images/eclipse/mac/step11-b.png" height="200">
				</a><br><br>
				Step 12: Click on "Eclipse" in the menubar, and select Preferences. Click on the FRC JavaDev tab, and enter 2134 for the team number<br>Then click "OK"<br><br>
				<a class="fancybox" href="images/eclipse/mac/step12.png">
					<img src="images/eclipse/mac/step12.png" height="200">
				</a><br><br>
				Congrats. You are Done!<br><br>
				<a class="fancybox" href="images/eclipse/mac/step13.png">
					<img src="images/eclipse/mac/step13.png" height="200">
				</a><br><br>
			</div>
			
		</div>
		
		<div class="tutorial" id="netbeans">
			
			<h2>Select an Operating System</h2>
			<a class="button" onclick="$('#netbeans > #mac > a:first').attr('href', 'MacNetBeans-7.0.1.dmg'); $('#netbeans > #mac > a:first').text('Download NetBeans for Mac'); $('#netbeans > #mac > div:first').text('');" href="mac">Mac</a>
			<a class="button" onclick="$('#netbeans > #mac > a:first').attr('href', 'PcNetBeans-7.0.1.exe'); $('#netbeans > #mac > a:first').text('Download NetBeans for Pc'); $('#netbeans > #mac > div:first').text('Note: Images are the same for windows.');" href="mac">Pc</a>
			
			<div class="tutorial" id="mac">
				<br>
				<div></div>
				<br>
				Step 1: <a href="MacNetBeans-7.0.1.dmg">Download NetBeans for Mac</a><br>
				Step 2: Install NetBeans and Install JUnit<br><br>
				<a class="fancybox" href="images/netbeans/mac/step2.png">
					<img src="images/netbeans/mac/step2.png" height="200">
				</a><br><br>
				Step 3: Make sure all updates are installed<br><br>
				<a class="fancybox" rel="netbeans_mac_step3" href="images/netbeans/mac/step3-a.png">
					<img src="images/netbeans/mac/step3-a.png" height="200">
				</a>
				<a class="fancybox" rel="netbeans_mac_step3" href="images/netbeans/mac/step3-b.png">
					<img src="images/netbeans/mac/step3-b.png" height="200">
				</a>
				<a class="fancybox" rel="netbeans_mac_step3" href="images/netbeans/mac/step3-c.png">
					<img src="images/netbeans/mac/step3-c.png" height="200">
				</a><br><br>
				Step 4: Restart after all updates are done<br><br>
				<a class="fancybox" href="images/netbeans/mac/step3-d.png">
					<img src="images/netbeans/mac/step3-d.png" height="200">
				</a>
				<br><br>
				Step 5: Click on "Install Plugins"<br><br>
				<a class="fancybox" href="images/netbeans/mac/step5.png">
					<img src="images/netbeans/mac/step5.png" height="200">
				</a>
				<br><br>
				Step 6: Click on "Settings"<br><br>
				<a class="fancybox" href="images/netbeans/mac/step6.png">
					<img src="images/netbeans/mac/step6.png" height="200">
				</a>
				<br><br>
				Step 7: Click "Add" and enter "FRC" for the name and<br>
				"http://first.wpi.edu/FRC/java/netbeans/update/updates.xml" for the url<br><br>
				<a class="fancybox" href="images/netbeans/mac/step7.png">
					<img src="images/netbeans/mac/step7.png" height="200">
				</a>
				<br><br>
				Step 8: Click on "Available Plugins" and then click "Reload Catalog"<br>
				Step 9: In the search box type "FRC" and check all of the options<br><br>
				<a class="fancybox" href="images/netbeans/mac/step9.png">
					<img src="images/netbeans/mac/step9.png" height="200">
				</a>
				<br><br>
				Step 10: Click "Install" and follow the instructions<br><br>
				<a class="fancybox" rel="netbeans_mac_step10" href="images/netbeans/mac/step10-a.png">
					<img src="images/netbeans/mac/step10-a.png" height="200">
				</a>
				<a class="fancybox" rel="netbeans_mac_step10" href="images/netbeans/mac/step10-b.png">
					<img src="images/netbeans/mac/step10-b.png" height="200">
				</a>
				<a class="fancybox" rel="netbeans_mac_step10" href="images/netbeans/mac/step10-c.png">
					<img src="images/netbeans/mac/step10-c.png" height="200">
				</a>
				<br><br>
				Step 11: Go to File, and click "New Project" then select "Simple Robot Template" then click "OK"<br>
				(You should now have a code window open)<br><br>
				Step 12: Go to "Netbeans" in the menubar and click Preferences. Click the Miscellaneous Tab. Then click FRC Configuration.<br>
				Enter "2134" for the team number then click "OK"<br><br>
				<a class="fancybox" href="images/netbeans/mac/step12.png">
					<img src="images/netbeans/mac/step12.png" height="200">
				</a>
				<br><br>
				Congrats! You got netbeans to work!
			</div>
		</div>
	</div>
	
	<script>
	
		$('.fancybox').fancybox();
	
		$(".button:not(#back)").on("click", function()
		{
			if($("#start").is(":visible"))
			{
				$("#start").slideUp();
				$("#back").fadeIn();
				$("#"+$(this).attr("href")).slideDown();
			}
			else
			{
				$("#"+$(this).parent().attr("id")+" > #"+$(this).attr("href")).slideDown();
			}
			return false;
		});
		
		$("#back").on("click", function()
		{
			$(".tutorial").slideUp();
			$("#start").slideDown();
			$("#back").fadeOut();
			return false;
		});
	</script>
</body>
</html>