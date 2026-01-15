@extends('layout.principal')
@section('content')
<div class="entete">
    <a href="/">
       <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
    </a> 
    <h2 class="titre2">~Formulaire de modification~</h2>
</div>

    <div class="Z">
        <div class="Z1">
            <div class="Style1">
                <img src="{{ asset('images/logos/inventory.png') }}" alt="" width="300px" height="300px" class="">
            </div>
            
            <div class="Style2">
                <p class="my">
                  Veuillez remplir tous les <br/>champs! <br/> 
                  <span class="text">Modifier le Stock</span>
                </p>
                <p class="page">
                  Mettez les informations relatives du Stock à.<br/>
                    modifier.
                </p>
            </div>

        </div>
        <div class="Z2">
                <form action="/update-stock/traitement" method="POST">
                    @csrf
                 <div class="formulaire">
                    <div class="form" style="display: none">
                        <label class="form-label">idStock:</label>
                        <input type="text" class="form-control" name="id" value="{{ $stocks->id }}"/>
                    </div>
                
                    <div class="form">
                        <label class="form-label">Nom du Stock:</label>
                        <input type="text" class="form-control" name="stock" value="{{ $stocks->stock }}"/>
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre de carton:</label>
                        <input type="number" class="form-control" name="nbrC" value="{{ $stocks->nbrCarton }}"/>
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre de pièce:</label>
                        <input type="number" class="form-control" name="nbrP" value="{{ $stocks->nbrPiece }}"/>
                    </div>
    
  
                    <div class="form">
                        <label class="form-label">nombre de pièce/carton:</label>
                        <input type="number" class="form-control" name="pc" value="{{ $stocks->pc }}"/>

                    </div>

                    <div class="form">
                        <label class="form-label">Date d'arrivée:</label>
                        <input type="date" class="form-control" name="dateA" value="{{ $stocks->dateArrivee }}"/>

                    </div

                  <br>

                  <button type="submit" class="btnAjout">
                    Modifier
                  </button>

                 </div>


                </form>

        </div>
    </div>
@endsection