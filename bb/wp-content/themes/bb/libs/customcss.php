<?php

/**
 * print custom css
 * @param array $colors from options
 */
function use_custom_colors($colors)
{
	$html = '<style type="text/css" id="custom_colors">';

	/**
	 * light_grey
	 */
	if($colors['light_grey'] != '')
	{
	  $html .= "#header,
	  	.dropotron,
	  	#shop-header { background: none repeat scroll ".$colors['light_grey']."; } ";

	  $html .= ".wpcf7-text,
	  	.headerbtn a:hover,
	  	.wpcf7-textarea,
	  	.div_text input, { color: ".$colors['light_grey']."; }";
	}

	/**
	 * dark_grey
	 */
	if($colors['dark_grey'] != '')
	{
		$html .= ".content,
			.wpcf7-text,
			.wpcf7-textarea,
			.div_text input,
			.interest_offer,
			.bb-constructor-head,
			#footer { background-color: ".$colors['dark_grey']."; }";

		$html .= ".headerbtn a:hover { color: ".$colors['dark_grey']."; }";
	}

	/**
	 * black
	 */
	if($colors['black'] != '')
	{
		$html .= "body,
			.menu li a,
			.headerbtn a,
			.dropotron>li>a,
			.price { color: ".$colors['black']." ; }";

		$html .= "#nav li:first-child,
			.headerbtn { border-left: 1px solid ".$colors['black']."; }";

		$html .= "#nav li { border-right: 1px solid ".$colors['black']."; }";

		$html .= ".bc_saved_conf { border-bottom: 1px solid ".$colors['black']."; }";
	}

	/**
	 * white
	 */
	if($colors['white'] != '')
	{
		$html .= "body { background: none repeat scroll 0 0 ".$colors['white']."; }";
		$html .= ".headerbtn a, 
			::-webkit-input-placeholder, 
			::-moz-placeholder, 
			::-webkit-input-placeholder, 
			:-ms-input-placeholder, 
			input:-moz-placeholder, 
			.wpcf7-submit,
			.button_div input,
			#footer,
			input.buttons_bold,
			.caption-wrap .conf-btn,
			.caption-wrap h1 { color: ".$colors['white']." }";
	}
	
	/**
	 * contrast_color
	 */
	if($colors['contrast'] != '')
	{
		$html .= ".menu li a:hover,
			.dropotron>li:hover>a,
			.menu li a .active,
			.dropotron>li.active>a,
			a { color: ".$colors['contrast']."; }";

		$html .= "#header,
			#shop-header { border-bottom: 2px solid ".$colors['contrast']."; }";
		//$html .= ".bb-constructor-head { border: 1px solid ".$colors['contrast']."; }";

		$html .= ".headerbtn,
            .conf-btn,
			#headerbuttons,
			.wpcf7-submit,
			.button_div input,
			.bc_select_wrap option { background-color: ".$colors['contrast']." !important; }";

		$html .= ".caption-wrap .conf-btn { background: url(".get_template_directory_uri()."/images/more-link-arrow.png) no-repeat 146px 97% 50%".$colors['contrast']."; }";
		$html .= ".caption-wrap .conf-btn { background-size: 4% !important; }";
		$html .= ".bc_select_wrap { background: url(".get_template_directory_uri()."/images/change_currency_bg.png) no-repeat scroll 93% 100% ".$colors['contrast']."; }";
		$html .= ".bbpaypal-btn { background: url(".get_template_directory_uri()."/images/arrow.png) 97% 50% no-repeat ".$colors['contrast']."; }";
		$html .= ".bbpaypal-btn { background-size: 4% !important; }";
	}

	$html .= "</style>";

	echo $html;
}