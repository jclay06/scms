<?php get_header(); ?>

<title>TheSpiffyLife | Search for &quot;<?php echo $comic->search->term;?>&quot;</title>
</head>

<?php get_sidebar(); ?>

<style type="text/css">
.search-results {
	margin: 0;
	padding: 5px;
}
.search-results div {
	margin: 10px;
}
.search-results a, .search-results a:visited, .search-results a:hover {
	color: #cc6600;
	font-size: 16px;
}
.search-results div div {
	margin: 0;
	margin-left: 20px;
	padding: 5px;
}
.search-results div div img {
	max-width: 400px;
}
.search-results div div span {
	background: #e6eed0;
}
.search-prev a, .search-next a {
	color: #5c8d0c;
}
.search-prev {
	float:left;
}
.search-next {
	float:right;
}
.search-zero {
	marg
	text-align: center;
	font-size: 18px;
}
</style>

<div id="content">
<div class="post">

  	<div class="title"><h5>Search Results For &quot;<?php echo $comic->search->term; ?>&quot;</h5></div>

	<div class="blog">
	
	<div class="search-results">

<?php
if($comic->search->num_results > 0) {
	$i = $comic->search->start;
	foreach($comic->search->results as $result) {
		$smalltext = smalltext($comic->search->term, $result->text); ?>
		
		<div>
			<a href="<?php echo $comic->get_permalink($result->PID); ?>">
				<?php echo ++$i; ?>. <?php echo $result->title; ?>
				<div><img src="<?php echo $comic->get_image_by_permalink($result->PID); ?>" alt="<?php echo highlight_text($comic->search->term, $smalltext); ?>" title="<?php echo $smalltext; ?>" /></div>
			</a>
		</div>

<?php
	}
}
else {
	echo '<p class="search-zero">Sorry, no results!</p>';
}
?>
	
	</div>
	
<?php search_prev(); search_next(); ?>	
	
	</div>

</div>
</div>
	
<?php get_footer(); ?>