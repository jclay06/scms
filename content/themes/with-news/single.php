<?php 
$comments->get_comments($comic->PID);
get_header();
?>

<meta name="author" content="<?php echo $comic->author_full; ?>"/>
<meta name="description" content="<?php echo $comic->title; ?>; <?php echo $comic->info; ?>"/> 
<title><?php echo $comic->filter_vars('#%id% - %title%'); ?></title>
</head>

<?php get_sidebar(); ?>

<div id="content">
<div class="post">

  		<a name="comic" id="comic"></a>
	  	<?php /* echo $comic->author_text(); */ ?>
	  	
		<div class="title2">
		<div class="post-date" align="center"><span class="post-month"><?php echo $comic->get_date('M'); ?></span><span class="post-day"><?php echo $comic->get_date('d'); ?></span></div>
		<h5><?php echo $comic->title; ?></h5>
		</div>

		<div class="post-cat">
		<?php echo "Posted By: <strong><a href='#'>",$comic->author,"</a></strong>"; 
		?>
		</div>
		<span class="post-comments"><?php echo "<a href='#comments' title='Leave Us A Comment!'>",$comments->num_comments('No Coments Yet','1 Comment','% Comments'),"</a>"; ?></span>

		<div class="post-content">

			<div class="comic"><img src="<?php echo $comic->get_image(); ?>" alt="<?php echo $comic->alt_text(); ?>" title="<?php echo $comic->info; ?>" /></div>
			
			<div class="newbuttons"><?php echo $comic->get_nav(); ?></div>
			<br />

<center>
			
<?php $rname = $comic->type . 'q' . $comic->ID;
#include('ratings/_drawrating.php'); echo rating_bar($rname,5); // show rating bar
?>
			
			<a href="#" id="copyme_link" rel="nofollow"><font size="+1">Put this on your MySpace, Blog, etc!</font></a>
			<div id="copyme" style="height:115px;">
			<h3>Put this on your MySpace, Blog, or WebSite!</h3>
			<input type="text" style="width:325px;" onclick="this.focus();this.select();" value="&lt;a href=&quot;<?php echo $comic->get_permalink(); ?>&quot; title=&quot;<?php echo htmlentities($comic->info); ?>&quot;&gt;&lt;img src=&quot;<?php echo $comic->get_image(); ?>&quot; alt=&quot;<?php echo htmlentities($comic->title); ?>&quot; /&gt;&lt;/a&gt;" readonly="readonly" />
			<h3>Or put it into a Forum ...</h3>
			<input type="text" style="width:325px;" onclick="this.focus();this.select();" value="[URL=<?php echo $comic->get_permalink(); ?>][IMG]<?php echo $comic->get_image(); ?>[/IMG][/URL]" readonly="readonly" />
			</div>
			
<script type="text/javascript">
window.addEvent('domready', function() {
	var copyme = $('copyme');
	var myFx = new Fx.Slide('copyme', {duration:300}).hide();
	var visible = 0;
	$('copyme_link').addEvent('click', function(e) {
		e.stop();
		if(visible == 0) {
		//	copyme.setStyle('display', 'block');
			myFx.slideIn();
			visible = 1;
		}
		else {
		//	copyme.setStyle('visibility', 'hidden');
			myFx.slideOut();
			visible = 0;
		}
	});
});
</script>
			
			<br /><br />	
		</div>

<a name="comments"></a>
<?php get_comments(); ?>

<p></p>

	</center>
	
</div>
</div>
	
<?php get_footer(); ?>