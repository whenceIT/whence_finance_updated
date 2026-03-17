<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;

class TestMemoryUsage extends Command
{
    protected $signature = 'test:memory';
    protected $description = 'Test memory usage of different loan loading approaches';

    public function handle()
    {
        $this->info('Testing memory usage...');
        
        // Test 1: Original approach - load all loans with all fields and client
        $this->line('1. Original approach - loading all fields:');
        gc_collect_cycles();
        $start1 = memory_get_usage(true);
        $loans1 = Loan::with('client')->orderBy('id', 'desc')->get();
        $end1 = memory_get_usage(true);
        $diff1 = $end1 - $start1;
        $this->info("   Loaded " . count($loans1) . " loans");
        $this->info("   Memory used: " . number_format($diff1 / 1024 / 1024, 2) . " MB");
        
        unset($loans1);
        gc_collect_cycles();
        
        // Test 2: Optimized approach - load only necessary fields
        $this->line("\n2. Optimized approach - loading only necessary fields:");
        $start2 = memory_get_usage(true);
        $loans2 = Loan::with(['client' => function($query) {
            $query->select('id', 'first_name', 'last_name');
        }])->select('id', 'client_id', 'principal')->orderBy('id', 'desc')->get();
        $end2 = memory_get_usage(true);
        $diff2 = $end2 - $start2;
        $this->info("   Loaded " . count($loans2) . " loans");
        $this->info("   Memory used: " . number_format($diff2 / 1024 / 1024, 2) . " MB");
        
        unset($loans2);
        gc_collect_cycles();
        
        // Calculate savings
        $savings = $diff1 - $diff2;
        $savingsPercent = ($savings / $diff1) * 100;
        $this->line("\nMemory saved: " . number_format($savings / 1024 / 1024, 2) . " MB (" . number_format($savingsPercent, 1) . "%)");
        
        return 0;
    }
}
