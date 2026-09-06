<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_email', 191);
            $table->string('customer_phone', 30);
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('nights')->default(1);
            $table->unsignedInteger('guests')->default(1);
            $table->unsignedInteger('rooms_count')->default(1);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('admin_remark')->nullable();
            $table->timestamps();

            $table->index(['check_in', 'check_out']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
