<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndikatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode'                       => 'nullable',
            'kode_tujuan'                => 'nullable|string|max:50',
            'kode_sasaran'               => 'nullable|string|max:50',
            'kode_indikator_kinerja'     => 'nullable|string|max:50',
            'tujuan'                     => 'nullable',
            'sasaran'                    => 'required',
            'indikator_kinerja'          => 'required',
            'jenis_indikator'            => 'required',
            'periode'                    => 'nullable',
            'tipe'                       => 'nullable',
            'satuan'                     => 'nullable',
            'target_tahunan'             => 'nullable|numeric',
            'tahun'                      => 'required|integer',
            'pic_id'                     => 'nullable|exists:pegawais,id',
            'dasar_hitung'               => 'nullable|string',
            'basis_data'                 => 'nullable|string',
            'definisi_x'                 => 'nullable|string|max:500',
            'definisi_y'                 => 'nullable|string|max:500',
        ];
    }
}
