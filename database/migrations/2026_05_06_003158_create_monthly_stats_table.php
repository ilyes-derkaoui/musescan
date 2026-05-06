<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->integer('total_scans')->default(0);
            $table->integer('total_visits')->default(0);
            $table->integer('total_feedback')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('monthly_stats'); }
};
