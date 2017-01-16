<?php get_header(); ?>

	<div id="maincontent">
		<?php 
			$pages = get_pages(); 
			foreach ($pages as $pagedata) 
			{
				$content = apply_filters('the_content', $pagedata->post_content);
				$title = $pagedata->post_title;
		?>
		<?php 
				echo $content;
			} //end foreach
		?>
	</div>

<?php get_footer(); ?>