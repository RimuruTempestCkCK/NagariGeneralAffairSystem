<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Static Asset Routes
|--------------------------------------------------------------------------
|
| Adminator dan brand asset (logo + favicon) dipindahkan ke dalam folder
| resources/ supaya runtime frontend tidak lagi bergantung pada folder
| template di luar project. Folder public/ tidak dapat menjadi symlink ke
| resources/ pada Windows tanpa hak administrator, sehingga file static
| disajikan lewat route di bawah.
|
| File sumber  : resources/<baseDir>
| URL publik   : /<prefix>/<path>
|
*/

$allowedExtensions = [
    'js', 'mjs', 'css', 'map',
    'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'ico',
    'woff', 'woff2', 'ttf', 'eot', 'otf',
    'json', 'html', 'htm', 'txt', 'xml',
];

$contentTypes = [
    'js'    => 'application/javascript; charset=utf-8',
    'mjs'   => 'application/javascript; charset=utf-8',
    'css'   => 'text/css; charset=utf-8',
    'svg'   => 'image/svg+xml',
    'ico'   => 'image/x-icon',
    'json'  => 'application/json',
    'map'   => 'application/json',
    'html'  => 'text/html; charset=utf-8',
    'htm'   => 'text/html; charset=utf-8',
    'txt'   => 'text/plain; charset=utf-8',
    'xml'   => 'application/xml',
    'png'   => 'image/png',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'webp'  => 'image/webp',
    'avif'  => 'image/avif',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf'   => 'font/ttf',
    'otf'   => 'font/otf',
    'eot'   => 'application/vnd.ms-fontobject',
];

$serveResourceFile = function (string $baseDir) use ($allowedExtensions, $contentTypes) {
    $baseDir = str_replace('\\', '/', $baseDir);

    return function (string $path) use ($baseDir, $allowedExtensions, $contentTypes) {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || str_contains($path, "\0") || str_contains($path, '..')) {
            abort(404);
        }

        $base = realpath(base_path($baseDir));

        if ($base === false) {
            abort(404);
        }

        $base = str_replace('\\', '/', $base);
        $full = realpath($base.'/'.$path);

        if ($full === false || ! str_starts_with(str_replace('\\', '/', $full), $base.'/')) {
            abort(404);
        }

        if (! is_file($full)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($full, PATHINFO_EXTENSION));

        if (! in_array($extension, $allowedExtensions, true)) {
            abort(404);
        }

        $headers = ['Cache-Control' => 'public, max-age=3600'];

        if (isset($contentTypes[$extension])) {
            $headers['Content-Type'] = $contentTypes[$extension];
        }

        return response()->file($full, $headers);
    };
};

// Template Adminator (sumber: resources/adminator_templete)
Route::get('/adminator_templete/{path}', $serveResourceFile('resources/adminator_templete'))
    ->where('path', '.*');

// Brand asset (sumber: resources/brand)
Route::get('/brand/{path}', $serveResourceFile('resources/brand'))
    ->where('path', '.*');

// Favicon (tetap di root URL agar browser tetap menamadainya)
Route::get('/favicon.ico', function () {
    $file = realpath(base_path('resources/brand/favicon.ico'));

    if ($file === false || ! is_file($file)) {
        abort(404);
    }

    // File .ico ini sebenarnya berformat PNG, jadi tipe kontennya
    // dideteksi dari magic bytes, bukan dari ekstensi.
    $header = (string) file_get_contents($file, false, null, 0, 8);
    $contentType = str_starts_with($header, "\x89PNG") ? 'image/png' : 'image/x-icon';

    return response()->file($file, [
        'Content-Type' => $contentType,
        'Cache-Control' => 'public, max-age=3600',
    ]);
});
