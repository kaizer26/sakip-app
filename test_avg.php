<?php
$triwulan = 1; 
$indikators = \App\Models\Indikator::with(['target', 'realisasis' => function($q) use ($triwulan) { $q->where('triwulan', $triwulan); }])->get();
$sumT = 0; $sumY = 0; $countAll = 0; $countT = 0;
foreach($indikators as $ind) {
    $real = $ind->realisasis->first() ? (float)$ind->realisasis->first()->realisasi_kumulatif : 0;
    
    $tgtT = $ind->target ? (float)$ind->target->target_tw1 : 0;
    $capT = $tgtT > 0 ? ($real / $tgtT)*100 : 0;
    
    $tgtY = (float)$ind->target_tahunan;
    $capY = $tgtY > 0 ? ($real / $tgtY)*100 : 0;
    
    echo "Indikator: {$ind->kode}, TargetT: {$tgtT}, TargetY: {$tgtY}, Real: {$real}, CapT: {$capT}, CapY: {$capY}\n";
    
    $sumT += $capT;
    $sumY += $capY;
    $countAll++;
    if($capT > 0) $countT++;
}
echo "Count All: " . $countAll . "\n";
echo "Avg Triwulan (All): " . ($sumT/$countAll) . "\n";
echo "Avg Tahunan (All): " . ($sumY/$countAll) . "\n";
