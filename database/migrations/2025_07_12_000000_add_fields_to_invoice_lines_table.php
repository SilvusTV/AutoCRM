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
        Schema::table('invoice_lines', function (Blueprint $table) {
            // Add item_type field to distinguish between service and product
            $table->enum('item_type', ['service', 'product'])->default('service')->after('invoice_id');

            // Add is_expense field to distinguish between regular items and expenses
            $table->boolean('is_expense')->default(false)->after('item_type');

            // Add discount_percent field for line item discounts
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');

            // Add tva_rate field for individual line item TVA rates
            $table->decimal('tva_rate', 5, 2)->nullable()->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropColumn('item_type');
            $table->dropColumn('is_expense');
            $table->dropColumn('discount_percent');
            $table->dropColumn('tva_rate');
        });
    }
};
