<?php
$page_title = "Comic Archives";
get_header();
?>

<center>

<br />

<h2>Comic Archives</h2>

<select onchange="if(!isNaN(this.value)){document.location='/'+this.value}">
	<option value="" class="center">--- Comic Archives ---</option>
<?php
foreach($comic->get_archives(1) as $x) {
	echo "\t",'<option value="',$x->ID,'">',get_date("m/d/Y", $x->Time),' - ',$x->Title,'</option>',"\n";
}
?>
</select>

<br />
<br />

<select onchange="if(!isNaN(this.value)){document.location='/'+this.value}">
	<option value="" class="center">--- Comic Archives ---</option>
<?php
foreach($comic->get_archives(3) as $x) {
	echo "\t",'<option value="',$x->ID,'">',get_date("m/d/Y", $x->Time),' - ',$x->Title,'</option>',"\n";
}
?>
</select>

</center>

<?php get_footer(); ?>
