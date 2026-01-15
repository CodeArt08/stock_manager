<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Stock Distribution</title>
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
    <h3 style="text-align: center;">Liste des distributions pour les stocks arrivés le {{ $dateArrivee }}</h3>
    <table>
        <thead>
            <tr>
                <th>DREN</th>
                <th>CISCO</th>
                <th>ZAP</th>
                <th>Nombre d'Établissements</th>
                @foreach ($stocks as $stock)
                    <th>{{ $stock->stock }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['dren'] }}</td>
                    <td>{{ $row['cisco'] }}</td>
                    <td>{{ $row['zap'] }}</td>
                    <td>{{ $row['nbrEtab'] }}</td>
                    @foreach ($stocks as $stock)
                        @php
                            $distribution = $row['distributions']->where('idStock', $stock->id)->first();
                        @endphp
                        <td>
                            {{ $distribution ? (($distribution->nbrCarton * $stock->pc) + $distribution->nbrPiece) . ' pièces' : '--' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="font-weight: bold; text-align: right;">Total</th>
                <th></th> <!-- Colonne vide pour "Nombre d'Établissements" -->
                @foreach ($stocks as $stock)
                    @php
                        $totalPieces = $data->reduce(function ($carry, $row) use ($stock) {
                            $distribution = $row['distributions']->where('idStock', $stock->id)->first();
                            return $carry + ($distribution ? ($distribution->nbrCarton * $stock->pc) + $distribution->nbrPiece : 0);
                        }, 0);
                    @endphp
                    <th style="font-weight: bold;">
                        {{ $totalPieces }} pièces
                    </th>
                @endforeach
            </tr>
        </tfoot>

        
    </table>
</body>
</html>
