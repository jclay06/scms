<?php
require('../config.php');
require(PHP_DIR.'functions.php');
require(PHP_DIR.'admin.class.php');
require(PHP_DIR.'comic.class.php');
require(PHP_DIR.'comments.class.php');

$page = isset($_GET['page']{1}) ? $_GET['page'] : 'home';
$option = isset($_GET['page']{1}) && isset($_GET['option']{1}) ? $_GET['option'] : '';

if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || !isset($_SESSION['level'])) {
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	exit(header('Location: login.php'));
}

function a_smenu($case) {
	global $inc_page;
	if($inc_page == $case.'.php') return 'class="active"';
}

switch($page) {

	case 'admin' :
		$page_menu = 'Administration';
		$page_menu_link = 'index.php?page=admin';
		$page_title = 'Admin';
		switch($option) {
			default : // comic-new
				$inc_page = 'admin_comic-new.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'New Comic';
				break;
			case 'comic-edit' :
				$inc_page = 'admin_comic-edit.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Edit Comics';
				break;
			case 'comments' :
				$inc_page = 'admin_comments.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Moderate Comments';
				break;
			case 'cat-new' :
				$inc_page = 'admin_cat-new.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'New Category';
				break;
			case 'cat-edit' :
				$inc_page = 'admin_cat-edit.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Edit Categories';
				break;
			case 'cat-del' :
				$inc_page = 'admin_cat-del.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Delete Category';
				break;
			case 'user-new' :
				$inc_page = 'admin_user-new.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'New User';
				break;
			case 'user-edit' :
				$inc_page = 'admin_user-edit.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Edit Users';
				break;
			case 'news' :
				$inc_page = 'news.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'News';
				$action = (isset($_GET['action'])) ? $_GET['action'] : '';
				switch($action) {
					default : // new
						$page_sub_menu .= ' &raquo; Post';
						break;
					case 'edit' :
						$page_sub_menu .= ' &raquo; Edit';
						break;
				}
		}
		$side_menu = '
		<li><a href="index.php?page=admin&option=comic-new" '.a_smenu('admin_comic-new').'>New Comic</a></li>
		<li><a href="index.php?page=admin&option=comic-edit" '.a_smenu('admin_comic-edit').'>Edit Comics</a></li>
		<li><a href="index.php?page=admin&option=comments" '.a_smenu('admin_comments').'>Comments</a></li>
		<li><a href="index.php?page=admin&option=cat-new" '.a_smenu('admin_cat-new').'>New Category</a></li>
		<li><a href="index.php?page=admin&option=cat-edit" '.a_smenu('admin_cat-edit').'>Edit Category</a></li>
		<li><a href="index.php?page=admin&option=user-new" '.a_smenu('admin_user-new').'>New User</a></li>
		<li><a href="index.php?page=admin&option=user-edit" '.a_smenu('admin_user-edit').'>Edit Users</a></li>';
		break;
		
	case 'design' :
		$page_title = 'Design';
		$page_menu = 'Design';
		$page_menu_link = 'index.php?page=design';
		break;
	
	case 'options' :
		$page_title = 'Options';
		$page_menu = 'Options';
		$page_menu_link = 'index.php?page=options';
		switch($option) {
			default : // locations
				$inc_page = 'options_config.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'URIs';
				break;
			case 'mysql' :
				$inc_page = 'options_mysql.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'MySQL';
				break;
			case 'other' :
				$inc_page = 'options_other.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = 'Other';
				break;
			
		}
		$side_menu = '
		<li><a href="index.php?page=options&option=locations" '.a_smenu('options_config').'>Locations</a></li>
		<li><a href="index.php?page=options&option=mysql" '.a_smenu('options_mysql').'>MySQL</a></li>
		<li><a href="index.php?page=options&option=other" '.a_smenu('options_other').'>Other</a></li>';
		break;
		
	case 'logout' :
		kill_session();
		exit(header('Location: login.php'));
		break;
	
	default : // Dashboard
		$inc_page = 'home.php';
		$page_menu = 'Dashboard';
		$page_menu_link = 'index.php';
		$page_title = 'Main';
		$page_sub_menu = 'Main';
		switch($option) {
			case 'profile' :
				$inc_page = 'profile.php';
				$page_title .= ' &raquo; ' . $page_sub_menu = ' Profile';
				break;
		}
		$side_menu = '
		<li><a href="index.php?page=admin&option=comic-new">New Comic</a></li>
		<li><a href="index.php?page=admin&option=news&action=new">News Post</a></li>
		<li><a href="index.php?page=dashboard&option=profile" '.a_smenu('profile').'>View/Edit Profile</a></li>';		
		break;

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comic Admin &raquo; <?php if(isset($page_title)) echo $page_title; ?></title>

<link href="style/css/transdmin.css?<?php echo filemtime(ROOT.'admin/style/css/transdmin.css'); ?>" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie7.css" /><![endif]-->

<script type="text/javascript" src="style/js/jquery.min.js"></script>
<script type="text/javascript" src="style/js/jNice.min.js"></script>

<script type="text/javascript" src="../content/js/mootools-1.2.1.js"></script>
<script type="text/javascript" src="../content/js/mootools-1.2m.js"></script>

<?php if(isset($inc_page) && 'news.php' == $inc_page) { ?>
<script type="text/javascript" src="../content/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});
</script>
<?php } ?>


</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="index.php"><span>Comic Admin</span></a></h1>
    	
    	<span id="returnto">Return to: <a href="<?php echo DOMAIN; ?>" title="">&ldquo;<?php echo SITE_NAME; ?>&rdquo;</a></span>

        <ul id="mainNav">
        	<li><a href="index.php" <?php if($page_menu == 'Dashboard') { ?>class="active"<?php } ?>>DASHBOARD</a></li>
        	<li><a href="index.php?page=admin" <?php if($page_menu == 'Administration') { ?>class="active"<?php } ?>>ADMINISTRATION</a></li>
        	<li><a href="index.php?page=design" <?php if($page_menu == 'Design') { ?>class="active"<?php } ?>>DESIGN</a></li>
        	<li><a href="index.php?page=options" <?php if($page_menu == 'Options') { ?>class="active"<?php } ?>>OPTIONS</a></li>
        	<li class="logout"><a href="index.php?page=logout">LOGOUT</a></li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
			<div id="container">
        		<div id="sidebar">
                	<ul class="sideNav">
                    	<?php if(isset($side_menu)) echo $side_menu; ?>
                    </ul>
                    <!-- // .sideNav -->
                </div>    
                <!-- // #sidebar -->
                
                <!-- h2 stays for breadcrumbs -->
                <h2><a href="<?php if(isset($page_menu_link)) echo $page_menu_link; ?>"><?php if(isset($page_menu)) echo $page_menu; ?></a> &raquo; <a href="#" class="active"><?php if(isset($page_sub_menu)) echo $page_sub_menu; ?></a></h2>
                
                <div id="main">
<?php if(isset($inc_page)) include($inc_page); ?>
                </div>
                <!-- // #main -->
                
                <div class="clear"></div>
            </div>
            <!-- // #container -->
        </div>	
        <!-- // #containerHolder -->
        
        <p id="footer"><a href="http://thespiffylife.com/">TheSpiffyLife.com</a></p>
    </div>
    <!-- // #wrapper -->
</body>
</html>