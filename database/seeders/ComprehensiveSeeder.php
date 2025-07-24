<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\URSSAFDeclaration;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // We'll use user_id 1 for everything as requested
        $userId = 1;

        // Make sure user 1 exists
        $user = User::find($userId);
        if (! $user) {
            echo "User with ID 1 not found. Please run the DatabaseSeeder first.\n";

            return;
        }

        // Create user's own company
        $ownCompany = Company::create([
            'user_id' => $userId,
            'name' => $faker->company,
            'email' => $faker->companyEmail,
            'phone' => $faker->phoneNumber,
            'address' => $faker->address,
            'siret' => $faker->numerify('##############'),
            'tva_number' => 'FR'.$faker->numerify('##############'),
            'naf_code' => $faker->numerify('####').$faker->randomLetter,
            'country' => 'France',
            'regime' => 'auto-entrepreneur',
            'is_own_company' => true,
        ]);

        // Create bank accounts for the user
        $bankAccount = BankAccount::create([
            'user_id' => $userId,
            'account_name' => 'Compte principal',
            'account_holder' => $user->name,
            'bank_name' => $faker->company,
            'iban' => $faker->iban('FR'),
            'bic' => $faker->swiftBicNumber,
        ]);

        // Create payment methods
        $paymentMethods = [];
        $methodTypes = ['other', 'other', 'other', 'other'];
        $methodDetails = ['Virement bancaire', 'Carte bancaire', 'Chèque', 'Espèces'];
        for ($i = 0; $i < count($methodTypes); $i++) {
            $paymentMethods[] = PaymentMethod::create([
                'user_id' => $userId,
                'type' => $methodTypes[$i],
                'details' => $methodDetails[$i],
                'is_default' => ($i === 0), // First one is default
            ]);
        }

        // Create 10 client companies
        $clientCompanies = [];
        for ($i = 0; $i < 10; $i++) {
            $clientCompanies[] = Company::create([
                'user_id' => $userId,
                'name' => $faker->company,
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'siret' => $faker->numerify('##############'),
                'tva_number' => 'FR'.$faker->numerify('##############'),
                'naf_code' => $faker->numerify('####').$faker->randomLetter,
                'country' => 'France',
                'is_own_company' => false,
            ]);
        }

        // Create 20 clients (some with companies, some without)
        $clients = [];
        for ($i = 0; $i < 20; $i++) {
            $hasCompany = $faker->boolean(70);
            $clients[] = Client::create([
                'user_id' => $userId,
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'country' => 'France',
                'company_id' => $hasCompany ? $clientCompanies[array_rand($clientCompanies)]->id : null,
            ]);
        }

        // Create 30 projects (multiple projects per client)
        $projects = [];
        foreach ($clients as $client) {
            $numProjects = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numProjects; $i++) {
                $projects[] = Project::create([
                    'client_id' => $client->id,
                    'name' => $faker->catchPhrase,
                    'description' => $faker->paragraph,
                    'status' => $faker->randomElement(['en_cours', 'termine', 'archive']),
                ]);
            }
        }

        // Create time entries for projects
        $timeEntries = [];
        foreach ($projects as $project) {
            $numEntries = $faker->numberBetween(5, 20);
            for ($i = 0; $i < $numEntries; $i++) {
                $date = $faker->dateTimeBetween('-6 months', 'now');

                // 50% chance to use start/end time, 50% chance to use duration only
                $useStartEndTime = $faker->boolean();

                if ($useStartEndTime) {
                    $startTime = clone $date;
                    $startTime->setTime(
                        $faker->numberBetween(8, 17),
                        $faker->randomElement([0, 15, 30, 45])
                    );

                    $endTime = clone $startTime;
                    $endTime->modify('+'.$faker->numberBetween(1, 8).' hours');

                    $timeEntries[] = TimeEntry::create([
                        'project_id' => $project->id,
                        'user_id' => $userId,
                        'description' => $faker->sentence,
                        'date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                } else {
                    $durationMinutes = $faker->numberBetween(30, 480); // 30 minutes to 8 hours

                    $timeEntries[] = TimeEntry::create([
                        'project_id' => $project->id,
                        'user_id' => $userId,
                        'description' => $faker->sentence,
                        'date' => $date,
                        'duration_minutes' => $durationMinutes,
                    ]);
                }
            }
        }

        // Create 40 invoices (some for each client)
        $invoices = [];
        foreach ($clients as $client) {
            $numInvoices = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numInvoices; $i++) {
                $date = $faker->dateTimeBetween('-6 months', 'now');
                $dueDate = (clone $date)->modify('+30 days');

                // Get client's projects
                $clientProjects = array_filter($projects, function ($project) use ($client) {
                    return $project->client_id === $client->id;
                });

                // If client has no projects, skip creating invoice
                if (empty($clientProjects)) {
                    continue;
                }

                // Randomly select one of the client's projects
                $project = $clientProjects[array_rand($clientProjects)];

                $invoice = Invoice::create([
                    'user_id' => $userId,
                    'client_id' => $client->id,
                    'company_id' => $ownCompany->id,
                    'project_id' => $project->id, // Add project_id
                    'invoice_number' => 'INV-'.$faker->unique()->numerify('######'),
                    'type' => 'invoice',
                    'status' => $faker->randomElement(['draft', 'sent', 'paid', 'overdue']),
                    'is_validated' => $faker->boolean(70),
                    'total_ht' => 0, // Will be calculated based on lines
                    'tva_rate' => $faker->randomElement([0, 5.5, 10, 20]),
                    'total_ttc' => 0, // Will be calculated
                    'issue_date' => $date,
                    'due_date' => $dueDate,
                    'payment_date' => $faker->optional(0.3)->dateTimeBetween($date, 'now'),
                    'payment_terms' => $faker->randomElement(['immediate', '15_days', '30_days', '45_days', '60_days', 'end_of_month']),
                    'payment_method' => $faker->randomElement(['bank_transfer', 'check', 'cash', 'credit_card', 'paypal']),
                    'notes' => $faker->optional(0.7)->paragraph,
                ]);

                // Create 1-5 invoice lines for each invoice
                $numLines = $faker->numberBetween(1, 5);
                $subtotal = 0;

                for ($j = 0; $j < $numLines; $j++) {
                    $quantity = $faker->randomFloat(2, 1, 10);
                    $unitPrice = $faker->randomFloat(2, 50, 500);
                    $amount = $quantity * $unitPrice;
                    $subtotal += $amount;

                    InvoiceLine::create([
                        'user_id' => $userId,
                        'invoice_id' => $invoice->id,
                        'item_type' => $faker->randomElement(['service', 'product']),
                        'is_expense' => $faker->boolean(20),
                        'description' => $faker->sentence,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percent' => $faker->randomElement([0, 5, 10, 15, 20]),
                        'tva_rate' => $invoice->tva_rate,
                        'total_ht' => $amount,
                    ]);
                }

                // Update invoice totals
                $taxAmount = $subtotal * ($invoice->tva_rate / 100);
                $total = $subtotal + $taxAmount;

                $invoice->update([
                    'total_ht' => $subtotal,
                    'total_ttc' => $total,
                ]);

                $invoices[] = $invoice;
            }
        }

        // Create URSSAF declarations
        for ($i = 0; $i < 12; $i++) {
            $month = $i + 1;
            $year = date('Y');
            $declaredRevenue = $faker->randomFloat(2, 1000, 5000);
            $chargeRate = 22.00; // Default charge rate
            $chargesAmount = $declaredRevenue * ($chargeRate / 100);
            $isPaid = $faker->boolean(70);

            URSSAFDeclaration::create([
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
                'declared_revenue' => $declaredRevenue,
                'charge_rate' => $chargeRate,
                'charges_amount' => $chargesAmount,
                'is_paid' => $isPaid,
                'payment_date' => $isPaid ? $faker->dateTimeBetween('-3 months', 'now') : null,
            ]);
        }

        echo "Comprehensive seeding completed successfully!\n";
    }
}
