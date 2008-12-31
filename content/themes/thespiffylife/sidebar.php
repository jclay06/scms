<body>
<div id="page">
  <div id="header">
	<div id="headerimg"></div>
	
<?php
	global $comic, $_ACT;
	$current = ' class="current"';
?>
	
	<ul id="nav">
		<li<?php if($_ACT == 'faq.php') echo $current; ?>><a href="/faq.php" title="frequently asked questions">FAQ?</a></li>
		<li<?php if($_ACT == 'news') echo $current; ?>><a href="/news/" title="Our own personal weblog to accompany the webcomics!">SpiffyNews</a></li>
		<li<?php if($_ACT == 'archive') echo $current; ?>><a href="/archive/" title="All of TheSpiffyLife Comics!">Comic Archive</a></li>
		<li<?php if(isset($comic) && $comic->got_comic && $comic->cat == 2) echo $current; ?>><a href="/extras/" title="the latest of TheSpiffyLife's extra art!">Extras</a></li>
		<li<?php if(isset($comic) && $comic->got_comic && $comic->cat == 1) echo $current; ?>><a href="/comics/" title="the latest of TheSpiffyLife comics!">Comics</a></li>
		<li<?php if(isset($comic) && $comic->got_comic && $comic->cat == 3) echo $current; ?>><a href="/story/" title="Guy's attempt at an ongoing story">TheSpiffyLife</a></li>
		<li<?php if($_SERVER['REQUEST_URI'] == '/') echo $current; ?>><a href="/" title="Run Forest, Run!">Home</a></li>
	</ul>	
	
	<div id="searchbox">
		<form action="<?php echo DOMAIN; ?>" method="get">
			<input type="text" id="search" name="s" value="Search Our Comics!" maxlength="50" size="25" />
			<input type="submit" id="search-submit" value="Go" />
		</form>
	</div>
	<div id="livesearch"></div>

  </div>

<div id="sidebar">
<ul>
    <li>
    
    <h2 class="sidebartitle">Updates Every</h2>
    <ul>
    	<li class='center'><font size='+2'><strong>Saturday</strong></font><br /><em>(and randomly!)</em></li>
	</ul>
    
	<h2 class="sidebartitle">Cool Swag</h2>
	<ul class="list-blogroll">
            
		<li><a href="/archive/#toprated" title="Our Top 10 Comics of All Time!">Top 10 List</a></li>
		<li><a href="http://www.cafepress.com/spiffyrus" title="buy t-shirts and junk!"><strong>Spiffy Store!</strong></a></li>
		<li><a href="http://josh.thespiffylife.com/" title="Josh&#8217;s Random Non-sense!">Josh&#8217;s Blog</a></li>
		<li><a href="http://www.facebook.com/apps/application.php?api_key=07456bccc0913897468fa45686700326" title="See Our Latest Comics on Your Facebook Profile!">FaceBook App</a></li>
		<li><a href="http://feeds.feedburner.com/thespiffylife-comics" rel="alternate" type="application/rss+xml" title="Subscribe to our RSS Feed!"><img src="/content/img/feed-icon16x16.png" alt="" style="vertical-align:middle;border:0"/>&nbsp;Comics RSS</a></li>
		<li><a href="http://feeds.feedburner.com/thespiffylife-comics-news" title="Subscribe to our RSS Feed!" rel="alternate" type="application/rss+xml"><img src="/content/img/feed-icon16x16.png" alt="" style="vertical-align:middle;border:0"/>&nbsp;Comics + News</a></li>
      
	</ul>
		
<?php global $comments; $comments->recent_comments(); ?>
      
	<center>
	<br />

<!--
	<a href="http://topwebcomics.com/vote/6000/default.aspx" title="Vote for TheSpiffyLife on TopWebComics!" target="_blank"><img src="http://topwebcomics.com/rankimages/rankimage.aspx?ImageTemplate=twclogo_gray&amp;SiteID=6000" alt="TopWebComics" /></a><br /><br />


<script type="text/javascript" language="javascript">var sc_project=2265130; var sc_invisible=0; var sc_partition=20; var sc_security="9f9a941f";</script>
<script type="text/javascript" language="javascript" src="http://www.statcounter.com/counter/counter.js"></script><noscript><a href="http://www.statcounter.com/" target="_blank"><img src="http://c21.statcounter.com/counter.php?sc_project=2265130&amp;java=0&amp;security=9f9a941f&amp;invisible=0" alt="blog counter" border="0" /></a></noscript>
-->

<br /><br />

<!-- Beginning of Project Wonderful ad code: -->
<!-- Ad box ID: 5774 -->
<script type="text/javascript">
<!--
var d=document;
d.projectwonderful_adbox_id = "5774";
d.projectwonderful_adbox_type = "2";
d.projectwonderful_foreground_color = "";
d.projectwonderful_background_color = "";
//-->
</script>
<script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script>
<!-- End of Project Wonderful ad code. -->

	</center>
	
	</li>
</ul>
</div>