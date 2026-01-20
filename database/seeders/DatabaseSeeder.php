<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\ServiceRequest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@disdukcapil.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas Loket 1',
            'email' => 'petugas1@disdukcapil.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // 2. Create Service Types
        $ktp = ServiceType::create([
            'name' => 'Pembuatan KTP Elektronik',
            'sla_days' => 3, // 3 Hari kerja
            'description' => 'Pencetakan KTP baru atau penggantian',
        ]);

        $kk = ServiceType::create([
            'name' => 'Kartu Keluarga (KK)',
            'sla_days' => 2,
            'description' => 'Penerbitan KK baru, pisah KK, atau perubahan data',
        ]);

        $akta = ServiceType::create([
            'name' => 'Akta Kelahiran',
            'sla_days' => 5,
            'description' => 'Pencatan sipil kelahiran baru',
        ]);

        // 3. Create Dummy Service Requests
        ServiceRequest::create([
            'registration_number' => 'REG-20230101-001',
            'service_type_id' => $ktp->id,
            'user_id' => 2, // Petugas 1
            'applicant_name' => 'Budi Santoso',
            'submission_date' => now()->subDays(5),
            'deadline_date' => now()->subDays(2), // Sudah lewat (Overdue)
            'status' => 'pending', // Nanti akan jadi overdue saat dicek scheduler
            'notes' => 'Menunggu blanko',
        ]);

        ServiceRequest::create([
            'registration_number' => 'REG-20230103-002',
            'service_type_id' => $kk->id,
            'user_id' => 2,
            'applicant_name' => 'Siti Aminah',
            'submission_date' => now()->subDays(1),
            'deadline_date' => now()->addDays(1), // Besok (Urgent)
            'status' => 'processing',
        ]);

        ServiceRequest::create([
            'registration_number' => 'REG-20230103-003',
            'service_type_id' => $akta->id,
            'user_id' => 2,
            'applicant_name' => 'Bayu Pradana',
            'submission_date' => now()->subDays(10),
            'deadline_date' => now()->subDays(5),
            'status' => 'completed',
        ]);
    }
}
