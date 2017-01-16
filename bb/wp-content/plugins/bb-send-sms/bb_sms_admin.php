<?php
/*
	Admin panel settings page for B&B Send SMS plugin

	Author: Igor Malinovskiy
	Author URI: http://woto-info.com/
	License: GPLv2 or later
*/
	function bb_sms_plugin_set_options() 
	{
		add_options_page(
			'B&B Send SMS', 
			'B&B Send SMS', 
			'manage_options', 
			'bb_send_sms_settings', 
			'bb_send_sms_settings_page'
		);

		bbsms_load_styles();
	}

	function bb_send_sms_settings_page()
	{
		if (isset($_POST['settings_submit']))
		{
			if(!wp_verify_nonce($_POST['settings_submit'], 'bb_sms_settings'))
			{
				echo "<h2>Something goes wrong</h2>";
			}
			else
			{
				$options = $_POST;
				foreach ($options as $opt => $value) 
				{
					update_option($opt, trim($value));
				}

				echo "<h2>Settings has been saved</h2>";
			}
		}
		else
		{
			ob_start();
			?>
				<h1>Butchers and Bicycles Send SMS Settings Page<h1>
				<form action="" method="post">
					<div class="setpage-wrapper">
						<div class="setpage-control">
							<p>
								<label for="msgtext">Message text:</label>
								<input 
									type="text"
									name="msgtext"
									id="msgtext"
									placeholder="Hello world"
									value="<?php echo get_option('msgtext'); ?>" 
								>
							</p>
						</div>
						<div class="setpage-control">
							<p>
								<label for="user">API username:</label>
								<input 
									type="text" 
									name="user" 
									id="user" 
									placeholder="username"
									value="<?php echo get_option('user'); ?>"
								>
							</p>
						</div>
						<div class="setpage-control">
							<p>
								<label for="password">API password:</label>
								<input 
									type="password" 
									name="password" 
									id="password" 
									placeholder="password"
									value="<?php echo get_option('password'); ?>"
								>
								<div class="showpassword"></div>
							</p>
						</div>
						<div class="setpage-control">
							<p>
								<label for="api_id">API ID:</label>
								<input 
									type="text" 
									name="api_id" 
									id="api_id" 
									placeholder="3488785"
									value="<?php echo get_option('api_id'); ?>" 
								>
							</p>
						</div>
						<div class="setpage-control">
							<p>
								<input type="submit">
							</p>
						</div>
						<?php wp_nonce_field('bb_sms_settings', 'settings_submit') ?>
					</div>
				</form>
			<?php
		}
  	}
?>