<?php

use App\Http\Controllers\Admin\BoutiqueController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GerantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FournisseurController;
use App\Http\Controllers\Admin\MagasinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Route::prefix('admin')->middleware(['auth'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });

    Route::get('gerants', [GerantController::class, 'index']);
    //Routes Magasin
    Route::controller(MagasinController::class)->group(function () {
        Route::get('magasins', 'index');
        Route::get('magasin/{nom}', 'magasin');
        Route::get('magasin/{nom}/produit', 'produitAjout');
        Route::post('magasin/{nom}/produit', 'produitSave');
        Route::get('magasin/{nom}/magasin', 'produitMag');
        Route::post('magasin/{nom}/magasin', 'produitMagSave');
        Route::get('magasin/{nom}/boutique', 'produitBout');
        Route::post('magasin/{nom}/boutique', 'produitBoutSave');
        Route::get('magasin/{nom}/historique', 'historique');
        Route::get('magasin/{nom}/historiques/{magasin}', 'historiqueMag');
        Route::get('magasin/{nom}/historiques/{magasin}/tout', 'historiqueMagTout');
        Route::get('magasin/{nom}/commande', 'commande');
        Route::post('magasin/{nom}/commande', 'savecommande');
        Route::get('magasin/{nom}/commande-list', 'commandeList');
        Route::get('magasin/{nom}/commande-list/{numero}/facture', 'facture');
        Route::get('magasin/{nom}/produit/{code}/edit', 'produitEdit');
        Route::post('magasin/{nom}/produit/{code}/edit', 'produitUpdate');
    });

    Route::controller(BoutiqueController::class)->group(function(){
        Route::get('boutiques','index');
        Route::get('boutique/{nom}', 'boutique');
        Route::get('boutique/{nom}/historiques', 'historique');
        Route::get('boutique/{nom}/historiques/{magasin}', 'historiqueMag');
        Route::get('boutique/{nom}/historiques/{magasin}/tout', 'historiqueMagTout');
        Route::get('boutique/{nom}/commande', 'commande');
        Route::post('boutique/{nom}/commande', 'savecommande');
        Route::get('boutique/{nom}/commande-list', 'commandeList');
        Route::get('boutique/{nom}/commande-list/{numero}/facture', 'facture');
    });
    Route::get('fournisseurs', [FournisseurController::class, 'index']);


});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
