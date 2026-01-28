<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class FixOrderPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fix-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and update the total_price, total_discount, and net_amount for all existing orders.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix order prices...');

        // Eager load details to avoid N+1 queries
        $orders = Order::with('details')->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to process.');

            return 0;
        }

        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        $updatedCount = 0;

        foreach ($orders as $order) {
            // Recalculate totals from details
            $total_price = $order->details->sum(function ($detail) {
                return $detail->ordd_original_price * $detail->ordd_count;
            });

            $net_amount = $order->details->sum(function ($detail) {
                return $detail->ordd_price * $detail->ordd_count;
            });

            $total_discount = $total_price - $net_amount;

            // Check if an update is needed
            if (
                $order->total_price != $total_price ||
                $order->total_discount != $total_discount ||
                $order->net_amount != $net_amount
            ) {
                $order->total_price = $total_price;
                $order->total_discount = $total_discount;
                // Assuming net_amount does not include shipping for this fix
                $order->net_amount = $net_amount;
                $order->save();
                $updatedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info('Processing complete.');
        $this->info("{$updatedCount} orders were updated.");

        return 0;
    }
}
