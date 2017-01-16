<?php if(!class_exists('raintpl')){exit;}?><div id="wpmem_login">
	<form action="<?php echo $permalink;?>" method="post" class="form">
		<legend> <?php echo $legend;?> </legend>
		<div>Already have an account?</div>
		<div class="form-inline-field-container">
			<div class="left">
				<div class="div_text">
					<?php echo $login;?>
				</div>
			</div>
			<div class="right">
				<div class="div_text">
					<?php echo $password;?>
				</div>
			</div>
		</div>
		<input type="hidden" name="redirect_to" value="<?php echo $redirect_to;?>">
		<?php echo $login_buttons;?>
		<div class="clear"></div>
		
	</form>
</div>