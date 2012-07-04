<!DOCTYPE html>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php  wp_title( '|', true, 'right' );?></title>
		<link rel="stylesheet" href="<?php bloginfo('template_url')?>/uniform/css/uniform.default.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" media="screen" href="<?php bloginfo('template_url')?>/yaff/css/base.css?v=2" /> <!--Load CSS-->
	<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/jquery-ui-1.8.21.custom.css" />
	<link rel="stylesheet" media="screen" href="<?php bloginfo('template_url')?>/css/app.css?v=2" /> <!--Load App CSS-->
	<link rel="stylesheet" media="handheld" href="<?php bloginfo('template_url')?>/yaff/css/handheld.css?v=2" /> <!-- Mobile -->
	<link href='http://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
	<script src="<?php bloginfo('template_url')?>/yaff/js/libs/modernizr-1.6.min.js"></script> <!-- Modernizr -->
<!--
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<script type="text/javascript" src="<?php bloginfo('template_url')?>/js/jquery.js"></script>

	
	<script type="text/javascript" src="<?php bloginfo('template_url')?>/js/jquery.loadmask.js"></script>
	
-->
</head>
<body>
	<div id="popup"></div>
	<div id="wrapper">
	<header id="top">
		<h1 id="applogo"><a href="<?php echo home_url( '/' ); ?>" title="" rel="home">TeBactEn</a></h1>
		<nav id="access" role="navigation">
		  <ul>
		  	<li><a href="<?php bloginfo('home') ?>">Home</a></li>
		  	<li><b>Search by:</b></li>
		  	<li><a href="<?php bloginfo('home') ?>/species" class="button gray">Species</a></li>
		  	<li><a href="<?php bloginfo('home') ?>/enzymes" class="button gray">Enzymes</a></li>
		  	<li><a href="<?php bloginfo('home') ?>/compounds" class="button gray">Chemicals</a></li>
		  	
		  	<li><a href="#">Help</a></li>
		  </ul>	
	  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
		<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
		<?php //wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>	
	</nav><!-- #access -->
	</header>	
	<section class="content" role="main">
