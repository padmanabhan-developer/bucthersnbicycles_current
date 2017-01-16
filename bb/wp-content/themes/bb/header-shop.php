<!DOCTYPE html>
<html>
<head>
	<title><?php bloginfo('name'); ?></title>
	<meta charset="utf-8">
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Mobile Specific Metas
  	================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<?php

		//jquery
		wp_enqueue_script('jquery');

		wp_head();
	?>
	
</head>
<body>
	<div id="shop-header">
		<div class="maincont">
			<div id="shop-logo" style="background-image: url(<?php echo get_option('bb_theme_shop_header_logo'); ?>);">
			</div>
			<div id="shop-nav">
				CONFIGURE AND BUY
			</div>
			<div id="headerbuttons-wrapper">
				<div id="headerbuttons">
					<div class="headerbtn">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to main site</a>
					</div>
				</div>
			</div>
		</div>
	</div>