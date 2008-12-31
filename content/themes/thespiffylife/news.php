<?php get_header(); ?>

<meta name="description" content="our blog of sorts"/> 
<title>SpiffyNews!</title>
</head>

<?php get_sidebar(); ?>

<div id="content">

<style type="text/css">
.post {
	width: 80%;
	margin: 0 auto;
	padding: 15px 0 15px 0;
}
</style>

<?php
$page = (int) isset($_GET['page']) ? $_GET['page'] : 1;
foreach($news->get_archives(5, 'all', $page) as $post) {
?>

<div class="post" id="post-<?php echo $post->ID; ?>">
	<div class="post-date-blog"><span class="post-month"><?php echo get_date('M', $post->time); ?></span>
	<span class="post-day"><?php echo get_date('j', $post->time); ?></span></div>

	<h5><a href="<?php echo DOMAIN,'news/?p=',$post->ID; ?>"><?php echo $post->title; ?></a></h5>
        
	<div class="blog-comments">
	<a href="<?php echo DOMAIN,'news/?p=',$post->ID; ?>>#respond">Comments</a></div>
        
	<div class="post-content">
	<div class="blog-author">Posted by: <strong><?php echo user_info($post->author)->name; ?></strong></div>
        
	<br />
        
	<div style="margin-top:-10px;padding-bottom:2px;" align="left">
	<?php
	if(user_info($post->author)->ID == 1) {
		echo "<img src='",get_theme_link(),"/img/josh.jpg' title='posted by Josh!' class='alignleft' alt='josh' />";
	}
	else {
		echo "<img src='",get_theme_link(),"/img/guy.jpg' title='posted by Guy!' class='alignleft' alt='guy' />";
	}
	echo "</div>"; 
	echo nl2br($post->content);
	?>
	</div>
</div>

<?php } ?>

</div>

<?php get_footer(); ?>