@extends('distribution.ajoutDist')
@section('content')

<form action="/calcul-piece" class="">
    <div  class="formulaireJr">
        <div class="form">
            <label class="form-label">Nombre de piece:</label>
            <input type="number" class="form-controlJr" name="nbrP"/>
        </div>
        <div class="form">
            <label class="form-label">Nombre de piece/carton:</label>
            <input type="number" class="form-controlJr" name="pc"/>
        </div>
        <button type="submit" class="btnCalcul"> = </button>
        <div class="form">
            <label class="form-label">Carton:</label>
            <input type="number" class="form-controlJr" value="{{ $carton }}"/>
        </div>
        <div class="form">
            <label class="form-label">Piece:</label>
            <input type="number" class="form-controlJr" value="{{ $piece }}"/>
        </div>
    </div>

</form>  



@endsection