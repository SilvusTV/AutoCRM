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
            // Add type field to distinguish between invoices and quotes
            $table->enum('type', ['invoice', 'quote'])->default('invoice')->after('invoice_number');
            // Add payment terms fields
            $table->enum('payment_terms', ['immediate', '15_days', '30_days', '45_days', '60_days', 'end_of_month'])
                ->default('30_days')
                ->after('payment_date');

            $table->enum('payment_method', ['bank_transfer', 'check', 'cash', 'credit_card', 'paypal'])
                ->default('bank_transfer')
                ->after('payment_terms');

            $table->enum('late_fees', ['none', 'legal_rate', 'fixed_percent'])
                ->default('none')
                ->after('payment_method');

            $table->string('bank_account')->nullable()->after('late_fees');

            // Add document text fields
            $table->text('intro_text')->nullable()->after('bank_account');
            $table->text('conclusion_text')->nullable()->after('intro_text');
            $table->text('footer_text')->nullable()->after('conclusion_text');

            // Add field for invoice validation
            $table->boolean('is_validated')->default(false)->after('status');

            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled', 'overdue'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('payment_terms');
            $table->dropColumn('payment_method');
            $table->dropColumn('late_fees');
            $table->dropColumn('bank_account');
            $table->dropColumn('intro_text');
            $table->dropColumn('conclusion_text');
            $table->dropColumn('footer_text');
            $table->dropColumn('is_validated');
            $table->enum('status', ['brouilon', 'envoyee', 'payee'])->default('brouillon')->change();
        });
    }
};
