<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->foreignId('saison_id')->constrained('saisons')->onDelete('cascade');
            $table->integer('matches_joues')->default(0);
            $table->integer('victoires')->default(0);
            $table->integer('nuls')->default(0);
            $table->integer('defaites')->default(0);
            $table->integer('buts_pour')->default(0);
            $table->integer('buts_contre')->default(0);
            $table->integer('difference_buts')->default(0);
            $table->integer('points')->default(0);
            $table->integer('position')->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->unique(['equipe_id', 'competition_id', 'saison_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('classements');
    }
};
