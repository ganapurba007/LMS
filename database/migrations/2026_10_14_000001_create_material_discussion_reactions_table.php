<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_discussion_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('material_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20)->default('thumbs_up');
            $table->timestamps();

            $table->unique(['discussion_id', 'user_id', 'type'], 'mat_disc_reaction_unique');
            $table->index(['discussion_id', 'type'], 'idx_mat_disc_reactions_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_discussion_reactions');
    }
};
