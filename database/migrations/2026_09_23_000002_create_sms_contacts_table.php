<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The hotel's phone book for marketing texts. Kept apart from the
     * "contacts" table, which holds Contact Us messages from the website.
     */
    public function up(): void
    {
        Schema::create('sms_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->nullable();
            $table->string('phone', 16)->unique();   // always +91XXXXXXXXXX
            $table->string('note', 190)->nullable(); // "walk-in", "wedding party"...
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_contacts');
    }
};
