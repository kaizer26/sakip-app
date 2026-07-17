<?php

namespace App\Imports;

use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class IndikatorXYImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kode = trim($row['kode_indikator'] ?? '');
            if (!$kode) continue;

            $indikator = Indikator::where('kode', $kode)->first();
            
            if ($indikator) {
                // Update Definisi X and Y in Indikator
                if (isset($row['definisi_x'])) {
                    $indikator->definisi_x = $row['definisi_x'];
                }
                if (isset($row['definisi_y'])) {
                    $indikator->definisi_y = $row['definisi_y'];
                }
                if (isset($row['target_tahunan_x'])) {
                    $indikator->target_tahunan_x = $row['target_tahunan_x'];
                }
                if (isset($row['target_tahunan_y'])) {
                    $indikator->target_tahunan_y = $row['target_tahunan_y'];
                }
                $indikator->save();

                // Update or Create Target
                $target = Target::firstOrNew(['indikator_id' => $indikator->id]);
                
                $target->target_x_tw1 = $row['target_x_tw_1'] ?? $target->target_x_tw1;
                $target->target_x_tw2 = $row['target_x_tw_2'] ?? $target->target_x_tw2;
                $target->target_x_tw3 = $row['target_x_tw_3'] ?? $target->target_x_tw3;
                $target->target_x_tw4 = $row['target_x_tw_4'] ?? $target->target_x_tw4;

                $target->target_y_tw1 = $row['target_y_tw_1'] ?? $target->target_y_tw1;
                $target->target_y_tw2 = $row['target_y_tw_2'] ?? $target->target_y_tw2;
                $target->target_y_tw3 = $row['target_y_tw_3'] ?? $target->target_y_tw3;
                $target->target_y_tw4 = $row['target_y_tw_4'] ?? $target->target_y_tw4;

                $target->save();
            }
        }
    }
}
