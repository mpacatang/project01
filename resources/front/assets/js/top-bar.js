
$(document).ready(function() {
	if ($(window).scrollTop() > 40) {
		$('.top-bar-box').addClass('scrolled')
	}

	$(window).scroll(function() {
		if ($(window).scrollTop() > 40) {
			$('.top-bar-box').addClass('scrolled')
		} else {
			$('.top-bar-box').removeClass('scrolled')
		}
	})
})