<?php 
	require_once("team2134.com.inc");
	require_once("functions.inc");

	$ca = new CentralAuth();
	$ca->protect("admin");

if ((isset($post["action"])) && ($post["action"] == "add")) {
	$insertSQL = sprintf("INSERT INTO navBar (id, link, name, parent, pageId) VALUES (%s, '%s', '%s', %s, %s)",
                       $post['id'],
                       $post['link'],
                       $post['name'],
                       $post['parent'],
                       $post['pageId']);

	
	$Result1 = $db->query($insertSQL);
	$message = "Link added";
} else if ((isset($post["action"])) && ($post["action"] == "edit")) {
	$deleteSQL = "UPDATE navBar SET link='".$post['link']."', name='".$post['name']."', parent=".$post['parent'].", pageId=".$post['pageId']." WHERE id=".$post['id'];
	
	$Result1 = $db->query($deleteSQL);
	$message = "Link edited";
} else if ((isset($post["action"])) && ($post["action"] == "delete")) {
	$deleteSQL = "DELETE FROM navBar WHERE id=".$post['id'];
	$Result1 = $db->query($deleteSQL);
	$message = "Link deleted";
}
?>
<br><br>
<div id="container">
	<a href="#new-link" style="float:right" class="button fancybox">New Link</a><div class="clear"></div>
	<div class="success" id="success-message"<?php echo isset($message) ? '' : 'style="display: none;"' ?>><?php echo $message; ?></div>
	<table id="admin">
		<thead><tr>
			<th>Name</th>
			<th>Link</th>
			<th>Parent</th>
			<th>Page ID</th>
			<th></th>
		</tr></thead>
		<tbody>
	<?php
		$query_pages = "SELECT * FROM navBar ORDER BY name ASC";
		$pages = $db->query($query_pages);
		$row_pages = mysqli_fetch_assoc($pages);
		do {
			echo '<tr>';
			echo '<td>'.$row_pages['name'].'</td>';
			echo '<td><a href="http://team2134.com/'.$row_pages['link'].'">'.$row_pages['link'].'</a></td>';
			echo '<td>'.$row_pages['parent'].'</td>';
			echo '<td>'.$row_pages['pageId'].'</td>';
			echo '<td><a href="#edit-link-'.$row_pages['id'].'" class="fancybox">edit</a> | <a href="#delete-link-'.$row_pages['id'].'" class="fancybox">delete</a></td>';
			echo '</tr>';
		} while ($row_pages = mysqli_fetch_assoc($pages));
	?>
		</tbody>
	</table>
</div>
<div id="hiddenShit" style="display: none;">
	<div id="new-link">
		<form method="post" id="linkAddForm" style="margin: 5px;">
			<h3>New Link</h3>
			<input type="hidden" name="action" value="add">
			
			<label for="name">Name:</label><br />
        	<input name="name" size="20" type="text" class="text"><br/>

        	<label for="link">Link:</label><br />
        	<input name="link" size="20" type="text" class="text"><br/>

			<label for="parent">Parent:</label>
			<select name="parent">
		        <option value="0">None</option>
		        <?php
				$query_pages = "SELECT * FROM pages ORDER BY title ASC";
				$pages = $db->query($query_pages);
				$row_pages = mysqli_fetch_assoc($pages);
				do {  ?>
				<option value="<?php echo $row_pages['id']?>" <?php if($parent == $row_pages['id']) {?> selected="selected" <?php }?> ><?php echo $row_pages['title']?></option>
				<?php
				} while ($row_pages = mysqli_fetch_assoc($pages));
				?>
			</select><br />
			
			<label for="pageId">Page ID:</label>
			<select name="pageId">
		        <option value="0">External Link</option>
		        <?php
				$query_pages = "SELECT * FROM pages ORDER BY title ASC";
				$pages = $db->query($query_pages);
				$row_pages = mysqli_fetch_assoc($pages);
				do {  ?>
				<option value="<?php echo $row_pages['id']?>"><?php echo $row_pages['title']?></option>
				<?php
				} while ($row_pages = mysqli_fetch_assoc($pages));
				?>
			</select><br /><br />
			<input type="submit" value="Add Link" />
		</form>
	</div>
<?php
	$query_links = "SELECT * FROM navBar ORDER BY name ASC";
	$links = $db->query($query_links);
	$row_links = mysqli_fetch_assoc($links);
	do {
		$id = $row_links['id'];
		$name = $row_links['name'];
		$link = $row_links['link'];
		$parent = $row_links['parent'];
		$pageid = $row_links['pageId'];
		$parent_output = '<option value="0"'.(($parent == "0") ? " selected='selected'" : "" ).'>None</option>';
		$pageid_output = '<option value="0"'.(($pageid == "") ? " selected='selected'" : "" ).'>External Link</option>';
		
		$query_pages = "SELECT * FROM pages ORDER BY title ASC";
		$pages = $db->query($query_pages);
		$row_pages = mysqli_fetch_assoc($pages);
		do {
		$parent_output .= '<option value="'.$row_pages['id'].'"'.(($parent == $row_pages['id']) ? ' selected="selected"' : '').'>'.$row_pages['title'].'</option>';
		} while ($row_pages = mysqli_fetch_assoc($pages));
		
		$query_pages = "SELECT * FROM pages ORDER BY title ASC";
		$pages = $db->query($query_pages);
		$row_pages = mysqli_fetch_assoc($pages);
		do {
			$pageid_output .= '<option value="'.$row_pages['id'].'"'.(($pageid == $row_pages['id']) ? ' selected="selected"' : '').'>'.$row_pages['title'].'</option>';
		} while ($row_pages = mysqli_fetch_assoc($pages));
		
		$eof = <<<EOF
		<div id="edit-link-$id">
			<form method="post" class="linkEditForm" style="margin: 5px;">
				<h3>Edit Link "$name"</h3>
				<input type="hidden" name="action" value="edit" />
				<input type="hidden" name="id" value="$id" />
			
				<label for="name">Name:</label><br />
	        	<input name="name" size="20" type="text" class="text" value="$name"><br/>

	        	<label for="link">Link:</label><br />
	        	<input name="link" size="20" type="text" class="text" value="$link"><br/>

				<label for="parent">Parent:</label>
				<select name="parent">$parent_output</select><br />
				
				<label for="pageId">Page ID:</label>
				<select name="pageId">$pageid_output</select><br /><br />
				<input type="submit" value="Edit Link" />
			</form>
		</div>
		<div id="delete-link-$id" style="padding:5px;">
			<form method="post">
				<input type="hidden" name="id" value="$id" />
				<input type="hidden" name="action" value="delete" />
				Are you sure you want to delete this link? <a href="#" class="button delete-link">Yes</a> or <a href="javascript:$.fancybox.close();" class="button">Cancel</a>
			</form>
		</div>
EOF;
		echo $eof;
	} while ($row_links = mysqli_fetch_assoc($links));
?>
</div>