<?php if(!class_exists('raintpl')){exit;}?><h1>B&amp;B Vimeo Gallery Settings page</h1>
<div class="setpage-wrapper">
	<form action="" method="post">
		<div class="primary">
            <h3>Full Frame Video:</h3>
			<label for="bb_vimeo_primary_video">Video ID:</label>
			<input 
				type="text" 
				name="bb_vimeo_primary_video"
				id="bb_vimeo_primary_video" 
				placeholder="89245058"
				value="<?php echo $bb_vimeo_primary_video;?>"
			>
			<label for="bb_vimeo_primary_title">Headline:</label>
			<input
				type="text"
				name="bb_vimeo_primary_title"
				id="bb_vimeo_primary_title"
				placeholder="Headline"
				value="<?php echo $bb_vimeo_primary_title;?>"
			>
			<label for="bb_vimeo_primary_descr">Description:</label>
			<input
				type="text"
				name="bb_vimeo_primary_descr"
				id="bb_vimeo_primary_descr"
				placeholder="Description"
				value="<?php echo $bb_vimeo_primary_descr;?>"
			>
		</div>
		<div>
            <h3>Other videos:</h3>
			<div>
				<label for="bb_other_videos_caption">Other videos caption:</label>
				<input 
					type="text" 
					name="bb_other_videos_caption" 
					id="bb_other_videos_caption"
					placeholder="How about these videos?"
					value="<?php echo $bb_other_videos_caption;?>"
				>
			</div>
			<div class="other_videos_container">
				<?php echo $other_videos_input;?>

			</div>
			<div>
				<input id="bb_vg_add_btn" type="button" value="Add video">
			</div>
		</div>

		<input type="submit" value="Save changes">
		<?php echo $nonce_field;?>

	</form>
</div>