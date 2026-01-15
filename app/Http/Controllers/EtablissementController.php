<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Zap;
use App\Models\Distribution;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    public function liste_etab()
    {
        try {
            $zaps = Zap::orderBy('codeZap', 'asc')->paginate(10);
            $totalZaps = Zap::count();
            $totalEtabs = Etablissement::count();
    
            return response()->json(compact('zaps','totalZaps','totalEtabs'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } 
    }

    public function etab($id)
    {
        $etabs = Etablissement::select(
            "etablissements.id",
            "etablissements.idZap",  // Ajout de l'ID réel du ZAP
            "zaps.zap as zapName",  // Nom du ZAP
            "etablissements.code",
            "etablissements.nomEtab",
            "etablissements.telephone"
        )
        ->where('idZap', $id)
        ->orderBy('code', 'asc')
        ->join('zaps', 'etablissements.idZap', '=', 'zaps.id')
        ->paginate(5);
    
        return response()->json(compact('etabs'));
           
    }

    public function ajouter_etab(Request $request)
    {
    
        $validated = $request->validate([
            'idZap' => 'required',
            'code' => 'required',
            'etab' => 'required',
            'telephone' => 'required',
        ]);
    
        $etab = new Etablissement();
        $etab->idZap = $validated['idZap'];
        $etab->code = $validated['code'];
        $etab->nomEtab = $validated['etab'];
        $etab->telephone = $validated['telephone'];
    
        $etab->save();
    
        $zap = Zap::find($validated['idZap']);
        $zap->updateNbrEtab();
    
        // Réponse JSON en cas de succès
        return response()->json(['success' => 'Établissement bien ajouté', 'etablissement' => $etab]);
    }
    

    // public function update_etab($id)
    // {    
    //     $tabs = Etablissement::find($id);
        
    //     if (!$tabs) {
    //         return redirect()->back()->with('error', 'Etablissement non trouvé');
    //     }
    
    //     $etabs = Etablissement::select(
    //         "etablissements.id",
    //         "zaps.zap as idZap",
    //         "etablissements.code",
    //         "etablissements.nomEtab"
    //     )
    //     ->where('etablissements.idZap', $tabs->idZap)
    //     ->join('zaps', 'etablissements.idZap', '=', 'zaps.id')
    //     ->paginate(7);
    
    //     $idZap = Zap::where('zap', $etabs->first()->idZap)->first()->id ?? null;
    
    //     return view('etablissement.updateEtab', compact('etabs', 'tabs', 'idZap'));   
    // }
    

    public function update_etab_traitement(Request $request)
    {    
        $request->validate([
            'idZap'=> 'required',
            'code' => 'required',
            'etab' => 'required',
            'telephone' => 'required',
        ]);

        $etab = Etablissement::find($request->id);
        $etab->idZap = $request->idZap;
        $etab->code = $request->code;
        $etab->nomEtab = $request->etab;
        $etab->telephone = $request->telephone;

        $etab->update();
        return response()->json(['success' => 'Etablissement bien modifié']);
    }

    public function delete_etab($id)
    {
        try {
            $etab = Etablissement::find($id);
    
            if (!$etab) {
                return response()->json(['error' => 'Établissement introuvable.'], 404);
            }
            Distribution::where('idEtab', $id)->delete();
            $zapId = $etab->idZap;
            $etab->delete();

            $zap = Zap::find($zapId);
            if ($zap) {
                $zap->updateNbrEtab();
            }
    
            return response()->json(['success' => 'Établissement bien supprimé.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }
    

    public function recherche_zapPrim(Request $request)
    {    
        $lettres = $request->input('lettres');
        $zaps = Zap::where('zap', 'LIKE', '%'.$lettres.'%')->orWhere('id', $lettres)->paginate(5);
        return response()->json(compact('zaps'));
    }

    public function recherche_etab(Request $request)
    {
        $lettres = $request->input('lettres');
    
        $etabs = Etablissement::select(
            "etablissements.id",
            "etablissements.idZap",
            "zaps.zap as zapName", // Inclure le nom du ZAP
            "etablissements.code",
            "etablissements.nomEtab",
            "etablissements.telephone"
        )
        ->join('zaps', 'etablissements.idZap', '=', 'zaps.id') // Effectuer la jointure
        ->where('etablissements.code', 'LIKE', "%{$lettres}%") // Recherche partielle si nécessaire
        ->paginate(5);
    
        return response()->json(compact('etabs'));
    }
    

}
