<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['user_id', 'category_id', 'spent_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['status', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'category_id', 'spent_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['status', 'name']);
        });
    }
};
