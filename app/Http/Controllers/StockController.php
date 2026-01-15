<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Distribution;

class StockController extends Controller
{
    public function liste_stock()
    {
        $stocks = Stock::orderBy('dateArrivee', 'desc')->paginate(7)->toArray(); 
        $totalCartons = Stock::sum('nbrCarton');
        $totalPieces = Stock::sum('nbrPiece');
        $totalCartonsDist = Distribution::sum('nbrCarton');
        $totalPiecesDist = Distribution::sum('nbrPiece');
    
        return response()->json(compact('stocks', 'totalCartons', 'totalPieces', 'totalCartonsDist', 'totalPiecesDist'));
    }
    

    public function ajouter_stock_traitement(Request $request) {
        $request->validate([
            'stock' => 'required',
            'nbrC' => 'required',
            'nbrP' => 'required',
            'pc' => 'required',
            'dateA' => 'required',
        ]);
    
        $stock = new Stock;
        $stock->stock = $request->stock;
        $stock->nbrCarton = $request->nbrC;
        $stock->nbrPiece = $request->nbrP;
        $stock->pc = $request->pc;
        $stock->dateArrivee = $request->dateA;
    
        $stock->save();
    
        return response()->json(['success' => 'Stock bien ajouté']);
    }
    
    public function update_stock($id)
    {   
        
        $stocks = Stock::find($id);
        if ($stocks) {
            return response()->json($stocks);
        }
    
        return response()->json(['error' => 'Stock non trouvé'], 404);
    }

    public function update_stock_traitement(Request $request)
    {   
        $request->validate([
            'stock' => 'required',
            'nbrC' => 'required',
            'nbrP' => 'required',
            'pc' => 'required',
            'dateA' => 'required',
        ]);
        
        $stock = Stock::find($request->id);
        $stock->stock = $request->stock;
        $stock->nbrCarton = $request->nbrC;
        $stock->nbrPiece = $request->nbrP;
        $stock->pc = $request->pc;
        $stock->dateArrivee = $request->dateA;
        $stock->update();

        return response()->json(['success' => 'Stock bien modifié']);
    }

    public function delete_stock($id)
    {   
        $cascade = Distribution::where('idStock', $id)->first();

        if ($cascade) {
          $cascade->delete();
        } else {
        }
        $stock = Stock::find($id);

        $stock->delete();


        return response()->json(['success' => 'Stock bien supprimé']);
    }

    public function recherche_stock(Request $request) {
        $dateRecherche = $request->input('lettres');
        $stocks = Stock::where('dateArrivee', $dateRecherche)->paginate(5);
    
        $tous = Stock::all();
        $totalCartons = $tous->sum('nbrCarton');
        $totalPieces = $tous->sum('nbrPiece');
        $tousDist = Distribution::all();
        $totalCartonsDist = $tousDist->sum('nbrCarton');
        $totalPiecesDist = $tousDist->sum('nbrPiece');
    
        return response()->json([
            'stocks' => $stocks,
            'totalCartons' => $totalCartons,
            'totalPieces' => $totalPieces,
            'totalCartonsDist' => $totalCartonsDist,
            'totalPiecesDist' => $totalPiecesDist,
        ]);
    }
    
}
