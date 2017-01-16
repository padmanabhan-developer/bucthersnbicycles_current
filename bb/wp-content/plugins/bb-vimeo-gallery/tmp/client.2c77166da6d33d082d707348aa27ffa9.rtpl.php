<?php if(!class_exists('raintpl')){exit;}?><div class="bb_vimeo_videos">
	<div class="full">
		<figure id="full-size-video" data-video-id="<?php echo $bb_vimeo_primary_video;?>">
			<iframe src="http://player.vimeo.com/video/<?php echo $bb_vimeo_primary_video;?>?title=0&amp;byline=0&amp;portrait=0" width="474" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		</figure>
        <div class="full_video_headline" id="full-video-headline" data-video-id="<?php echo $bb_vimeo_primary_video;?>">
			<?php echo $bb_vimeo_primary_title;?>

		</div>
		<div class="full_video_descr">
			<?php echo $bb_vimeo_primary_descr;?>

		</div>
	</div>
	<span><?php echo $bb_other_videos_caption;?></span>
	<div class="bb_other_videos_wrapper">
		<?php echo $bb_vimeo_other_videos;?>

	</div>
</div>