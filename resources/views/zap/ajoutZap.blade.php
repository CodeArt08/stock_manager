@extends('layout.principal')
@section('content')
<div class="entete">
    <a href="/liste-zap">
       <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
    </a> 
    <h2 class="titre2">~Formulaire d'Ajout~</h2>
</div>

    <div class="Z">
        <div class="Z1">
            <div class="Style1">
                <img src="{{ asset('images/logos/bird.png') }}" alt="" width="300px" height="300px" class="">
            </div>
            
            <div class="Style2">
                <p class="my">
                  Veuillez remplir tous les <br/>champs! <br/> 
                  <span class="text">Ajouter un nouveau ZAP</span>
                </p>
                <p class="page">
                  Mettez les informations relatives du ZAP<br/>
                  appartenant au CISCO Brickaville.
                </p>
            </div>

        </div>
        <div class="Z2">
                <form action="/ajout-zap/traitement" method="POST">
                    @csrf
                 <div class="formulaire">
                
                    <div class="form">
                        <label class="form-label">DREN:</label>
                        <input type="text" class="form-control" name="dren"/>
                    </div>

                    <div class="form">
                        <label class="form-label">CISCO:</label>
                        <input type="text" class="form-control" name="cisco"/>
                    </div>

                    <div class="form">
                        <label class="form-label">ZAP:</label>
                        <input type="text" class="form-control" name="zap"/>
                    </div>
    
  
                    <div class="form">
                        <label class="form-label">nombre d'Etablissement:</label>
                        <input type="number" class="form-control" name="nbrEtab"/>

                    </div>

                    {{-- <div class="form">
                        <label class="form-label">Date de distribution:</label>
                        <input type="date" class="form-control" name="dateA"/>

                    </div --}}

                  <br>

                  <button type="submit" class="btnAjout">
                    Ajouter
                  </button>

                 </div>


                </form>

        </div>
    </div>

    @if(Session('status'))
    <script>
        swal("Message","{{ Session::get('status') }}", 'success',{
          button: true,
          button:"ok",
        });
    </script>
    @endif
    
@endsection