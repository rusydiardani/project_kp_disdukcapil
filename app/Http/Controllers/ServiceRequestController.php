<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['serviceType', 'user']);

        // Filter Role Petugas
        if (auth()->user()->role === 'petugas') {
            $query->where('user_id', auth()->id());
        }

        // Filter Search
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = ServiceType::all();
        return view('services.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type_id' => 'required|exists:service_types,id',
            'nik' => 'required|string|size:16',
            'applicant_name' => 'required|string|max:255',
            'submission_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $serviceType = ServiceType::find($validated['service_type_id']);

        // Hitung Deadline
        $deadline = Carbon::parse($validated['submission_date'])->addDays($serviceType->sla_days);

        // Generate Registration Number (Simple)
        $regNumber = 'REG-' . date('Ymd') . '-' . rand(1000, 9999);

        ServiceRequest::create([
            'registration_number' => $regNumber,
            'nik' => $validated['nik'],
            'service_type_id' => $validated['service_type_id'],
            'user_id' => Auth::id(),
            'applicant_name' => $validated['applicant_name'],
            'submission_date' => $validated['submission_date'],
            'deadline_date' => $deadline,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('services.index')->with('success', 'Layanan berhasil didaftarkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequest $service)
    {
        // Authorization check could go here
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequest $service)
    {
        // Petugas hanya bisa edit punya sendiri
        if (auth()->user()->role === 'petugas' && $service->user_id !== auth()->id()) {
            abort(403);
        }

        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequest $service)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,overdue',
            'notes' => 'nullable|string',
        ]);

        $service->update($validated);

        // Log Activity could function here

        return redirect()->route('services.index')->with('success', 'Status layanan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequest $service)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Layanan dihapus.');
    }


    public function scan()
    {
        return view('services.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
        ]);

        $service = ServiceRequest::where('nik', $request->nik)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$service) {
            $completed = ServiceRequest::where('nik', $request->nik)->where('status', 'completed')->exists();

            if ($completed) {
                return response()->json(['status' => 'error', 'message' => 'Layanan NIK ini sudah SELESAI sebelumnya.'], 400);
            }

            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan atau belum terdaftar.'], 404);
        }

        $service->update(['status' => 'completed']);

        // Return JSON success
        return response()->json([
            'status' => 'success',
            'message' => 'Status layanan untuk ' . $service->applicant_name . ' berhasil diperbarui.',
            'data' => $service
        ]);
    }
}
