<?php
	/*
		Template Name: Index
	*/

	get_header();
?>
	<div id="content-wrapper">
		<div id="maincontent">
			<?php
	
			   $args = array(
			   		'post_type' => 'post',
			   		'orderby' => 'menu_order', 
			   		'order' => 'ASC', 
			   		'category_name' => 'main'
			   );
			   $category_posts = new WP_Query($args);
	
			   if ($category_posts->have_posts())
			   {
			   		while ($category_posts->have_posts()) 
			   		{
			   			$category_posts->the_post();
			   			the_content();
			   		}
			   }
			   else
			   {
			   		echo 'Oops, there are no posts.';
			   }
			?>
		</div> <!-- #maincontent -->
		<div class="nav-holder">
				<!-- pagination -->
				<nav id="site-pagination" class="right-navigation">
				<ul id="right-menu" class="nav-anchor">

				<?php $section_labels = get_option('section_label'); ?>
					
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section1']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><img src="<?php bloginfo('template_url'); ?>/images/home-link-ico.png" width="21" height="18" alt="image description"></a>
						<span><?php echo $section_labels['section1']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section2']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section2']; ?></em></a>
						<span><?php echo $section_labels['section2']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section3']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section3']; ?></em></a>
						<span><?php echo $section_labels['section3']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section4']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section4']; ?></em></a>
						<span><?php echo $section_labels['section4']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section5']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section5']; ?></em></a>
						<span><?php echo $section_labels['section5']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section6']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section6']; ?></em></a>
						<span><?php echo $section_labels['section6']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor($section_labels['section7']); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="#<?php echo $anchor; ?>"><em><?php echo $section_labels['section7']; ?></em></a>
						<span><?php echo $section_labels['section7']; ?></span>
					</li>
					<?php $anchor = bb_theme_get_menu_anchor(isset($section_labels['section8']) ? $section_labels['section8'] : ''); ?>
					<li data-menuanchor="<?php echo $anchor; ?>">
						<a href="/shop"><img src="<?php bloginfo('template_url'); ?>/images/shop-link-ico.png" width="21" height="18" style="margin: 3.7px 0 0 2.5px;" alt="image description"></a>
						<span><?php echo $section_labels['section8']; ?></span>
					</li>
				</ul>
			</nav>
		</div>
	</div>

<?php get_footer(); ?>