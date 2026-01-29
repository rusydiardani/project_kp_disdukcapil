<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;

class PublicTrackingController extends Controller
{
    public function index()
    {
        return view('public.tracking.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
        ]);

        $service = ServiceRequest::with('serviceType')
            ->where('nik', $request->nik)
            ->latest() // Ambil yang paling baru jika ada history
            ->first();

        if (!$service) {
            return back()->with('error', 'Data layanan tidak ditemukan. Periksa kembali Nomor Registrasi dan Token Anda.');
        }

        return view('public.tracking.show', compact('service'));
    }
}
