<div class="blog-home" style="text-align:left">

    <center><h6><a href="/blog/">SpiffyNews!</a></h6></center>

<?php    
	$num = 1;
	
	foreach($scdb->get_results('SELECT * FROM `wpblog`.`wp_posts` WHERE `post_status` = "publish" && `post_type` = "post" ORDER BY `ID` DESC LIMIT 2', ARRAY_A) as $post) {
		
		$datetime = explode(" ",$post['post_date']);
		$date = explode("-",$datetime[0]);
		$time = explode(":",$datetime[1]);
		$stamp = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
			
		$authorID = $post['post_author'];
		$author = $scdb->get_var("SELECT `user_nicename` FROM `wpblog`.`wp_users` WHERE `ID` = '".$post['post_author']."' LIMIT 1");
		$title = $post['post_title'];
#		$link = $post['guid'];
		$link = "/blog/" . $post['post_name'] . "-" . $post['ID'] . "/";
		$comments = (int) $post['comment_count'];

		$content = replace_smilies(nl2br($post['post_content']));
		
		$more = strpos($content, "<!--more-->");
		if($more) {
			$content = substr($content, 0, $more);
			$content .= "<a href='".$link."#more-".$post['ID']."'>Read the rest of this entry ...</a>";
		}
	
    ?>
		<a name="post-<?php echo $num; ?>"></a>
        <div class="post-date-blog"><span class="post-month"><?php echo date("M",$stamp); ?></span>
        <span class="post-day"><?php echo date("j",$stamp); ?></span></div>

        <h5><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h5>
        
        <div class="blog-comments">
        <a href="<?php echo $link; ?>#respond"><?php if($comments === 0) echo "No Comments Yet";
           elseif($comments === 1) echo $comments . " Comment";
           else echo $comments. " Comments"; ?></a></div>
        
        <div class="post-content">
        <div class="blog-author">Posted by: <strong><?php echo $author; ?></strong></div>
        
        <br />
        
        <div style="margin-top:-10px;padding-bottom:2px;" align="left">
        <?php
        if($author == "josh") {echo "<img src='";theme_link();echo "/img/josh.jpg' title='posted by Josh!' class='alignleft' alt='josh' />";}
        else {echo "<img src='";theme_link();echo "/img/guy.jpg' title='posted by Guy!' class='alignleft' alt='guy' />";}
        echo "</div>";
        
        echo $content; ?>
        </div>
        <br /><br />

	<?php } ?>
	
</div>