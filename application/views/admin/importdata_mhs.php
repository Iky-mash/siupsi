<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50 p-6">

    <!-- Card untuk Upload Excel -->
    <div class="max-w-xl mx-auto mb-6">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Upload File Excel untuk Import User</h2>

            <?php if ($this->session->flashdata('message')): ?>
                <div class="bg-yellow-100 text-yellow-700 p-3 rounded mb-4">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" action="<?php echo site_url('excel_import/import'); ?>">
                <div class="mb-4">
                    <input type="file" name="file" required class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                    Import Data
                </button>
            </form>
        </div>
    </div>

  

</body>
</html>
