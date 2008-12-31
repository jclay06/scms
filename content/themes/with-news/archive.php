<?php get_header(); ?>
<title>Comic Archive</title>
</head>

<?php get_sidebar(); ?>
<div id="content">
<div class="post">
		
	<center>
	<h5>The Comic Archives ...</h5>

	<br />
	
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('#archives select').addEvent('change', function() {
		document.location = '/' + this.parentNode.id + '/' + this.value;
	});
});
</script>

<div id="archives">
	
<?php foreach($comic->categories as $category) : 

	$archives = $comic->get_archives('all', $category['ID']);
	if(count($archives) === 0) continue;

?>

	<form id="<?php echo $category['nicename']; ?>">
	<select>
		<option value="" class="center">Choose from the &quot;<?php echo $category['category']; ?>&quot; Series</option>
<?php foreach($archives as $c) : ?>
		<option value="<?php echo $c->ID; ?>"><?php echo get_date('m/d/Y', $c->time); ?> - <?php echo $c->title; ?></option>
<?php endforeach; ?>
	</select>
	</form>
	
	<br /><br />

<?php endforeach; ?>

</div>

<style type="text/css">
#toprated {
	width: 330px;
	margin: 0 auto;
}
#toprated table {
	background: #eefecf;
}
#toprated thead {
	font: bold 1.1em "Trebuchet MS", Verdana, Arial;
	color: #5870ff;
	background: #fff;
	border-bottom: 1px solid #333;
	padding: 2px;
}
#toprated tr {
	text-align: center;
	margin: 0 auto;
}
#toprated tr.alt {
	background: #fff;
}
#toprated td {
	width: 10%;
}
#toprated td + td {
	width: 75%;
	padding: 2%;
}
#toprated td + td img {
	max-width: 98%;
}
#toprated td + td + td {
	width: 15%;
}
</style>

<div id="toprated">
<h3>Top 10 Viewed Comics!</h3>
<table cellpadding="0" cellspacing="0">
	<thead>
		<th>Rank</th>
		<th>Comic</th>
		<th>Views</th>
	</thead>
	<tbody>
<?php 
	$i = 1;
	foreach($scdb->get_results("SELECT * FROM `$scdb->comics` ORDER BY `views` DESC LIMIT 10") as $c) {
		$vals = array('title'=>$c->title, 'ID'=>$c->ID, 'PID'=>$c->PID, 'cat'=>$c->cat);
		$alt = (isset($alt) && $alt == '') ? 'alt' : '';
?>
		<tr class="<?php echo $alt; ?>">
			<td># <?php echo $i; ?></td>
			<td><a href="<?php echo $comic->get_filtered_link($vals); ?>" title=""><?php echo $c->title; ?><br /><img src="<?php echo IMAGES, $comic->cat_info($c->cat)->nicename,'/',$c->image; ?>" alt="<?php echo $c->title; ?>" height="75px" /></a></td>
			<td><?php echo $c->views; ?></td>
		</tr>
<?php ++$i;
	}
?>
	</tbody>
</table>
</div>

	</center>
	
</div>
</div>
	
<?php get_footer(); ?>