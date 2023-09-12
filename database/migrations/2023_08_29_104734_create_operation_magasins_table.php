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
        Schema::create('operation_magasins', function (Blueprint $table) {
            $table->id();
            $table->integer('magasin_depart')->constrained('magasins')->onDelete('cascade');
            $table->foreignId('magasin_arrive')->constrained('magasins')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->float('nombre_carton');
            $table->integer('nombre_piece');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_magasins');
    }
};
