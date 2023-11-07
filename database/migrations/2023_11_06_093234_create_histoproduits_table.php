<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoproduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histoproduits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('nom_produit');
            $table->float('nombre_carton');
            $table->integer('nombre_piece')->default('1');
            $table->integer('piece_totale')->nullable();
            $table->integer('delete_as')->default('0');
            $table->float('prix_unitaire');
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade')->nullable();
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade')->nullable();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
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
        Schema::dropIfExists('histoproduits');
    }
}
