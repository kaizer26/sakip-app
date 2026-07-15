<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Analisis;
use App\Http\Requests\AnalisisRequest;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnalisisController extends Controller
{
    public function index(Request $request)
    {
        $triwulan = $request->get('triwulan', \App\Models\Setting::get('default_triwulan', ceil(date('n') / 3)));
        $query = Analisis::with('indikator');
        if ($triwulan) {
            $query->where('triwulan', $triwulan);
        }
        $analisiss = $query->get();
        return view('analisis.index', compact('analisiss'));
    }

    public function create()
    {
        $indikators = Indikator::all();
        $pegawais = Pegawai::orderBy('pangkat_golongan', 'desc')->orderBy('nip', 'asc')->get();
        return view('analisis.create', compact('indikators', 'pegawais'));
    }

    public function store(AnalisisRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('file_bukti_kinerja')) {
            $data['file_bukti_kinerja'] = $request->file('file_bukti_kinerja')->store('bukti/kinerja', 'public');
        }
        
        if ($request->hasFile('file_bukti_tindak_lanjut')) {
            $data['file_bukti_tindak_lanjut'] = $request->file('file_bukti_tindak_lanjut')->store('bukti/tindak_lanjut', 'public');
        }

        Analisis::create($data);
        return redirect()->route('analisis.index')->with('success', 'Analisis berhasil ditambahkan');
    }

    public function edit(Analisis $analisi)
    {
        $indikators = Indikator::all();
        $pegawais = Pegawai::orderBy('pangkat_golongan', 'desc')->orderBy('nip', 'asc')->get();
        return view('analisis.edit', compact('analisi', 'indikators', 'pegawais'));
    }

    public function update(AnalisisRequest $request, Analisis $analisi)
    {
        $data = $request->validated();
        
        if ($request->hasFile('file_bukti_kinerja')) {
            if ($analisi->file_bukti_kinerja) Storage::disk('public')->delete($analisi->file_bukti_kinerja);
            $data['file_bukti_kinerja'] = $request->file('file_bukti_kinerja')->store('bukti/kinerja', 'public');
        }
        
        if ($request->hasFile('file_bukti_tindak_lanjut')) {
            if ($analisi->file_bukti_tindak_lanjut) Storage::disk('public')->delete($analisi->file_bukti_tindak_lanjut);
            $data['file_bukti_tindak_lanjut'] = $request->file('file_bukti_tindak_lanjut')->store('bukti/tindak_lanjut', 'public');
        }

        $analisi->update($data);
        return redirect()->route('analisis.index')->with('success', 'Analisis berhasil diperbarui');
    }

    public function destroy(Analisis $analisi)
    {
        if ($analisi->file_bukti_kinerja) Storage::disk('public')->delete($analisi->file_bukti_kinerja);
        if ($analisi->file_bukti_tindak_lanjut) Storage::disk('public')->delete($analisi->file_bukti_tindak_lanjut);
        $analisi->delete();
        return redirect()->route('analisis.index')->with('success', 'Analisis berhasil dihapus');
    }
}
