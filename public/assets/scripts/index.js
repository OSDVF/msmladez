/**
 * Adds a simple parallax effect to an element.
 *
 * @param elementSelector a jQuery-like selector to identify the animated
 * 		element
 * @param updateFn a function executed on each scroll event that should update
 * 		the element. Receives the object's styles as the first argument and
 * 		window.pageYOffset as the second argument.
 */
function addParallaxEffect(elementSelector, updateFn) {
	window.addEventListener("load", function () {
		const element = document.querySelector(elementSelector);

		if (!element) {
			return;
		}

		window.addEventListener("scroll", function () {
			updateFn(element.style, window.pageYOffset);
		});

		updateFn(element.style, window.pageYOffset);
	});
}

// Add a parallax effect to the blue and yellow header backgrounds (which are
// both set on the same element).
addParallaxEffect(
	".page-jumbotron-header",
	function (style, offset) {
		const BLUE_BG_TOP_OFFSET = 40; // Needs to be kept in sync with the styles.
		const SCROLL_SPEED = 0.1;

		const blueBgPosition =
			"center " + (BLUE_BG_TOP_OFFSET + offset * SCROLL_SPEED) + "px";
		const yellowBgPosition = "center " + -1 * offset * SCROLL_SPEED + "px";

		style.backgroundPosition = blueBgPosition + ", " + yellowBgPosition;
	});

// Add a parallax effect to the header text.
addParallaxEffect(
	".front-page__header h1",
	function (style, offset) {
		const SCROLL_SPEED = 0.011;
		const FADE_SPEED = 0.003;

		style.transform = "translateY(" + offset * SCROLL_SPEED + "px)";
		style.opacity = Math.max(1 - offset * FADE_SPEED, 0);
	});

// Add a parallax effect to the header boat image.
addParallaxEffect(
	".front-page__header__boat",
	function (style, offset) {
		const SCROLL_SPEED = 0.05;
		const FADE_SPEED = 0.002;

		style.transform = "translateY(" + offset * SCROLL_SPEED + "px)";
		style.opacity = Math.max((1 - offset * FADE_SPEED) * 0.9, 0);
	});


function fbPost(page, fbId) {
	var a = new URL(page);
	a.searchParams.set('fbId', fbId);
	location.href = a.href;
}
