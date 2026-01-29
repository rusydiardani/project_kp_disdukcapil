@extends('layouts.app')

@section('content')
    <div class="row min-vh-75 align-items-center justify-content-center py-5">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-5">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                    style="width: 80px; height: 80px; margin-bottom: 20px;">
                    <i class="bi bi-search fs-1"></i>
                </div>
                <h1 class="fw-bold text-primary mb-2">Cek Status Layanan</h1>
                <p class="text-muted fs-5">Pantau dokumen kependudukan Anda secara real-time.</p>
            </div>

            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-3">
                    <small class="text-uppercase fw-bold letter-spacing-1">Formulir Pengecekan</small>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('error'))
                        <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('tracking.search') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">NIK (NOMOR INDUK KEPENDUDUKAN)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" name="nik" class="form-control border-start-0 ps-0 fw-bold"
                                    placeholder="Contoh: 1234567890123456" required maxlength="16" minlength="16">
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Masukkan 16 digit NIK yang
                                tertera di KTP/KK.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm py-3 rounded-pill fw-bold">
                            <i class="bi bi-search me-2"></i> Lacak Status Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-muted">
                    <small>Login Petugas Dinas &rarr;</small>
                </a>
            </div>
        </div>
    </div>
@endsection