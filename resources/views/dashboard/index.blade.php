@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-primary">Dashboard Monitoring</h2>
            <p class="text-muted">Ringkasan aktivitas layanan kependudukan hari ini.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Total Services -->
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-uppercase opacity-75 fw-bold small">Total Layanan</h6>
                    <h2 class="display-4 fw-bold mb-0">{{ $stats['total'] }}</h2>
                    <i class="bi bi-folder2-open icon-bg"></i>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                <div class="card-body">
                    <h6 class="text-success text-uppercase fw-bold small">Selesai</h6>
                    <h2 class="display-4 fw-bold mb-0 text-success">{{ $stats['completed'] }}</h2>
                    <i class="bi bi-check-circle icon-bg text-success"></i>
                </div>
            </div>
        </div>

        <!-- Processing -->
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-left: 5px solid #0dcaf0 !important;">
                <div class="card-body">
                    <h6 class="text-info text-uppercase fw-bold small">Diproses</h6>
                    <h2 class="display-4 fw-bold mb-0 text-info">{{ $stats['pending'] }}</h2>
                    <i class="bi bi-hourglass-split icon-bg text-info"></i>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body">
                    <h6 class="text-danger text-uppercase fw-bold small">Terlambat</h6>
                    <h2 class="display-4 fw-bold mb-0 text-danger">{{ $stats['overdue'] }}</h2>
                    <i class="bi bi-exclamation-triangle icon-bg text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    @if($urgentServices->count() > 0)
        <div class="card border-0 shadow-lg mb-5 overflow-hidden">
            <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill"></i>
                    <span class="fw-bold">PERHATIAN: Layanan Urgent / Terlambat</span>
                </div>
                <span class="badge bg-white text-danger">{{ $urgentServices->count() }} Data</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4">No. Registrasi</th>
                            <th>Pemohon</th>
                            <th>Layanan</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($urgentServices as $service)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $service->registration_number }}</td>
                                <td>{{ $service->applicant_name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $service->serviceType->name }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold {{ $service->is_overdue_calc ? 'text-danger' : 'text-warning' }}">
                                            {{ \Carbon\Carbon::parse($service->deadline_date)->translatedFormat('d M Y') }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.75rem">
                                            {{ \Carbon\Carbon::parse($service->deadline_date)->diffForHumans() }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @if($service->status == 'overdue' || $service->is_overdue_calc)
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning">Mendekati
                                            Deadline</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('services.show', $service->id) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
        <a href="{{ route('services.create') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm px-4">
            <i class="bi bi-plus-lg me-2"></i> Input Layanan Baru
        </a>
    </div>
@endsection