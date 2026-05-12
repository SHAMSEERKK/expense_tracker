<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->after('user_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2)->after('category_id');
            $table->text('description')->after('amount');
            $table->timestamp('spent_at')->after('description');

            $table->index(['user_id', 'spent_at']);
            $table->index(['category_id', 'spent_at']);
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'spent_at']);
            $table->dropIndex(['category_id', 'spent_at']);
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['amount', 'description', 'spent_at']);
        });
    }
};
