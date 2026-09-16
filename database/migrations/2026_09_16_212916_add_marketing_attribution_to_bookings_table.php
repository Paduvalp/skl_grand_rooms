<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records where each booking came from.
 *
 * The five utm_* columns are the standard link tags used by Google, Meta and
 * every other ad platform. The last three are the fallback for visitors who
 * arrive without any tags, which is most of them.
 *
 * Every column is nullable: bookings taken before this was added, and guests
 * who simply typed the address in, keep working exactly as before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('utm_source', 100)->nullable()->after('admin_remark');
            $table->string('utm_medium', 100)->nullable()->after('utm_source');
            $table->string('utm_campaign', 150)->nullable()->after('utm_medium');
            $table->string('utm_term', 150)->nullable()->after('utm_campaign');
            $table->string('utm_content', 150)->nullable()->after('utm_term');

            // Where the visitor came from when the link carried no utm tags.
            $table->string('referrer_host', 191)->nullable()->after('utm_content');

            // The first page of the visit, so you can see which page the ad
            // actually landed on.
            $table->string('landing_page', 255)->nullable()->after('referrer_host');

            // When the visit that led to this booking started.
            $table->timestamp('first_seen_at')->nullable()->after('landing_page');

            // The two columns the reports group by.
            $table->index('utm_source');
            $table->index('utm_campaign');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['utm_source']);
            $table->dropIndex(['utm_campaign']);

            $table->dropColumn([
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'referrer_host',
                'landing_page',
                'first_seen_at',
            ]);
        });
    }
};
