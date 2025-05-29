document.addEventListener("DOMContentLoaded", function () {
	var total = document.querySelectorAll("[nav-pills]");

	total.forEach(function (item, i) {
		var moving_div = document.createElement("div");
		var first_li = item.querySelector("li:first-child [nav-link]");
		var tab = first_li.cloneNode();
		tab.innerHTML = "-";
		tab.classList.remove("bg-inherit");
		tab.classList.add("bg-white", "text-white", "shadow-soft-xxs");
		tab.style.animation = ".2s ease";

		moving_div.classList.add(
			"z-10",
			"absolute",
			"text-slate-700",
			"rounded-lg",
			"bg-inherit",
			"flex-auto",
			"text-center",
			"bg-none",
			"border-0",
			"block"
		);
		moving_div.setAttribute("moving-tab", "");
		moving_div.setAttribute("nav-link", "");
		moving_div.appendChild(tab);
		item.appendChild(moving_div);

		var list_length = item.getElementsByTagName("li").length;

		moving_div.style.boxShadow = "0 1px 5px 1px #ddd";
		moving_div.style.padding = "0px";
		moving_div.style.width =
			item.querySelector("li:nth-child(1)").offsetWidth + "px";
		moving_div.style.transform = "translate3d(0px, 0px, 0px)";
		moving_div.style.transition = ".5s ease";

		item.onmouseover = function (event) {
			let target = event.target || event.srcElement;
			let li = target.closest("li");
			if (li) {
				let nodes = Array.from(li.closest("ul").children);
				let index = nodes.indexOf(li) + 1;
				item.querySelector("li:nth-child(" + index + ") [nav-link]").onclick =
					function () {
						item.querySelectorAll("li").forEach(function (list_item) {
							list_item.firstElementChild.removeAttribute("active");
						});
						li.firstElementChild.setAttribute("active", "");
						moving_div = item.querySelector("[moving-tab]");
						let sum = 0;
						if (item.classList.contains("flex-col")) {
							for (var j = 1; j <= nodes.indexOf(li); j++) {
								sum += item.querySelector(
									"li:nth-child(" + j + ")"
								).offsetHeight;
							}
							moving_div.style.transform =
								"translate3d(0px," + sum + "px, 0px)";
							moving_div.style.height = item.querySelector(
								"li:nth-child(" + j + ")"
							).offsetHeight;
						} else {
							for (var j = 1; j <= nodes.indexOf(li); j++) {
								sum += item.querySelector(
									"li:nth-child(" + j + ")"
								).offsetWidth;
							}
							moving_div.style.transform =
								"translate3d(" + sum + "px, 0px, 0px)";
							moving_div.style.width =
								item.querySelector("li:nth-child(" + index + ")").offsetWidth +
								"px";
						}
					};
			}
		};
	});
});
