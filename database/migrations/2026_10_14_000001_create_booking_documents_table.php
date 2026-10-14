<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A failed earlier run can leave the table half made. It never held
        // data, because the migration was not recorded as done.
        Schema::dropIfExists('booking_documents');

        Schema::create('booking_documents', function (Blueprint $table) {
            $table->id();
            // No database foreign key: the older tables may be MyISAM, which
            // an InnoDB table cannot reference. Booking::deleting removes a
            // booking's documents and their files instead.
            $table->unsignedBigInteger('booking_id')->index();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedInteger('size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_documents');
    }
};
