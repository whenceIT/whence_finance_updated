<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDebtRecoveryToLoanTransactionsPpFp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MySQL enum modification requires dropping and recreating the column
        // This is a workaround to add 'debt_recovery' to the enum
        
        // First, check if we're using MySQL
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Get the current enum values
            $table = 'loan_transactions_pp_fp';
            $column = 'payment_apply_to';
            
            // Check if the column already has debt_recovery
            $results = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'");
            
            if (!empty($results)) {
                $type = $results[0]->Type;
                
                // Check if debt_recovery is already in the enum
                if (strpos($type, 'debt_recovery') === false) {
                    // Modify the enum to include debt_recovery
                    $newType = str_replace(
                        "enum('full_payment','part_payment','reloan_payment')",
                        "enum('full_payment','part_payment','reloan_payment','debt_recovery')",
                        $type
                    );
                    
                    DB::statement("ALTER TABLE {$table} MODIFY {$column} {$newType}");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally remove the debt_recovery value
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            $table = 'loan_transactions_pp_fp';
            $column = 'payment_apply_to';
            
            $results = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'");
            
            if (!empty($results)) {
                $type = $results[0]->Type;
                
                if (strpos($type, 'debt_recovery') !== false) {
                    $newType = str_replace(
                        "enum('full_payment','part_payment','reloan_payment','debt_recovery')",
                        "enum('full_payment','part_payment','reloan_payment')",
                        $type
                    );
                    
                    DB::statement("ALTER TABLE {$table} MODIFY {$column} {$newType}");
                }
            }
        }
    }
}
