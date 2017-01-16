jQuery(document).ready(function($) {
	
	$('input.bb_imagemap_slide_input:first').attr('checked', true);

	$('input.bb_imagemap_slide_input').change(function() {
		var data = {
			id:$('input.bb_imagemap_slide_input:checked').attr('id').substring(4),
			title:$('input.bb_imagemap_slide_input:checked').val()
		}
		sendModelData(data);
	});

	function displayImage(image_url) {
		var html = '<div class="map-image-holder"><img src="' + image_url + '" alt="imagemap"/></div>';

		return html;
	}

	var changeModel = function (data) {
		var json = JSON.parse(data);
		var image = displayImage(json.img);

		$('.map-image-holder').replaceWith(image);
		$('a.display_tech').attr('data-name', json.title);

		MapImageIcon.showIconSet(json.coords);
	}

	function sendModelData(model) {

		model.action = 'bbimg_display_map';

		$.ajax({
			type: 'post',
			url: '/wp-admin/admin-ajax.php',
			data: model,
			success	: changeModel
		});
	}

	var sendPostRequestSuccess = function(data) {
		if (data && data.hasOwnProperty('success') && data.success == 1)
		{
			$('.techdialog-mpname').text(data.name);
			$('.techdialog-mpdescr').html($('<div />').html(data.descr).text());
			$('.techdialog-epdescr').html(displayExternal(data.external));
			//in php file
			showModal('.tech-dialog');
		}
		else
		{
			alert(data.error);
		}
		//console.log(data);
	}

	function displayExternal(external) {
		var html = '';
		for (var i = 0; i < external.length; i++) {
				html += '<p>'+external[i].name+' <span>'+
				external[i].descr+'</span></p>';
		};

		return html;
	}

	$('.display_tech').click(function() {
		
		var data = {};
		data.action = 'get_tech_details';
		data.name = $(this).attr('data-name');

		sendPostRequest(data, sendPostRequestSuccess);
		return false;
	});

	function sendPostRequest(data, success)
	{
		$.ajax({
			type: 'post',
			url: '/wp-admin/admin-ajax.php',
			dataType: 'json',
			data: data,
			success: success
		});
	}
	
	// Show icons with tooltips
	MapImageIcon.showIconSet(iconSet);
});