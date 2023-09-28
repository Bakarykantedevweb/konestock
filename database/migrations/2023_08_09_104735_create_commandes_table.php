<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade')->nullable();
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade')->nullable();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date');
            $table->string('telephone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commandes');
    }
}
