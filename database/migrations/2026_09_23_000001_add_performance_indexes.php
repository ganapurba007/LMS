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
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_notif_user_read_created');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->index(['user_id', 'is_completed'], 'idx_mat_prog_user_completed');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['quiz_id', 'submitted_at'], 'idx_quiz_attempts_quiz_submitted');
            $table->index(['student_id', 'submitted_at'], 'idx_quiz_attempts_student_submitted');
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->index(['student_id', 'grade'], 'idx_asg_sub_student_grade');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->index(['class_id', 'order'], 'idx_materials_class_order');
            $table->index(['instructor_id', 'order'], 'idx_materials_instructor_order');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->index(['class_id', 'deadline'], 'idx_quizzes_class_deadline');
            $table->index(['instructor_id', 'deadline'], 'idx_quizzes_instructor_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notif_user_read_created');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropIndex('idx_mat_prog_user_completed');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_quiz_attempts_quiz_submitted');
            $table->dropIndex('idx_quiz_attempts_student_submitted');
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropIndex('idx_asg_sub_student_grade');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex('idx_materials_class_order');
            $table->dropIndex('idx_materials_instructor_order');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex('idx_quizzes_class_deadline');
            $table->dropIndex('idx_quizzes_instructor_deadline');
        });
    }
};
