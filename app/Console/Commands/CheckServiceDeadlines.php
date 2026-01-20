<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceRequest;
use Carbon\Carbon;

class CheckServiceDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check service deadlines and update status to overdue if necessary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking service deadlines...');

        // 1. Update status menjadi 'overdue' jika sudah lewat hari ini dan status belum selesai
        $count = ServiceRequest::whereNotIn('status', ['completed', 'overdue'])
            ->whereDate('deadline_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        if ($count > 0) {
            $this->error("Updated {$count} services to OVERDUE status.");
        } else {
            $this->info('No overdue services found.');
        }

        // 2. Logic Notifikasi (Log dulu untuk sekarang)
        $nearDeadline = ServiceRequest::whereNotIn('status', ['completed', 'overdue'])
            ->whereDate('deadline_date', '=', Carbon::now()->addDays(2))
            ->get();

        foreach ($nearDeadline as $service) {
            $this->comment("Reminder: Service {$service->registration_number} is approaching deadline in 2 days.");
        }

        $this->info('Done.');
    }
}
