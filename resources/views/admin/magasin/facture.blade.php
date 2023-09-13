<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            background-color: #f2f2f2;
            padding: 10px;
        }

        .invoice {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 20px;
        }

        .invoice table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice th,
        .invoice td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Facture {{ $commande->numero }}-{{ $magasin->nom }}</h1>
        </div>
        <div class="invoice">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th class="border-top-0">Nombre Carton</th>
                        <th class="border-top-0">Nombre Piece</th>
                        <th class="border-top-0">Piece restante</th>
                        <th>Quantite demande</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPrice = 0;
                    @endphp
                    @foreach ($commandeProduits as $produit)
                        <tr>
                            <td>{{ $commande->date }}</td>
                            <td>{{ $produit->nom_produit }}</td>
                            <td>{{ ($produit->pivot->quantite - ($produit->pivot->quantite % $produit->nombre_piece)) / $produit->nombre_piece }}</td>
                            <td>{{ $produit->nombre_piece }}</td>
                            <td>{{ $produit->pivot->quantite % $produit->nombre_piece }}</td>
                            <td>{{ $produit->pivot->quantite }}</td>
                            <td>{{ $produit->prix_unitaire }}</td>
                            <td>{{ $produit->pivot->quantite * $produit->prix_unitaire }}</td>
                        </tr>
                        @php
                            $totalPrice += $produit->pivot->quantite * $produit->prix_unitaire;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            <div class="total">
                <p>Total à payer: {{ number_format($totalPrice); }} F</p>
            </div>
        </div>
    </div>
</body>

</html>
