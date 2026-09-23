<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A record of every text sent from the admin panel: what was said, who
     * it went to, and whether it left the building. Failures are kept too,
     * so nobody has to guess later whether a guest was told something.
     */
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->text('numbers');                       // +91… , one per line
            $table->unsignedInteger('recipients')->default(0);
            $table->unsignedInteger('parts')->default(1);  // SMS per number
            $table->unsignedInteger('cost')->default(0);   // recipients x parts
            $table->string('status', 20)->default('sent'); // sent | failed
            $table->text('error')->nullable();
            $table->string('mode', 10)->default('cloud');  // cloud | local
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
