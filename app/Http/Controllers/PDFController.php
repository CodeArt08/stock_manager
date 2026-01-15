<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Zap;
use App\Models\Distribution;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class PDFController extends Controller
{
    public function generateStockPdf2(Request $request)
{
    try {
        $dateArrivee = $request->input('dateArrivee');
        $zapId = $request->input('zapId');

        // Récupérer la ZAP
        $zap = Zap::findOrFail($zapId);

        // Récupérer les stocks par date d'arrivée
        $stocks = Stock::where('dateArrivee', $dateArrivee)->get();
        if ($stocks->isEmpty()) {
            throw new \Exception('Aucun stock trouvé pour cette date.');
        }

        // Récupérer les distributions liées au ZAP et aux stocks
        $distributions = Distribution::with(['zap', 'stock', 'etablissement'])
            ->where('idZap', $zap->id)
            ->whereIn('idStock', $stocks->pluck('id'))
            ->get();

        if ($distributions->isEmpty()) {
            throw new \Exception('Aucune distribution trouvée pour ce ZAP et cette date d\'arrivée.');
        }

        // Calcul des totaux pour chaque distribution
        foreach ($distributions as $distribution) {
            $stock = $stocks->firstWhere('id', $distribution->idStock);

            // Vérifier si $stock et $stock->pc sont valides
            if ($stock && $stock->pc) {
                $distribution->totalPieces = ($distribution->nbrCarton * $stock->pc) + $distribution->nbrPiece;
            } else {
                $distribution->totalPieces = $distribution->nbrPiece; // Pas de conversion si $stock->pc est manquant
            }
        }

        // Génération du PDF
        $pdf = PDF::loadView('pdf.distributions', compact('zap', 'stocks', 'distributions', 'dateArrivee'));
        $fileName = 'distributions-de-la-ZAP-' . str_replace(' ', '-', $zap->zap) . '-pour-les-stocks-arrives-le-' . $dateArrivee . '.pdf';

        return $pdf->download($fileName);
    } catch (\Exception $e) {
        // Gérer les erreurs et retourner un message JSON
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
}

    
    
    

    public function generateStockPdf(Request $request)
    {
        $dateArrivee = $request->input('dateArrivee');
    
        // Récupération des stocks arrivés à une date spécifique
        $stocks = Stock::where('dateArrivee', $dateArrivee)->get();
    
        // Récupération des distributions associées aux stocks filtrés
        $distributions = Distribution::with(['zap', 'stock', 'etablissement'])
            ->whereHas('stock', function ($query) use ($dateArrivee) {
                $query->where('dateArrivee', $dateArrivee);
            })
            ->get();
    
        // Récupération des ZAPs associés
        $zaps = Zap::whereIn('id', $distributions->pluck('idZap'))->get();
    
        // Calcul des totaux par ZAP
        $data = $zaps->map(function ($zap) use ($distributions, $stocks) {
            $filteredDistributions = $distributions->where('idZap', $zap->id);
    
            return [
                'dren' => $zap->dren,
                'cisco' => $zap->cisco,
                'zap' => $zap->zap,
                'nbrEtab' => $zap->nbrEtab,
                'distributions' => $filteredDistributions,
            ];
        });
    
        // Génération du PDF
        $pdf = PDF::loadView('pdf.stock-distribution-pdf', [
            'stocks' => $stocks,
            'data' => $data,
            'dateArrivee' => $dateArrivee,
        ]);
    
        return $pdf->download('stock-distribution.pdf');
    }
    
    
    
}
