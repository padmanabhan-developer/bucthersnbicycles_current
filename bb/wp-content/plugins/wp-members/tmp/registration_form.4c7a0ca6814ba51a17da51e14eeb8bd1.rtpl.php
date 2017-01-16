<?php if(!class_exists('raintpl')){exit;}?><div id="wpmem_reg">
	<form action="<?php echo $permalink;?>" method="post" class="form">
		<legend> <?php echo $heading;?> </legend>
		<div> <?php echo $legend;?> </div>
		<?php echo $username;?>
		<?php echo $Name;?>
		<?php echo $addr1;?>
		<?php echo $addr2;?>
		<div class="form-inline-field-container">
			<div class="left"><?php echo $city;?></div>	
			<div class="right"><?php echo $zip;?></div>
		</div>
		<?php echo $country;?>
		<?php echo $user_email;?>
		<div class="req-text">
			<font class="req">*</font>
			We kindly ask you to provide this basic information for us to accurately respond to your request - thank you
		</div>
		<div class="news_subscription_checkbox">
			<?php echo $news_subscription;?>
		</div>
		<input name="a" type="hidden" value="<?php echo $a_value;?>" />
		<input name="redirect_to" type="hidden" value=" <?php echo $permalink;?> " />
		<div class="button_div">
			<div class="left">
				<input name="submit" type="submit" value="Save Profile" class="buttons buttons_bold left" />
			</div>
			<div class="right">
				<?php echo $delete_account;?>
			</div>
		</div>
	</form>
	<?php echo $nonce_field;?>
</div>