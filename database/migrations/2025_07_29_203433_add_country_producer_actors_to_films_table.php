<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->string('country')->nullable()->after('release_date');
            $table->string('producer')->nullable()->after('country');
            $table->text('actors')->nullable()->after('producer');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn(['country', 'producer', 'actors']);
        });
    }
};
