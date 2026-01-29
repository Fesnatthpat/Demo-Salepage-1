<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class GenerateAdminCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admins:generate-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique 6-digit codes for admins who do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for admins without an admin code...');

        $adminsWithoutCode = Admin::whereNull('admin_code')->get();

        if ($adminsWithoutCode->isEmpty()) {
            $this->info('All admins already have a code. Nothing to do.');
            return 0;
        }

        $this->info("Found {$adminsWithoutCode->count()} admin(s) to update.");

        $bar = $this->output->createProgressBar($adminsWithoutCode->count());
        $bar->start();

        foreach ($adminsWithoutCode as $admin) {
            do {
                $code = rand(100000, 999999);
            } while (Admin::where('admin_code', $code)->exists());

            $admin->admin_code = $code;
            $admin->save();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nSuccessfully generated codes for all admins.");
        return 0;
    }
}
