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
            'registration_number' => 'required|string',
            'tracking_token' => 'required|string',
        ]);

        $service = ServiceRequest::with('serviceType')
            ->where('registration_number', $request->registration_number)
            ->where('tracking_token', $request->tracking_token)
            ->first();

        if (!$service) {
            return back()->with('error', 'Data layanan tidak ditemukan. Periksa kembali Nomor Registrasi dan Token Anda.');
        }

        return view('public.tracking.show', compact('service'));
    }
}
