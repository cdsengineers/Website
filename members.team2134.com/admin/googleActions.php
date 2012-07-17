<?php

	ini_set("memory_limit","200M");


	ini_set("include_path", ".:/usr/local/lib/php:/usr/local/php5/lib/pear:/home/alampiss/team2134.com:/home/alampiss/commonTools:/home/alampiss/commonTools/team2134");
	
	require_once("team2134.com.inc");
	require_once("functions.inc");	
	
	//Load the gdata interface for pulling from google services
	require_once 'Zend/Loader.php';
	Zend_Loader :: loadClass('Zend_Gdata');
	Zend_Loader :: loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader :: loadClass('Zend_Gdata_Photos');
	Zend_Loader :: loadClass('Zend_Http_Client');
	
	//Login with google account username and password to pull data as user
	$svc = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
	
	$user = "cdsengineers@gmail.com";
	$pass = "cdsrobotics0";
	
	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $svc);
	$gphoto = new Zend_Gdata_Photos($client);

	$db->select_db("googleservices_db");

	if($_REQUEST['action'] == "updateAlbumsToSync")
	{
		$selectedAlbumsToSync = $_REQUEST['picasaAlbumId'];
		$results;
		for($i = 0; $i < count($selectedAlbumsToSync); $i++)
		{
			$gid = $selectedAlbumsToSync[$i];
			$query = $db->query("SELECT * FROM picasaAlbumsToSync WHERE gid='".$gid."'");
			if($row = mysqli_fetch_assoc($query))
			{
				if($row["sync"] != 1)
				{
					$db->query("UPDATE picasaAlbumsToSync SET sync=1 WHERE gid='".$gid."'");
					$results[] = "Album <b>".$row["name"]."</b> changed to active";
				}
			}
			else
			{
				$db->query("INSERT INTO picasaAlbumsToSync (gid, sync) VALUES ('".$gid."', 1)");
				$results[] = "Added gid $gid to sync database";
			}
		}
		
		$allAblumsInDB;
		$query = $db->query("SELECT * FROM picasaAlbumsToSync WHERE sync=1");
		while($row = mysqli_fetch_assoc($query))
			$allAblumsInDB[] = $row['gid'];
		
		for($i = 0; $i < count($allAblumsInDB); $i++)
		{
			if(!in_array($allAblumsInDB[$i], $selectedAlbumsToSync))
			{
				$db->query("UPDATE picasaAlbumsToSync SET sync=0 WHERE gid='".$allAblumsInDB[$i]."'");
				$tempQ = $db->query("SELECT * FROM picasaAlbumsToSync WHERE gid='".$allAblumsInDB[$i]."'");
				$temp = mysqli_fetch_assoc($tempQ);
				$results[] = "Album <b>".$temp['name']."</b> changed to inactive";
			}
		}
		
		if(count($results) > 0)
			$db->query("INSERT INTO changeLog (action, changed, status) VALUES ('Updated Albums that Sync', '".serialize($results)."', 1)");
		
		updateAllAlbums();	
	}
	
	if($_REQUEST['action'] == "updateAlbums")
	{
		updateAllAlbums();
	}

	if(isset($get['return']))
		header("Location: ".$get['return']);
		
	function updateAllAlbums($cron="")
	{
		global $gphoto, $db;
		$query = $gphoto->newAlbumQuery();

		$updated = false;
		
		$dbQuery = $db->query("SELECT * FROM picasaAlbumsToSync WHERE sync=1");
		while($row = mysqli_fetch_assoc($dbQuery))
		{
			$query->setAlbumId($row['gid']);
			$feed = $gphoto->getAlbumFeed($query);

			$pictures = null;
			$gid = $row['gid'];

			foreach ($feed as $entry)
			{
				$media = $entry->getMediaGroup();
				$content = $media->getContent();

				$pid = $entry->getGphotoId()->getText();
				$pictures[$pid]["id"] = $pid;

				$pictures[$pid]["link"] = $content[0]->url;

				$thumbnail = $media->getThumbnail();
				$pictures[$pid]["thumb1"] = $thumbnail[0]->url;
				$pictures[$pid]["thumb2"] = $thumbnail[1]->url;
				$pictures[$pid]["thumb3"] = $thumbnail[2]->url;

				$pictures[$pid]["description"] = $entry->getSummary()->text;
			}

			$album_query = $db->query("SELECT * FROM picasaAlbums WHERE gid='$gid'");
			if($album_row = mysqli_fetch_assoc($album_query))
			{
				$db_array = unserialize($album_row['pictures']);
				$diff = array_diff_assoc($pictures, $db_array);

				$added = null;
				$removed = null;
				$crap = 0;

				foreach($diff as $id=>$item)
				{
					if($db_array[$id]['id'] == $id)
					{
						$removed[] = $db_array[$id];
					}
					else if($pictures[$id]["id"] == $id)
					{
						$added[] = $pictures[$id]; 
					}
					else
					{
						$crap++;
					}
				}

				$changed = array("Added ".count($added)." new pictures","Removed ".count($removed)." pictures", "Updated name in sync table");

				$userFeed = $gphoto->getUserFeed();

				foreach($userFeed as $entry)
				{
					if($entry->getGphotoId() == $gid)
					{
						$albumName = $entry->getTitle();
						$db->query("UPDATE picasaAlbums SET name='$albumName', dateSync='".date('Y-m-d H:i:s')."', pictures='".serialize($pictures)."', changesFromLastSync='".serialize($changed)."' WHERE gid='$gid'");
						$db->query("UPDATE picasaAlbumsToSync SET name='$albumName' WHERE gid='$gid'");
					}
				}

				if(count($added) > 0 || count($removed) > 0)
				{
					$updated = true;
					$db->query("INSERT INTO changeLog (action, changed, status) VALUES ('Updated $albumName ', '".serialize($changed)."', 1)");
				}
				
				if($album_row['name'] != $albumName)
				{
					$updated = true;
					$db->query("INSERT INTO changeLog (action, changed, status) VALUES ('Updated album name', '".serialize(array($album_row['name']." was updated to $albumName"))."', 1)");
				}
			}
			else
			{
				$userFeed = $gphoto->getUserFeed();

				foreach($userFeed as $entry)
				{
					if($entry->getGphotoId() == $gid)
					{
						$albumName = $entry->getTitle();

						$changed = array("Added ".count($pictures)." new pictures", "Updated name in sync table");

						$updated = true;

						$db->query("INSERT INTO picasaAlbums (gid, name, pictures, status) VALUES ('$gid', '$albumName', '".serialize($pictures)."', 1)");
						$db->query("UPDATE picasaAlbumsToSync SET name='$albumName' WHERE gid='$gid'");
						$db->query("INSERT INTO changeLog (action, changed, status) VALUES ('$albumName synced for first time', '".serialize($changed)."', 1)");
					}
				}
			}
		}
		
		if(!$updated)
		{
			$db->query("INSERT INTO changeLog (action, changed, status) VALUES ('Albums Update$cron', '".serialize(array("All albums are up-to-date"))."', 1)");
		}
	}
?>