<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}
?>

<style type="text/css">
.comm_name {
	color: #000;
	font-weight: bold;
}
.comm_text {
	width: 90%;
	margin: 0 auto;
}
</style>

<script type="text/javascript">
window.addEvent('domready', function() {
	$$('a.delete').addEvent('click', function(e) {
		e.stop();
		var ask = confirm('are you sure you wanna do that?');
		if(ask == false) { return false; }
		else { document.location = 'index.php?page=admin&option=comments&edit='+this.parentNode.id; }
	});
});
</script>

<form action="" class="jNice">
<h3>Recent Comments</h3>

<?php 
$odd_comment = 'odd';
if($comments->get_archives(5) !== false) { ?>

<table cellpadding="0" cellspacing="0">

<?php foreach($comments->get_archives(5) as $comment) { ?>
	
	<tr class="<?php echo $odd_comment; ?>">
		<td>
			<div class="comm_name"><?php echo $comment->name; ?></div>
			<div class="comm_text"><?php echo replace_smilies($comment->comment); ?></div>
		</td>
		<td class="action" id="<?php echo $comment->ID; ?>"><a href="<?php echo $comment->url; ?>" class="view" target="_blank">View</a><a href="index.php?page=admin&option=comments&edit=<?php echo $comment->ID; ?>" class="edit">Edit</a><a href="index.php?page=admin&option=comments&delete=<?php echo $comment->ID; ?>" class="delete" onclick="if(confirm('are you sure you wanna do that?')==false){return false;}">Delete</a></td>
	</tr>
	
<?php
	$odd_comment = ($odd_comment == 'odd') ? '' : 'odd';
}
?>

</table>

<br />
<p class="center"><a href="index.php?page=admin&option=comments">See Them All</a></p>

<?php
}
else {
	echo '<p style="text-align:center;margin:10px;">No Comments</p>';
}
?>
