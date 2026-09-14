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
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('question_type')->default('multiple_choice')->after('question_text');
        });

        Schema::table('question_bank', function (Blueprint $table) {
            $table->string('question_type')->default('multiple_choice')->after('question_text');
        });

        Schema::table('quiz_question_options', function (Blueprint $table) {
            $table->text('match_text')->nullable()->after('option_text');
        });

        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->text('match_text')->nullable()->after('option_text');
        });

        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->json('answer_data')->nullable()->after('selected_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
        });

        Schema::table('question_bank', function (Blueprint $table) {
            $table->dropColumn('question_type');
        });

        Schema::table('quiz_question_options', function (Blueprint $table) {
            $table->dropColumn('match_text');
        });

        Schema::table('question_bank_options', function (Blueprint $table) {
            $table->dropColumn('match_text');
        });

        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropColumn('answer_data');
        });
    }
};
