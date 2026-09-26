<?php
$files = ['atk.blade.php', 'kendaraan.blade.php', 'aset.blade.php'];
foreach($files as $f) {
    $p = 'resources/views/laporan/' . $f;
    $c = file_get_contents($p);
    $routeBase = str_replace('.blade.php', '', $f);
    $replace = '<a href="{{ route(\'laporan.'.$routeBase.'.export\', array_merge(request()->query(), [\'format\' => \'excel\'])) }}" class="btn btn--success" style="padding: 6px 12px; margin-right: 5px; background: #28a745; border-color: #28a745; color: white; text-decoration: none;">Excel</a> <a href="{{ route(\'laporan.'.$routeBase.'.export\', array_merge(request()->query(), [\'format\' => \'pdf\'])) }}" target="_blank" class="btn btn--danger" style="padding: 6px 12px; margin-right: 5px; background: #dc3545; border-color: #dc3545; color: white; text-decoration: none;">PDF</a> <button class="btn btn--primary" onclick="window.print()"';
    $c = str_replace('<button class="btn btn--primary" onclick="window.print()"', $replace, $c);
    file_put_contents($p, $c);
}
echo "Done UI Buttons";
