<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('declaration_frequency', ['monthly', 'quarterly', 'annually'])
                ->nullable()
                ->after('email_verified_at');

            $table->decimal('tax_level', '5,2')->nullable()->after('declaration_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('declaration_frequency');
            $table->dropColumn('tax_level');
        });
    }
};
