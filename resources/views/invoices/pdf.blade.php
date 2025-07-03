<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info .row {
            display: flex;
            margin-bottom: 10px;
        }
        .invoice-info .col {
            flex: 1;
        }
        .invoice-info .label {
            font-weight: bold;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
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
        .footer {
            margin-top: 50px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-brouillon {
            background-color: #f2f2f2;
            color: #333;
        }
        .status-envoyee {
            background-color: #e6f7ff;
            color: #0066cc;
        }
        .status-payee {
            background-color: #e6ffe6;
            color: #009900;
        }
        .client-info, .company-info {
            margin-bottom: 20px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FACTURE</h1>
            <p>N° {{ $invoice->invoice_number }}</p>
        </div>

        <div class="invoice-info">
            <div class="row">
                <div class="col company-info">
                    <h3>ÉMETTEUR</h3>
                    <p>Votre Entreprise</p>
                    <p>Adresse de l'entreprise</p>
                    <p>Code postal, Ville</p>
                    <p>Email: contact@entreprise.com</p>
                    <p>Téléphone: 01 23 45 67 89</p>
                    <p>SIRET: 123 456 789 00012</p>
                </div>
                <div class="col client-info">
                    <h3>DESTINATAIRE</h3>
                    <p><strong>{{ $invoice->client->name }}</strong></p>
                    <p>{{ $invoice->client->address }}</p>
                    <p>{{ $invoice->client->postal_code }}, {{ $invoice->client->city }}</p>
                    <p>Email: {{ $invoice->client->email }}</p>
                    <p>Téléphone: {{ $invoice->client->phone }}</p>
                    @if($invoice->client->siret)
                    <p>SIRET: {{ $invoice->client->siret }}</p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p><span class="label">Projet:</span> {{ $invoice->project->name }}</p>
                    <p><span class="label">Date d'émission:</span> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                    <p><span class="label">Date d'échéance:</span> {{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>
                <div class="col">
                    <p>
                        <span class="label">Statut:</span> 
                        <span class="status status-{{ $invoice->status }}">{{ $invoice->status }}</span>
                    </p>
                    @if($invoice->payment_date)
                    <p><span class="label">Date de paiement:</span> {{ $invoice->payment_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceLines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format($line->unit_price, 2, ',', ' ') }} €</td>
                    <td>{{ number_format($line->total_ht, 2, ',', ' ') }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="row">
                <span class="label">Total HT:</span>
                <span>{{ number_format($invoice->total_ht, 2, ',', ' ') }} €</span>
            </div>
            <div class="row">
                <span class="label">TVA ({{ $invoice->tva_rate }}%):</span>
                <span>{{ number_format($invoice->total_ttc - $invoice->total_ht, 2, ',', ' ') }} €</span>
            </div>
            <div class="row total-ttc">
                <span class="label">Total TTC:</span>
                <span>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} €</span>
            </div>
        </div>

        @if($invoice->notes)
        <div class="notes">
            <h3>Notes</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Facture générée le {{ now()->format('d/m/Y') }} via Mini-CRM Freelance</p>
            <p>En cas de retard de paiement, une pénalité de 3 fois le taux d'intérêt légal sera appliquée, ainsi qu'une indemnité forfaitaire pour frais de recouvrement de 40€.</p>
            <p>TVA non applicable, art. 293 B du CGI</p>
        </div>
    </div>
</body>
</html>