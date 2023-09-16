<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoutiqueEnBoutiqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boutique_en_boutique', function (Blueprint $table) {
            $table->id();
            $table->integer('boutique_depart')->constrained('boutiques')->onDelete('cascade');
            $table->foreignId('boutique_arrive')->constrained('boutiques')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->float('nombre_carton')->nullable();
            $table->integer('nombre_piece');
            $table->date('date');
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
        Schema::dropIfExists('boutique_en_boutique');
    }
}
