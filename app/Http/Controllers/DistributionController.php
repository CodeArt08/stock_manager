<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Zap;
use App\Models\Stock;
use App\Models\Etablissement;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function liste_dist()
    {
        $dists = Distribution::select(
            "distributions.id",
            "stocks.stock as idStock",
            "zaps.zap as idZap",
            "etablissements.nomEtab as idEtab",
            "distributions.nbrCarton",
            "distributions.nbrPiece",
            "distributions.dateDist",
            "stocks.pc"
        )   
            ->orderBy('dateDist', 'desc')
            ->join('stocks', 'distributions.idStock', '=', 'stocks.id')
            ->join('zaps', 'distributions.idZap', '=', 'zaps.id')
            ->join('etablissements', 'distributions.idEtab', '=', 'etablissements.id')
            ->paginate(7);
         

            $tous = Stock::all();
            $totalCartons = $tous->sum('nbrCarton');
            $totalPieces = $tous->sum('nbrPiece');
            $tousDist = Distribution::all();
            $totalCartonsDist = $tousDist->sum('nbrCarton');
            $totalPiecesDist = $tousDist->sum('nbrPiece');

        // $pc = Stock::findOrFail($dists->idStock)->pc->firstOrFail();
        // $totalPiece = ($dists->nbrCarton * $pc) + $dists->nbrPiece;

        return response()->json(compact('dists','totalCartons','totalPieces','totalCartonsDist','totalPiecesDist'));
    }


    public function ajouter_dist()
    {
        $stocks = Stock::all();
        $zaps = Zap::all();
        return response()->json(['stocks' => $stocks, 'zaps' => $zaps]);
    }
    

    public function ajouter_dist_traitement(Request $request)
    {
        $request->validate([
            'stock' => 'required',
            'zap' => 'required',
            'code' => 'required',
            'nbrCarton' => 'required|integer',
            'nbrPiece' => 'required|integer',
            'dateDist' => 'required|date',
        ]);
    
        $modelStock = Stock::where('stock', $request->stock)->firstOrFail();
        $modelZap = Zap::where('zap', $request->zap)->firstOrFail();
        $modelEtab = Etablissement::where('code', $request->code)->first();
    
        // Vérification si l'établissement existe et appartient au ZAP
        if (!$modelEtab || $modelEtab->idZap !== $modelZap->id) {
            return response()->json([
                'error' => 'L\'établissement n\'existe pas dans le ZAP spécifié.',
            ]);
        }
    
        $pc = $modelStock->pc; // nombre de pièces par carton
    
        // Si le stock est géré uniquement par pièces (pc == 0)
        if ($pc == 0) {
            // Vérifiez si le stock disponible est suffisant
            if ($request->nbrPiece > $modelStock->nbrPiece) {
                return response()->json([
                    'error' => 'Stock insuffisant pour la distribution demandée.',
                ]);
            }
    
            // Réduisez directement les pièces dans le stock
            $modelStock->update([
                'nbrPiece' => $modelStock->nbrPiece - $request->nbrPiece,
            ]);
    
            // Créez l'enregistrement de distribution
            Distribution::create([
                'idStock' => $modelStock->id,
                'idZap' => $modelZap->id,
                'idEtab' => $modelEtab->id,
                'nbrCarton' => 0, // Aucun carton distribué
                'nbrPiece' => $request->nbrPiece,
                'dateDist' => $request->dateDist,
            ]);
    
            return response()->json(['success' => 'Distribution bien ajoutée (par pièces uniquement)']);
        }
    
        // Gestion normale (pc > 0)
        $totalRequestedPieces = ($request->nbrCarton * $pc) + $request->nbrPiece;
        $totalStockPieces = ($modelStock->nbrCarton * $pc) + $modelStock->nbrPiece;
    
        if ($totalRequestedPieces > $totalStockPieces) {
            return response()->json([
                'error' => 'Stock insuffisant pour la distribution demandée.',
            ]);
        }
    
        $nbrC = $modelStock->nbrCarton;
        $nbrP = $modelStock->nbrPiece;
    
        $testPiece = $request->nbrPiece / $pc;
    
        if ($testPiece < 1) {
            $nbrC -= $request->nbrCarton;
            $nbrP -= $request->nbrPiece;
        } else {
            $pieceEnCarton = intval($testPiece);
            $cartonDist = $request->nbrCarton + $pieceEnCarton;
            $pieceDist = $request->nbrPiece % $pc;
    
            $nbrC -= $cartonDist;
            $nbrP -= $pieceDist;
        }
    
        if ($nbrP < 0) {
            $nbrC--;
            $nbrP += $pc;
        }
    
        $modelStock->update([
            'nbrCarton' => $nbrC,
            'nbrPiece' => $nbrP,
        ]);
    
        Distribution::create([
            'idStock' => $modelStock->id,
            'idZap' => $modelZap->id,
            'idEtab' => $modelEtab->id,
            'nbrCarton' => $request->nbrCarton,
            'nbrPiece' => $request->nbrPiece,
            'dateDist' => $request->dateDist,
        ]);
    
        return response()->json(['success' => 'Distribution bien ajoutée']);
    }
    
    


    public function annuler_dist($id)
    {
    
        $distribution = Distribution::findOrFail($id);
        $stock = Stock::findOrFail($distribution->idStock);

        $pc = $stock->pc; 
        $nbrCartonDist = $distribution->nbrCarton;
        $nbrPieceDist = $distribution->nbrPiece;

        $nbrCartonUpdated = $stock->nbrCarton + $nbrCartonDist;
        $nbrPieceUpdated = $stock->nbrPiece + $nbrPieceDist;

        if ($nbrPieceUpdated >= $pc) {
            $nbrCartonUpdated += intval($nbrPieceUpdated / $pc); 
            $nbrPieceUpdated = $nbrPieceUpdated % $pc; 
        }
        $stock->update([
            'nbrCarton' => $nbrCartonUpdated,
            'nbrPiece' => $nbrPieceUpdated,
        ]);

        $distribution->delete();
        return response()->json(['success' => 'Distribution bien annulée']);
    }


}
