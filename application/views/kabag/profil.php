<!-- Tambahkan di <head> HTML kamu -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Nunito', 'sans-serif'],
                }
            }
        }
    }
</script>
<script src="https://cdn.tailwindcss.com"></script>

<!-- cards -->
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
  <!-- row 1 -->



<!-- Card Container -->
<div class="w-full bg-white shadow-md border-t border-gray-200 rounded-none p-8 font-sans">
    <div class="flex items-center space-x-4 mb-6">
        <div class="flex-shrink-0">
            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 flex items-center justify-center rounded-full text-xl font-bold">
                <?= strtoupper(substr($kabag['nama'], 0, 1)) ?>
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-semibold text-gray-800"><?= $kabag['nama'] ?></h2>
            <p class="text-sm text-gray-500"><?= $kabag['email'] ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 text-sm text-gray-700">
        <div class="space-y-4 font-medium text-base">
            <div class="flex">
                <div class="w-40 text-gray-600">Nama</div>
                <div class="w-3 text-gray-600">:</div>
                <div><?= $kabag['nama'] ?></div>
            </div>
            <div class="flex">
                <div class="w-40 text-gray-600">NIP</div>
                <div class="w-3 text-gray-600">:</div>
                <div><?= $kabag['nip'] ?></div>
            </div>
            <div class="flex">
                <div class="w-40 text-gray-600">Email</div>
                <div class="w-3 text-gray-600">:</div>
                <div><?= $kabag['email'] ?></div>
            </div>
           
        </div>
    </div>
</div>
  
</div>