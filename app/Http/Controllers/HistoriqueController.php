<?php

namespace App\Http\Controllers;

use App\Models\Zap;
use App\Models\Distribution;
use App\Models\Etablissement;
use App\Models\Stock;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class HistoriqueController extends Controller
{
    public function liste_historique()
    {
        $zaps = Zap::orderBy('codeZap', 'asc')->paginate(7)->toArray();
        return response()->json(compact('zaps'));
    }

    public function fetchZap()
    {
        $zap = Zap::orderBy('codeZap', 'asc')->get(['id', 'zap']);
        return response()->json(['zap' => $zap]); 
    }
    

    public function liste_nonDist(Request $request)
    {
        try {
            // Valider le champ date
            $request->validate([
                'date' => 'required|date',
            ]);
    
            $dateArrivee = $request->input('date');
    
            $dateExists = Stock::where('dateArrivee', $dateArrivee)->exists();
    
            if (!$dateExists) {
                return response()->json([
                    'success' => false,
                    'message' => "Aucun stock n'est arrivé à la date spécifiée.",
                ], 404);
            }
    
            $etablissementsSansStock = Etablissement::whereDoesntHave('distributions', function ($query) use ($dateArrivee) {
                    $query->whereHas('stock', function ($stockQuery) use ($dateArrivee) {
                        $stockQuery->where('dateArrivee', '<=', $dateArrivee);
                    });
                })
                ->with(['zap:id,zap']) 
                ->paginate(5)
                ->appends($request->query());

            return response()->json([
                'success' => true,
                'etabs' => $etablissementsSansStock,
                'dateArrivee' => $dateArrivee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des établissements.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function listeNonDist2(Request $request)
    {
        $date = $request->input('date');
        $zapName = $request->input('zap');
    
        $query = Etablissement::query()
            ->whereDoesntHave('distributions', function ($query) use ($date) {
                $query->whereHas('stock', function ($stockQuery) use ($date) {
                    $stockQuery->where('dateArrivee', $date);
                });
            });
    
        if ($zapName) {
            $query->whereHas('zap', function ($zapQuery) use ($zapName) {
                $zapQuery->where('zap', $zapName);
            });
        }
    
        $etabs = $query->with('zap')->paginate(6);
    
        if ($etabs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun établissement trouvé pour les critères spécifiés.',
            ]);
        }
    
        return response()->json([
            'success' => true,
            'etabs' => $etabs,
        ]);
    }
    
    

    
    

    



    public function historique_dist($id)
    {

        $zap = Zap::find($id);
        if (!$zap) {
            return response()->json(['error' => 'ZAP introuvable'], 404);
        }

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
            ->where('distributions.idZap', $id)
            ->join('stocks', 'distributions.idStock', '=', 'stocks.id')
            ->join('zaps', 'distributions.idZap', '=', 'zaps.id')
            ->join('etablissements', 'distributions.idEtab', '=', 'etablissements.id')
            ->paginate(5);

        return response()->json([
            'dists' => $dists,
            'zap' => $zap->zap
        ]);
    }


    public function liste_date(Request $request)
    {
        $request->validate([
            'date1' => 'required|date',
            'date2' => 'required|date',
        ]);

        $dists = Distribution::with(['stock', 'zap', 'etablissement'])
            ->whereBetween('dateDist', [$request->date1, $request->date2])
            ->paginate(5)
            ->appends($request->all());

        return response()->json(['dists' => $dists, 'date1' => $request->date1, 'date2' => $request->date2]);
    }


    public function recherche(Request $request)
    {

        $lettres = $request->input('lettres');
        $zaps = Zap::where('zap', 'LIKE', '%' . $lettres . '%')->orWhere('id', $lettres)->paginate(5);
        return view('historique.recherche', compact('zaps'));
    }


    public function filterByDate(Request $request)
    {
        $dateArrivee = $request->dateArrivee;
        $zapId = $request->zapId;
    
        $distributions = Distribution::with(['stock', 'zap', 'etablissement'])
            ->whereHas('stock', function ($query) use ($dateArrivee) {
                $query->where('dateArrivee', $dateArrivee);
            })
            ->whereHas('zap', function ($query) use ($zapId) {
                $query->where('id', $zapId);
            })
            ->paginate(5);
    
        return response()->json(['dists' => $distributions]);
    }
    
}
