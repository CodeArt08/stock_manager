@extends('layout.principal')
@section('content')
  
<div class="entete">
  <a href="/liste-historique">
     <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
  </a> 
  <form action="/recherche" class="formsearch">
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
            <td class="C">
              <a href="/historique-dist/{{ $zap->id }}" class="h">Historique</a>
            </td>
          </tr>
      @endforeach
    </tbody>
  </table>

</div> 
@endsection