<footer class="pt-4">
    <div class="w-full px-6 mx-auto">
        <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
            <div
                class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
                <div
                    class="leading-normal text-center text-sm text-slate-500 lg:text-left">
                    ©
                    <script>
                        document.write(new Date().getFullYear() + ",");
                    </script>
                    made with <i class="fa fa-heart"></i> by
                    <a
                        href="#"
                        class="font-semibold text-slate-700"
                        target="_blank">Subkhi Mashadi</a>
                    for a better web.
                </div>
            </div>
            <div
                class="w-full max-w-full px-3 mt-0 shrink-0 lg:w-1/2 lg:flex-none">
                <ul
                    class="flex flex-wrap justify-center pl-0 mb-0 list-none lg:justify-end">
                    <li class="nav-item">
                        <a
                            href="#"
                            class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500"
                            target="_blank">Creative Tim</a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500"
                            target="_blank">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500"
                            target="_blank">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="block px-4 pt-0 pb-1 pr-0 font-normal transition-colors ease-soft-in-out text-sm text-slate-500"
                            target="_blank">License</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</div>
<!-- end cards -->
</main>
<script>
function toggleForm() {
  const formJadwal = document.getElementById('form-jadwal');
  if (formJadwal) {
    formJadwal.classList.toggle('hidden');
  } else {
    console.error("Elemen dengan ID 'form-jadwal' tidak ditemukan.");
  }
}
</script>
<script src="assets/js/sidenav-burger.js"></script>
    <script src="assets/js/dropdown.js"></script>
</body>
<!-- plugin for charts  -->
<script src="<?= base_url() ?>assets/js/core/popper.min.js"></script> 
<script src="<?= base_url() ?>assets/js/core/bootstrap.min.js"></script> 
<script src="<?= base_url() ?>assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= base_url() ?>assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="<?= base_url() ?>assets/js/plugins/chartjs.min.js"></script> 
<script src="<?= base_url() ?>assets/js/soft-ui-dashboard-tailwind.min.js?v=1.0.5"></script>

<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="<?= base_url() ?>assets/js/plugins/chartjs.min.js" async></script>
<!-- plugin for scrollbar  -->
<script src="<?= base_url() ?>assets/js/plugins/perfect-scrollbar.min.js" async></script>
<!-- github button -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- main script file  -->
<script
src="<?= base_url() ?>assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5"
async></script>

</html>