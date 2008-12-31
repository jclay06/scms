<?php get_header(); ?>

<meta name="description" content="Crazy, Random Comics For Your Viewing Pleasure!"/> 
<title>TheSpiffyLife | WebComics!</title>
</head>

<?php get_sidebar(); ?>

<div id="content">
<div class="post">

	<div class="title"><h5>Latest Comics!</h5></div>

	<div class="post-content">
	
<style type="text/css">
#panels img {
border: 2px solid #000;
}
.ct {
margin-top: 3px;
font-weight:normal;
font-size:14px;
color:#C60;
}
</style>


<div align="center">

	<table width="0" border="0" cellspacing="10" cellpadding="10">
		<tr id="panels"> 
			<td> 
				<div align="center"><?php
$comic->cat = 1; // comic
$comic->get_comic();
echo '<h3>',$comic->cat_info($comic->cat)->category,'</h3>';
echo '<a href="',$comic->get_link(),'" title="',$comic->title,'"><img src="',$comic->get_image(),'" alt="',$comic->title,'" width="150" /></a>';
echo '<div class="ct">#',$comic->ID,' &mdash; &ldquo;',$comic->title,'&rdquo;</div>';
				?></div>
			</td>
			<td> 
				<div align="center"><?php
$comic->cat = 3; // story
$comic->get_comic();
echo '<h3>',$comic->cat_info($comic->cat)->category,'</h3>';
echo '<a href="',$comic->get_link(),'" title="',$comic->title,'"><img src="',$comic->get_image(),'" alt="',$comic->title,'" width="330" /></a>';
echo '<div class="ct">#',$comic->ID,' &mdash; &ldquo;',$comic->title,'&rdquo;</div>';
				?></div>
			</td>
            <td> 
				<div align="center"><?php
$comic->cat = 2; // extra
$comic->get_comic();
echo '<h3>',$comic->cat_info($comic->cat)->category,'</h3>';
echo '<a href="',$comic->get_link(),'" title="',$comic->title,'"><img src="',$comic->get_image(),'" alt="',$comic->title,'" width="150" /></a>';
echo '<div class="ct">#',$comic->ID,' &mdash; &ldquo;',$comic->title,'&rdquo;</div>';
				?></div>
			</td>
		</tr>
	</table>

</div>
			
	</div>
	
	<br /><br />

	<div id="wrapper">  	
	<?php include(theme().'/blog.php'); ?>  	
	
	<div class="poll">
	
<br />

<center>

<!-- Beginning of Project Wonderful ad code: -->
<!-- Ad box ID: 5692 -->
<script type="text/javascript">
<!--
var d=document;
d.projectwonderful_adbox_id = "5692";
d.projectwonderful_adbox_type = "4";
d.projectwonderful_foreground_color = "";
d.projectwonderful_background_color = "";
//-->
</script>
<script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script>
<!-- End of Project Wonderful ad code. -->
	
<p></p>

<!-- Beginning of Project Wonderful ad code: -->
<!-- Ad box ID: 5776 -->
<script type="text/javascript">
<!--
var d=document;
d.projectwonderful_adbox_id = "5776";
d.projectwonderful_adbox_type = "3";
d.projectwonderful_foreground_color = "";
d.projectwonderful_background_color = "";
//-->
</script>
<script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script>
<!-- End of Project Wonderful ad code. -->

</center>

	</div>
	</div>

</div>
</div>
	
<script type="text/javascript">
function copier(){var A=document.getElementById("copyme");if(A.style.display=="none"){A.style.display="block"}else{A.style.display="none"}}
</script>

<?php get_footer(); ?> 