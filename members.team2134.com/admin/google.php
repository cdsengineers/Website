	<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");
	
	$ca->protect("Officer");
	
	//Load the gdata interface for pulling from google services
	require_once 'Zend/Loader.php';
	Zend_Loader :: loadClass('Zend_Gdata');
	Zend_Loader :: loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader :: loadClass('Zend_Gdata_Photos');
	Zend_Loader :: loadClass('Zend_Http_Client');
	
	//Login with google account username and password to pull data as user
	$svc = Zend_Gdata_Photos :: AUTH_SERVICE_NAME;
	$user = "cdsengineers";
	$pass = "cdsrobotics0";
	$client = Zend_Gdata_ClientLogin :: getHttpClient($user, $pass, $svc);
	$gphoto = new Zend_Gdata_Photos($client);
	
?>
<style>
</style>
<div id="container">
	<br><br>
	<a href="#upload" style="float:right" class="button fancybox">Upload Content</a><div class="clear"></div>
	<table id="admin">
		<tr>
			<th>File Name</th>
			<th>Description</th>
			<th>Location</th>
			<th>Upload Date</th>
			<th></th>
		</tr>
<?php
	$query = $db->query("SELECT * FROM resources ORDER BY fileUploadDate ASC");
	while($row = mysqli_fetch_assoc($query))
	{
?>
		<tr>
			<td><a href="<?=$row["fileLocation"]?>"><?=$row["fileName"]?></a></td>
			<td><?=$row["fileDescription"]?></td>
			<td><?=$row["fileLocation"]?></td>
			<td><?=date("n/j/y", strtotime($row["fileUploadDate"]))?></td>
			<td><a class="delete" href="/functions.php?action=deleteFile&id=<?=$row["id"]?>">delete</a></td>
		</tr>
<?php
	}
?>
	</table><br><br><br>
	<?php
		$db->select_db("googleservices_db");
	?>
	<table width="100%">
		<tr>
			<td width="50%">
				<h6>Albums to Sync</h6>
				<form action="googleActions.php" method="get">
					<select name="picasaAlbumId[]" multiple>
					<?php
						$albumsToSync = $db->query("SELECT * FROM  picasaAlbumsToSync WHERE sync=1");
						$syncAlbumIds;
						while($row = mysqli_fetch_assoc($albumsToSync))
								$syncAlbumIds[] = $row['gid'];
						
						$userFeed = $gphoto->getUserFeed();
						foreach ($userFeed as $userEntry)
							echo "<option value='" . $userEntry->getGphotoId() . "' ".((in_array($userEntry->getGphotoId(), $syncAlbumIds))?"selected":"").">" . $userEntry->getTitle() . "</option>";
					?>
					</select><br/>
					<input type="hidden" name="action" value="updateAlbumsToSync" />
					<input type="hidden" name="return" value="google" />
					<input type="submit" value="Update" />
				</form>
				<?php 
					echo "<br/><br/>Currently syncing ".mysqli_num_rows($albumsToSync)." albums.<br/><br/>";
				?>
				<form action="googleActions.php" method="get">
					<input type="hidden" name="action" value="updateAlbums" />
					<input type="hidden" name="return" value="google" />
					<input type="submit" value="Force All Albums Sync Now" />
				</form>
			</td>
			<td>
				<?php
					$query = $db->query("SELECT * FROM changeLog ORDER BY time DESC LIMIT 4");
					$row = mysqli_fetch_assoc($query);
				?>
				<h6>Last Updated at <?php echo(date("g:ia \o\\n F jS", strtotime($row['time']))); ?></h6>
				<?php 
					do
					{
						echo $row['action'];
					
						$events = unserialize($row['changed']);
						echo "<ul>";
						for($i = 0; $i < count($events); $i++)
							echo "<li>".$events[$i]."</li>";
						echo "</ul>";
					} while($row = mysqli_fetch_assoc($query));
				?>
			</td>
		</tr>
	</table>
</div>

<div style="display:none;">
<div id="upload">
	<h2>File Upload</h2>
	<form id="uploadForm">
		File Description: <input type="text" name="desc" /><br><br>
		Please select a file to upload.<br>
		<input type="file" name="file" id="fileUpload" /><br>
		<input type="submit" value="upload" />
		<input type="hidden" name="action" value="uploadFile" />
	</form>
</div>
</div>
<script>
	$('.fancybox').fancybox();
	
	$("#uploadForm").submit(function()
	{	
		var formElement = document.getElementById("uploadForm");  
		var xhr = new XMLHttpRequest();  

		xhr.upload.addEventListener("progress", uploadStatus, false);
		xhr.addEventListener("load", 
		function(evt)
		{
			data = JSON.parse(evt["currentTarget"]["responseText"]);
			
			if(data["status"])
			{
				$.ajax({
					url: "google",
					success: function(html)
					{
						$("#admin").html($(html).find("#admin").html());
						$("#fileUpload").val("")
						$.fancybox.close();
					}
				});
			}
			else
			{
				hudMessage(data["msg"]);
			}
		}, false);
		
		xhr.open("POST", "/functions.php");  
		xhr.send(new FormData(formElement));
		
		return false;
	});
	
	$(".delete").live("click", function()
	{
		console.log($(this).attr("href"))
		
		$.ajax({
			url: $(this).attr("href"),
			success: function(data)
			{
				console.log(data["status"])
				if(data["status"])
				{
					hudMessage(data["msg"]);
					$.ajax({
						url: "google",
						success: function(html)
						{
							$("#admin").html($(html).find("#admin").html());
						}
					});
				}
				else
				{
					hudMessage(data["msg"]);
				}
			},
			dataType: "json"
		});
	
		return false;
	})
	
	function uploadStatus(evt)
	{
		if (evt.lengthComputable) {
			var percentComplete = Math.round(evt.loaded * 100 / evt.total);
			loadingMessage(percentComplete);
		}
		else {
		  console.log('unable to compute');
		}
	}
</script>