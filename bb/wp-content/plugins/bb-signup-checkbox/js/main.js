/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
	
	function sendAjaxQuery(data, success)
	{
		$.ajax({
			type: "post",
			dataType: "json",
			url: "/wp-admin/admin-ajax.php",
			data: data,
			success: success
		});
	}
	
	function showMessage(selector, message) {
		jQuery(selector).empty();
		jQuery(selector).append('<p>' + message + '</p>');
		jQuery(selector).slideToggle("slow").delay(30000).slideToggle("slow");
	}
	
	var checkboxStateAjaxQuerySuccess = function(data) {
		if(data && data.hasOwnProperty('user_id') && data.user_id == 0)
			showMessage('.bb_signup_checkbox_response', data.error + ' Please, log in or register <a href="/shop">here</a>');
	}
	
	$(".bb_signup_checkbox_inp").change(function() {
		var data = {};
		data.action = 'bb_signup_checkbox_changed';
		
		($(this).attr('checked') == 'checked') ? data.state = 1 : data.state = 0;
		
		sendAjaxQuery(data, checkboxStateAjaxQuerySuccess);
	});
});