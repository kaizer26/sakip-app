<?php
$files = glob('app/Http/Controllers/*.php');
foreach($files as $file) {
    $c = file_get_contents($file);
    $c = str_replace('auth()->user()->pegawai?->nip ?? auth()->user()->pegawai?->email_bps', 'auth()->user()->pegawai?->nip ?? auth()->user()->pegawai?->email_bps ?? auth()->user()->email', $c);
    $c = str_replace('$user->pegawai?->nip ?? $user->pegawai?->email_bps', '$user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email', $c);
    
    // Some lines had ?? null at the end
    $c = str_replace('auth()->user()->email ?? null', 'auth()->user()->email', $c);
    $c = str_replace('$user->email ?? null', '$user->email', $c);

    file_put_contents($file, $c);
    echo "Processed $file\n";
}
