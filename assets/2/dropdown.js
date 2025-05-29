// Navbar notifications dropdown

var dropdown_triggers = document.querySelectorAll("[dropdown-trigger]");
dropdown_triggers.forEach((dropdown_trigger) => {
	let dropdown_menu =
		dropdown_trigger.parentElement.querySelector("[dropdown-menu]");

	if (!dropdown_menu) {
		// console.warn("Dropdown trigger found without a corresponding dropdown-menu:", dropdown_trigger);
		return; // Lewati jika tidak ada menu
	}

	dropdown_trigger.addEventListener("click", function () {
		// Logika untuk membuka/menutup dropdown (sepertinya sudah benar)
		dropdown_menu.classList.toggle("opacity-0");
		dropdown_menu.classList.toggle("pointer-events-none");
		dropdown_menu.classList.toggle("before:-top-5");
		if (dropdown_trigger.getAttribute("aria-expanded") == "false") {
			dropdown_trigger.setAttribute("aria-expanded", "true");
			dropdown_menu.classList.remove("transform-dropdown");
			dropdown_menu.classList.add("transform-dropdown-show");
		} else {
			dropdown_trigger.setAttribute("aria-expanded", "false");
			dropdown_menu.classList.remove("transform-dropdown-show");
			dropdown_menu.classList.add("transform-dropdown");
		}
	});

	// Listener untuk menutup dropdown saat klik di luar
	window.addEventListener("click", function (e) {
		const clicked_target = e.target; // Ambil target dari event

		// ---- PERBAIKAN PENTING ----
		// Pastikan clicked_target adalah instance dari Node sebelum menggunakan .contains()
		if (!(clicked_target instanceof Node)) {
			// Jika bukan Node, lebih baik jangan lanjutkan untuk menghindari error.
			// Ini adalah kasus yang jarang terjadi, tapi error Anda menunjukkan ini mungkin terjadi.
			// console.warn("Event target is not a valid Node:", clicked_target);
			return;
		}
		// ---- AKHIR PERBAIKAN ----

		// Cek apakah klik terjadi di dalam menu atau pada trigger itu sendiri
		// Elemen dropdown_menu dan dropdown_trigger sendiri sudah pasti Node karena ditemukan oleh querySelector
		// dan kita sudah cek dropdown_menu tidak null.
		const isClickInsideMenu = dropdown_menu.contains(clicked_target);
		const isClickOnTrigger = dropdown_trigger.contains(clicked_target);

		if (!isClickInsideMenu && !isClickOnTrigger) {
			// Jika klik di luar menu DAN di luar trigger
			if (dropdown_trigger.getAttribute("aria-expanded") == "true") {
				// Tutup dropdown dengan mensimulasikan klik pada trigger
				dropdown_trigger.click();
			}
		}
	});
});
