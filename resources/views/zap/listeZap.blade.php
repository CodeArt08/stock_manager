@extends('layout.principal')
@section('content')
    

  <form action="/recherche-zap" class="formsearch" role="search">
        <input type="text" placeholder="recherche" class="inputsearch" name="lettres">
        <button class="btnsearch" type="submit">
          <img src="{{ asset('images/logos/magnifying-glass.png') }}" alt="" width="20px" height="20px">
        </button>   
  </form>



<div class="parent1">

    <a href="/ajout-zap" class="ajout"><img src="{{ asset('images/logos/plusvrai.png') }}" width="35px" height="35px"><span class="ajt">AJOUT DE ZAP</span></a>
    <div class="totalZap">
      <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total ZAP: {{ $total }}</span>
    </div>
    <div class="total">
      <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total Stock: {{ $totalCartons }}C + {{ $totalPieces }}P</span>
  </div>
  <div class="total">
      <img src="{{ asset('images/logos/total-fat.png') }}" width="35px" height="35px"><span class="ajt">Total distribuée: {{ $totalCartonsDist }}C + {{ $totalPiecesDist }}P</span>
  </div>
</div>

<div> 
    <h3>Liste des ZAP</h3>
    <table class="table">
      <thead>
        <tr>
          <th class="B"><span class="A">numZAP</span></th>
          <th class="B"><span class="A">DREN</span></th>
          <th class="B"><span class="A">CISCO</span></th>
          <th class="B"><span class="A">ZAP</span></th>
          <th class="B"><span class="A">Nombre d'Etablissement</span></th>
          <th class="B"><span class="A">Actions</span></th>
        </tr>
      </thead>
      
      <tbody> 
        @foreach($zaps as $zap)
            <tr>
              <td class="C">{{ $zap->id }}</td>
              <td class="C">{{ $zap->dren }}</td>
              <td class="C">{{ $zap->cisco }}</td>
              <td class="C">{{ $zap->zap }}</td>
              <td class="C">{{ $zap->nbrEtab }}</td>
              <td class="Ci">
                <a href="/update-zap/{{ $zap->id }}"><img src="{{ asset('images/logos/edit.png') }}" alt="" width="35px" height="35px" ></a>
                <a  class="delete" onclick="confirmDelete({{ $zap->id }})"><img src="{{ asset('images/logos/delete.png') }}" alt="" width="30px" height="30px"></a>
              </td>
            </tr>
        @endforeach
      </tbody>
    </table>

    {{ $zaps->links() }}

</div> 




@endsection

@section('scripts')
<script>
    function confirmDelete(zapId) {
        Swal.fire({
            title: "Êtes-vous sûr?",
            text: "Voulez-vous vraiment supprimer ce ZAP?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
            dangerMode: true
        }).then((result) => {
            if (result.isConfirmed) {
               
                window.location.href = "/delete-zap/" + zapId;
            }
        });
    }
</script>
@endsection