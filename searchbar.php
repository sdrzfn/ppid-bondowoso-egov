<?php
// Default konfigurasi (bisa dioverride sebelum include)
$sb_target_id = $sb_target_id ?? 'cardsContainer';
$sb_search_endpoint = $sb_search_endpoint ?? 'search-index.php';
$sb_filter_endpoint = $sb_filter_endpoint ?? 'filter-index.php';
$sb_filter_fields = $sb_filter_fields ?? [
    'status' => [
        'label' => 'Status',
        'type' => 'select',
        'options' => [
            '' => 'Semua',
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif'
        ]
    ],
    'sort' => [
        'label' => 'Urutkan',
        'type' => 'select',
        'options' => [
            'desc' => 'Terbaru',
            'asc' => 'Terlama'
        ]
    ]
];
?>

<!-- Search & Filter -->
<section class="container mx-auto px-4 py-10">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3">
            <div class="flex-1 relative">
                <input type="text" id="searchInput" placeholder="Cari informasi, dokumen, atau layanan..."
                    class="w-full rounded-full border border-slate-300 px-12 py-3 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    autocomplete="off">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd"
                            d="M10.5 3.75a6.75 6.75 0 1 0 4.243 12.023l4.242 4.242 1.06-1.06-4.241-4.243A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <!-- Tombol Filter -->
            <button
                class="shrink-0 inline-flex items-center justify-center w-12 h-12 rounded-full border border-slate-300 hover:bg-slate-50"
                title="Filter" id="filterBtn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M6.75 12h10.5M10.5 17.25h3" />
                </svg>
            </button>

            <!-- Modal Filter -->
            <div id="filterModal"
                class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex items-center justify-center">
                <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 relative">
                    <h2 class="text-lg font-semibold mb-4">Filter</h2>
                    <form id="filterForm" class="space-y-4">
                        <?php foreach ($sb_filter_fields as $field_name => $field): ?>
                            <div>
                                <label class="block text-sm font-medium text-slate-700"><?= htmlspecialchars($field['label']) ?></label>
                                <?php if ($field['type'] === 'select'): ?>
                                    <select name="<?= htmlspecialchars($field_name) ?>" class="w-full border rounded-lg px-3 py-2">
                                        <?php foreach ($field['options'] as $value => $label): ?>
                                            <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Hidden search keyword -->
                        <input type="hidden" name="q" id="filterSearchKeyword" value="">

                        <div class="flex justify-end gap-3">
                            <button type="button" id="closeFilter"
                                class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-sky-600 text-white hover:bg-sky-700">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Pass konfigurasi ke JavaScript
    window.searchbarConfig = {
        targetId: <?= json_encode($sb_target_id) ?>,
        searchEndpoint: <?= json_encode($sb_search_endpoint) ?>,
        filterEndpoint: <?= json_encode($sb_filter_endpoint) ?>,
        filterFields: <?= json_encode(array_keys($sb_filter_fields)) ?>
    };
</script>