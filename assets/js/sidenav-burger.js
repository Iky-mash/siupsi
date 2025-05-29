// sidenav-burger.js
document.addEventListener("DOMContentLoaded", function () {
	const sidenav = document.querySelector("aside");
	const sidenavTrigger = document.querySelector("[sidenav-trigger]");
	const sidenavCloseButton = document.querySelector("[sidenav-close]"); // Elemen ini ada di HTML Anda

	// Logika untuk variabel 'page' dari kode asli Anda:
	// Anda perlu cara untuk menentukan 'page' jika logika ini penting.
	// Misalnya, Anda bisa mengatur ID pada tag <body> dan mengambilnya:
	// const page = document.body.id;
	// Untuk saat ini, logika yang bergantung 'page' akan dikomentari agar tidak error.

	if (!sidenav) {
		console.error(
			"Kesalahan: Elemen <aside> (sidenav) tidak ditemukan di HTML."
		);
		return; // Hentikan eksekusi jika elemen penting tidak ada
	}
	if (!sidenavTrigger) {
		console.error(
			"Kesalahan: Elemen [sidenav-trigger] tidak ditemukan di HTML."
		);
		return; // Hentikan eksekusi
	}

	// Dapatkan elemen untuk animasi burger dengan aman
	const burger = sidenavTrigger.firstElementChild;
	const topBread = burger ? burger.firstElementChild : null;
	const bottomBread = burger ? burger.lastElementChild : null;

	if (!burger || !topBread || !bottomBread) {
		console.warn(
			"Peringatan: Struktur internal [sidenav-trigger] tidak lengkap. Animasi burger mungkin tidak bekerja."
		);
	}

	sidenavTrigger.addEventListener("click", function () {
		// Contoh logika 'page' yang dikomentari:
		// if (page == "virtual-reality") {
		//     sidenav.classList.toggle("xl:left-[18%]"); // Pastikan kelas ini ada dan sesuai
		// }

		if (sidenavCloseButton) {
			sidenavCloseButton.classList.toggle("hidden"); // Untuk menampilkan/menyembunyikan tombol close di mobile
		}

		// Kelas utama untuk menampilkan dan menyembunyikan sidenav
		sidenav.classList.toggle("-translate-x-full"); // Asumsi ini kelas untuk menyembunyikan
		sidenav.classList.toggle("translate-x-0"); // Asumsi ini kelas untuk menampilkan
		sidenav.classList.toggle("shadow-soft-xl"); // Toggle shadow jika diperlukan

		// Animasi untuk ikon burger (garis-garis)
		if (topBread && bottomBread) {
			// Contoh logika 'page' yang dikomentari:
			// if (page == "rtl") {
			//     topBread.classList.toggle("-translate-x-[5px]");
			//     bottomBread.classList.toggle("-translate-x-[5px]");
			// } else {
			//     topBread.classList.toggle("translate-x-[5px]");
			//     bottomBread.classList.toggle("translate-x-[5px]");
			// }
			// Kode asli Anda men-toggle 'translate-x-[5px]' pada kedua elemen ini.
			// Pastikan ini menghasilkan animasi yang diinginkan.
			topBread.classList.toggle("translate-x-[5px]");
			// i tengah tidak dianimasikan di kode asli, jadi kita biarkan.
			bottomBread.classList.toggle("translate-x-[5px]");
		}
	});

	// Event listener untuk tombol close (jika ada)
	if (sidenavCloseButton) {
		sidenavCloseButton.addEventListener("click", function () {
			if (sidenavTrigger) {
				sidenavTrigger.click(); // Panggil klik pada trigger untuk menutup
			}
		});
	}

	// Event listener untuk menutup sidenav jika diklik di luar area sidenav
	window.addEventListener("click", function (e) {
		if (!(e.target instanceof Node)) {
			// Jika target klik bukan Node, abaikan (sangat jarang terjadi)
			return;
		}

		// Pastikan sidenav dan trigger sudah terdefinisi
		if (sidenav && sidenavTrigger) {
			// Cek apakah sidenav sedang terbuka (misalnya, tidak memiliki kelas -translate-x-full)
			// DAN klik tidak terjadi di dalam sidenav
			// DAN klik tidak terjadi pada sidenavTrigger atau salah satu anaknya
			if (
				sidenav.classList.contains("translate-x-0") &&
				!sidenav.contains(e.target) &&
				!sidenavTrigger.contains(e.target) &&
				sidenavTrigger !== e.target &&
				!sidenavTrigger.contains(e.target)
			) {
				sidenavTrigger.click(); // Panggil klik pada trigger untuk menutup
			}
		}
	});
});
