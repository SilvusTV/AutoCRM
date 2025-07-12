<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        .brouillon-container {
            position: absolute;
            bottom: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            font-size: 8rem;
            cursor: default;
            pointer-events: none;
            user-select: none;
        }

        .brouillon-text {
            text-align: center;
            color: rgba(200, 200, 200, 0.25);
            font-weight: bold;
            transform: rotate(-45deg);
            letter-spacing: 10px;
        }

        body {
            font-family: Aptos, sans-serif;
            font-size: 12px;
            color: #000;
            height: 100vh;
        }

        header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        header h2 {
            margin: 0;
            line-height: 20px;
        }

        .two-columns {
            width: 100%;
        }

        .two-columns .left {
            width: 100%;
            min-width: 50%;
        }

        .two-columns .right {
            width: 50%;
            max-width: 50%;
        }

        .two-columns td {
            width: 50%;
            min-width: 50%;
            max-width: 50%;
            vertical-align: top;
        }

        .sub-two-columns tr .line-label {
            text-wrap: nowrap;
            word-break-wrap: nowrap;
            word-break: keep-all;
            white-space: nowrap;
            min-width: 70px;
            max-width: 50%;
            vertical-align: top;
        }

        .sub-two-columns tr .line-value {
            width: 100%;
            min-width: 50%;
            vertical-align: top;
        }

        .invoice-header {
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th, .invoice-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .invoice-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .totals {
            width: 300px;
            margin-left: auto;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .totals .label {
            font-weight: bold;
        }

        .totals .total-ttc {
            font-size: 16px;
            font-weight: bold;
        }

        .payment-details {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .payment-part {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
<header>
    <div>
        <h2>Facture {{ $invoice->invoice_number }}</h2>
        <p>Date : {{ $invoice->issue_date->format('d/m/Y') }}</p>
    </div>
</header>
@if($invoice->status === 'brouillon')
    <div class="brouillon-container">
        <span class="brouillon-text">
            Provisoire
        </span>
    </div>
@endif
<!-- Partie prenants de la facture -->
<table class="two-columns invoice-header">
    <tr>
        <!-- Émetteur de la facture (Société du freelance) -->
        <td class="left">
            <h3>ÉMETTEUR</h3>
            <table class="sub-two-columns">
                <tr>
                    <td class="line-label"><strong>Société :</strong></td>
                    <td class="line-value"><strong>Nom de l'entreprise</strong></td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Votre contact :</strong></td>
                    <td class="line-value">Nom du représentant</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Adresse :</strong></td>
                    <td class="line-value">Adresse de l'entreprise</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Pays :</strong></td>
                    <td class="line-value">France</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Numéro d’entreprise :</strong></td>
                    <td class="line-value">901 506 949 00014</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Code d’activité :</strong></td>
                    <td class="line-value">6202B</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Numéro de téléphone :</strong></td>
                    <td class="line-value">0647508341</td>
                </tr>
                <tr>
                    <td class="line-label"><strong>Adresse email :</strong></td>
                    <td class="line-value">contact@entreprise.com<</td>
                </tr>
            </table>
        </td>
        <!-- Destinataire (Client) -->
        <td class="right">
            <h3>DESTINATAIRE</h3>
            @if($invoice->client)
                <table class="sub-two-columns">
                    <tr>
                        <td class="line-label"><strong>Nom du client :</strong></td>
                        <td class="line-value">{{ $invoice->client->name }}</td>
                    </tr>
                    @if(isset($invoice->client->phone))
                        <tr>
                            <td class="line-label"><strong>Téléphone :</strong></td>
                            <td class="line-value">{{ $invoice->client->phone }}</td>
                        </tr>
                    @endif
                    @if(isset($invoice->client->email))
                        <tr>
                            <td class="line-label"><strong>Email :</strong></td>
                            <td class="line-value">{{ $invoice->client->email }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="line-label"><strong>Adresse :</strong></td>
                        <td class="line-value">{{ $invoice->client->address }}</td>
                    </tr>
                    <tr>
                        <td class="line-label"><strong>Pays :</strong></td>
                        <td class="line-value">{{$invoice->client->country}}</td>
                    </tr>
                </table>
            @endif
            @if($invoice->client && $invoice->company)
                <hr style="margin: 10px 0;">
            @endif

            @if($invoice->company)
                <table class="sub-two-columns">
                    <tr>
                        <td class="line-label"><strong>Société :</strong></td>
                        <td class="line-value">{{ $invoice->company->name }}</td>
                    </tr>
                    <tr>
                        <td class="line-label"><strong>Adresse :</strong></td>
                        <td class="line-value">{{ $invoice->company->address }}</td>
                    </tr>
                    <tr>
                        <td class="line-label"><strong>Pays :</strong></td>
                        <td class="line-value">{{$invoice->company->country}}</td>
                    </tr>
                    @if($invoice->company->siret)
                        <tr>
                            <td class="line-label"><strong>Numéro d’entreprise :</strong></td>
                            <td class="line-value">{{ $invoice->company->siret }}</td>
                        </tr>
                    @endif
                    @if($invoice->company->vat_number)
                        <tr>
                            <td class="line-label"><strong>Numéro de TVA intracommunautaire :</strong></td>
                            <td class="line-value">{{ $invoice->company->vat_number }}</td>
                        </tr>
                    @endif
                    @if($invoice->company->ape_code)
                        <tr>
                            <td class="line-label"><strong>Code d’activité :</strong></td>
                            <td class="line-value">{{ $invoice->company->ape_code }}</td>
                        </tr>
                    @endif
                    @if($invoice->company->phone)
                        <tr>
                            <td class="line-label"><strong>Téléphone :</strong></td>
                            <td class="line-value">{{ $invoice->company->phone }}</td>
                        </tr>
                    @endif
                    @if($invoice->company->email)
                        <tr>
                            <td class="line-label"><strong>Email :</strong></td>
                            <td class="line-value">{{ $invoice->company->email }}</td>
                        </tr>
                    @endif
                </table>
            @endif
        </td>
    </tr>
</table>

<!-- Tableau de détails de la facture -->
<table class="invoice-table">
    <thead>
    <tr>
        <th>Type</th>
        <th>Description</th>
        <th>Prix unitaire HT</th>
        <th>Quantité</th>
        <th>Total HT</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->invoiceLines as $line)
        <tr>
            <td>{{ $line->item_type ?? 'Service' }}</td>
            <td>{{ $line->description }}</td>
            <td>{{ number_format($line->unit_price, 2, ',', ' ') }} €</td>
            <td>{{ $line->quantity }}</td>
            <td>{{ number_format($line->total_ht, 2, ',', ' ') }} €</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Section TVA et Totaux -->
<div class="totals">
    <div class="row">
                <span>
                    @if($invoice->tva_rate > 0)
                        <span class="label">TVA:</span>
                        {{ $invoice->tva_rate }}%
                    @else
                        TVA non applicable, art. 293 B du CGI
                    @endif
                </span>
    </div>
    <div class="row">
        <span class="label">Total HT brut:</span>
        <span>{{ number_format($invoice->total_ht, 2, ',', ' ') }} €</span>
    </div>
    @if(isset($invoice->discount_percentage) && $invoice->discount_percentage > 0)
        <div class="row">
            <span class="label">Remise générale:</span>
            <span>{{ $invoice->discount_percentage }}%</span>
        </div>
    @endif
    <div class="row total-ttc">
        <span class="label">Total final TTC:</span>
        <span>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} €</span>
    </div>
</div>

<!-- Conditions de paiement -->
<table class="payment-part two-columns">
    <tr>
        <td>
            <h3>Conditions de paiement</h3>
            <p>
                <span><strong>Conditions de règlement:</strong></span> {{ $invoice->payment_terms ?? 'Paiement à réception de facture' }}
            </p>
            <p><span><strong>Mode de règlement:</strong></span> {{ $invoice->payment_method ?? 'Virement bancaire' }}
            </p>
        </td>
        <td>
            @if($invoice->payment_method == 'bank_transfer' && $invoice->bank_account)
                <div class="payment-details">
                    <h3>Coordonnées bancaires</h3>
                    <p>{{ $invoice->bank_account }}</p>
                </div>
            @endif
        </td>
    </tr>
</table>
<p>En cas de retard de paiement, une pénalité de {{ $invoice->late_fees ?? '3 fois le taux d\'intérêt légal' }}
    sera appliquée, ainsi qu'une indemnité forfaitaire pour frais de recouvrement de 40€.</p>

<!-- Notes et texte de conclusion -->
@if($invoice->conclusion_text)
    <div class="notes">
        <h3>Conclusion</h3>
        <p>{{ $invoice->conclusion_text }}</p>
    </div>
@endif

<!-- En-pied de la facture -->
<footer>
    <p>Facture générée le {{ now()->format('d/m/Y') }} via Auto-CRM Freelance</p>
    @if($invoice->footer_text)
        <p>{{ $invoice->footer_text }}</p>
    @endif
</footer>
</body>
</html>
