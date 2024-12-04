<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->integer('vote')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE exams ADD CONSTRAINT check_vote CHECK (vote IS NULL OR (vote >= 18 AND vote <= 30));');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
