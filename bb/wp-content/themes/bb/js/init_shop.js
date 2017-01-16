jQuery(document).ready(function($) {
/*----------------------------------------------------*/
/* scroll
/*----------------------------------------------------*/
/*
function getAnchors () {
		var anchors = new Array();
		$('div.section').each(function() {
			if ( $(this).attr('id') !== 'undefined')
				anchors.push($(this).attr('id'));
		});
		console.log(anchors);
		return anchors;
	}
*/

	var pepe = $.fn.fullpage({
		scrollingSpeed: 700,
		easing: 'easeInExpo',
		resize: false,
		verticalCentered: false,
		scrollOverflow:true,
		//anchors: getAnchors(),
		anchors: ['welcome-page', 'user-profile', 'configure'],
		menu: '#right-menu'
	});


/*----------------------------------------------------*/
/* scroll
/*----------------------------------------------------*/

	$('input.gc-conf:first').attr('checked', true);
		
		/*----- Custom Form -------*/
	jQuery("input[type='radio'], input[type='checkbox'].customCheckbox").each(function(){
		jQuery(this).wrap('<span class="checkArea"></span>');
		if (jQuery(this).is(':checked')){
			jQuery(this).closest('.checkArea').addClass('checkAreaChecked');
		}
	});
	jQuery("input[type='radio']").each(function(){
		if (jQuery(this).hasClass('green-radio_btn')){
			jQuery(this).closest('span').addClass('checkArea-green');
		}
	});    
	//chekbox
	jQuery("input[type='checkbox']").change(function(){
	       if(jQuery(this).is(':checked')){
	           jQuery(this).closest('.checkArea').addClass('checkAreaChecked')
	       } else{
	           jQuery(this).closest('.checkArea').removeClass('checkAreaChecked')
	       }
	   });
	//radio buttons
	jQuery("input[type='radio']").change(function(){
		jQuery("input[type='radio']").each(function(){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.checkArea').addClass('checkAreaChecked');
			} else{
				jQuery(this).closest('.checkArea').removeClass('checkAreaChecked');
			}
		});
	 });
	/*-----End Custom Form -------*/
});


