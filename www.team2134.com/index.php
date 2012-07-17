<?php

	require_once("team2134.com.inc");
	require_once("functions.inc");

	$navBar = $db->query("SELECT * FROM navBar ORDER BY name ASC");
	$totalRows_navBar = mysqli_num_rows($navBar);

	$colname_pageData = "9";
	if (isset($get['page'])) {
		$query_pageData = ("SELECT * FROM pages WHERE id = $get[page]");
	} else if (isset($get['perm'])) {
		$query_pageData = ("SELECT * FROM pages WHERE title LIKE '$get[perm]' LIMIT 1");
	} else {
		$query_pageData = ("SELECT * FROM pages WHERE id = $colname_pageData");
	}
	
	$pageData = $db->query($query_pageData);
	$row_pageData = mysqli_fetch_assoc($pageData);
	$totalRows_pageData = mysqli_num_rows($pageData);
	
	if($totalRows_pageData < 1)
	{
		$query_pageData = ("SELECT * FROM pages WHERE id = 9");
		$pageData = $db->query($query_pageData);
		$row_pageData = mysqli_fetch_assoc($pageData);
		$totalRows_pageData = mysqli_num_rows($pageData);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>CDS Robotics - <?php echo $row_pageData['title'];?></title>
        <link rel="stylesheet" href="/style.css" type="text/css" charset="utf-8">
       	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css">
		<script type="text/javascript" src="/shadowbox/shadowbox.js"></script>

        
    </head>
    
    <body>
		<div id="wraper">    
            <div id="container">
            
                <div id="header">
                    <a href="/"><img src="/images/logo.png" /></a>
                </div>
                
                <div id="navigation">
					<?php 
						$shift = 0;
							
						while ($row_navBar = mysqli_fetch_assoc($navBar)) { 
							if($row_navBar['parent'] == 0) { ?>
		  					<a href="<?php if(preg_match('/^[a-zA-Z]+[:\/\/]+/', $row_navBar['link'])) {} else { echo "/"; }?><?php echo $row_navBar['link']; ?>" style="vertical-align:<?php echo $shift+=2; ?>px; <?php if($row_pageData['id'] == $row_navBar['pageId']) { echo 'color:#8e0915;'; }?>"><?php echo $row_navBar['name']; ?></a>
       				<?php } } ?>
				</div>
				
                <div id="content">
                	<div id="content-top"><h1><?php echo $row_pageData['fullTitle']; ?></h1></div>
                    
                    <div id="verticalSpacer"></div>
                    <div id="content-center">
                    	<div id="content-text">
             				<?php echo $row_pageData['content']; ?>
                        </div>
                    </div>
                    
                    <div id="content-bottom">&nbsp;
                    <div id="copyright">Copyright &copy; 2011 Corona Del Sol Robotics</div></div>
                </div>
            </div>
        </div>
    </body>
</html>
