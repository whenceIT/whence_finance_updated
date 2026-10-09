<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetExpensesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('fleet_expenses');
        Schema::create('fleet_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('fleet_id');
            $table->date('expense_date');
            // Category: Maintenance | Repairs | Accidents | Tyres | Insurance | Spare Parts | Other
            $table->string('category');
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reference_number')->nullable(); // receipt / invoice ref
            $table->string('document_path')->nullable();
            $table->string('recorded_by')->nullable();
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('fleet_expenses');
    }
}
