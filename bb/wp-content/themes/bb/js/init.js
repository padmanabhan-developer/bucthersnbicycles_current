/*-----------------------------------------------------------------------------------
/*
/* Init JS
/*
-----------------------------------------------------------------------------------*/

 jQuery(document).ready(function($) {
	
	/*----- animated page scroll -------*/
	function scrollingTo(id){
		$('html,body').animate({scrollTop: ($(id).offset().top)},  900);
	}
	$('#menu-navigation-menu a, #right-menu a, a.scroll-link').live('click', function() {
		var target = $(this).attr('href');
		scrollingTo(target);
		return false
	});
	/*----- end animated page scroll -------*/
	
	/*----- sub-menu show -------*/
	
	$(".sub-menu").each(function(){
		var widthListPr = 0;
		$(this).children('li').each(function() {
			widthListPr = widthListPr + $(this).width() + 1;
		});
		$(this).width(widthListPr).css('margin-left',-(widthListPr/2 + 8)).css('opacity','1').hide();
	});

	$('#menu-navigation-menu>li').hover(function() {
		$(this).css('paddingBottom','15px');
		$(this).find('.sub-menu').fadeIn(300);
	}, function () {
		$(this).css('paddingBottom','0');
		$(this).find('.sub-menu').hide();
	});
	/*----- end sub-menu show -------*/
	
	/*----- right menu animated text -------*/
	$('.nav-anchor li').hover(
		function() {
			$(this).find('span').animate({
			right: "38px",
			opacity: "0.7",
			},  {
				duration: 300,
				specialEasing: {
					opacity: 'linear',
					right: 'swing'
				}
			});
		},
		function() {
			$(this).find('span').animate({
			right: "-250px",
			opacity: "0",
			},  {
				duration: 0,
				specialEasing: {
					opacity: 'linear',
					right: 'swing'
				}
			});
		}
	);
	$('.share-list li').hover(
		function() {
			$(this).find('ul').slideDown(300);
		}, function() {
			$(this).find('ul').slideUp(100);
		}
	);
	/*----- end right menu animated text -------*/
	
 	$('input.gc-conf:first').attr('checked', true);
 	
 	/*----- Custom Form -------*/
    jQuery.fn.customBtns = {
        init: function () {
            jQuery("input[type='radio'], input[type='checkbox'].customCheckbox").each(function(){
                if(!jQuery(this).parent().hasClass("checkArea")) {
                    jQuery(this).wrap('<span class="checkArea"></span>');
                }
                if (jQuery(this).is(':checked')){
                    jQuery(this).closest('.checkArea').addClass('checkAreaChecked');
                }
                else {
                    jQuery(this).closest('.checkArea').removeClass('checkAreaChecked');
                }
            });
            jQuery("input[type='radio']").each(function(){
                if (jQuery(this).hasClass('green-radio_btn')){
                    jQuery(this).closest('span').addClass('checkArea-green');
                }
            });
            
            this.bindChange("input[type='radio']");
            this.bindChange("input[type='checkbox']");
       },
       
       addInputClass: function (selector) {
            jQuery(selector).each(function(){
                if(jQuery(this).is(':checked')){
                    jQuery(this).closest('.checkArea').addClass('checkAreaChecked');
                } else{
                    jQuery(this).closest('.checkArea').removeClass('checkAreaChecked');
                }
            });
        },
        
        bindChange: function (selector) {
            var that = this;
            jQuery(selector).unbind("change", clickHandler);
            jQuery(selector).change(clickHandler);
            
            function clickHandler() {
                that.addInputClass(selector);
            } 
        }
    };
    
    jQuery.fn.customBtns.init();
    
	/*-----End Custom Form -------*/
	
	// Add out of ctock text
	$("li[data-in-stock='0'], div[data-in-stock='0']").each(function() {
		var CurrentTag = $(this);
		CurrentTag.find('input').attr("disabled","disabled");
		CurrentTag.addClass('DataCostWrapper');
		$('<span class="out-stock-text">Out of stock</span>').appendTo(CurrentTag);
	});
	
/*----------------------------------------------------*/
/* Dropdown menu
/*----------------------------------------------------*/


  //var drop = $('.menu_header > ul');
   //drop.dropotron();


/*----------------------------------------------------*/
/* scroll
/*----------------------------------------------------*/

	// function getAnchors() {
	// 	var anchors = new Array();
	// 	$("#right-menu>li").each(function() {
	// 		anchors.push($(this).attr("data-menuanchor"));
	// 	});

	// 	return anchors;
	// }

	// var pepe = $.fn.fullpage({
	// 	scrollingSpeed: 700,
	// 	easing: 'easeInExpo',
	// 	resize: false,
	// 	verticalCentered: false,
	// 	scrollOverflow:true,
	// 	//anchors: ['discover', 'videos', 'products', 'about', 'contact-form', 'where', 'contact-maps'],
	// 	anchors: getAnchors(),
	// 	menu: '#right-menu'
	// });

/*----------------------------------------------------*/
/* responsive menu
/*----------------------------------------------------*/

	jQuery('#nav').meanmenu();

/*----------------------------------------------------*/
/* slider share button
/*----------------------------------------------------*/

	jQuery("#head-discover .ml-slider").unbind();
	SocialShare.showShareButton("#head-discover .ml-slider", "#head-discover .share-wrapper");
	
	jQuery(".share-wrapper").unbind("click");
	jQuery(".share-wrapper").click(function() {
		SocialShare.title = jQuery(this).parent().find(".bike-model").text();
		SocialShare.description = jQuery(this).parent().find("h1").text();
		SocialShare.image = jQuery(this).parent().find("img").attr("src");
		jQuery('#bbshare_popup_dialog').dialog("open");
	});
	
/*----------------------------------------------------*/
/* image map share button
/*----------------------------------------------------*/
	
	jQuery(".image-map-wrapper").unbind();
	SocialShare.showShareButton(".image-map-wrapper", ".bbimg-share-wrapper");

	jQuery(".bbimg-share-wrapper").unbind("click");
	jQuery('.bbimg-share-wrapper').click(function() {
		SocialShare.title = jQuery(this).parent().find("input[type='radio']:checked").val();
		SocialShare.image = jQuery(this).parent().find("img").attr("src");
		jQuery('#bbshare_popup_dialog').dialog("open");
	});

/*----------------------------------------------------*/
/* configuration share button
/*----------------------------------------------------*/

	jQuery(".gc-conf-img").unbind();
	SocialShare.showShareButton(".gc-conf-img", ".conf-share-wrapper");

	jQuery(".conf-share-wrapper").unbind("click");
	jQuery(".conf-share-wrapper").click(function() {
		SocialShare.title = jQuery(".gc-conf-name").find("input[type='radio']:checked").parent().parent().parent().find("p.gc-conf-name").text();
		SocialShare.image = jQuery(this).parent().find("img").attr("src");
		jQuery('#bbshare_popup_dialog').dialog("open");
	});

/*----------------------------------------------------*/
/* constructor share button
/*----------------------------------------------------*/

	jQuery(".bc_featured_image").unbind();
	SocialShare.showShareButton(".bc_featured_image", ".constr-share-wrapper");

	jQuery(".constr-share-wrapper").unbind("click");
	jQuery(".constr-share-wrapper").click(function() {
		SocialShare.image = jQuery(this).parent().find("img").attr("src");
		SocialShare.title = jQuery("input.main_part:checked").attr("data-main-part-name");
		jQuery("input.external_detail:checked").each(function() {
			SocialShare.title += ", " + jQuery(this).attr("data-external-item-name");
		});

		jQuery('#bbshare_popup_dialog').dialog("open");
	});
	
 }); // end of document.ready function 