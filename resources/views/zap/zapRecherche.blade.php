@extends('layout.principal')
@section('content')
    
<div class="yes">
    <a href="/liste-zap">
       <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
    </a> 
    <form action="/recherche-zap" class="formsearch">
        <input type="text" placeholder="recherche" class="inputsearch" name="lettres">
        <button class="btnsearch" type="submit">
          <img src="{{ asset('images/logos/magnifying-glass.png') }}" alt="" width="20px" height="20px">
        </button>   
  </form>
</div>




<div class="parent1">

    <a href="/ajout-zap" class="ajout"><img src="{{ asset('images/logos/plusvrai.png') }}" width="35px" height="35px"><span class="ajt">AJOUT DE ZAP</span></a>
    <div class="total1">
      <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total ZAP: {{ $total }}</span>
    </div>
    <div class="total">
        <img src="{{ asset('images/logos/all.png') }}" width="35px" height="35px"><span class="ajt">Total Stock: {{ $totalCartons }}C + {{ $totalPieces }}P</span>
    </div>
    <div class="total">
        <img src="{{ asset('images/logos/total-fat.png') }}" width="35px" height="35px"><span class="ajt">Total restant: 89C + 0P</span>
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


</div> 

@if(Session('status'))
<script>
    swal("Message","{{ Session::get('status') }}", 'success',{
      button: true,
      button:"ok",
    });
</script>
@endif

<script>
  function confirmDelete(zapId) {
      swal({
          title: "Êtes-vous sûr?",
          text: "Voulez-vous vraiment supprimer ce ZAP?",
          icon: "warning",
          buttons: ["Annuler", "Oui"],
          dangerMode: true,
      })
      .then((willDelete) => {
          if (willDelete) {
              
              window.location.href = "/delete-zap/" + zapId;
          }else{

          }
      });
  }
</script>
@endsection