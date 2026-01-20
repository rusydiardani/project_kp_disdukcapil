@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Layanan</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary">Tambah Layanan</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('services.index') }}" method="GET" class="row g-3">
                <div class="col-auto">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No Reg</th>
                        <th>Jenis Layanan</th>
                        <th>Pemohon</th>
                        <th>Tgl Masuk</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                        <tr>
                            <td>{{ $s->registration_number }}</td>
                            <td>{{ $s->serviceType->name ?? '-' }}</td>
                            <td>{{ $s->applicant_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->submission_date)->format('d/m/Y') }}</td>
                            <td>
                                <span class="{{ $s->is_urgent ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($s->deadline_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ match ($s->status) { 'completed' => 'success', 'overdue' => 'danger', 'processing' => 'info', default => 'secondary'} }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('services.show', $s->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data layanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $services->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection