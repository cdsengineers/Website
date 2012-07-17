<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("admin");
?>
<?php
$editFormAction = "";
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if (isset($_POST["action"]))
{
	if($_POST["action"] == "add")
	{
	  $insertSQL = "INSERT INTO pages (title, content, parent, fullTitle) VALUES ('".$postValues['title']."', '".$postValues['content']."', ".$postValues['parent'].", '".$postValues['fullTitle']."')";
	  $Result1 = $db->query($insertSQL);
	  
	  $navBar = $db->query("SELECT * FROM pages ORDER BY id DESC");
	  $row_navBar = mysqli_fetch_assoc($navBar);
	  
	  
	  if($_POST['nav'] != "")
	  {
		  $insertSQL = sprintf("INSERT INTO navBar (name, link, parent, pageId) VALUES (%s, %s, %s, %s)",
							   $postValues['title'],
							   $postValues['title'],
							   $postValues['parent'],
							   $row_navBar['id']);
		
		  $Result1 = $db->query($insertSQL);
	  }
	  
	  $message = 'Add Successful!';
	  
	}
	else if($_POST['action'] == "edit")
	{
		$insertSQL = "UPDATE pages SET title='". $postValues['title']."', content='". $postValues['content']."', parent=".$postValues['parent'].", fullTitle=".$postValues['fullTitle']." WHERE id=".$postValues['pageId'];
		$Result1 = $db->query($insertSQL);
		
		$insertSQL = "UPDATE navBar SET parent=".$postValues['parent']." WHERE pageId=".$postValues['pageId'];
		$Result1 = $db->query($insertSQL);
		
		$message ='Edit Successful!';
	}
	else if($_POST['action'] == "delete")
	{	
		$insertSQL = "DELETE FROM pages WHERE id=".$postValues['pageId'];
		$Result1 = $db->query($insertSQL);
		
		$insertSQL = "DELETE FROM pages WHERE parent=".$postValues['pageId'];
		$Result1 = $db->query($insertSQL);
		
		$insertSQL = "DELETE FROM navBar WHERE link='?page=".$postValues['pageId']."'";
		$Result1 = $db->query($insertSQL);
		
		$insertSQL = "DELETE FROM navBar WHERE pageId=".$postValues['pageId'];
		$Result1 = $db->query($insertSQL);
		
		header("Location: ?action=delete&message=Delete Successful!");
	}
}

	$query_pages = "SELECT * FROM pages ORDER BY title ASC";
	$pages = $db->query($query_pages);
	$row_pages = mysqli_fetch_assoc($pages);
	$totalRows_pages = mysqli_num_rows($pages);
	
	$title = "";
	$fulltitle = "";
	$content = "";
	$parent = 0;
	
	if( ($_GET['action'] == "edit" || $_GET['action'] == "delete") && $_GET['pageId'] != "")
	{
		$query_edit = "SELECT * FROM pages WHERE id=".$getValues['pageId'];
		$edit = $db->query($query_edit);
		$row_edit = mysqli_fetch_assoc($edit);
		$totalRows_edit = mysqli_num_rows($edit);
		
		$title = $row_edit['title'];
		$fulltitle = $row_edit['fullTitle'];
		$content = $row_edit['content'];
		$parent = $row_edit['parent'];
	}
?>
<link rel="stylesheet" href="/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<br><br>
<div id="container">
	<?php if($_GET['action']) { ?><a href="?" style="float:right" class="button">&laquo; Back</a>&nbsp;&nbsp;<?php } else { ?><a href="?action=add" style="float:right" class="button">New Page</a><?php } ?><div class="clear"></div>
	<?php
		$sql = "SELECT * FROM pages";
		$command4 =  $db->query($sql);	
		$page = mysqli_fetch_assoc($command4)
	?>
	<div class="success" id="success-message"<?php echo isset($_GET['message']) ? '' : 'style="display: none;"' ?>><?php echo $_GET['message']; ?></div>
	<div class="success" id="success-message"<?php echo isset($message) ? '' : 'style="display: none;"' ?>><?php echo $message; ?></div>
	<?php
		if($_GET['action'] == "add" || ($_GET['action'] == "edit" && isset($_GET['pageId'])) )
		{
			$query_pages = "SELECT * FROM navBar ORDER BY name ASC";
			$pages = $db->query($query_pages);
			$row_pages = mysqli_fetch_assoc($pages);
	?>
	<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
		<span style="display: inline-block; width:100px; text-align: right; margin-right: 5px;"><label for="title">Short Title:</label></span><input type="text" name="title" size="32" value="<?php echo $title; ?>" class="text" /><br />
		<span style="display: inline-block; width:100px; text-align: right; margin-right: 5px;"><label for="fullTitle">Full Title:</label></span><input type="text" name="fullTitle" value="<?php echo $fulltitle; ?>" size="32" class="text" /><br />
		<span style="display: inline-block; width:100px; text-align: right; margin-right: 5px; vertical-align: top;"><label for="content">Content:</label></span><div style="display: inline-block;"><textarea name="content" id="wysiwyg" rows="5" cols="103"><?php echo $content; ?></textarea></div><br />
		<span style="display: inline-block; width:100px; text-align: right; margin-right: 5px;"><label for="parent">Parent:</label></span><select name="parent"><option value="0">None</option>
			<?php do {  ?>
			<option value="<?php echo $row_pages['id']?>" <?php if($parent == $row_pages['id']) {?> selected="selected" <?php }?> ><?php echo $row_pages['name']?></option>
			<?php } while ($row_pages = mysqli_fetch_assoc($pages)); ?>
		</select><br />
		<?php if($_GET['action'] == "add") {?><span style="display: inline-block; width:100px; text-align: right; margin-right: 5px;"><label for="nav">Add to Nav Bar:</label></span><input type="checkbox" name="nav" checked="checked" /><br /><?php } ?>
		<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
		<input type="hidden" name="pageId" value="<?php echo $_GET['pageId']; ?>" />
		<span style="display: inline-block; width:100px; text-align: right; margin-right: 5px;"></span><input type="submit" value="<?php echo $_GET['action'] == "add" ? "Create" : "Save"; ?>" />
	</form>

	<?php
	} 
	else if($_GET['action'] == "delete" && $_GET['pageId'] != "")
	{
	?>
	<p></p>
	<div align="center">
		Delete the page <?php echo $title; ?>?
	    <form onsubmit="return confirm('Are you sure you want to delete the <?php echo $title; ?> page?\nThis will also delete all subpages.')" method="post">
	    	<input type="submit" value="Yes" /><input type="button" value="No" onclick="window.location = '?action=<?php echo $_GET['action']; ?>'" />
	        <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
	  		<input type="hidden" name="pageId" value="<?php echo $_GET['pageId']; ?>" />
	    </form>
	</div>
	<?php } ?>
	<?php if(!($_GET['action'])) { ?>
	<table id="admin">
		<thead><tr>
			<th>ID</th>
			<th>Short Title</th>
			<th>Full Title</th>
			<th>Parent</th>
			<th></th>
		</tr></thead>
		<tbody>
		<?php do {
			echo "<tr>";
			echo "<td>".$page['id']."</td>";
			echo "<td>".$page['title']."</td>";
			echo "<td>".$page['fullTitle']."</td>";
			echo "<td>".$page['parent']."</td>";
			if($page['title'] == "Blog") {echo "<td><a href='http://tumblr.com'>edit</a> | <a href='#delete-page-".$page['id']."' class='fancybox'>delete</a></td>";} else if($page['title'] == "Media") {echo "<td><a href='http://picasaweb.google.com/'>edit</a> | <a href='#delete-page-".$page['id']."' class='fancybox'>delete</a></td>";} else { echo "<td><a href='?action=edit&pageId=".$page['id']."'>edit</a> | <a href='#delete-page-".$page['id']."' class='fancybox'>delete</a></td>";}
			echo "</tr>\n";
		} while($page = mysqli_fetch_assoc($command4)); 
		?>
		</tbody>
	</table>
	<?php } ?>
</div>
<div id="hiddenShit" style="display: none;">
<?php
	$sql = "SELECT * FROM pages";
	$command4 =  $db->query($sql);	
	$page = mysqli_fetch_assoc($command4)
?>
<?php do {
	echo '<div id="delete-page-'.$page['id'].'" style="padding:5px;">';
	echo '<form action="" method="post">';
	echo '<input type="hidden" name="pageId" value="'.$page['id'].'" />';
	echo '<input type="hidden" name="action" value="delete" />';
	echo 'Are you sure you want to delete this page? It will also delete all sub-pages. <a href="#" class="button delete-page">Yes</a> or <a href="javascript:$.fancybox.close();" class="button">Cancel</a>';
	echo '</form>';
	echo "</div>\n";
} while($page = mysqli_fetch_assoc($command4)); 
?>
</div>
<script type="text/javascript" src="/jwysiwyg/jquery.wysiwyg.js"></script>
<script type="text/javascript">
$(function () {
	$('.fancybox').fancybox();
	$('.delete-page').live('click', function () {
		$(this).parent().submit();
		return false;
	});
});
</script>
<script type="text/javascript">
(function($)
{
  $('#wysiwyg').wysiwyg({
    controls: {
      strikeThrough : { visible : true },
      underline     : { visible : true },

      separator00 : { visible : true },

      justifyLeft   : { visible : true },
      justifyCenter : { visible : true },
      justifyRight  : { visible : true },
      justifyFull   : { visible : true },

      separator01 : { visible : true },

      indent  : { visible : true },
      outdent : { visible : true },

      separator02 : { visible : true },

      subscript   : { visible : true },
      superscript : { visible : true },

      separator03 : { visible : true },

      undo : { visible : true },
      redo : { visible : true },

      separator04 : { visible : true },

      insertOrderedList    : { visible : true },
      insertUnorderedList  : { visible : true },
      insertHorizontalRule : { visible : true },

      h4mozilla : { visible : true && $.browser.mozilla, className : 'h4', command : 'heading', arguments : ['h4'], tags : ['h4'], tooltip : "Header 4" },
      h5mozilla : { visible : true && $.browser.mozilla, className : 'h5', command : 'heading', arguments : ['h5'], tags : ['h5'], tooltip : "Header 5" },
      h6mozilla : { visible : true && $.browser.mozilla, className : 'h6', command : 'heading', arguments : ['h6'], tags : ['h6'], tooltip : "Header 6" },

      h4 : { visible : true && !( $.browser.mozilla ), className : 'h4', command : 'formatBlock', arguments : ['<H4>'], tags : ['h4'], tooltip : "Header 4" },
      h5 : { visible : true && !( $.browser.mozilla ), className : 'h5', command : 'formatBlock', arguments : ['<H5>'], tags : ['h5'], tooltip : "Header 5" },
      h6 : { visible : true && !( $.browser.mozilla ), className : 'h6', command : 'formatBlock', arguments : ['<H6>'], tags : ['h6'], tooltip : "Header 6" },

      separator07 : { visible : true },

      cut   : { visible : true },
      copy  : { visible : true },
      paste : { visible : true }
    }
  });
})(jQuery);
</script>