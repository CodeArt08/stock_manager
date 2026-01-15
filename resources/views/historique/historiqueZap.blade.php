@extends('layout.principal')
@section('content')
    
<div class="yes">
    <a href="/liste-historique">
        <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
     </a> 
    <form action="recherche" class="formsearch">
        <input type="text" placeholder="recherche" class="inputsearch" name="lettres">
        <button class="btnsearch" type="submit">
          <img src="{{ asset('images/logos/magnifying-glass.png') }}" alt="" width="20px" height="20px">
        </button>   
  </form>
</div>




<div class="parent1">
    <form action="/liste-date" class="hist">
        <button class="btnHist" type="submit">
          <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Distributions entre</span>
        </button>
        <div class="dateStyle">

              <input type="date" class="date1" name="date1">
                
              <input type="date" class="date1" name="date2">
        </div>
    </form>

    <form action="/liste-nonDist" class="nonDist">
        <button class="btnNonDist" type="submit">
            <img src="{{ asset('images/logos/total-fat.png') }}" width="35px" height="35px"><span class="ajt">ZAP n'ayant pas encore réçu</span>
        </button>
        <div class="style">
            <h4>date d'arrivée du stock</h4>
            <input type="date" class="date1" name="date">
        </div>
    </form>

    <form action="/generate-stock-pdf" method="POST" class="pdf">
      @csrf
      <button class="btnPdf" type="submit">
          <img src="{{ asset('images/logos/folder.png') }}" width="35px" height="35px">
          <span class="ajt">Générer PDF</span>
      </button>
      <div class="style">
          <h4>Date d'arrivée du stock</h4>
          <input type="date" name="dateArrivee" class="date1" required>
      </div>
  </form>
    
</div>

<div> 
    
    <h3>Liste des distribution du ZAP {{ $zap }}</h3>
    <table class="table">
      <thead>
        <tr>
        
          <th class="B"><span class="A">nom du stock</span></th>
          <th class="B"><span class="A">ZAP</span></th>
          <th class="B"><span class="A">carton(s)</span></th>
          <th class="B"><span class="A">pièce(s)</span></th>
          <th class="B"><span class="A">total en pièce</span></th>
          <th class="B"><span class="A">date de distribution</span></th>

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

            </tr>
       @endforeach
      </tbody>
    </table>
    {{ $dists->links() }}
</div> 
@endsection