// dropdown.js
document.addEventListener("DOMContentLoaded", function () {
	const dropdownTriggers = document.querySelectorAll("[dropdown-trigger]");

	if (dropdownTriggers.length === 0) {
		// Tidak ada elemen [dropdown-trigger] di halaman ini.
		// Anda bisa mengabaikan pesan ini jika memang tidak ada dropdown.
		// console.log("Info: Tidak ada elemen [dropdown-trigger] yang ditemukan untuk dropdown.js.");
		return; // Hentikan jika tidak ada trigger
	}

	dropdownTriggers.forEach((dropdownTrigger) => {
		// Cari dropdown-menu sebagai sibling atau di dalam parent yang sama
		const parent = dropdownTrigger.parentElement;
		const dropdownMenu = parent
			? parent.querySelector("[dropdown-menu]")
			: null;

		if (!dropdownMenu) {
			console.warn(
				"Peringatan: Elemen [dropdown-menu] tidak ditemukan untuk trigger:",
				dropdownTrigger
			);
			return; // Lanjut ke trigger berikutnya jika menu tidak ada
		}

		dropdownTrigger.addEventListener("click", function (event) {
			event.stopPropagation(); // Mencegah klik menyebar ke window listener, yang mungkin langsung menutupnya.
			const isExpanded =
				dropdownTrigger.getAttribute("aria-expanded") === "true";

			// Tutup semua dropdown lain yang mungkin sedang terbuka
			document
				.querySelectorAll("[dropdown-trigger][aria-expanded='true']")
				.forEach((otherTrigger) => {
					if (otherTrigger !== dropdownTrigger) {
						const otherMenu =
							otherTrigger.parentElement.querySelector("[dropdown-menu]");
						if (otherMenu) {
							otherTrigger.setAttribute("aria-expanded", "false");
							otherMenu.classList.remove("transform-dropdown-show");
							otherMenu.classList.add(
								"transform-dropdown",
								"opacity-0",
								"pointer-events-none"
							);
							// Jika ada kelas 'before:-top-5' untuk arrow, tambahkan juga
							otherMenu.classList.add("before:-top-5");
						}
					}
				});

			// Toggle dropdown saat ini
			if (!isExpanded) {
				dropdownTrigger.setAttribute("aria-expanded", "true");
				dropdownMenu.classList.remove(
					"transform-dropdown",
					"opacity-0",
					"pointer-events-none",
					"before:-top-5"
				);
				dropdownMenu.classList.add("transform-dropdown-show");
			} else {
				dropdownTrigger.setAttribute("aria-expanded", "false");
				dropdownMenu.classList.remove("transform-dropdown-show");
				dropdownMenu.classList.add(
					"transform-dropdown",
					"opacity-0",
					"pointer-events-none",
					"before:-top-5"
				);
			}
		});
	});

	// Listener global untuk menutup dropdown jika diklik di luar
	window.addEventListener("click", function (e) {
		if (!(e.target instanceof Node)) {
			return;
		}

		document
			.querySelectorAll("[dropdown-trigger][aria-expanded='true']")
			.forEach((openTrigger) => {
				const openMenu =
					openTrigger.parentElement.querySelector("[dropdown-menu]");
				// Cek apakah klik terjadi di luar trigger DAN di luar menu yang sedang terbuka
				if (
					openMenu &&
					!openTrigger.contains(e.target) &&
					!openMenu.contains(e.target) &&
					openTrigger !== e.target // Pastikan bukan klik pada trigger itu sendiri
				) {
					openTrigger.setAttribute("aria-expanded", "false");
					openMenu.classList.remove("transform-dropdown-show");
					openMenu.classList.add(
						"transform-dropdown",
						"opacity-0",
						"pointer-events-none",
						"before:-top-5"
					);
				}
			});
	});
});
