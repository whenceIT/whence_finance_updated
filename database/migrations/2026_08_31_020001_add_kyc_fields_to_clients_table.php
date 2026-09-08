<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('tpin')->nullable()->after('id');
            $table->string('address_line1')->nullable()->after('address');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('employer')->nullable()->after('salary');
            $table->string('employer_address')->nullable()->after('employer');
            $table->string('business_name')->nullable()->after('employer_address');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('annual_income')->nullable()->after('salary');
            $table->string('phone_primary')->nullable()->after('mobile');
            $table->string('phone_secondary')->nullable()->after('phone_primary');
            $table->string('email_primary')->nullable()->after('email');
            $table->string('next_of_kin_name')->nullable()->after('phone_secondary');
            $table->string('next_of_kin_relationship')->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_phone')->nullable()->after('next_of_kin_relationship');
            $table->string('next_of_kin_address')->nullable()->after('next_of_kin_phone');
            $table->string('guarantor_name')->nullable()->after('next_of_kin_address');
            $table->string('guarantor_nrc')->nullable()->after('guarantor_name');
            $table->string('guarantor_phone')->nullable()->after('guarantor_nrc');
            $table->string('guarantor_address')->nullable()->after('guarantor_phone');
            $table->string('guarantor_employer')->nullable()->after('guarantor_address');
            $table->string('guarantor_relationship')->nullable()->after('guarantor_employer');
            $table->string('verification_status')->default('pending');
            $table->unsignedBigInteger('verified_by_id')->nullable();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'tpin', 'address_line1', 'address_line2', 'city',
                'employer', 'employer_address', 'business_name', 'business_type',
                'annual_income', 'phone_primary', 'phone_secondary', 'email_primary',
                'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_phone',
                'next_of_kin_address', 'guarantor_name', 'guarantor_nrc', 'guarantor_phone',
                'guarantor_address', 'guarantor_employer', 'guarantor_relationship',
                'verification_status', 'verified_by_id'
            ]);
        });
    }
};
