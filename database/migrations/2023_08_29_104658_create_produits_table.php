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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('nom_produit');
            $table->float('nombre_carton');
            $table->integer('nombre_piece');
            $table->integer('piece_totale')->nullable();
            $table->float('prix_unitaire');
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade')->nullable();
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade')->nullable();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
