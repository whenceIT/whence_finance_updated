<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rule', 80)->index();
            $table->string('severity', 20)->default('info'); // critical | warning | info
            $table->string('title', 255);
            $table->text('description');
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->json('meta')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();

            $table->index(['rule', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
