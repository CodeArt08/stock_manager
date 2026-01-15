@extends('layout.principal')
@section('content')
    
<h1>~Gestion de stock de CISCO Brickaville~</h1>

<div class="parent1">

    <a href="/ajout-stock" class="ajout"><img src="{{ asset('images/logos/plusvrai.png') }}" width="35px" height="35px"><span class="ajt">AJOUT DE STOCK</span></a>
  
    <div class="total">
        <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total Stock: {{ $totalCartons }}C + {{ $totalPieces }}P</span>
    </div>
    <div class="total">
        <img src="{{ asset('images/logos/total-fat.png') }}" width="35px" height="35px"><span class="ajt">Total distribuée: {{ $totalCartonsDist }}C + {{ $totalPiecesDist }}P</span>
    </div>
</div>

<div> 
  <div class="fin">
    <h3>Liste des stocks</h3>
    <form action="recherche-stock" class="formsearch2">
      <input type="date" placeholder="recherche" class="inputsearch" name="lettres">
      <button class="btnsearch" type="submit">
        <img src="{{ asset('images/logos/magnifying-glass.png') }}" alt="" width="20px" height="20px">
      </button>   
    </form>
  </div>

    <table class="table">
      <thead>
        <tr>
          {{-- <th class="B"><span class="A">numEmp</span></th> --}}
          <th class="B"><span class="A">nom du stock</span></th>
          <th class="B"><span class="A">nombre de carton</span></th>
          <th class="B"><span class="A">nombre de pièce</span></th>
          <th class="B"><span class="A">pièce/carton</span></th>
          <th class="B"><span class="A">date d'arrivée</span></th>
          <th class="B"><span class="A">Actions</span></th>
        </tr>
      </thead>
      
      <tbody> 
        @foreach($stocks as $stock)
            <tr>
              <td class="C">{{ $stock->stock }}</td>
              <td class="C">{{ $stock->nbrCarton }}</td>
              <td class="C">{{ $stock->nbrPiece }}</td>
              <td class="C">{{ $stock->pc }}</td>
              <td class="C">{{ $stock->dateArrivee }}</td>
              <td class="Ci">
                <a href="/update-stock/{{ $stock->id }}"><img src="{{ asset('images/logos/edit.png') }}" alt="" width="35px" height="35px" ></a>
                <a  class="delete" onclick="confirmDelete({{ $stock->id }})"><img src="{{ asset('images/logos/delete.png') }}" alt="" width="30px" height="30px"></a>
              </td>
            </tr>
         @endforeach
        
      </tbody>
    </table>

  
      {{ $stocks->links() }}


</div> 

{{-- @if(Session('status'))
<script>
    swal("Message","{{ Session::get('status') }}", 'success',{
      button: true,
      button:"ok",
    });
</script>
@endif --}}



@endsection

@section('scripts')
<script>
    function confirmDelete(stockId) {
        Swal.fire({
            title: "Êtes-vous sûr?",
            text: "Voulez-vous vraiment supprimer ce stock?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
            dangerMode: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirige vers la route de suppression
                window.location.href = "/delete-stock/" + stockId;
            }
        });
    }
</script>
@endsection