<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('users', 'google_token')) {
                $table->text('google_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'facebook_token')) {
                $table->text('facebook_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'google_refresh_token')) {
                $table->text('google_refresh_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'facebook_refresh_token')) {
                $table->text('facebook_refresh_token')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'google_id', 'facebook_id',
                'google_token', 'facebook_token',
                'google_refresh_token', 'facebook_refresh_token',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
