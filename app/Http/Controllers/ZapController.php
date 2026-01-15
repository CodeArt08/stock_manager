<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zap;
use App\Models\Stock;
use App\Models\Distribution;
use App\Models\Etablissement;

use function Symfony\Component\VarDumper\Dumper\esc;

class ZapController extends Controller
{
    public function liste_zap()
    {
        try {
            $zaps = Zap::orderBy('codeZap', 'asc')->paginate(9);
    
            $total = Zap::count();
            $totalCartons = Stock::sum('nbrCarton');
            $totalPieces = Stock::sum('nbrPiece');
            $totalCartonsDist = Distribution::sum('nbrCarton');
            $totalPiecesDist = Distribution::sum('nbrPiece');
    
            return response()->json(compact('zaps', 'total', 'totalCartons', 'totalPieces', 'totalCartonsDist', 'totalPiecesDist'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    
    public function ajouter_zap(){ 
        return view('zap.ajoutZap');
    }

    public function ajouter_zap_traitement(Request $request){ 
        $request->validate([
            'codeZap' => 'required',
            'dren' => 'required',
            'cisco' => 'required',
            'zap' => 'required',
            'nbrEtab' => 'required',
        ]);

        $zap = new Zap;
        $zap->codeZap = $request->codeZap;
        $zap->dren = $request->dren;
        $zap->cisco = $request->cisco;
        $zap->zap = $request->zap;
        $zap->nbrEtab = $request->nbrEtab;
        $zap->save();

        return response()->json(['success' => 'ZAP bien ajouté']);
    }

    public function update_zap($id){
        $zaps = Zap::find($id);
        return response()->json($zaps);
    }

    public function update_zap_traitement(Request $request){
        $request->validate([
            'codeZap' => 'required',
            'dren' => 'required',
            'cisco' => 'required',
            'zap' => 'required',
            'nbrEtab' => 'required',
        ]);
        $zap = Zap::find($request->id);
        $zap->codeZap = $request->codeZap;
        $zap->dren = $request->dren;
        $zap->cisco = $request->cisco;
        $zap->zap = $request->zap;
        $zap->nbrEtab = $request->nbrEtab;
        $zap->update();

        return response()->json(['success' => 'ZAP bien modifié']);
    }

    public function delete_zap($id)
    {
        try {
            Distribution::where('idZap', $id)->delete();
    
            Etablissement::where('idZap', $id)->delete();
    
            // Supprimer le ZAP
            $zap = Zap::findOrFail($id);
            $zap->delete();
    
            return response()->json(['success' => 'ZAP et ses établissements associés ont été supprimés avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }
    

    public function recherche_zap(Request $request){
        $total = Zap::count();
        
        $tous = Stock::all();
        $totalCartons = $tous->sum('nbrCarton');
        $totalPieces = $tous->sum('nbrPiece');
        $tousDist = Distribution::all();
        $totalCartonsDist = $tousDist->sum('nbrCarton');
        $totalPiecesDist = $tousDist->sum('nbrPiece');
        $lettres = $request->input('lettres');
        
        $zaps = Zap::where('zap', 'LIKE', '%'.$lettres.'%')->orWhere('id', $lettres)->paginate(5);
        return response()->json(compact('zaps','total', 'totalCartons','totalPieces','totalCartonsDist','totalPiecesDist'));
    }

}
