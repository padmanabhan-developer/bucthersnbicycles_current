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

	var deleteUserSuccess = function(data)
	{
		if(data && data.hasOwnProperty('error')) {
			showMessage(".delete_user_message", data.error);
		}
	}
	
	function showMessage(selector, message) {
		jQuery(selector).empty();
		jQuery(selector).append('<p>' + message + '</p>');
		jQuery(selector).slideToggle("slow").delay(3000).slideToggle("slow");
	}

	function delete_me()
	{
		var data = {};
	 	data.action = "bbdelete_user_ajax";

	 	sendAjaxQuery(data, deleteUserSuccess);
	}

	$('#delete_user_account_btn').click(function(){
		$("#delete_confirm").dialog("open");
	});
	
	$('#delete_user_account_2').click(function(event) {
		event.preventDefault();
		$("#delete_confirm").dialog("open");
	});

	$(function() {
		$("#delete_confirm").dialog({
			resizable: false,
			autoOpen: false
		});
	})
	
	$("#deleteUserConfirmed").click(function() {
		delete_me();
		$("#delete_confirm").dialog("close");
	});
	
	$("#deleteUserCancel").click(function() {
		$("#delete_confirm").dialog("close");
	});
});