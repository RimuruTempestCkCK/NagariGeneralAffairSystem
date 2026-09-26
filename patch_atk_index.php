<?php
$f = 'resources/views/atk/index.blade.php';
$c = file_get_contents($f);

// 1. Add "Bulk Print QR" button
$searchBtn = '<!-- Filter & Search Toolbar -->';
$addBtn = '<!-- Filter & Search Toolbar -->
        <div class="mb-4">
            <button type="button" onclick="submitBulkPrint()" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2.5">Bulk Print QR</button>
        </div>';
$c = str_replace($searchBtn, $addBtn, $c);

// 2. Wrap table in a form
$searchTableStart = '<table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">';
$addTableStart = '<form id="bulkPrintForm" action="{{ route(\'atk.bulk-print\') }}" method="GET" target="_blank">
                  <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">';
$c = str_replace($searchTableStart, $addTableStart, $c);

// 3. Add checkbox th
$searchTh = '<th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kode ATK</th>';
$addTh = '<th scope="col" class="p-4"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                              <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kode ATK</th>';
$c = str_replace($searchTh, $addTh, $c);

// 4. Add checkbox td
$searchTd = '<td class="p-4 text-sm font-semibold text-primary-600 dark:text-primary-400 whitespace-nowrap">
                                  {{ $item->kode_atk }}
                              </td>';
$addTd = '<td class="p-4">
                                  @if(!$item->trashed() && $item->status === \'Aktif\')
                                      <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="bulk-cb">
                                  @endif
                              </td>
                              <td class="p-4 text-sm font-semibold text-primary-600 dark:text-primary-400 whitespace-nowrap">
                                  {{ $item->kode_atk }}
                              </td>';
$c = str_replace($searchTd, $addTd, $c);

// 5. Add colspan to Empty State
$searchEmpty = '<td colspan="8" class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada data ATK.</td>';
$addEmpty = '<td colspan="9" class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada data ATK.</td>';
$c = str_replace($searchEmpty, $addEmpty, $c);

// 6. Close form
$searchTableEnd = '</table>';
$addTableEnd = '</table></form>';
$c = str_replace($searchTableEnd, $addTableEnd, $c);

// 7. Add JS
$searchJs = '@section(\'scripts\')';
$addJs = '@section(\'scripts\')
<script>
function toggleSelectAll(source) {
    let checkboxes = document.querySelectorAll(".bulk-cb");
    for(let i=0; i<checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
function submitBulkPrint() {
    let checked = document.querySelectorAll(".bulk-cb:checked");
    if(checked.length === 0) {
        Swal.fire("Peringatan", "Silakan pilih minimal satu ATK.", "warning");
        return;
    }
    document.getElementById("bulkPrintForm").submit();
}
</script>';
$c = str_replace($searchJs, $addJs, $c);

file_put_contents($f, $c);
echo "Done patching ATK Index";
