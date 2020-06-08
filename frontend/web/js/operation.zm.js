/**
 * Created by Administrator on 2014/9/13.
 */
(function($) {

	$(function() {

		if ($('.flexslider').size() > 0) {
			$('.flexslider').flexslider({
				directionNav: false,
				pauseOnAction: false
			});
		}

		if ($("#bannerbg").size() > 0) {
			$("#bannerbg").Slide({
				effect: "fade",
				speed: 600,
				timer: 5000
			});
		}
		
		if ($('.j-product-scroll').size() > 0) {
			$('.j-product-scroll').carouFredSel({
				responsive: true,
				direction: "left",
				prev: '.j-prev-1',
				next: '.j-next-1',
				width: '100%',
				auto: {
					play: true,
					pauseOnHover: true,
					timeoutDuration: 3000
				},
				pause: 3000,
				scroll: 1,
				items: {
					visible: {
						min: 3,
						max: 3
					}
				}
			});
		}

	});

}(jQuery));