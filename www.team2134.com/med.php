<?php
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$catAlbums = "<div id='albums-selection'>";
	$catPhotos = "";
	
	$db->select_db("googleservices_db");
	$albums = $db->query("SELECT * FROM picasaAlbums JOIN picasaAlbumsToSync ON picasaAlbums.gid=picasaAlbumsToSync.gid WHERE sync=1");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Media Page</title>

</head>

<body>
	<?php
		while($row = mysqli_fetch_assoc($albums))
		{	
			$pictures = unserialize($row["pictures"]);
			$albumCoverImage = current($pictures);
			$catAlbums .= "<a class='album' href='#album-".str_replace(" ", "", $row["name"])."'><img src='".$albumCoverImage["thumb2"]."' /><br />".$row["name"]."</a>";
			
			$catPhotos .= "<div id='album-".str_replace(" ", "", $row["name"])."' style='display: none;'><a href='#' class='back'>&laquo; Back</a><br />";
			foreach($pictures as $item)
			{
				$catPhotos .= '<a href="'.$item["link"].'" rel="shadowbox['.str_replace(" ", "", $row["name"]).']"><img src="'.$item["thumb2"].'" /></a>';
			}
		 	$catPhotos .= "</div>";
		}
		$catAlbums .= "</div>";
		echo $catAlbums;
		echo $catPhotos;
		
	?>
	<script type="text/javascript">
    Shadowbox.init();
	$(function () {
		$('.album').live('click', function () {
			$($(this).attr('href')).show();
			$('#albums-selection').hide();
		});
		$('.back').live('click', function () {
			$(this).parent().hide();
			$('#albums-selection').show();
		});
	});
    </script>
</body>
</html>