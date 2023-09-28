<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GerantController;
use App\Http\Controllers\Admin\MagasinController;
use App\Http\Controllers\Admin\ProduitController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\BoutiqueController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FournisseurController;
use App\Http\Controllers\Admin\ProduitRetourController;
use App\Http\Controllers\Admin\CommandeMagasinController;
use App\Http\Controllers\Admin\CommandeBoutiqueController;
use App\Http\Controllers\Admin\OperationMagasinController;
use App\Http\Controllers\Admin\OperationBoutiqueController;
use App\Http\Controllers\Admin\BoutiqueEnBoutiqueController;

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
        Route::get('magasin/{nom}/gerant/{prenom}', 'magasin');
        Route::get('magasin/{nom}/gerant/{prenom}/produit', 'produitAjout');
        Route::post('magasin/{nom}/gerant/{prenom}/produit', 'produitSave');
        Route::get('magasin/{nom}/gerant/{prenom}/produit/{code}/edit', 'produitEdit');
        Route::post('magasin/{nom}/gerant/{prenom}/produit/{code}/edit', 'produitUpdate');
        Route::get('magasin/{nom}/gerant/{prenom}/produit/{code}/delete', 'produitDelete');
        Route::get('/search/products', 'search')->name('search.products');

    });

    // Route Operation Magasin en Magasin
    Route::controller(OperationMagasinController::class)->group(function(){
        Route::get('operation/{nom}/gerant/{prenom}', 'listMagasin');
        Route::get('operation/{nom}/gerant/{prenom}/index/{magasinA}', 'index');
        Route::get('operation/{nom}/gerant/{prenom}/create/{magasinA}', 'create');
        Route::post('operation/{nom}/gerant/{prenom}/create/{magasinA}', 'saveOperationMagasin');
        Route::get('operation/{nom}/historiques', 'historiquesMagasin');
        Route::get('operation/{nom}/edit/{operation_id}', 'edit');
        Route::post('operation/{nom}/edit/{operation_id}', 'update');
    });

    // Route Operation Magasin en Boutique
    Route::controller(OperationBoutiqueController::class)->group(function () {
        Route::get('operationBoutique/{nom}/boutique', 'index');
        Route::get('operationBoutique/{nom}/boutique/{nomBoutique}', 'histoBoutique');
        Route::get('operationBoutique/{nom}/boutique/{nomBoutique}/create', 'create');
        Route::post('operationBoutique/{nom}/boutique/{nomBoutique}/create', 'saveOperationBoutique');
        Route::get('operationBoutique/{nom}/boutique/{nomBoutique}/historique', 'Historique');
        Route::get('operationBoutique/{nom}/boutique/{nomBoutique}/edit/{operation_id}', 'edit');
        Route::post('operationBoutique/{nom}/boutique/{nomBoutique}/edit/{operation_id}', 'update');
    });

    // Route Commande Magasin
    Route::controller(CommandeMagasinController::class)->group(function(){
        Route::get('commandeMagasin/{nom}','index');
        Route::get('commandeMagasin/{nom}/create', 'create');
        Route::post('commandeMagasin/{nom}/create', 'save');
        Route::get('commandeMagasin/{nom}/facture/{numero}', 'facture');
    });

    // Route Commande Boutique
    Route::controller(CommandeBoutiqueController::class)->group(function () {
        Route::get('commande/{nom}', 'index');
        Route::get('commande/{nom}/create', 'create');
        Route::post('commande/{nom}/create', 'save');
        Route::get('commande/{nom}/facture/{numero}', 'facture');
    });

    Route::controller(BoutiqueController::class)->group(function(){
        Route::get('boutiques','index');
        Route::get('boutique/{nom}', 'boutique');
        Route::get('boutique/{nom}/create', 'create');
        Route::post('boutique/{nom}/create', 'save');
        Route::get('boutique/{nom}/edit/{code}', 'edit');
        Route::post('boutique/{nom}/edit/{code}', 'update');
    });

    // Route Boutique en Boutique
    Route::controller(BoutiqueEnBoutiqueController::class)->group(function(){
        Route::get('boutique/{nom}/operation', 'index');
        Route::get('boutique/{nom}/operation/{nom_boutique}', 'list');
        Route::get('boutique/{nom}/operation/{nom_boutique}/historique', 'historique');
        Route::get('boutique/{nom}/operation/{nom_boutique}/create', 'create');
        Route::post('boutique/{nom}/operation/{nom_boutique}/create', 'save');
    });

    // Produit Retour Controller
    Route::controller(ProduitRetourController::class)->group(function(){
        Route::get('boutique/{nom}/retour','index');
        Route::get('boutique/{nom}/retour/{nomMagasin}', 'list');
        Route::get('boutique/{nom}/retour/{nomMagasin}/create', 'create');
        Route::post('boutique/{nom}/retour/{nomMagasin}/create', 'save');
        Route::get('boutique/{nom}/retour/{nomMagasin}/edit/{operation_id}', 'edit');
        Route::post('boutique/{nom}/retour/{nomMagasin}/edit/{operation_id}', 'update');
    });


    Route::get('fournisseurs', [FournisseurController::class, 'index']);
    
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'index')->name('user.index');
        Route::get('activity/log', 'activityLog')->name('activity.log');
    });

    //Route Session
    Route::resource('session', SessionController::class, ['except' => ['destroy', 'update', 'edit']]);


});

Auth::routes();

// -----------------------------login----------------------------------------//
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'authenticate']);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
