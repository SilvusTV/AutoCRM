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
        Schema::table('invoices', function (Blueprint $table) {
            // First drop the foreign key constraint on client_id
            $table->dropForeign(['client_id']);

            // Make client_id nullable
            $table->foreignId('client_id')->nullable()->change();

            // Re-add the foreign key constraint with onDelete('set null')
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');

            // Add company_id foreign key
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop the company_id foreign key and column
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');

            // Drop the client_id foreign key
            $table->dropForeign(['client_id']);

            // Make client_id required again
            $table->foreignId('client_id')->nullable(false)->change();

            // Re-add the original foreign key constraint with onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
