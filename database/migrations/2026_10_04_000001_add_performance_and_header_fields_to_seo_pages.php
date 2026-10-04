<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('seo_pages', 'headers')) {
                $table->json('headers')->nullable()->after('headings');
            }
            if (!Schema::hasColumn('seo_pages', 'ttfb_ms')) {
                $table->integer('ttfb_ms')->nullable()->after('headers');
            }
            if (!Schema::hasColumn('seo_pages', 'html_size_bytes')) {
                $table->integer('html_size_bytes')->nullable()->after('ttfb_ms');
            }
            if (!Schema::hasColumn('seo_pages', 'redirect_chain')) {
                $table->json('redirect_chain')->nullable()->after('html_size_bytes');
            }
            if (!Schema::hasColumn('seo_pages', 'redirect_count')) {
                $table->integer('redirect_count')->default(0)->after('redirect_chain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seo_pages', function (Blueprint $table) {
            $table->dropColumn(['headers', 'ttfb_ms', 'html_size_bytes', 'redirect_chain', 'redirect_count']);
        });
    }
};
