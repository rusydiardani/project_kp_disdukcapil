<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Base Query
        $query = ServiceRequest::with('serviceType');

        // Jika Petugas (bukan Admin/Supervisor), hanya lihat tugas sendiri
        if ($user->role === 'petugas') {
            $query->where('user_id', $user->id);
        }

        $allServices = $query->get();

        // Statistik
        $stats = [
            'total' => $allServices->count(),
            'completed' => $allServices->where('status', 'completed')->count(),
            'pending' => $allServices->whereIn('status', ['pending', 'processing'])->count(),
            'overdue' => $allServices->where('status', 'overdue')->count(),
            'urgent' => $allServices->filter(fn($s) => $s->is_urgent)->count(),
        ];

        // Layanan Perlu Perhatian (Urgent < 2 hari atau Overdue)
        // Kita ambil 10 teratas yang deadline-nya paling dekat/lewat
        $urgentServices = $query->where('status', '!=', 'completed')
            ->orderBy('deadline_date', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.index', compact('stats', 'urgentServices'));
    }
}
