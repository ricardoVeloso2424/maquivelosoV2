<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->index('status', 'machines_status_idx');
            $table->index('category_id', 'machines_category_id_idx');
            $table->index('price', 'machines_price_idx');
            $table->index('created_at', 'machines_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropIndex('machines_status_idx');
            $table->dropIndex('machines_category_id_idx');
            $table->dropIndex('machines_price_idx');
            $table->dropIndex('machines_created_at_idx');
        });
    }
};
