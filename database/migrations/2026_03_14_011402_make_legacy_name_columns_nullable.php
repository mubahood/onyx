<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make legacy name columns nullable so form submissions that only
        // provide full_name do not fail the NOT NULL constraint.
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name', 191)->nullable()->change();
            $table->string('last_name', 191)->nullable()->change();
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->string('first_name', 191)->nullable()->change();
            $table->string('last_name', 191)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name', 191)->nullable(false)->change();
            $table->string('last_name', 191)->nullable(false)->change();
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->string('first_name', 191)->nullable(false)->change();
            $table->string('last_name', 191)->nullable(false)->change();
        });
    }
};
