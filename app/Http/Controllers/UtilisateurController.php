<?php

namespace App\Http\Controllers;
use App\Models\Utilisateur;

use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $user = Utilisateur::where('nom', $request->username)
            ->where('mdp', $request->password)
            ->first();

        if ($user) {
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid username or password',
            ], 401);
        }
    }


    public function liste_user()
    {
        $users = Utilisateur::orderBy('id', 'asc')->paginate(10); 
        return response()->json(compact('users'));
    }
    public function ajouter_user(Request $request){
        $request->validate([
            'username'=> 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $user = new Utilisateur;
        $user->nom = $request->username;
        $user->role = $request->role;
        $user->mdp = $request->password;

        $user->save();
        return response()->json(['success' => 'Utilisateur bien ajouté']);
        
    }

    public function update_user($id)
    {   
        
        $users = Utilisateur::find($id);
        if ($users) {
            return response()->json($users);
        }
    
        return response()->json(['error' => 'Stock non trouvé'], 404);
    }

    public function update_user_traitement(Request $request){
        $request->validate([
            'username'=> 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $user = Utilisateur::find($request->id);
        $user->id = $request->id;
        $user->nom = $request->username;
        $user->role = $request->role;
        $user->mdp = $request->password;

        $user->update();
        return response()->json(['success' => 'Utilisateur bien modifié']);
        
    }

    public function delete_user($id)
{

        $user = Utilisateur::find($id);

        $user->delete();

        return response()->json(['success' => 'Utilisateur bien supprimé']);
    }
}
