@extends('layouts.app')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-md-9">
            <a href="{{ route('tracking.index') }}"
                class="btn btn-outline-secondary rounded-pill mb-4 px-4 text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Pencarian
            </a>

            <div class="card border-0 shadow-lg overflow-hidden">
                <div
                    class="card-header bg-white border-0 py-4 px-4 px-md-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <small class="text-uppercase text-muted fw-bold letter-spacing-1">Nomor Registrasi</small>
                        <h2 class="fw-bold text-primary mb-0 letter-spacing-1">{{ $service->registration_number }}</h2>
                    </div>
                    <div class="text-end">
                        @php
                            $statusInfo = match ($service->status) {
                                'completed' => ['color' => 'success', 'icon' => 'check-circle-fill', 'label' => 'SELESAI / SIAP AMBIL'],
                                'processing' => ['color' => 'info', 'icon' => 'gear-wide-connected', 'label' => 'SEDANG DIPROSES'],
                                'overdue' => ['color' => 'danger', 'icon' => 'exclamation-circle-fill', 'label' => 'TERLAMBAT'],
                                default => ['color' => 'secondary', 'icon' => 'clock-fill', 'label' => 'MENUNGGU VERIFIKASI']
                            };
                        @endphp
                        <span class="badge bg-{{ $statusInfo['color'] }} fs-6 py-2 px-3 rounded-pill shadow-sm">
                            <i class="bi bi-{{ $statusInfo['icon'] }} me-1"></i> {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Timeline Section -->
                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Riwayat Proses</h5>
                    <div class="tracking-timeline">
                        <!-- Submission Step -->
                        <div class="timeline-item active">
                            <div class="timeline-date">
                                {{ \Carbon\Carbon::parse($service->submission_date)->translatedFormat('d F Y') }}</div>
                            <h6 class="fw-bold">Layanan Diajukan</h6>
                            <p class="text-muted small">Permohonan <strong>{{ $service->serviceType->name }}</strong> telah
                                diterima oleh sistem.</p>
                        </div>

                        <!-- Processing Step -->
                        <div
                            class="timeline-item {{ in_array($service->status, ['processing', 'completed', 'overdue']) ? 'active' : '' }}">
                            <div class="timeline-date">
                                {{ in_array($service->status, ['processing', 'completed', 'overdue']) ? 'Dalam Proses' : 'Menunggu' }}
                            </div>
                            <h6 class="fw-bold">Verifikasi & Pemrosesan</h6>
                            <p class="text-muted small">Petugas sedang memverifikasi berkas dan memproses dokumen Anda.</p>
                        </div>

                        <!-- Completion Step -->
                        <div class="timeline-item {{ $service->status == 'completed' ? 'active' : '' }}">
                            <div class="timeline-date">
                                {{ $service->status == 'completed' ? 'Selesai' : 'Estimasi: ' . \Carbon\Carbon::parse($service->deadline_date)->translatedFormat('d F Y') }}
                            </div>
                            <h6 class="fw-bold">Dokumen Selesai</h6>
                            <p class="text-muted small">Dokumen telah selesai dicetak dan siap untuk diambil di kantor
                                Dinas.</p>
                        </div>
                    </div>

                    <!-- Info Cards -->
                    <div class="row g-3 mt-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1">Estimasi Selesai
                                    (Deadline)</small>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-primary fs-4 me-3"></i>
                                    <span
                                        class="fw-bold fs-5">{{ \Carbon\Carbon::parse($service->deadline_date)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($service->notes)
                        <div class="alert alert-info border-0 shadow-sm mt-4 d-flex gap-3">
                            <i class="bi bi-info-circle-fill fs-4 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Catatan Petugas Layanan:</h6>
                                <p class="mb-0">{{ $service->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-0 text-center py-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock-fill me-1"></i>
                        Data ini dilindungi dan hanya dapat diakses dengan Token Layanan yang valid.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection