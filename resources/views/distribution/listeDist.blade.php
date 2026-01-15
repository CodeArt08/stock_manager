@extends('layout.principal')
@section('content')
    
<h1>~Gestion de stock de CISCO Brickaville~</h1>

<div class="parent1">

    <a href="/ajout-dist" class="ajout"><img src="{{ asset('images/logos/plusvrai.png') }}" width="35px" height="35px"><span class="ajt">AJOUT DE DISTRIBUTION</span></a>
  
    <div class="total">
      <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total Stock: {{ $totalCartons }}C + {{ $totalPieces }}P</span>
    </div>
     <div class="total">
        <img src="{{ asset('images/logos/total-fat.png') }}" width="35px" height="35px"><span class="ajt">Total distribuée: {{ $totalCartonsDist }}C + {{ $totalPiecesDist }}P</span>
    </div>
</div>

<div> 
    <h3>Liste des distribution</h3>
    <table class="table2">
      <thead>
        <tr>
        
          <th class="B"><span class="A">designation</span></th>
          <th class="B"><span class="A">ZAP</span></th>
          <th class="B"><span class="A">Etablissement</span></th>
          <th class="B"><span class="A">carton(s)</span></th>
          <th class="B"><span class="A">pièce(s)</span></th>
          <th class="B"><span class="A">total en pièce</span></th>
          <th class="B"><span class="A">date de distribution</span></th>
          <th class="B"><span class="A">Actions</span></th>
        </tr>
      </thead>
      
      <tbody> 
        @foreach($dists as $dist)
            <tr>
              <td class="C">{{ $dist->idStock }}</td>
              <td class="C">{{ $dist->idZap }}</td>
              <td class="C">{{ $dist->nbrCarton }}</td>
              <td class="C">{{ $dist->nbrPiece }}</td>
                              <!-- Calcul du total des pièces (cartons convertis en pièces + pièces restantes) -->
                          @php
                              $totalPiece = ($dist->nbrCarton * $dist->pc) + $dist->nbrPiece;
                          @endphp
              <td class="C">{{ $totalPiece }}</td>
              <td class="C">{{ $dist->dateDist }}</td>
              <td class="Ci">
                <a  class="delete" onclick="confirmDelete({{ $dist->id }})"><img src="{{ asset('images/logos/delete.png') }}" alt="" width="30px" height="30px"></a>
              </td>
            </tr>
       @endforeach
      </tbody>
    </table>
    {{ $dists->links() }}
</div> 

@endsection

@section('scripts')
<script>
    function confirmDelete(distId) {
        Swal.fire({
            title: "Êtes-vous sûr?",
            text: "Voulez-vous vraiment supprimer cette distribution?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
            dangerMode: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirige vers la route de suppression
                window.location.href = "/annuler-dist/" + distId;
            }
        });
    }
</script>
@endsection