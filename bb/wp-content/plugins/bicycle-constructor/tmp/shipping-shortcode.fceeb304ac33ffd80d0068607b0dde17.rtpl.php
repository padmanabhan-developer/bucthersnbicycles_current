<?php if(!class_exists('raintpl')){exit;}?><div class="bc-price">
	<div class="price left">
		Shipping:
	</div>
	<div class="price right">
		<div class="currency shipping-currency" data-shipping-currency="<?php echo $currency;?>">
			<?php echo $currency;?>

		</div>
		<div class="shipping-price" data-shipping-price="<?php echo $price;?>">
			<?php echo $price;?>

		</div>
	</div>
</div>
<div style="width:100%; display: inline-block"></div>
<div class="sc_buttons">
	<div class="bc_select_wrap">
		<select id="shipping-countries" class="wpcf7-submit bc_cur_select">
			<?php echo $shipping_countries_options;?>

		</select>
	</div>
	<div class="bc_select_wrap" style="margin-left: 10px;">
        <select id="shipping-regions" class="wpcf7-submit bc_cur_select" disabled="disabled">

		</select>
	</div>
	<div class="bc_select_wrap" style="margin-left: 10px;">
        <select id="shipping-services" class="wpcf7-submit bc_cur_select" disabled="disabled">
		</select>
	</div>
</div>