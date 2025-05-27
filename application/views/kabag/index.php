<!-- cards -->
<div class="min-h-screen flex flex-col px-6 py-6 mx-auto">
 <?php
$cards = [
    'Dikonfirmasi' => [
        'icon' => '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>',
        'bg' => 'bg-green-50',
        'text' => 'text-green-700'
    ],
    'Menunggu' => [
        'icon' => '<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z" /></svg>',
        'bg' => 'bg-yellow-50',
        'text' => 'text-yellow-700'
    ],
    'Ditolak' => [
        'icon' => '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>',
        'bg' => 'bg-red-50',
        'text' => 'text-red-700'
    ],
];
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
    <?php foreach ($status_summary as $row): 
        $status = $row->status_konfirmasi;
        $config = $cards[$status] ?? [
            'icon' => '<svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" /></svg>',
            'bg' => 'bg-gray-50',
            'text' => 'text-gray-700'
        ];
    ?>
        <div class="rounded-2xl shadow-md p-5 <?= $config['bg'] ?> border border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium <?= $config['text'] ?> uppercase mb-1">
                    <?= ucfirst($status) ?>
                </h3>
                <p class="text-3xl font-bold text-gray-800"><?= $row->total ?></p>
            </div>
            <div class="bg-white p-2 rounded-full shadow-sm">
                <?= $config['icon'] ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>


</div>
