@extends('layout.principal')
@section('content')
<div class="entete">
    <a href="/liste-dist">
       <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
    </a> 
    <h2 class="titre2">~Formulaire d'Ajout~</h2>
</div>

    <div class="Z">
        <div class="Z1">
            <div class="Style1">
                <div>
                    <img src="{{ asset('images/logos/drone-delivery.png') }}" alt="" width="280px" height="280px" class="">
                </div>
                <div>
                    <form action="" class="formulaireJr" onsubmit="return convertirPieces(event)">
                        <div class="form">
                            <label class="form-label">Nombre de pièces:</label>
                            <input type="number" class="form-controlJr" name="nbrP" id="nbrP" required />
                        </div>
                        <div class="form">
                            <label class="form-label">Nombre de pièces/carton:</label>
                            <input type="number" class="form-controlJr" name="pc" id="pc" required />
                        </div>
                        <button type="submit" class="btnCalcul"> = </button>
                        <div class="form">
                            <label class="form-label">Carton(s):</label>
                            <input type="number" class="form-controlJr" id="carton" readonly />
                        </div>
                        <div class="form">
                            <label class="form-label">Pièce(s):</label>
                            <input type="number" class="form-controlJr" id="pieceRestante" readonly />
                        </div>
                    </form>    
                </div>
                
                <script>
                function convertirPieces(event) {
                    event.preventDefault(); // Empêche le rechargement de la page à la soumission du formulaire
                
                    // Récupérer les valeurs du formulaire
                    const nbrP = parseInt(document.getElementById('nbrP').value); // Nombre total de pièces
                    const pc = parseInt(document.getElementById('pc').value); // Nombre de pièces par carton
                
                    // Validation simple
                    if (isNaN(nbrP) || isNaN(pc) || nbrP < 0 || pc <= 0) {
                        alert("Veuillez entrer des valeurs valides.");
                        return;
                    }
                
                    // Calcul du nombre de cartons et des pièces restantes
                    const cartons = Math.floor(nbrP / pc); // Nombre total de cartons
                    const piecesRestantes = nbrP % pc; // Nombre de pièces restantes après avoir rempli les cartons
                
                    // Mettre à jour les champs du formulaire
                    document.getElementById('carton').value = cartons;
                    document.getElementById('pieceRestante').value = piecesRestantes;
                }
                </script>
                
            </div>
            
            <div class="Style2">
                <p class="my">
                  Veuillez remplir tous les <br/>champs! <br/> 
                  <span class="text">Ajouter une distribution</span>
                </p>
                <p class="page">
                  Mettez les informations relatives aux Stocks nouvellement.<br/>
                    arrivés dans l'enceinte de CISCO Brickaville.
                </p>
            </div>

        </div>
        <div class="Z2">
                <form action="/ajout-dist/traitement" method="POST">
                    @csrf
                 <div class="formulaire">
                
                    <div class="form">
                        <label class="form-label">Designation:</label>
                        <select name="stock" class="select-control">
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->stock }}"> {{ $stock->stock }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form">
                        <label class="form-label">ZAP:</label>
                        <select name="zap" class="select-control">
                            @foreach ($zaps as $zap)
                                <option value="{{ $zap->zap }}"> {{ $zap->zap }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form">
                        <label class="form-label">Code de l'Etablissement:</label>
                        <input type="text" class="form-control" name="code"/>
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre de carton:</label>
                        <input type="number" class="form-control" name="nbrCarton"/>
                    </div>
    
  
                    <div class="form">
                        <label class="form-label">Nombre de pièce:</label>
                        <input type="number" class="form-control" name="nbrPiece"/>

                    </div>

                    <div class="form">
                        <label class="form-label">Date de distribution:</label>
                        <input type="date" class="form-control" name="dateDist"/>

                    </div

                  <br>

                  <button type="submit" class="btnAjout2">
                    Ajouter
                  </button>

                 </div>


                </form>

        </div>
    </div>

    {{-- @if(Session('status'))
    <script>
        swal("Message","{{ Session::get('status') }}", 'success',{
          button: true,
          button:"ok",
        });
    </script>
    @endif --}}
    
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@endsection