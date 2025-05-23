<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Discount;

class CheckDiscounts extends Command
{
    protected $signature = 'discounts:check';
    protected $description = 'Check the current state of discounts';

    public function handle()
    {
        $discounts = Discount::with('product')->get();

        $this->info('Current Discounts Status:');
        $this->table(
            ['Product', 'Percentage', 'Is Active', 'Active Status'],
            $discounts->map(function ($discount) {
                return [
                    'product' => $discount->product->name,
                    'percentage' => $discount->percentage . '%',
                    'is_active' => $discount->is_active ? 'true' : 'false',
                    'active_status' => $discount->is_active ? 'Aktif' : 'Nonaktif'
                ];
            })
        );
    }
}