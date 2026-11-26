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
    Schema::table('visit_records', function (Blueprint $table) {
        $table->index('badge_number');
        $table->index('current_status');
        $table->index('check_in');
        $table->index('check_out');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_records', function (Blueprint $table) {
            //
        });
    }
};
