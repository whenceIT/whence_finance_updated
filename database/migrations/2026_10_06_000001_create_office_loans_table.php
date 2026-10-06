<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeLoansTable extends Migration
{
    public function up()
    {
        Schema::create('office_loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('office_id');
            $table->unsignedInteger('staff_id');
            $table->decimal('principal', 15, 2)->default(0);
            $table->decimal('interest', 15, 2)->default(0);
            $table->enum('status', [
                'pending',
                'approved',
                'disbursed',
                'partially_paid',
                'fully_paid',
                'declined',
            ])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('office_id');
            $table->index('staff_id');
            $table->index('status');

            $table->foreign('office_id')
                  ->references('id')->on('offices')
                  ->onDelete('restrict');

            $table->foreign('staff_id')
                  ->references('id')->on('users')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_loans');
    }
}
