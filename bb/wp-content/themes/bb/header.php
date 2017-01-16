<!DOCTYPE html>
<!--[if (gte IE 8)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php wp_title('&laquo;', true, 'right') . bloginfo('name'); ?></title>
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Mobile Specific Metas
  	================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- favicon
   	================================================== -->
	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/x-icon">

	<!-- tell wordpress where to insert extra <head> stuff
   	================================================== -->
	<?php 

		//jquery
  		wp_enqueue_script('jquery');
  		
		wp_head(); 
	?>

</head>
<body>
	<div id="wrapper">
		<div id="header">
			<div class="maincont">
				<div id="logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo get_option('bb_theme_header_logo'); ?>" alt=""></a>
				</div>
				<div id="nav">
					<?php 
						wp_nav_menu(
							array(
								'sort_column' => 'menu_order',
								'container_class' => 'menu_header',
								'walker' => new Description_Walker
							)
						);
					?>
				</div>
				<div id="headerbuttons-wrapper">
					<div id="headerbuttons">
						<div class="headerbtn" id="configbtn">
							<a href="/shop">Configure &amp; Buy</a>
						</div>
					</div>
				</div>
			</div>
		</div>
