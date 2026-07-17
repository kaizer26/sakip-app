<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Rtl;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IssueController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'triwulan' => 'required|integer|between:1,4',
            'tahun' => 'required|integer',
            'status_kendala' => 'required|in:Selesai,Sebagian Selesai,Belum Ditangani',
            'deskripsi' => 'required|string',
            'solusi_sementara' => 'nullable|string',
            'rtl' => 'nullable|array',
            'rtl.*.deskripsi_rtl' => 'required_with:rtl|string',
            'rtl.*.pic_nip' => 'required_with:rtl|string',
            'rtl.*.due_date' => 'required_with:rtl|date',
        ]);

        // Business Logic Validation
        $status = $validated['status_kendala'];
        $hasSolusi = !empty($validated['solusi_sementara']);
        $hasRtl = !empty($validated['rtl']) && count($validated['rtl']) > 0;

        if ($status === 'Selesai' && !$hasSolusi) {
            return back()->withErrors(['solusi_sementara' => 'Solusi sementara wajib diisi jika status Selesai.'])->withInput();
        }

        if ($status === 'Sebagian Selesai') {
            if (!$hasSolusi) {
                return back()->withErrors(['solusi_sementara' => 'Solusi sementara wajib diisi jika status Sebagian Selesai.'])->withInput();
            }
            if (!$hasRtl) {
                return back()->withErrors(['rtl' => 'Rencana Tindak Lanjut (RTL) wajib diisi jika status Sebagian Selesai.'])->withInput();
            }
        }

        if ($status === 'Belum Ditangani' && !$hasRtl) {
            return back()->withErrors(['rtl' => 'Rencana Tindak Lanjut (RTL) wajib diisi jika status Belum Ditangani.'])->withInput();
        }

        $pegawaiNip = auth()->user()->pegawai?->nip ?? auth()->user()->pegawai?->email_bps ?? auth()->user()->email;

        // Check if triwulan is locked
        // For simplicity, assuming if it's past the triwulan by a lot, we don't lock yet unless specified. 
        // We will just store it for now.

        $issue = Issue::create([
            'indikator_id' => $validated['indikator_id'],
            'triwulan' => $validated['triwulan'],
            'tahun' => $validated['tahun'],
            'status_kendala' => $status,
            'deskripsi' => $validated['deskripsi'],
            'solusi_sementara' => $validated['solusi_sementara'] ?? null,
            'pegawai_nip' => $pegawaiNip,
        ]);

        if ($hasRtl) {
            foreach ($validated['rtl'] as $rtlData) {
                Rtl::create([
                    'issue_id' => $issue->id,
                    'deskripsi_rtl' => $rtlData['deskripsi_rtl'],
                    'pic_nip' => $rtlData['pic_nip'],
                    'due_date' => $rtlData['due_date'],
                    'status_rtl' => 'Open',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Kendala dan RTL berhasil dilaporkan.');
    }
}
