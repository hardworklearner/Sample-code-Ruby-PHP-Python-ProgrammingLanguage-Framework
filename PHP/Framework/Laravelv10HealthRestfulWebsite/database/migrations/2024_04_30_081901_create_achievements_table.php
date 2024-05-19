<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('achievement_name');
            $table->integer('level')->default(1);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained(
                table: 'categories',
                indexName: 'achievements_categories_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            // Here, 'your_foreign_key_name' is the name of the foreign key constraint
            $table->dropForeign('achievements_categories_id');
        });
        Schema::dropIfExists('achievements');
    }
};
