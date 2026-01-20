@extends('layouts.app')

@section('content')
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card auth-card shadow-lg border-0">
                        <div class="auth-header">
                            <h4 class="mb-0 fw-bold">Login Sistem</h4>
                            <p class="mb-0 opacity-75 small">Masuk untuk mengelola layanan</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label text-muted small fw-bold">EMAIL ADDRESS</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-envelope text-muted"></i></span>
                                        <input id="email" type="email"
                                            class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                            placeholder="name@example.com">
                                    </div>
                                    @error('email')
                                        <span class="text-danger small mt-1 d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label text-muted small fw-bold">PASSWORD</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-lock text-muted"></i></span>
                                        <input id="password" type="password"
                                            class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password"
                                            placeholder="Enter your password">
                                    </div>
                                    @error('password')
                                        <span class="text-danger small mt-1 d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="remember">
                                            {{ __('Ingat Saya') }}
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a class="text-decoration-none small text-primary fw-bold"
                                            href="{{ route('password.request') }}">
                                            Lupa Password?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Masuk Sistem') }}
                                </button>

                                <div class="text-center">
                                    <a href="{{ route('tracking.index') }}" class="small text-muted text-decoration-none">
                                        &larr; Cek Layanan Publik
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3 text-muted small">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection