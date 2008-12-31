<?php get_header(); ?>

<meta name="description" content="our blog of sorts"/> 
<title>SpiffyNews!</title>
</head>

<?php get_sidebar(); ?>

<div id="content">

<?php
$page = (int) isset($_GET['page']) ? $_GET['page'] : 1;
if('page' == $_VAR && isset($_VARS[0]) && is_nat($_VARS[0])) {
	$page = is_nat($_VARS[0]);
}
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

	<div class="navigation">
		<span class="previous-entries"><?php $news->previous_page_link(); ?></span>
		<span class="next-entries"><?php $news->next_page_link(); ?></span>
	</div>


</div>

<?php get_footer(); ?>