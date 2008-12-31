<?php
$page_title = $comic->page_title();
require(THEMES.THEME.'/header.php');
?>
	
<div id="main">

	<br />
	<p>
		<h2><?php echo $comic->title; ?></h2>
		<div class="date"><?php echo $comic->get_date('F j, Y'); ?></div>
	</p>
	
	<img src="<?php echo $comic->get_image(); ?>" title="<?php echo $comic->info; ?>" alt="<?php echo $comic->alt_text(); ?>" class="comic" />
	
	<br /><br />
	
	<div class="cnav"><?php echo $comic->get_nav('', '', ' | '); ?></div>
	
	<br /><br />

<?php if($comic->is_index === false) require(THEMES.THEME.'/comments.php'); ?>
	
</div>

</div>

<?php require(THEMES.THEME.'/footer.php'); ?>