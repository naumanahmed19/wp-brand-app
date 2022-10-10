//TODO: Refactoring...
document.addEventListener("DOMContentLoaded", function () {
	const ele = document.querySelectorAll(".mdc-tab-bar");
	if (!ele) return;

	ele.forEach((tabElement, index) => {
		var tabBar = new mdc.tabBar.MDCTabBar(tabElement);
		let tabWrap = document.querySelectorAll(".tab-wrap");
		var contentEls = tabWrap[index].querySelectorAll(".brand-tab-screen");
		contentEls[0].classList.add("brand-tab-screen--active");
		tabBar.listen("MDCTabBar:activated", function (event) {
			// Hide currently-active content

			if (tabWrap[index].querySelector(".brand-tab-screen--active"))
				tabWrap[index]
					.querySelector(".brand-tab-screen--active")
					.classList.remove("brand-tab-screen--active");
			// Show content for newly-activated tab
			contentEls[event.detail.index].classList.add("brand-tab-screen--active");
		});
	});
});

/**
 * @component Accordion component
 * @param {element} target
 * @param {*} one
 */
function brandAccrdionComponent(one = true) {
	let targets = document.querySelectorAll(".brand-accordion");
	if (targets.length) {
		targets.forEach((target) => {
			// (A) ADD CSS CLASS TO TARGET
			target.classList.add("awrap");
			// (B) ATTACH ONCLICK
			let all = target.querySelectorAll("li");
			if (typeof one != "boolean") {
				one = false;
			}
			for (let i = 0; i < all.length; i++) {
				if (i % 2 == 0) {
					all[i].classList.add("ahead");
					if (one) {
						all[i].onclick = () => {
							if (all[i].classList.contains("open")) {
								all[i].classList.remove("open");
							} else {
								for (let i = 0; i < all.length; i += 2) {
									all[i].classList.remove("open");
								}
								all[i].classList.add("open");
							}
						};
					} else {
						all[i].onclick = () => {
							all[i].classList.toggle("open");
						};
					}
				} else {
					all[i].classList.add("abody");
				}
			}
		});
	}
}

/**
 * @component Slick slider init
 *
 */
// function silckSlicer() {
// 	var light = jQuery(".lightSlider");
// 	light.each(function () {
// 		var $this = jQuery(this);
// 		$this
// 			.not(".slick-initialized")
// 			.slick({
// 				centerMode: $this.data("center-mode"),
// 				centerPadding: $this.data("center-padding"),
// 				slidesToShow: $this.data("item"),
// 				prevArrow: null,
// 				nextArrow: null,
// 				responsive: [
// 					{
// 						breakpoint: 768,
// 						settings: {
// 							arrows: false,
// 							centerMode: true,
// 							centerPadding: "40px",
// 							slidesToShow: 3,
// 						},
// 					},
// 					{
// 						breakpoint: 480,
// 						settings: {
// 							arrows: false,
// 							centerMode: true,
// 							centerPadding: "40px",
// 							slidesToShow: 1,
// 						},
// 					},
// 				],
// 			})
// 			.slick("refresh");
// 	});
// }

// /**
//  * Refresh slick slider
//  * @description on tab change slick should be refresh to display carousels
//  * Todo: Change class name
//  */

//TODO: Refactoring...
document.addEventListener("DOMContentLoaded", function () {
	console.log("load slider");

	// silckSlicer();
	// setTimeout(() => {
	// 	silckSlicer();
	// 	brandAccrdionComponent();
	// }, 2000);

	// jQuery(document).on("click", ".components-button", function () {
	// 	//jQuery(".lightSlider").slick("refresh");
	// 	silckSlicer();
	// });

	jQuery(".editor-styles-wrapper").click(function (e) {
		e.stopPropagation();
	});

	/**
	 * Refresh slider
	 */

	// jQuery(document).on("click", ".mdc-tab", function () {
	// 	console.log("refresh now...");

	// 	silckSlicer();
	// });
});

window;
