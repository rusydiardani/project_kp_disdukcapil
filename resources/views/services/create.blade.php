@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Input Layanan Baru</div>
                <div class="card-body">
                    <form action="{{ route('services.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Jenis Layanan</label>
                            <select name="service_type_id"
                                class="form-select @error('service_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }} (Estimasi: {{ $type->sla_days }} hari)
                                    </option>
                                @endforeach
                            </select>
                            @error('service_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pemohon</label>
                            <input type="text" name="applicant_name"
                                class="form-control @error('applicant_name') is-invalid @enderror" required>
                            @error('applicant_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengajuan</label>
                            <input type="date" name="submission_date"
                                class="form-control @error('submission_date') is-invalid @enderror"
                                value="{{ date('Y-m-d') }}" required>
                            @error('submission_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Awal (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                        <a href="{{ route('services.index') }}" class="btn btn-link">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection