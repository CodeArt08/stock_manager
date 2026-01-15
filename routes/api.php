<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ZapController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\UtilisateurController;
use App\Models\Utilisateur;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/stocks', [StockController::class, 'liste_stock']);
Route::post('/ajout-stock/traitement', [StockController::class, 'ajouter_stock_traitement']);
Route::delete('/delete-stock/{id}', [StockController::class, 'delete_stock']);
Route::get('/update-stock/{id}', [StockController::class, 'update_stock']);
Route::post('/update-stock/traitement', [StockController::class, 'update_stock_traitement']);
Route::get('/recherche-stock', [StockController::class, 'recherche_stock']);

Route::get('/zaps', [ZapController::class, 'liste_zap']);
Route::post('/ajout-zap/traitement', [ZapController::class, 'ajouter_zap_traitement']);
Route::delete('/delete-zap/{id}', [ZapController::class, 'delete_zap']);
Route::get('/update-zap/{id}', [ZapController::class, 'update_zap']);
Route::post('/update-zap/traitement', [ZapController::class, 'update_zap_traitement']);
Route::get('/recherche-zap', [ZapController::class, 'recherche_zap']);
Route::get('/recherche-zapPrim', [EtablissementController::class, 'recherche_zapPrim']);

Route::get('/historiques', [HistoriqueController::class, 'liste_historique']);
Route::get('/liste-date', [HistoriqueController::class, 'liste_date']);
Route::get('/liste-nonDist', [HistoriqueController::class, 'liste_nonDist']);
Route::get('/historique-dist/{id}', [HistoriqueController::class, 'historique_dist']);
Route::get('/hist-filter', [HistoriqueController::class, 'filterByDate']);
Route::get('/liste-nonDist', [HistoriqueController::class, 'liste_nonDist']);
Route::get('/liste-nonDist2', [HistoriqueController::class, 'listeNonDist2']);
Route::get('/liste-date', [HistoriqueController::class, 'liste_date']);
Route::get('/fetchZap', [HistoriqueController::class, 'fetchZap']);


Route::post('/pdf-zap', [PDFController::class, 'generateStockPdf2']);
Route::post('/generate-stock-pdf', [PDFController::class, 'generateStockPdf']);


Route::get('/distributions', [DistributionController::class, 'liste_dist']);
Route::post('/ajout-dist/traitement', [DistributionController::class, 'ajouter_dist_traitement']);
Route::get('/ajout-dist', [DistributionController::class, 'ajouter_dist']);
Route::delete('/annuler-dist/{id}', [DistributionController::class, 'annuler_dist']);

Route::get('/etablissements', [EtablissementController::class, 'liste_etab']);
Route::get('/etab/{id}', [EtablissementController::class, 'etab']);
Route::post('/ajout-etab', [EtablissementController::class, 'ajouter_etab']);
Route::put('/update-etab/traitement', [EtablissementController::class, 'update_etab_traitement']);
Route::get('/recherche-etab', [EtablissementController::class, 'recherche_etab']);
Route::delete('/delete-etab/{id}', [EtablissementController::class, 'delete_etab']);

Route::get('/users', [UtilisateurController::class, 'liste_user']);
Route::post('/ajout-user/traitement', [UtilisateurController::class, 'ajouter_user']);
Route::get('/update-user/{id}', [UtilisateurController::class, 'update_user']);
Route::post('/update-user/traitement', [UtilisateurController::class, 'update_user_traitement']);
Route::delete('/delete-user/{id}', [UtilisateurController::class, 'delete_user']);
Route::post('/login', [UtilisateurController::class, 'login']);
