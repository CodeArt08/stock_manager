@extends('layout.principal')
@section('content')
    

<div class="yes">
    <a href="/liste-etab">
        <button class="retour"><img src="{{ asset('images/logos/arrow.png') }}" width="30px" height="30px" class="fleche"><span class="ajtR">Retour</span></button>
     </a> 
     <form action="/recherche-zap" class="formsearch" role="search">
      <input type="text" placeholder="recherche" class="inputsearch" name="lettres">
      <button class="btnsearch" type="submit">
        <img src="{{ asset('images/logos/magnifying-glass.png') }}" alt="" width="20px" height="20px">
      </button>   
  </form>
  </div>



<div class="FormEtab">
        <form action="/update-etab/traitement" method="POST">
          
          @csrf
          <div class="tab">
              <div style="display: none;">
                <label class="form-label2">id:</label>
                <input type="text" class="form-control2" name="id" value="{{ $tabs->id }}"/>
              </div>
              <div style="">
                  <label class="form-label2">idZap :</label>
                  <input type="text" class="form-control2" name="idZap" value="{{ $tabs->idZap }}"/>
              </div>
              <div>
                  <label class="form-label2">Code :</label>
                  <input type="number" name="code" class="form-control2" value="{{ $tabs->code }}">
              </div>
              <div>
                  <label class="form-label2">Nom de l'Etablissement :</label>
                  <input type="text" name="etab" class="form-control2" value="{{ $tabs->nomEtab }}">
              </div>

          </div>
          <br>
          <button type="submit" class="bm">
            Modifier
          </button>
        </form>
        <a href="/etab/{{ $idZap }}" class="calme">
          Ajout
        </a>
</div>

<div class="bad"> 
    <h3>Liste des Etablissements</h3>
    <table class="tableEtab">
      <thead>
        <tr>
          <th class="B"><span class="A">ZAP</span></th>
          <th class="B"><span class="A">code</span></th>
          <th class="B"><span class="A">nom de l'Etablissement</span></th>
          <th class="B"><span class="A">Actions</span></th>
        </tr>
      </thead>
      
      <tbody> 
        @foreach($etabs as $etab)
            <tr>
              <td class="C">{{ $etab->idZap }}</td>
              <td class="C">{{ $etab->code }}</td>
              <td class="C">{{ $etab->nomEtab }}</td>
              <td class="Ci">
                <a href="/update-etab/{{ $etab->id }}"><img src="{{ asset('images/logos/edit.png') }}" alt="" width="35px" height="35px" ></a>
                <a  class="delete" onclick="confirmDelete({{ $etab->id }})"><img src="{{ asset('images/logos/delete.png') }}" alt="" width="30px" height="30px"></a>
              </td>
            </tr>
        @endforeach
      </tbody>
    </table>

    {{ $etabs->links() }}

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