<?php
// Script to generate the document and save it to storage
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pegawai = \App\Models\Pegawai::first();

$controller = app(\App\Http\Controllers\TemplateWordController::class);
$request = \Illuminate\Http\Request::create('/template-word/export-notulen-capaian', 'POST', [
    'jenis_rapat' => 'capaian_kinerja',
    'tahun' => 2026,
    'triwulan' => 1,
    'tanggal' => '2026-07-16',
    'waktu' => '09:00',
    'tempat' => 'Ruang Rapat',
    'pimpinan_id' => $pegawai->id,
    'notulis_id' => $pegawai->id
]);
// Simulate admin user
$user = \App\Models\User::where('role', 'admin')->first();
auth()->login($user);

$response = $controller->exportNotulenCapaian($request);
if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
    echo "File generated at: " . $response->getFile()->getPathname() . "\n";
    copy($response->getFile()->getPathname(), __DIR__ . '/../storage/app/public/test_corrupt.docx');
    echo "Copied to test_corrupt.docx\n";
} else {
    echo "Failed to generate.\n";
}
