<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Distributions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            text-align: center;
            word-wrap: break-word;
            padding: 5px 5px;
            font-size: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <!-- Titre -->
    <h2>Liste des distributions de la ZAP {{ $zap->zap }} pour les stocks arrivés le {{ $dateArrivee }}</h2>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>ZAP</th>
                <th>Établissement</th>
                
                <!-- Colonnes dynamiques pour les noms des stocks -->
                @foreach ($stocks as $stock)
                    <th>{{ $stock->stock }}</th>
                @endforeach
                
                <th>Total en pièces</th>
                <th>Date distribution</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distributions as $distribution)
                <tr>
                    <td>{{ $distribution->zap->zap ?? 'Inconnu' }}</td>
                    <td>{{ $distribution->etablissement->nomEtab ?? 'Inconnu' }}</td>

                    <!-- Colonnes dynamiques pour les quantités de chaque stock -->
                    @foreach ($stocks as $stock)
                        <td>
                            @if ($distribution->idStock == $stock->id)
                                {{ ($distribution->nbrCarton * $stock->pc) + $distribution->nbrPiece }} pièces
                            @else
                                -
                            @endif
                        </td>
                    @endforeach

                    <td>{{ $distribution->totalPieces }} pièces</td>
                    <td>{{ $distribution->dateDist }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">TOTAL</th>
                
                <!-- Totaux pour chaque stock -->
                @foreach ($stocks as $stock)
                    <th>
                        {{ $distributions->where('idStock', $stock->id)->sum(function ($dist) use ($stock) {
                            return ($dist->nbrCarton * $stock->pc) + $dist->nbrPiece;
                        }) }} pièces
                    </th>
                @endforeach

                <!-- Total général en pièces -->
                <th>{{ $distributions->sum('totalPieces') }} pièces</th>
                <th></th> <!-- Colonne vide pour la date de distribution -->
            </tr>
        </tfoot>
    </table>
</body>
</html>
