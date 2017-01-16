jQuery(function (){
	jQuery('#send_mail_btn').click(function (){
		//alert();
		
		var theme = jQuery('#theme').val();
		
		if(theme.length == 0)
		{
			alert('Theme can not be empty');
			return;
		}
		
		var text = tinyMCE.get('email_text').getContent({format : 'raw'});
		
		jQuery('#send_mail_btn').attr("disabled", "disabled");
		
		var data = {};
		data.action = 'send_email_to_subscribers';
		data.text = text;
		data.theme = theme;
		
		jQuery.ajax({
			type: "post",
			url: "/wp-admin/admin-ajax.php",
			dataType: "json",
			data: data,
			success: function(){
				jQuery('#theme').val('');
				tinyMCE.get('email_text').setContent('');
			},
			complete: function(){
				jQuery('#send_mail_btn').removeAttr("disabled");
			}
		});
		
	});
});


