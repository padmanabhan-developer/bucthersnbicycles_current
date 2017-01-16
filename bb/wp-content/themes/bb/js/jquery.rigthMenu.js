 jQuery(document).ready(function($) {
	$('.nav-anchor li').hover(
		function() {
            var span = $(this).find('span');
            //span.css("z-index","1000");
			span.animate({
			right: "38px",
			opacity: "1",
			},  {
				duration: 400,
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
				duration: 10,
				specialEasing: {
					opacity: 'linear',
					right: 'swing'
				}
			});
		}
	);
	$('.share-list li').hover(
		function() {
			$(this).find('ul').slideDown(400);
		}, function() {
			$(this).find('ul').slideUp(400);
		}
	);

	//var headerHeight = $('#header').outerHeight();

});