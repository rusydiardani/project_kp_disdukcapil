@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detail Layanan: <strong>{{ $service->registration_number }}</strong></span>
                    <span
                        class="badge bg-{{ match ($service->status) { 'completed' => 'success', 'overdue' => 'danger', 'processing' => 'info', default => 'secondary'} }}">
                        {{ ucfirst($service->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-primary">TOKEN TRACKING</th>
                            <td class="text-primary fw-bold fs-5">{{ $service->tracking_token ?? '(Belum ada)' }}</td>
                        </tr>
                        <tr>
                            <th width="30%">Jenis Layanan</th>
                            <td>{{ $service->serviceType->name }}</td>
                        </tr>
                        <tr>
                            <th>Pemohon</th>
                            <td>{{ $service->applicant_name }}</td>
                        </tr>
                        <tr>
                            <th>Petugas</th>
                            <td>{{ $service->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>{{ \Carbon\Carbon::parse($service->submission_date)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Deadline</th>
                            <td>
                                {{ \Carbon\Carbon::parse($service->deadline_date)->translatedFormat('d F Y') }}
                                @if($service->is_overdue_calc)
                                    <span class="badge bg-danger ms-2">Terlewat</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $service->notes ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Update Status Card -->
            <div class="card">
                <div class="card-header">Update Status</div>
                <div class="card-body">
                    <form action="{{ route('services.update', $service->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Ubah Status Ke:</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $service->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $service->status == 'processing' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="completed" {{ $service->status == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="overdue" {{ $service->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Update Catatan</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $service->notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>

                    <hr>

                    @if(auth()->user()->role === 'admin')
                        <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                            onsubmit="return confirm('Yakin hapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">Hapus Data</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection