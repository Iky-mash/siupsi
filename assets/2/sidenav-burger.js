document.addEventListener("DOMContentLoaded", function () {
	console.log("sidenav-burger.js: DOMContentLoaded, mulai eksekusi."); // LOG 0

	const sidenav = document.querySelector("aside");
	const sidenav_trigger = document.querySelector("[sidenav-trigger]");
	const sidenav_close_button = document.querySelector("[sidenav-close]");

	// --- Pengecekan Elemen ---
	if (!sidenav) {
		console.error(
			"sidenav-burger.js: Elemen <aside> TIDAK DITEMUKAN. Sidenav tidak akan berfungsi."
		);
		return; // Keluar jika elemen inti tidak ada
	}
	console.log("sidenav-burger.js: Elemen <aside> DITEMUKAN:", sidenav);

	if (!sidenav_trigger) {
		console.error(
			"sidenav-burger.js: Elemen [sidenav-trigger] TIDAK DITEMUKAN. HTML Anda HARUS memiliki elemen dengan atribut 'sidenav-trigger'. Sidenav tidak akan berfungsi."
		);
		return; // Keluar jika elemen inti tidak ada
	}
	console.log(
		"sidenav-burger.js: Elemen [sidenav-trigger] DITEMUKAN:",
		sidenav_trigger
	);

	if (sidenav_close_button) {
		console.log(
			"sidenav-burger.js: Elemen [sidenav-close] DITEMUKAN:",
			sidenav_close_button
		);
	} else {
		console.warn(
			"sidenav-burger.js: Elemen [sidenav-close] TIDAK DITEMUKAN. Sidenav tidak bisa ditutup dengan tombol close internal."
		);
	}

	// --- Bagian untuk animasi burger icon (sesuaikan dengan struktur HTML Anda jika diperlukan) ---
	// Ini berdasarkan struktur HTML trigger yang Anda berikan: <a><div><i></i><i></i><i></i></div></a>
	let burger = null;
	let top_bread = null;
	let bottom_bread = null;

	if (
		sidenav_trigger.firstElementChild &&
		sidenav_trigger.firstElementChild.tagName === "DIV"
	) {
		burger = sidenav_trigger.firstElementChild;
		if (burger.firstElementChild && burger.firstElementChild.tagName === "I") {
			top_bread = burger.firstElementChild;
		}
		// Untuk bottom_bread, kita perlu mengambil anak terakhir dari burger
		// Jika ada 3 <i>, burger.lastElementChild akan benar.
		if (burger.lastElementChild && burger.lastElementChild.tagName === "I") {
			bottom_bread = burger.lastElementChild;
		}
		// console.log("sidenav-burger.js: Elemen burger parts:", burger, top_bread, bottom_bread);
	} else {
		// console.warn("sidenav-burger.js: Struktur anak elemen [sidenav-trigger] untuk animasi burger tidak sesuai harapan.");
	}
	// --- Akhir bagian animasi burger ---

	// Fungsi untuk membuka sidenav
	function openSidenav() {
		if (sidenav.classList.contains("-translate-x-full")) {
			// Hanya buka jika sedang tertutup
			sidenav.classList.remove("-translate-x-full");
			sidenav.classList.add("translate-x-0");
			sidenav.classList.add("shadow-soft-xl"); // Kelas dari HTML asli Anda
			if (sidenav_close_button) sidenav_close_button.classList.remove("hidden"); // Tampilkan tombol close jika ada
			console.log("sidenav-burger.js: MEMBUKA sidenav.");
		}
		// Update ARIA attribute
		sidenav_trigger.setAttribute("aria-expanded", "true");
	}

	// Fungsi untuk menutup sidenav
	function closeSidenav() {
		if (sidenav.classList.contains("translate-x-0")) {
			// Hanya tutup jika sedang terbuka
			sidenav.classList.remove("translate-x-0");
			sidenav.classList.add("-translate-x-full");
			sidenav.classList.remove("shadow-soft-xl");
			if (sidenav_close_button) sidenav_close_button.classList.add("hidden"); // Sembunyikan tombol close jika ada
			console.log("sidenav-burger.js: MENUTUP sidenav.");
		}
		// Update ARIA attribute
		sidenav_trigger.setAttribute("aria-expanded", "false");
	}

	// Event Listener untuk Sidenav Trigger
	sidenav_trigger.addEventListener("click", function (event) {
		event.preventDefault(); // Mencegah aksi default dari <a> tag jika href="#"
		console.log("sidenav-burger.js: Tombol [sidenav-trigger] DIKLIK!"); // LOG 1

		// !!! PERHATIAN PENTING: Variabel 'page' !!!
		// Jika Anda tidak menggunakan logika 'page', pastikan bagian ini dikomentari atau dihapus.
		// Jika Anda menggunakannya, pastikan variabel 'page' sudah didefinisikan.
		/*
        if (typeof page !== 'undefined' && page == "virtual-reality") {
            console.log("sidenav-burger.js: Kondisi page virtual-reality terpenuhi.");
            // Logika spesifik untuk halaman virtual-reality (misalnya, sidenav.classList.toggle("xl:left-[18%]"))
            // Untuk sekarang, ini tidak mempengaruhi buka/tutup dasar
        }
        */

		// Logika buka/tutup eksplisit
		if (sidenav.classList.contains("-translate-x-full")) {
			openSidenav();
		} else {
			closeSidenav();
		}

		console.log(
			"sidenav-burger.js: Sidenav classList setelah diubah:",
			sidenav.classList
		); // LOG 2
		console.log(
			"sidenav-burger.js: Apakah sidenav terbuka (memiliki 'translate-x-0')?",
			sidenav.classList.contains("translate-x-0")
		); // LOG 3

		// !!! PERHATIAN PENTING: Animasi Burger dengan Variabel 'page' !!!
		// Jika Anda tidak menggunakan logika 'page' atau animasi burger ini, komentari/hapus.
		/*
        if (top_bread && bottom_bread) {
            if (typeof page !== 'undefined' && page == "rtl") {
                top_bread.classList.toggle("-translate-x-[5px]");
                bottom_bread.classList.toggle("-translate-x-[5px]");
            } else {
                top_bread.classList.toggle("translate-x-[5px]");
                bottom_bread.classList.toggle("translate-x-[5px]");
            }
        }
        */
	});

	// Event Listener untuk Tombol Tutup Sidenav (jika ada)
	if (sidenav_close_button) {
		sidenav_close_button.addEventListener("click", function (event) {
			event.preventDefault();
			console.log("sidenav-burger.js: Tombol [sidenav-close] DIKLIK!"); // LOG 4
			closeSidenav(); // Langsung panggil fungsi closeSidenav
		});
	}

	// Event Listener untuk Klik di Luar Sidenav (Menutup Sidenav)
	window.addEventListener("click", function (e) {
		const clickedTarget = e.target;

		// Pengecekan apakah target adalah Node yang valid
		if (!(clickedTarget instanceof Node)) {
			// Pesan ini normal jika ada skrip seperti simulator.js
			// console.warn("sidenav-burger.js: (Window Click) Target klik bukan Node yang valid:", clickedTarget);
			return;
		}

		// Hanya proses jika sidenav sedang terbuka
		if (sidenav.classList.contains("translate-x-0")) {
			const isClickInsideSidenav = sidenav.contains(clickedTarget);
			// Perlu dicek apakah sidenav_trigger ada dan apakah klik terjadi padanya atau anaknya
			const isClickOnTriggerOrChild =
				sidenav_trigger && sidenav_trigger.contains(clickedTarget);

			if (!isClickInsideSidenav && !isClickOnTriggerOrChild) {
				console.log(
					"sidenav-burger.js: (Window Click) Klik di luar, menutup sidenav."
				); // LOG 5
				closeSidenav();
			}
		}
	});
}); // Akhir dari DOMContentLoaded
