@extends('layout.principal')
@section('content')
<div class="entete">
    <a href="/">
       <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
    </a> 
    <h2 class="titre2">~Formulaire d'Ajout~</h2>
</div>

    <div class="Z">
        <div class="Z1">
            <div class="Style1">
                <img src="{{ asset('images/logos/checklist.png') }}" alt="" width="300px" height="300px" class="">
            </div>
            
            <div class="Style2">
                <p class="my">
                  Veuillez remplir tous les <br/>champs! <br/> 
                  <span class="text">Ajouter un Stock</span>
                </p>
                <p class="page">
                  Mettez les informations relatives aux Stocks nouvellement.<br/>
                    arrivés dans l'enceinte de CISCO Brickaville.
                </p>
            </div>

        </div>
        <div class="Z2">
                <form action="/ajout-stock/traitement" method="POST">
                    @csrf
                 <div class="formulaire">
                
                    <div class="form">
                        <label class="form-label">Nom du Stock:</label>
                        <input type="text" class="form-control" name="stock"/>
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre de carton:</label>
                        <input type="number" class="form-control" name="nbrC"/>
                    </div>

                    <div class="form">
                        <label class="form-label">Nombre de pièce:</label>
                        <input type="number" class="form-control" name="nbrP"/>
                    </div>
    
  
                    <div class="form">
                        <label class="form-label">nombre de pièce/carton:</label>
                        <input type="number" class="form-control" name="pc"/>

                    </div>

                    <div class="form">
                        <label class="form-label">Date d'arrivée:</label>
                        <input type="date" class="form-control" name="dateA"/>

                    </div

                  <br>

                  <button type="submit" class="btnAjout">
                    Ajouter
                  </button>

                 </div>


                </form>

                @if(Session('status'))
                <script>
                    swal("Message","{{ Session::get('status') }}", 'success',{
                      button: true,
                      button:"ok",
                    });
                </script>
                @endif
        </div>
    </div>
@endsection