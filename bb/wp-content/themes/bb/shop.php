<?php
	/*
		Template Name: Shop
	*/

	get_header('shop');

?>

	<div id="maincontent">
		<?php

			$args = array(
				'post_type' => 'post',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'category_name' => 'shop'
			);
			$category_posts = new WP_Query($args);

			if ($category_posts->have_posts())
			{
				while($category_posts->have_posts())
				{
					$category_posts->the_post();
					the_content();
				}
			}
			else
			{
				echo "Oops, there are no posts.";
			}
		?>
	</div>

<?php get_footer('shop'); ?>