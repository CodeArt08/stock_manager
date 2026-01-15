<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\ZapController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\PDFController;
use App\Models\Etablissement;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any}', function () {
    return view('app'); // Vue principale (app.blade.php) qui charge l'application Vue
})->where('any', '.*');



// Route::get('/recherche-stock', [StockController::class, 'recherche_stock']);


// // Route::get('/liste-dist', [DistributionController::class, 'liste_dist']);
// Route::get('/ajout-dist', [DistributionController::class, 'ajouter_dist']);
// Route::post('/ajout-dist/traitement', [DistributionController::class, 'ajouter_dist_traitement']);
// Route::get('/annuler-dist/{id}', [DistributionController::class, 'annuler_dist']);


// //
// Route::get('/ajout-zap', [ZapController::class, 'ajouter_zap']);

// Route::post('/update-zap/traitement', [ZapController::class, 'update_zap_traitement']);
// Route::get('/delete-zap/{id}', [ZapController::class, 'delete_zap']);




// Route::get('/etab/{id}', [EtablissementController::class, 'etab']);
// Route::post('/ajout-etab', [EtablissementController::class, 'ajouter_etab']);
// Route::get('/update-etab/{id}', [EtablissementController::class, 'update_etab']);
// Route::post('/update-etab/traitement', [EtablissementController::class, 'update_etab_traitement']);
// Route::get('/delete-etab/{id}', [EtablissementController::class, 'delete_etab']);


//  
// Route::get('/liste-nonDist', [HistoriqueController::class, 'liste_nonDist']);
// Route::get('/historique-dist/{id}', [HistoriqueController::class, 'historique_dist']);
// Route::get('/liste-date', [HistoriqueController::class, 'liste_date']);
// Route::get('/recherche', [HistoriqueController::class, 'recherche']);


// // PDF

// Route::post('/generate-stock-pdf', [PDFController::class, 'generateStockPDF']);

