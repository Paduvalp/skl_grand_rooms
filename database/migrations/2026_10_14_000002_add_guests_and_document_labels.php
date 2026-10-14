<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Everyone staying besides the person who booked, as a list of
        // {name, phone} pairs.
        Schema::table('bookings', function (Blueprint $table) {
            $table->json('additional_guests')->nullable()->after('customer_phone');
        });

        // What the file is, e.g. "Aadhaar - Ravi" or "Payment receipt".
        Schema::table('booking_documents', function (Blueprint $table) {
            $table->string('label', 100)->nullable()->after('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn('additional_guests'));
        Schema::table('booking_documents', fn (Blueprint $table) => $table->dropColumn('label'));
    }
};
